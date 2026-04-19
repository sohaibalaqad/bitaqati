<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $ahmed = User::where('phone', '0599111111')->first();
        $sara  = User::where('phone', '0599222222')->first();
        $omar  = User::where('phone', '0599333333')->first();

        $pkg1 = Package::where('name', 'يومي 2 ميجا')->first();
        $pkg2 = Package::where('name', 'أسبوعي 4 ميجا')->first();
        $pkg3 = Package::where('name', 'شهري 8 ميجا')->first();
        $pkg4 = Package::where('name', 'شهري 16 ميجا')->first();

        $soldCard1 = Card::where('username', '340433433526')->first();
        $soldCard2 = Card::where('username', '789012345678')->first();
        $soldCard3 = Card::where('username', '456789012345')->first();

        $tenantId = app(\App\Services\TenantContext::class)->id();

        Invoice::insert([
            ['tenant_id' => $tenantId, 'user_id' => $ahmed->id, 'package_id' => $pkg2->id, 'card_id' => $soldCard1->id, 'amount' => 20.00,  'status' => 'paid',    'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => $tenantId, 'user_id' => $sara->id,  'package_id' => $pkg3->id, 'card_id' => $soldCard2->id, 'amount' => 60.00,  'status' => 'paid',    'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => $tenantId, 'user_id' => $omar->id,  'package_id' => $pkg1->id, 'card_id' => $soldCard3->id, 'amount' => 5.00,   'status' => 'paid',    'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => $tenantId, 'user_id' => $ahmed->id, 'package_id' => $pkg4->id, 'card_id' => null,           'amount' => 100.00, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
