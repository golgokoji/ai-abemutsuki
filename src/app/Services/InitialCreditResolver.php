<?php
namespace App\Services;

use App\Models\CouponInitialCredit;

class InitialCreditResolver
{
    /**
     * クーポンコードに応じた初期クレジットを返す
     * @param string $code
     * @return int
     */
    public function resolve(string $code): int
    {
        $now = now();
        $coupon = CouponInitialCredit::where('code', $code)
            ->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
            })
            ->first();
        return $coupon ? (int)$coupon->credit : 0;
    }
    /**
     * クーポンの有効性を判定
     * @param string $code
     * @return CouponInitialCredit|null
     */
    public function getValidCoupon(string $code): ?CouponInitialCredit
    {
        $now = now();
        return CouponInitialCredit::where('code', $code)
            ->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
            })
            ->first();
    }
}
