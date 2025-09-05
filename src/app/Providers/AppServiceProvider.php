<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 開発環境で ngrok を使うときだけ、ルートURLを上書き
        if (app()->environment('local') && env('TUNNEL_URL')) {
            URL::forceRootUrl(env('TUNNEL_URL'));
            URL::forceScheme('https');

            // app.url / filesystems.public.url も動的に合わせる（Storage::url() 対策）
            config(['app.url' => env('TUNNEL_URL')]);
            config(['filesystems.disks.public.url' => env('TUNNEL_URL') . '/storage']);
        }
    }
}
