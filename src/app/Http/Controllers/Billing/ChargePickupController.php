<?php
namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\PayzPendingGrant;
use App\Models\User;
use App\Models\CreditHistory;
use App\Support\StrEx;

class ChargePickupController extends Controller
{
    public function pickup(Request $request)
    {
        $purchaseUid = $request->query('purchase_uid');
        $queryEmail = $request->query('email');

        if (!$purchaseUid || !$queryEmail) {
            session()->flash('status', '不正なリンクです');
            return redirect()->route('dashboard');
        }

        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('url.intended', $request->fullUrl());
        }

        $pending = PayzPendingGrant::where('purchase_uid', $purchaseUid)->first();
        if (!$pending) {
            session()->flash('status', '該当の決済が見つかりません');
            Log::info('charge.pickup.denied', [
                'reason' => 'not_found',
                'purchase_uid' => $purchaseUid,
                'email' => $queryEmail,
                'loginEmail' => Auth::user()->email,
                'paymentEmail' => null,
            ]);
            return redirect()->route('dashboard');
        }
        if ($pending->claimed_at) {
            session()->flash('status', 'すでに引き取り済みです');
            Log::info('charge.pickup.denied', [
                'reason' => 'already_claimed',
                'purchase_uid' => $purchaseUid,
                'email' => $queryEmail,
                'loginEmail' => Auth::user()->email,
                'paymentEmail' => $pending->payment_email,
            ]);
            return redirect()->route('dashboard');
        }
        if ($pending->expires_at && now()->greaterThan($pending->expires_at)) {
            session()->flash('status', '引き取り期限を過ぎています');
            Log::info('charge.pickup.denied', [
                'reason' => 'expired',
                'purchase_uid' => $purchaseUid,
                'email' => $queryEmail,
                'loginEmail' => Auth::user()->email,
                'paymentEmail' => $pending->payment_email,
            ]);
            return redirect()->route('dashboard');
        }
        if (!StrEx::secureEquals(StrEx::normalizeEmail($queryEmail), StrEx::normalizeEmail($pending->payment_email))) {
            session()->flash('status', 'メール照合に失敗しました（リンク無効）');
            Log::info('charge.pickup.denied', [
                'reason' => 'email_mismatch',
                'purchase_uid' => $purchaseUid,
                'email' => $queryEmail,
                'loginEmail' => Auth::user()->email,
                'paymentEmail' => $pending->payment_email,
            ]);
            return redirect()->route('dashboard');
        }
        if (!StrEx::secureEquals(StrEx::normalizeEmail(Auth::user()->email), StrEx::normalizeEmail($queryEmail))) {
            session()->flash('status', 'ログイン中アカウントとメールが一致しません');
            Log::info('charge.pickup.denied', [
                'reason' => 'login_email_mismatch',
                'purchase_uid' => $purchaseUid,
                'email' => $queryEmail,
                'loginEmail' => Auth::user()->email,
                'paymentEmail' => $pending->payment_email,
            ]);
            return redirect()->route('dashboard');
        }

        DB::transaction(function () use ($pending) {
            $user = User::find(Auth::id());
            $user->credit_balance += $pending->credits;
            $user->save();
            CreditHistory::create([
                'user_id' => $user->id,
                'credit' => $pending->credits,
                'amount' => $pending->amount,
                'note' => 'Payz決済（メールリンク引き取り）: ' . $pending->payment_email,
                'granted_at' => now(),
                'meta' => json_encode([
                    'source' => 'payz-link',
                    'purchase_uid' => $pending->purchase_uid,
                ]),
            ]);
            $pending->claimed_user_id = $user->id;
            $pending->claimed_at = now();
            $pending->save();
            Log::info('charge.pickup.granted', [
                'user_id' => $user->id,
                'purchase_uid' => $pending->purchase_uid,
                'credits' => $pending->credits,
            ]);
        });

        session()->flash('status', '決済を引き取り、クレジットを付与しました');
        return redirect()->route('dashboard');
    }
}
