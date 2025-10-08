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
        // ここでクーポン処理ロジックを実装
        // ...
        return redirect()->route('dashboard')->with('status', 'クーポン処理が完了しました');
    }
}
