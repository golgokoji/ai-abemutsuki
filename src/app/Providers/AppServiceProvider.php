<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Facades\FilamentAsset;
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

        // Flatpickr日本語化（Filament管理画面のカレンダー）
        Filament::serving(function () {
            Filament::registerRenderHook(
                'head.end',
                fn () => <<<HTML
                    <script src="https://npmcdn.com/flatpickr/dist/l10n/ja.js"></script>
                    <script>flatpickr.localize(flatpickr.l10ns.ja);</script>
                HTML
            );
        });
    }
}
