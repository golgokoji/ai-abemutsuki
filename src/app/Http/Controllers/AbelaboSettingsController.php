<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AbelaboUserSetting;

class AbelaboSettingsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $abelabo = AbelaboUserSetting::where('user_id', $user->id)->first();
        return view('abelabo.settings', [
            'user' => $user,
            'abelabo' => $abelabo,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'abelabo_name' => 'required|string|max:255',
            'abelabo_email' => [
                'required',
                'email',
                'max:255',
                // 他ユーザーと重複しないように
                'unique:abelabo_user_settings,email,' . $user->id . ',user_id',
            ],
            'abelabo_tel' => 'nullable|string|max:32',
        ], [
            'abelabo_email.unique' => 'このメールアドレスは使えません。',
        ]);
        $abelabo = AbelaboUserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $validated['abelabo_name'],
                'email' => $validated['abelabo_email'],
                'tel' => $validated['abelabo_tel'],
            ]
        );
        return redirect()->route('abelabo.settings')->with('status', 'あべラボ登録情報を保存しました');
    }
}
