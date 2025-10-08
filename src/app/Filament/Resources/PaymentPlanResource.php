<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentPlanResource\Pages;
use App\Filament\Resources\PaymentPlanResource\RelationManagers;
use App\Models\PaymentPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentPlanResource extends Resource
{
    protected static ?string $navigationGroup = '決済管理';
    
    protected static ?string $model = PaymentPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\View::make('admin.payment_plan_email_template'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('プラン名'),
                Forms\Components\TextInput::make('product_uid')
                    ->required()
                    ->label('商品ID'),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->label('金額'),
                Forms\Components\TextInput::make('credit')
                    ->numeric()
                    ->required()
                    ->label('付与クレジット'),
                Forms\Components\Toggle::make('is_active')
                    ->label('有効')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('プラン名'),
                Tables\Columns\TextColumn::make('product_uid')->label('商品ID'),
                Tables\Columns\TextColumn::make('amount')->label('金額'),
                Tables\Columns\TextColumn::make('credit')->label('付与クレジット'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('有効'),
            ])
            ->filters([
                // 必要ならフィルタ追加
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentPlans::route('/'),
            'create' => Pages\CreatePaymentPlan::route('/create'),
            'edit' => Pages\EditPaymentPlan::route('/{record}/edit'),
        ];
    }
}
