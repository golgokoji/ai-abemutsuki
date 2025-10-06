<?php
namespace App\Filament\Resources\CreditHistoriesResource\Pages;

use App\Filament\Resources\CreditHistoriesResource;
use Filament\Resources\Pages\EditRecord;

class EditCreditHistory extends EditRecord
{
    protected static string $resource = CreditHistoriesResource::class;

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        $userId = $this->record?->user_id;
        $userLink = null;
        if ($userId) {
            $userLink = route('filament.admin.resources.users.view', ['record' => $userId]);
        }
        $userId = $this->record?->user_id;
        $userLink = null;
        if ($userId) {
            $userLink = route('filament.admin.resources.users.view', ['record' => $userId]);
        }
        $userId = $this->record?->user_id;
        $userLink = null;
        if ($userId) {
            $userLink = route('filament.admin.resources.users.view', ['record' => $userId]);
        }
        return $form->schema([
            \Filament\Forms\Components\TextInput::make('credit')
                ->label('クレジット数')
                ->numeric()
                ->required(),
            \Filament\Forms\Components\TextInput::make('amount')
                ->label('金額')
                ->numeric()
                ->required(),
            \Filament\Forms\Components\Textarea::make('note')
                ->label('備考')
                ->rows(3),
            \Filament\Forms\Components\TextInput::make('system')
                ->label('システム')
                ->required(),
            \Filament\Forms\Components\TextInput::make('order_id')
                ->label('注文ID')
                ->required(),
            \Filament\Forms\Components\TextInput::make('user_id')
                ->label('ユーザーID')
                ->required(),
            $userLink ? \Filament\Forms\Components\View::make('filament.components.user-link-label')
                ->viewData(['url' => $userLink, 'userId' => $userId]) : null,
            \Filament\Forms\Components\TextInput::make('granted_at')
                ->label('付与日時')
                ->required(),
        ]);
    }
}
