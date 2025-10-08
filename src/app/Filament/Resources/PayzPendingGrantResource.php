<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayzPendingGrantResource\Pages;
use App\Models\PayzPendingGrant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PayzPendingGrantResource extends Resource
{
    protected static ?string $model = PayzPendingGrant::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Payz Pending Grants';
    protected static ?string $navigationGroup = '決済管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('charge_link')
                    ->label('Chargeリンク（コピペ用）')
                    ->content(fn($record) => $record ? url('/charge?purchase_uid=' . $record->purchase_uid . '&email=' . $record->payment_email) : '')
                    ->extraAttributes(['style' => 'font-family:monospace; background:#f8fafc; padding:8px; border-radius:6px;']),
                Forms\Components\TextInput::make('purchase_uid')->required(),
                Forms\Components\TextInput::make('payment_email'),
                Forms\Components\TextInput::make('amount')->numeric(),
                Forms\Components\TextInput::make('credit')->numeric(),
                Forms\Components\TextInput::make('claimed_user_id')->numeric(),
                Forms\Components\DateTimePicker::make('claimed_at'),
                Forms\Components\DateTimePicker::make('expires_at'),
                Forms\Components\Textarea::make('payload'),
                Forms\Components\DateTimePicker::make('created_at')->disabled(),
                Forms\Components\DateTimePicker::make('updated_at')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('purchase_uid')->searchable(),
                Tables\Columns\TextColumn::make('payment_email')->searchable(),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('credit'),
                Tables\Columns\TextColumn::make('claimed_user_id'),
                Tables\Columns\TextColumn::make('claimed_at')->dateTime(),
                Tables\Columns\TextColumn::make('expires_at')->dateTime(),
                Tables\Columns\TextColumn::make('payload')->limit(40),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                // 必要に応じてフィルタ追加
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayzPendingGrants::route('/'),
            'create' => Pages\CreatePayzPendingGrant::route('/create'),
            'edit' => Pages\EditPayzPendingGrant::route('/{record}/edit'),
        ];
    }
}
