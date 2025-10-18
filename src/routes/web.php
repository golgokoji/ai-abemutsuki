<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TtsController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\AbelaboSettingsController;


use Illuminate\Support\Facades\Log;


$GLOBALS['__TIMESTAMP__'] = microtime(true);


// ルートクロージャの最初
// 台本作成方法ガイドページ
Route::get('/about-make-scripts', function() {
    return view('about_make_scripts');
})->name('about.make.scripts');
// クレジットチャージページ（誰でもアクセス可）
Route::get('/charge-page', function() {
    return view('charge');
})->name('charge.page');
Route::get('/test', function () {
    \App\Support\DebugTimer::log(__FILE__, __LINE__, 'testルート開始');
    // ...処理...
});
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
    // 動画削除
    Route::delete('/avatar-videos/{id}', [\App\Http\Controllers\AvatarVideoController::class, 'destroy'])->name('avatar_videos.destroy');

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
// サポートページ

Route::get('/support', function () {
    return view('support');
});


/*
|--------------------------------------------------------------------------
| Breeze が生成する auth ルートを最後に読み込み
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
