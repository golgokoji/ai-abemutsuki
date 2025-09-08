<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('name')
            ->label('名前')
            ->required()
            ->maxLength(255),

        TextInput::make('email')
            ->label('メール')
            ->email()
            ->required()
            ->unique(ignoreRecord: true),

        Toggle::make('is_admin')
            ->label('管理者'),

        TextInput::make('credit_balance')
            ->label('クレジット残高')
            ->numeric()
            ->disabled(), // ここは編集不可（調整は後で別アクションで実装）
    ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')->label('名前')->searchable()->sortable(),
            TextColumn::make('email')->label('メール')->searchable(),
            IconColumn::make('is_admin')->label('管理者')->boolean()->sortable(),
            TextColumn::make('credit_balance')->label('クレジット残高')->sortable(),
            TextColumn::make('created_at')->label('作成日')->dateTime()->sortable(),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
