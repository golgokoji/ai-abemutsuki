<?php

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
Route::get('/', fn () => view('welcome'))->name('home');

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
    return redirect()->route('register.complete'); // ここを変更
})->name('google.callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/register/complete', [\App\Http\Controllers\RegisterCompleteController::class, 'show'])->name('register.complete');
    Route::post('/register/complete', [\App\Http\Controllers\RegisterCompleteController::class, 'store'])->name('register.complete.store');
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

/*
|--------------------------------------------------------------------------
| Breeze が生成する auth ルートを最後に読み込み
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
