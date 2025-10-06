<?php

namespace App\Filament\Resources\CouponInitialCreditResource\Pages;

use App\Filament\Resources\CouponInitialCreditResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCouponInitialCredit extends EditRecord
{
    protected static string $resource = CouponInitialCreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'クーポン編集';
    }
}
