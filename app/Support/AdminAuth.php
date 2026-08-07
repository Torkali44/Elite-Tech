<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAuth
{
    public static function email(): string
    {
        return strtolower(trim((string) config('admin.email', 'admin@elitetech.com')));
    }

    public static function isAdminEmail(string $email): bool
    {
        $expected = self::email();

        return $expected !== '' && hash_equals($expected, strtolower(trim($email)));
    }

    public static function user(): ?User
    {
        $email = self::email();
        if ($email === '') {
            return null;
        }

        return User::query()->whereRaw('LOWER(email) = ?', [$email])->first();
    }

    public static function passwordMatches(string $password, ?User $user = null): bool
    {
        $user ??= self::user();
        $configured = (string) config('admin.password');

        if ($configured !== '') {
            if (preg_match('/^\$2[ayb]\$/', $configured) === 1) {
                if (Hash::check($password, $configured)) {
                    return true;
                }
            } elseif (hash_equals($configured, $password)) {
                return true;
            }
        }

        return $user !== null && Hash::check($password, $user->password);
    }
}
