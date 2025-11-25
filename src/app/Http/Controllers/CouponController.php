<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    // クーポン入力フォーム表示
    public function show()
    {
        $user = Auth::user();
        return view('coupon_form', compact('user'));
    }

    // クーポンコード送信・処理（仮）
    public function store(Request $request)
    {
        $request->validate([
            'coupon_code' => ['required', 'string', 'max:32'],
        ]);
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }
        $code = trim($request->input('coupon_code', ''));
        // 重複利用防止
        if (\App\Models\CouponLog::where('code', $code)->where('email', $user->email)->exists()) {
            
            return back()->with('error', 'このクーポンは既に利用済みです');
        }
        // クーポン有効性チェック
        $resolver = app(\App\Services\InitialCreditResolver::class);
        $coupon = $resolver->getValidCoupon($code);
        $credit = $coupon ? (int)$coupon->credit : 0;
        \DB::transaction(function() use ($user, $credit, $code, $coupon) {
            if ($credit > 0) {
                $user->credit_balance = ($user->credit_balance ?? 0) + $credit;
                $user->save();

                \App\Models\CreditHistory::create([
                    'user_id'    => $user->id,
                    'amount'     => 0,
                    'credit'     => $credit,
                    'system'     => 'coupon',
                    'granted_at' => now(),
                    'note'       => 'クーポンによるクレジット付与 code:' . $code,
                ]);
            }
            \App\Models\CouponLog::create([
                'code' => $code,
                'email' => $user->email,
                'credit' => $credit,
                'user_id' => $user->id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
        return redirect()->route('dashboard')->with('status', 'クーポン処理が完了しました');
    }
}
