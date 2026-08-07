<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Production seeder creates ONLY the required administrator account.
     */
    public function run(): void
    {
        $adminEmail    = config('admin.email', 'admin@elitetech.com');
        $adminPassword = config('admin.password', 'Admin12345');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name'              => 'إدارة النظام',
                'password'          => $adminPassword,
                'role'              => 'admin',
                'roles'             => ['admin'],
                'title'             => 'مدير النظام',
                'location'          => 'القاهرة',
                'bio'               => 'حساب الإدارة الرئيسي لمجتمع إليت تك.',
                'email_verified_at' => now(),
                'kyc_status'        => 'approved',
            ]
        );
    }
}
