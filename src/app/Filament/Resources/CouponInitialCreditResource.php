<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponInitialCreditResource\Pages;
use App\Filament\Resources\CouponInitialCreditResource\RelationManagers;
use App\Models\CouponInitialCredit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Placeholder;
use Carbon\Carbon;

class CouponInitialCreditResource extends Resource
{
    protected static ?string $model = CouponInitialCredit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'クレジット管理';
    protected static ?string $navigationLabel = 'クーポン管理';
    protected static ?string $modelLabel = 'クーポン';
    protected static ?string $pluralModelLabel = 'クーポン一覧';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('クーポンコード')
                    ->nullable()
                    ->dehydrateStateUsing(fn($state) => ($state === '' ? null : $state))
                    ->visibleOn('create'),

                Placeholder::make('code_display')
                    ->label('クーポンコード')
                    ->visibleOn('edit')
                    ->content(fn($record) => $record->code ?? '（未設定）'),
                Forms\Components\TextInput::make('credit')
                    ->label('初期クレジット')
                    ->numeric()
                    ->required()
                    ->extraAttributes([
                        'inputmode' => 'numeric',           // モバイルで数字キーパッド誘導
                        'style'     => 'ime-mode:disabled', // 一部ブラウザでIMEを抑制（非推奨だが実効性あり）
                    ]),
                Forms\Components\Toggle::make('is_active')
                    ->label('有効')
                    ->default(true),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DateTimePicker::make('valid_from')
                            ->label('有効開始日'),
                        Forms\Components\DateTimePicker::make('valid_until')
                            ->label('有効終了日'),
                    ]),
                Forms\Components\Textarea::make('promo_text')
                    ->label('宣伝用文章（コピー用）')
                    ->disabled() // 編集不可（コピーのみ）
                    ->visibleOn('edit') // 編集画面のみ表示
                    ->rows(6)
                    ->afterStateHydrated(function ($component, $record) {
                        if (! $record) return;

                        $code = $record->code ?? '（コード未設定）';
                        $url  = config('app.url') . '/register'; // ← 会員登録URL（適宜変更）

                        $validUntil = $record->valid_until
                            ? '有効期限：' . Carbon::parse($record->valid_until)->format('Y年m月d日 H:i')
                            : '';
                        $text = <<<TEXT
AIあべむつきのオトクなクーポンコードはこちら！

コード：{$code}
会員登録URL：{$url}

会員登録後、クーポンコードの入力画面で上記のコードを入力して
無料のクレジットを受け取ることができます！

{$validUntil}
TEXT;

                        $component->state($text);
                    }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('クーポンコード')->searchable(),
                Tables\Columns\TextColumn::make('credit')->label('初期クレジット')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('有効')->boolean(),
                Tables\Columns\TextColumn::make('valid_from')->label('有効開始日')->dateTime('Y年m月d日 H:i'),
                Tables\Columns\TextColumn::make('valid_until')->label('有効終了日')->dateTime('Y年m月d日 H:i'),
                Tables\Columns\TextColumn::make('created_at')->label('作成日')->dateTime('Y年m月d日 H:i')->sortable(),
            ])
            ->filters([
                // 必要に応じてフィルタ追加
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
            'index' => Pages\ListCouponInitialCredits::route('/'),
            'create' => Pages\CreateCouponInitialCredit::route('/create'),
            'edit' => Pages\EditCouponInitialCredit::route('/{record}/edit'),
        ];
    }
}
