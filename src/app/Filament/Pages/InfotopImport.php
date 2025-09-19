<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Jobs\ImportInfotopSalesJob;
use Illuminate\Support\Facades\Bus;

class InfotopImport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'インフォトップ売上取込';
    protected static string $view = 'filament.pages.infotop-import';

    public function import()
    {
        Bus::dispatch(new ImportInfotopSalesJob());
        Notification::make()
            ->title('売上取込を実行しました')
            ->success()
            ->send();
    }
}
