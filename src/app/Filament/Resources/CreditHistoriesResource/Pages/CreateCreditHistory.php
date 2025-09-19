<?php
namespace App\Filament\Resources\CreditHistoriesResource\Pages;

use App\Filament\Resources\CreditHistoriesResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Card;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;
use Filament\Actions;
class CreateCreditHistory extends CreateRecord
{
    protected static string $resource = CreditHistoriesResource::class;
    public ?User $user = null;

    

// …
protected function getFormActions(): array
{
    return [
        $this->getCreateFormAction()->label('クレジット追加'),
        $this->getCancelFormAction()->label('戻る'),
    ];
}



    protected function getRedirectUrl(): string
    {
        // 作成後はリソースの一覧へ
        return CreditHistoriesResource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        if ($record && $record->user_id && is_numeric($record->credit)) {
            $user = User::find($record->user_id);
            if ($user) {
                $user->credit_balance = ($user->credit_balance ?? 0) + $record->credit;
                $user->save();
            }
        }
    }

    public function getTitle(): string
    {
        return 'クレジット追加';
    }

    public function getBreadcrumbs(): array
    {
        $userId = request()->get('user_id');
        $user = $this->user;
        $userName = ($user instanceof User && is_string($user->name)) ? $user->name : '会員情報';
        $breadcrumbs = [
            route('filament.admin.resources.users.index') => 'ユーザー管理',
        ];
        if ($userId) {
            $breadcrumbs[route('filament.admin.resources.users.view', ['record' => $userId])] = $userName;
        }
        $breadcrumbs[''] = 'クレジット追加';
        return $breadcrumbs;
    }

    public function mount(): void
    {
        parent::mount();
        $userId = request()->get('user_id');
        if ($userId) {
            $user = User::find($userId);
            $this->user = ($user instanceof User) ? $user : null;
            
        }
    }

    public static function canCreateAnother(): bool
    {
        return false;
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        $userId = request()->get('user_id');
        $user = $userId ? \App\Models\User::find($userId) : null;
        $userInfoHtml = '';
        if ($user) {
            $userInfoHtml .= '<div style="margin-bottom:0.3em;"><span style="font-weight:600;color:#555;">ID：</span>' . e($user->id) . '</div>';
            $userInfoHtml .= '<div style="margin-bottom:0.3em;"><span style="font-weight:600;color:#555;">名前：</span>' . e($user->name) . '</div>';
            $userInfoHtml .= '<div><span style="font-weight:600;color:#555;">メール：</span>' . e($user->email) . '</div>';
        } else {
            $userInfoHtml .= '<div style="color:#c00;">ユーザー情報が取得できません</div>';
        }

        return $form->schema([
            Card::make([
                View::make('user-info-block')
                    ->view('filament.components.raw-html')
                    ->viewData(['html' => $userInfoHtml])
            ])
            ->heading('以下のユーザーにクレジットを付与します')
            ->columnSpanFull(),
            TextInput::make('credit')
                ->label('クレジット数')
                ->numeric()
                ->required()
                ->extraAttributes(['inputmode' => 'numeric', 'style' => 'ime-mode:disabled;'])
                ->columnSpanFull(),
            TextInput::make('amount')
                ->label('金額')
                ->numeric()
                ->required()
                ->default(0)
                ->extraAttributes(['inputmode' => 'numeric', 'style' => 'ime-mode:disabled;'])
                ->columnSpanFull(),
            Textarea::make('note')
                ->label('備考')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = $this->user ? $this->user->id : request()->get('user_id');
        $data['system'] = 'admin';
        $data['order_id'] = 'ADMIN_' . Str::random(8); // 英数字大文字・小文字可
        $data['granted_at'] = Carbon::now();
        // amountはフォーム入力値をそのまま使う
        return $data;
    }
}
