<?php

namespace App\Filament\Resources\CouponInitialCreditResource\Pages;

use App\Filament\Resources\CouponInitialCreditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCouponInitialCredits extends ListRecords
{
    protected static string $resource = CouponInitialCreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
