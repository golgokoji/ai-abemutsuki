<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CreditHistoriesResource\Pages;
use App\Models\CreditHistory;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;

class CreditHistoriesResource extends Resource
{
    protected static ?string $model = CreditHistory::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'クレジット履歴';
    protected static ?string $navigationGroup = 'クレジット管理';


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user_id')->sortable(),
                TextColumn::make('order_id')->searchable(),
                TextColumn::make('amount')->sortable(),
                TextColumn::make('credit')->sortable()->color(fn($state) => $state < 0 ? 'danger' : 'success'),
                TextColumn::make('system')->sortable(),
                TextColumn::make('granted_at')->dateTime()->sortable(),
                TextColumn::make('note')->limit(30),
            ])
            ->filters([
                Filter::make('system')
                    ->query(fn($query, $value) => $query->where('system', $value)),
            ])
            ->defaultSort('granted_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditHistories::route('/'),
            'create' => Pages\CreateCreditHistory::route('/create'),
            'edit' => Pages\EditCreditHistory::route('/{record}/edit'),
        ];
    }
}
