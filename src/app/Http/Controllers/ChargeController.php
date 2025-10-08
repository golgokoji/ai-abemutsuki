<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PayzPendingGrant;
use App\Models\User;
use App\Models\CreditHistory;
use Illuminate\Support\Facades\DB;

class ChargeController extends Controller
{
    /**
     * GET: 確認画面表示
     */
    public function show(Request $request)
    {
        $purchaseUid = $request->query('purchase_uid');
        $email = $request->query('email');
        $user = auth()->user();
        if (!$purchaseUid || !$email || !$user) {
            return response('Invalid request', 400);
        }
        $pending = PayzPendingGrant::where('purchase_uid', $purchaseUid)
            ->where('payment_email', $email)
            ->first();
        if (!$pending) {
            return response('No pending found', 404);
        }
        // 既に付与済みなら確認画面で「付与済み」表示
        $alreadyCharged = CreditHistory::where('order_id', $purchaseUid)->exists();
        return view('charge_confirm', [
            'user' => $user,
            'pending' => $pending,
            'alreadyCharged' => $alreadyCharged,
            'purchaseUid' => $purchaseUid,
            'email' => $email,
        ]);
    }

    /**
     * POST: クレジット付与処理
     */
    public function store(Request $request)
    {
        $purchaseUid = $request->input('purchase_uid');
        $email = $request->input('email');
        $user = auth()->user();
        if (!$purchaseUid || !$email || !$user) {
            return response('Invalid request', 400);
        }
        $pending = PayzPendingGrant::where('purchase_uid', $purchaseUid)
            ->where('payment_email', $email)
            ->first();
        if (!$pending) {
            return response('No pending found', 404);
        }
        // 冪等性: 既に付与済みならスキップ
        if (CreditHistory::where('order_id', $purchaseUid)->exists()) {
            return redirect()->route('dashboard')->with('status', 'Already charged');
        }
        DB::transaction(function () use ($user, $pending, $purchaseUid) {
            CreditHistory::create([
                'user_id' => $user->id,
                'order_id' => $purchaseUid,
                'credit' => $pending->credit,
                'amount' => $pending->amount,
                'system' => 'payz_charge',
                'note' => 'charge link',
                'granted_at' => now(),
            ]);
            $user->increment('credit_balance', $pending->credit);
            $pending->delete();
        });
        Log::info('charge completed', ['purchase_uid' => $purchaseUid, 'email' => $email, 'user_id' => $user->id]);
        return redirect()->route('dashboard')->with('status', 'Charged successfully');
    }
}
