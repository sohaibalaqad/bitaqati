<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Network Admin ──────────────────────────────────────────────────
        User::create([
            'name'     => 'صهيب العقاد',
            'phone'    => '0599000000',
            'email'    => 'sohaib@xnet-wifi.store',
            'password' => Hash::make('admin'),
            'role'     => 'network_admin',
            'balance'  => 0,
            'status'   => 'active',
        ]);

        // ── Clients ────────────────────────────────────────────────────────
        User::insert([
            [
                'name'       => 'أحمد محمد',
                'phone'      => '0599111111',
                'email'      => 'ahmed@example.com',
                'password'   => Hash::make('123456'),
                'role'       => 'client',
                'balance'    => 150.00,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'سارة علي',
                'phone'      => '0599222222',
                'email'      => 'sara@example.com',
                'password'   => Hash::make('123456'),
                'role'       => 'client',
                'balance'    => 85.50,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'عمر خالد',
                'phone'      => '0599333333',
                'email'      => 'omar@example.com',
                'password'   => Hash::make('123456'),
                'role'       => 'client',
                'balance'    => 200.00,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'منى حسن',
                'phone'      => '0599444444',
                'email'      => 'mona@example.com',
                'password'   => Hash::make('123456'),
                'role'       => 'client',
                'balance'    => 0,
                'status'     => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
