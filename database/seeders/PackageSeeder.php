<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['name' => 'يومي 2 ميجا',    'speed' => '2 ميجا',  'duration' => '24 ساعة', 'price' => 5.00,   'cost' => 3.00],
            ['name' => 'أسبوعي 4 ميجا',  'speed' => '4 ميجا',  'duration' => '7 أيام',  'price' => 20.00,  'cost' => 12.00],
            ['name' => 'شهري 8 ميجا',    'speed' => '8 ميجا',  'duration' => '30 يوم',  'price' => 60.00,  'cost' => 40.00],
            ['name' => 'شهري 16 ميجا',   'speed' => '16 ميجا', 'duration' => '30 يوم',  'price' => 100.00, 'cost' => 70.00],
        ];

        foreach ($packages as $pkg) {
            Package::create($pkg);
        }
    }
}
