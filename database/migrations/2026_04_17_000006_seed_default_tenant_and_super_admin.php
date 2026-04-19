<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create default plan (unlimited)
        $planId = DB::table('plans')->insertGetId([
            'name'           => 'Default',
            'max_cards'      => 0,
            'max_users'      => 0,
            'max_packages'   => 0,
            'price'          => 0,
            'features'       => null,
            'is_active'      => 1,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 2. Find existing network admin
        $adminUser   = DB::table('users')->where('role', 'network_admin')->first();
        $networkName = DB::table('settings')->where('key', 'network_name')->value('value') ?? 'Default Network';

        // 3. Create default tenant
        $subdomain = env('DEFAULT_TENANT_SUBDOMAIN', 'default');
        $tenantId  = DB::table('tenants')->insertGetId([
            'name'       => $networkName,
            'subdomain'  => $subdomain,
            'owner_id'   => $adminUser?->id,
            'plan_id'    => $planId,
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Assign ALL existing data to the default tenant
        $tables = [
            'users', 'cards', 'packages', 'tickets', 'ticket_replies',
            'chat_messages', 'invoices', 'transactions', 'recharge_requests',
            'notifications', 'settings',
        ];
        foreach ($tables as $t) {
            DB::table($t)->whereNull('tenant_id')->update(['tenant_id' => $tenantId]);
        }

        // 5. Create Super Admin (platform owner — no tenant_id)
        $superEmail    = env('SUPER_ADMIN_EMAIL',    'superadmin@platform.com');
        $superPassword = env('SUPER_ADMIN_PASSWORD', 'changeme123');
        $superName     = env('SUPER_ADMIN_NAME',     'Super Admin');
        $superPhone    = env('SUPER_ADMIN_PHONE',    '0000000000');

        // Avoid duplicate if already exists
        $exists = DB::table('users')->where('email', $superEmail)->exists();
        if (! $exists) {
            DB::table('users')->insert([
                'tenant_id'  => null,
                'name'       => $superName,
                'email'      => $superEmail,
                'phone'      => $superPhone,
                'password'   => bcrypt($superPassword),
                'role'       => 'super_admin',
                'balance'    => 0,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remove super admin
        DB::table('users')->where('role', 'super_admin')->delete();

        // Clear tenant_id from all tables
        $tables = [
            'users', 'cards', 'packages', 'tickets', 'ticket_replies',
            'chat_messages', 'invoices', 'transactions', 'recharge_requests',
            'notifications', 'settings',
        ];
        foreach ($tables as $t) {
            DB::table($t)->update(['tenant_id' => null]);
        }

        // Remove tenants and plans
        DB::table('tenants')->truncate();
        DB::table('plans')->truncate();
    }
};
