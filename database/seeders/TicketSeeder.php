<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $ahmed = User::where('phone', '0599111111')->first();
        $sara  = User::where('phone', '0599222222')->first();
        $omar  = User::where('phone', '0599333333')->first();

        Ticket::insert([
            [
                'user_id'     => $ahmed->id,
                'title'       => 'البطاقة لا تعمل',
                'description' => 'اشتريت بطاقة ولم تعمل معي',
                'priority'    => 'high',
                'status'      => 'open',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'user_id'     => $sara->id,
                'title'       => 'استفسار عن الباقات',
                'description' => 'أريد معرفة الفرق بين الباقات',
                'priority'    => 'low',
                'status'      => 'resolved',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'user_id'     => $omar->id,
                'title'       => 'مشكلة في الرصيد',
                'description' => 'الرصيد لم يتم إضافته بعد الشحن',
                'priority'    => 'medium',
                'status'      => 'in-progress',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
