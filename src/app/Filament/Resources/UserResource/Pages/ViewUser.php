<?php
namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Widgets\CreditHistoryTableWidget;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'ユーザー詳細';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('会員情報編集'),
            Actions\Action::make('add_credit')
                ->label('クレジット追加')
                ->url(fn($record) => route('filament.admin.resources.credit-histories.create', ['user_id' => $record->id]))
                ->color('success'),
        ];
    }

    // ▼ここを Header から Footer に変更
    protected function getFooterWidgets(): array
    {
        return [
            CreditHistoryTableWidget::make([
                'userId' => $this->record->id,
            ]),
        ];
    }
}
