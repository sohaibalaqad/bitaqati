<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $ahmed = User::where('phone', '0599111111')->first();
        $sara  = User::where('phone', '0599222222')->first();
        $omar  = User::where('phone', '0599333333')->first();

        $rows = [
            ['user_id' => $ahmed->id, 'type' => 'deposit',  'amount' => 200.00, 'note' => 'شحن رصيد'],
            ['user_id' => $ahmed->id, 'type' => 'purchase', 'amount' => 20.00,  'note' => 'شراء بطاقة - أسبوعي 4 ميجا'],
            ['user_id' => $sara->id,  'type' => 'deposit',  'amount' => 150.00, 'note' => 'شحن رصيد'],
            ['user_id' => $sara->id,  'type' => 'purchase', 'amount' => 60.00,  'note' => 'شراء بطاقة - شهري 8 ميجا'],
            ['user_id' => $omar->id,  'type' => 'deposit',  'amount' => 250.00, 'note' => 'شحن رصيد'],
            ['user_id' => $omar->id,  'type' => 'purchase', 'amount' => 5.00,   'note' => 'شراء بطاقة - يومي 2 ميجا'],
            ['user_id' => $omar->id,  'type' => 'withdraw', 'amount' => 45.00,  'note' => 'سحب رصيد'],
        ];

        foreach ($rows as &$row) {
            $row['created_at'] = now();
            $row['updated_at'] = now();
        }

        Transaction::insert($rows);
    }
}
