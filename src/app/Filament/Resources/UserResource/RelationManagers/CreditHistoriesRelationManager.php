<?php
namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use App\Models\CreditHistory;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class CreditHistoriesRelationManager extends RelationManager
{

    protected static string $relationship = 'creditHistories';
    protected static ?string $title = 'クレジット追加履歴（直近20件）';

    public function table(Tables\Table $table): Tables\Table
    {
        $userId = $this->getOwnerRecord()->getKey();
        return $table
            ->query(
                CreditHistory::query()
                    ->where('user_id', $userId)
                    ->latest('created_at')
                    ->limit(20)
            )
            ->columns([
                TextColumn::make('user_id')->label('ユーザーID'),
                TextColumn::make('credit')->label('クレジット数')->alignRight(),
                TextColumn::make('amount')->label('金額')->alignRight()->formatStateUsing(fn($state) => number_format($state) . '円'),
                TextColumn::make('note')->label('備考'),
                TextColumn::make('system')->label('付与者'),
                TextColumn::make('order_id')->label('注文ID'),
                TextColumn::make('granted_at')->label('日時')->formatStateUsing(fn($state) => $state ? date('Y/m/d H:i:s', strtotime($state)) : ''),
            ])
            ->paginated(false)
            ->headerActions([])
            ->actions([]);
    }
}
