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
