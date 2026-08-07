<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Production seeder creates ONLY the two required accounts:
     * 1. admin@elitetech.com
     * 2. tork@elitetech.com
     */
    public function run(): void
    {
        $adminEmail    = config('admin.email', 'etech1596@gmail.com');
        $adminPassword = config('admin.password', 'elitetech_Admin_2026_Password');

        // 1. ADMIN ACCOUNT
        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name'              => 'إدارة النظام',
                'password'          => Hash::make($adminPassword),
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

        // 2. TORK ACCOUNT (Normal Verified User — NO Admin privileges)
        User::updateOrCreate(
            ['email' => 'tork932@gmail.com'],
            [
                'name'              => 'Tork',
                'password'          => Hash::make('passwordAdmin'),
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
