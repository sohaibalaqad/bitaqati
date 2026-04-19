<?php

namespace Database\Seeders;

use App\Models\RechargeRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class RechargeRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'network_admin')->first();
        $ahmed = User::where('phone', '0599111111')->first();
        $sara  = User::where('phone', '0599222222')->first();
        $omar  = User::where('phone', '0599333333')->first();
        $mona  = User::where('phone', '0599444444')->first();

        $tenantId = app(\App\Services\TenantContext::class)->id();

        RechargeRequest::insert([
            [
                'tenant_id'     => $tenantId,
                'user_id'       => $ahmed->id,
                'amount'        => 100.00,
                'note'          => 'شحن عبر التحويل البنكي',
                'status'        => 'approved',
                'handled_by'    => $admin->id,
                'reject_reason' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'tenant_id'     => $tenantId,
                'user_id'       => $sara->id,
                'amount'        => 50.00,
                'note'          => '',
                'status'        => 'pending',
                'handled_by'    => null,
                'reject_reason' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'tenant_id'     => $tenantId,
                'user_id'       => $omar->id,
                'amount'        => 200.00,
                'note'          => 'شحن عاجل',
                'status'        => 'pending',
                'handled_by'    => null,
                'reject_reason' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'tenant_id'     => $tenantId,
                'user_id'       => $mona->id,
                'amount'        => 30.00,
                'note'          => '',
                'status'        => 'rejected',
                'handled_by'    => $admin->id,
                'reject_reason' => 'لم يتم التحويل',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
