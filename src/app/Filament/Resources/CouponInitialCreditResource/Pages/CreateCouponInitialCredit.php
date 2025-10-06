<?php
namespace App\Filament\Resources\CouponInitialCreditResource\Pages;

use App\Filament\Resources\CouponInitialCreditResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCouponInitialCredit extends CreateRecord
{
    protected static string $resource = CouponInitialCreditResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $code = trim((string)($data['code'] ?? '')) ?: null;
        $data['code'] = $code;

        if ($code === null) {
            $exists = \App\Models\CouponInitialCredit::query()->whereNull('code')->exists();
            if ($exists) {
                Notification::make()
                    ->title('空欄（未設定）のクーポンコードは既に1件登録されています。')
                    ->danger()
                    ->send();
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'code' => '空欄（未設定）のクーポンコードは既に1件登録されています。',
                ]);
            }
        } else {
            $exists = \App\Models\CouponInitialCredit::query()->where('code', $code)->exists();
            if ($exists) {
                Notification::make()
                    ->title('このクーポンコードは既に登録されています。')
                    ->danger()
                    ->send();
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'code' => 'このクーポンコードは既に登録されています。',
                ]);
            }
        }

        return $data;
    }

}