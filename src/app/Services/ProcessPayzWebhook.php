<?php
namespace App\Services;

class ProcessPayzWebhook
{
    public static function amountToCredit(int $amount): int
    {
        $map = config('billing.amount_to_credits', []);
        if (isset($map[$amount])) {
            return $map[$amount];
        }
        $rate = config('billing.default_rate', 500);
        return intdiv($amount, $rate);
    }
}
