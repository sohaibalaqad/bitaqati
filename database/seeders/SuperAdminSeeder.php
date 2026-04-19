<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create (or update) the platform Super Admin account.
     * Credentials are read from .env — never hard-coded.
     *
     * Required .env keys:
     *   SUPER_ADMIN_NAME
     *   SUPER_ADMIN_EMAIL
     *   SUPER_ADMIN_PHONE
     *   SUPER_ADMIN_PASSWORD
     */
    public function run(): void
    {
        $name     = env('SUPER_ADMIN_NAME',     'Super Admin');
        $email    = env('SUPER_ADMIN_EMAIL',    'superadmin@platform.com');
        $phone    = env('SUPER_ADMIN_PHONE',    '0000000000');
        $password = env('SUPER_ADMIN_PASSWORD', 'changeme123');

        DB::table('users')->updateOrInsert(
            ['email' => $email],
            [
                'tenant_id'  => null,
                'name'       => $name,
                'email'      => $email,
                'phone'      => $phone,
                'password'   => Hash::make($password),
                'role'       => 'super_admin',
                'balance'    => 0,
                'status'     => 'active',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
