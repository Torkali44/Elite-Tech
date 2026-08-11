<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuth
{
    public static function email(): string
    {
        return strtolower(trim((string) config('admin.email')));
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

    /**
     * CRIT-02: Plaintext password comparison removed entirely.
     * Admin password MUST be a bcrypt hash stored in ADMIN_PASSWORD env var.
     * Comparison is always against the DB-stored hash using Hash::check().
     */
    public static function passwordMatches(string $password, ?User $user = null): bool
    {
        $user ??= self::user();

        if ($user === null) {
            return false;
        }

        // Enforce bcrypt-only: reject if the stored password is not a recognised hash.
        if (! str_starts_with($user->password, '$2')) {
            Log::warning('AdminAuth: stored password is not a bcrypt hash — admin login blocked.');
            return false;
        }

        return Hash::check($password, $user->password);
    }
}
