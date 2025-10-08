<?php
use Illuminate\Http\Request;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TtsController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\AbelaboSettingsController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
// クレジット付与リンク（認証必須）
Route::middleware(['auth'])->group(function () {
    Route::get('/charge', [\App\Http\Controllers\ChargeController::class, 'show'])->name('charge.show');
    Route::post('/charge', [\App\Http\Controllers\ChargeController::class, 'store'])->name('charge.store');
});
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/_sess', function (Request $r) {
    if (! $r->session()->has('probe')) {
        $token = Str::random(8);
        $r->session()->put('probe', $token);
        return response()->json(['action' => 'write', 'token' => $token]);
    }
    return response()->json(['action' => 'read', 'token' => $r->session()->get('probe')]);
});
/*
|--------------------------------------------------------------------------
| Google OAuth (Socialite)
|--------------------------------------------------------------------------
| ※ Google Cloud Console 側のリダイレクトURIは
|    http://localhost:8080/auth/google/callback
|    に合わせてください。
*/
Route::get('/auth/google/redirect', function () {
    // 開発中は stateless() のほうがトラブル少ない場合があります
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    // 必要に応じて ->stateless()
    $googleUser = Socialite::driver('google')->user();

    $user = \App\Models\User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name'     => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Google User'),
            'password' => bcrypt(str()->random(32)), // ダミー
        ]
    );

    Auth::login($user);
    // 新規登録かどうか判定（created_atとupdated_atがほぼ同じなら新規）
    if ($user->created_at->eq($user->updated_at)) {
        return redirect()->route('register.complete');
    } else {
        return redirect()->route('dashboard');
    }
})->name('google.callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/register/complete', [\App\Http\Controllers\RegisterCompleteController::class, 'show'])->name('register.complete');
    Route::post('/register/complete', [\App\Http\Controllers\RegisterCompleteController::class, 'store'])->name('register.complete.store');
    // クーポン入力フォーム
    Route::get('/coupon', [\App\Http\Controllers\CouponController::class, 'show'])->name('coupon.form');
    Route::post('/coupon', [\App\Http\Controllers\CouponController::class, 'store'])->name('coupon.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // 動画一覧
    Route::get('/avatar-videos', [\App\Http\Controllers\AvatarVideoController::class, 'index'])->name('avatar_videos.index');

    // Breeze 既定のダッシュボード
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/voices', [\App\Http\Controllers\VoiceController::class, 'index'])->name('voices.index');
    Route::get('/avatar-videos', [\App\Http\Controllers\AvatarVideoController::class, 'index'])->name('avatar_videos.index');

    // プロフィール（Breeze）
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    // TTS フォーム & 送信
        // 管理画面：クレジット履歴一覧
    Route::get('/tts',  [TtsController::class, 'form'])->name('tts.form');
    Route::post('/tts', [TtsController::class, 'submit'])->name('tts.submit');

    // HeyGen アバター生成
    Route::post('/avatar/{voice}', [AvatarController::class, 'generate'])->name('avatar.generate');

    // Abelabo 設定
    Route::get('/abelabo/settings', [AbelaboSettingsController::class, 'edit'])
        ->name('abelabo.settings');
Route::post('/abelabo/settings', [AbelaboSettingsController::class, 'update'])
    ->name('abelabo.settings.update')
    ->middleware(['auth']);

    // クレジット履歴（ユーザー向け）
    Route::get('/credit-history', [\App\Http\Controllers\CreditHistoryController::class, 'index'])->name('credit_history');
    Route::get('/credit-history/{id}', [\App\Http\Controllers\CreditHistoryController::class, 'show'])->name('credit_history.show');
});

use App\Http\Controllers\HeygenTestController;

Route::get('/heygen-test', [HeygenTestController::class, 'create']);

/*
|--------------------------------------------------------------------------
| 利用規約・プライバシーポリシー
|--------------------------------------------------------------------------
*/
Route::get('/terms', function() {
    return view('terms');
})->name('terms');

Route::get('/privacy', function() {
    return view('privacy');
})->name('privacy');
Route::get('/law', function() {
    return view('law');
})->name('law');
// FAQページ
Route::get('/faq', function () {
    return view('faq');
});
/*
|--------------------------------------------------------------------------
| Breeze が生成する auth ルートを最後に読み込み
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
