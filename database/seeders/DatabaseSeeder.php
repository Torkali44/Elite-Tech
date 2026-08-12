<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Production seeder creates ONLY the required admin account.
     *
     * Credentials are loaded exclusively from environment variables via config/admin.php.
     * ADMIN_PASSWORD must be a bcrypt hash in .env.
     * If an env var is missing, that account is simply skipped — no fallback.
     *
     * Generate a bcrypt hash with:
     *   php artisan tinker --execute="echo bcrypt('your-password');"
     */
    public function run(): void
    {
        $adminEmail    = config('admin.email');
        $adminPassword = config('admin.password');

        // ADMIN ACCOUNT
        if ($adminEmail && $adminPassword) {
            $admin = User::firstOrNew(['email' => $adminEmail]);
            $admin->forceFill([
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
            ])->save();
        }
    }
}
