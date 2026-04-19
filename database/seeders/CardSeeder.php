<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $packages = Package::all();
        $ahmed    = User::where('phone', '0599111111')->first();
        $sara     = User::where('phone', '0599222222')->first();
        $omar     = User::where('phone', '0599333333')->first();

        // ── Available cards (5 per package) ───────────────────────────────
        foreach ($packages as $pkg) {
            for ($i = 1; $i <= 5; $i++) {
                Card::create([
                    'username'   => rand(100000000, 999999999),
                    'password'   => rand(100000, 999999),
                    'package_id' => $pkg->id,
                    'status'     => 'available',
                ]);
            }
        }

        // ── Sold cards ─────────────────────────────────────────────────────
        $pkg2 = Package::where('name', 'أسبوعي 4 ميجا')->first();
        $pkg3 = Package::where('name', 'شهري 8 ميجا')->first();
        $pkg1 = Package::where('name', 'يومي 2 ميجا')->first();

        Card::create([
            'username'   => '340433433526',
            'password'   => '564354',
            'package_id' => $pkg2->id,
            'status'     => 'sold',
            'sold_to'    => $ahmed->id,
            'sold_at'    => now()->subDays(2),
        ]);

        Card::create([
            'username'   => '789012345678',
            'password'   => '112233',
            'package_id' => $pkg3->id,
            'status'     => 'sold',
            'sold_to'    => $sara->id,
            'sold_at'    => now()->subDay(),
        ]);

        Card::create([
            'username'   => '456789012345',
            'password'   => '998877',
            'package_id' => $pkg1->id,
            'status'     => 'sold',
            'sold_to'    => $omar->id,
            'sold_at'    => now(),
        ]);
    }
}
