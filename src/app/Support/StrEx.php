<?php
namespace App\Support;

class StrEx
{
    public static function normalizeEmail(?string $email): ?string
    {
        return $email ? mb_strtolower(trim($email)) : null;
    }

    public static function secureEquals($a, $b): bool
    {
        return hash_equals((string)$a, (string)$b);
    }
}
