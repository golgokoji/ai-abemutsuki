<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\InitialCreditResolver;
use App\Models\CouponLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\CreditHistory;

class RegisterCompleteController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }
        return view('auth.register_complete', [
            'user' => $user,
        ]);
    }

    public function store(Request $request, InitialCreditResolver $resolver)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }
        $code = trim($request->input('coupon_code', ''));
        // 重複利用防止
        if (CouponLog::where('code', $code)->where('email', $user->email)->exists()) {
            return back()->withErrors(['coupon_code' => 'このクーポンは既に利用済みです']);
        }
        // クーポン有効性チェック
        $coupon = $resolver->getValidCoupon($code);
        $credit = $coupon ? (int)$coupon->credit : 0;
        // クレジット付与
        DB::transaction(function() use ($user, $credit, $code, $coupon) {
            if ($credit > 0) {
                $user->credit_balance = ($user->credit_balance ?? 0) + $credit;
                $user->save();

                CreditHistory::create([
                    'user_id'    => $user->id,
                    'amount'     => $credit,
                    'credit'     => $user->credit_balance,
                    'system'     => 'coupon',
                    'granted_at' => now(),
                    'note'       => 'クーポンによるクレジット付与 code:' . $code,
                ]);
            }
            CouponLog::create([
                'code' => $code,
                'email' => $user->email,
                'credit' => $credit,
                'user_id' => $user->id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
        return redirect('/dashboard')->with('status', '初期クレジットが付与されました');
    }
}
