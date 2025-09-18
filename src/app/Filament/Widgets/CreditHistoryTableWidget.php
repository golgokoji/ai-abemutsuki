<?php
namespace App\Filament\Widgets;

use App\Models\CreditHistory;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class CreditHistoryTableWidget extends TableWidget
{
    public ?int $userId = null;
    // ❌ protected static ?string $record = 'record'; は不要です
    protected static ?string $heading = 'クレジット追加履歴（直近20件）';
    protected int|string|array $columnSpan = 'full';

protected function getTableQuery(): Builder
{
    if (!$this->userId) {
        // userIdが未設定なら空クエリ
        return CreditHistory::query()->whereRaw('0=1');
    }
    return CreditHistory::query()
        ->where('user_id', $this->userId)
        ->latest('created_at')
        ->limit(20);
}

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('granted_at')->label('日時')->dateTime(),
            TextColumn::make('credit')->label('クレジット数')->alignRight(),
            TextColumn::make('amount')->label('金額')->alignRight(),
            TextColumn::make('note')->label('備考'),
            TextColumn::make('system')->label('付与者'),
            TextColumn::make('order_id')->label('注文ID'),
        ];
    }
}
