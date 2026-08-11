<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Production seeder creates ONLY the two required admin accounts.
     *
     * Credentials are loaded exclusively from environment variables via config/admin.php.
     * ADMIN_PASSWORD / ADMIN2_PASSWORD must be bcrypt hashes in .env.
     * If an env var is missing, that account is simply skipped — no fallback.
     *
     * Generate a bcrypt hash with:
     *   php artisan tinker --execute="echo bcrypt('your-password');"
     */
    public function run(): void
    {
        $adminEmail    = config('admin.email');
        $adminPassword = config('admin.password');

        // 1. PRIMARY ADMIN ACCOUNT
        if ($adminEmail && $adminPassword) {
            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name'              => 'إدارة النظام',
                    'password'          => $adminPassword,  // already a bcrypt hash from env
                    'role'              => 'admin',
                    'roles'             => ['admin'],
                    'title'             => 'مدير النظام',
                    'location'          => 'القاهرة',
                    'bio'               => 'حساب الإدارة الرئيسي لمجتمع إليت تك.',
                    'email_verified_at' => now(),
                    'kyc_status'        => 'approved',
                    'is_suspended'      => false,
                ]
            );
        }

        $admin2Email    = config('admin.email2');
        $admin2Password = config('admin.password2');

        // 2. SECONDARY ADMIN ACCOUNT (Tork)
        if ($admin2Email && $admin2Password) {
            User::updateOrCreate(
                ['email' => $admin2Email],
                [
                    'name'              => 'Tork',
                    'password'          => $admin2Password,  // already a bcrypt hash from env
                    'role'              => 'admin',
                    'roles'             => ['admin'],
                    'title'             => 'Tork Account',
                    'location'          => 'Cairo',
                    'bio'               => 'Tork Account',
                    'email_verified_at' => now(),
                    'kyc_status'        => 'approved',
                    'is_suspended'      => false,
                ]
            );
        }
    }
}
