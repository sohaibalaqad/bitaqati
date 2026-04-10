<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Package;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\RechargeRequest;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Admin =====
        $admin = User::create([
            'name' => 'صهيب العقاد',
            'phone' => '0599000000',
            'email' => 'sohaib@xnet-wifi.store',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'balance' => 0,
            'status' => 'active',
        ]);

//        // ===== Clients =====
//        $ahmed = User::create([
//            'name' => 'أحمد محمد',
//            'phone' => '0599111111',
//            'email' => 'ahmed@example.com',
//            'password' => Hash::make('123456'),
//            'role' => 'client',
//            'balance' => 150.00,
//            'status' => 'active',
//        ]);
//
//        $sara = User::create([
//            'name' => 'سارة علي',
//            'phone' => '0599222222',
//            'email' => 'sara@example.com',
//            'password' => Hash::make('123456'),
//            'role' => 'client',
//            'balance' => 85.50,
//            'status' => 'active',
//        ]);
//
//        $omar = User::create([
//            'name' => 'عمر خالد',
//            'phone' => '0599333333',
//            'email' => 'omar@example.com',
//            'password' => Hash::make('123456'),
//            'role' => 'client',
//            'balance' => 200.00,
//            'status' => 'active',
//        ]);
//
//        $mona = User::create([
//            'name' => 'منى حسن',
//            'phone' => '0599444444',
//            'email' => 'mona@example.com',
//            'password' => Hash::make('123456'),
//            'role' => 'client',
//            'balance' => 0,
//            'status' => 'inactive',
//        ]);
//
//        // ===== Packages =====
//        $pkg1 = Package::create(['name' => 'يومي 2 ميجا', 'speed' => '2 ميجا', 'duration' => '24 ساعة', 'price' => 5.00, 'cost' => 3.00]);
//        $pkg2 = Package::create(['name' => 'أسبوعي 4 ميجا', 'speed' => '4 ميجا', 'duration' => '7 أيام', 'price' => 20.00, 'cost' => 12.00]);
//        $pkg3 = Package::create(['name' => 'شهري 8 ميجا', 'speed' => '8 ميجا', 'duration' => '30 يوم', 'price' => 60.00, 'cost' => 40.00]);
//        $pkg4 = Package::create(['name' => 'شهري 16 ميجا', 'speed' => '16 ميجا', 'duration' => '30 يوم', 'price' => 100.00, 'cost' => 70.00]);
//
//        // ===== Cards (Available) =====
//        $packages = [$pkg1, $pkg2, $pkg3, $pkg4];
//        foreach ($packages as $pkg) {
//            for ($i = 1; $i <= 5; $i++) {
//                Card::create([
//                    'username' => rand(100000000, 999999999),
//                    'password' => rand(100000, 999999),
//                    'package_id' => $pkg->id,
//                    'status' => 'available',
//                ]);
//            }
//        }
//
//        // ===== Sold Cards =====
//        $soldCard1 = Card::create([
//            'username' => '340433433526',
//            'password' => '564354',
//            'package_id' => $pkg2->id,
//            'status' => 'sold',
//            'sold_to' => $ahmed->id,
//            'sold_at' => now()->subDays(2),
//        ]);
//
//        $soldCard2 = Card::create([
//            'username' => '789012345678',
//            'password' => '112233',
//            'package_id' => $pkg3->id,
//            'status' => 'sold',
//            'sold_to' => $sara->id,
//            'sold_at' => now()->subDay(),
//        ]);
//
//        $soldCard3 = Card::create([
//            'username' => '456789012345',
//            'password' => '998877',
//            'package_id' => $pkg1->id,
//            'status' => 'sold',
//            'sold_to' => $omar->id,
//            'sold_at' => now(),
//        ]);
//
//        // ===== Invoices =====
//        Invoice::create(['user_id' => $ahmed->id, 'package_id' => $pkg2->id, 'card_id' => $soldCard1->id, 'amount' => 20.00, 'status' => 'paid']);
//        Invoice::create(['user_id' => $sara->id, 'package_id' => $pkg3->id, 'card_id' => $soldCard2->id, 'amount' => 60.00, 'status' => 'paid']);
//        Invoice::create(['user_id' => $omar->id, 'package_id' => $pkg1->id, 'card_id' => $soldCard3->id, 'amount' => 5.00, 'status' => 'paid']);
//        Invoice::create(['user_id' => $ahmed->id, 'package_id' => $pkg4->id, 'card_id' => null, 'amount' => 100.00, 'status' => 'pending']);
//
//        // ===== Transactions =====
//        Transaction::create(['user_id' => $ahmed->id, 'type' => 'deposit', 'amount' => 200.00, 'note' => 'شحن رصيد']);
//        Transaction::create(['user_id' => $ahmed->id, 'type' => 'purchase', 'amount' => 20.00, 'note' => 'شراء بطاقة - أسبوعي 4 ميجا']);
//        Transaction::create(['user_id' => $sara->id, 'type' => 'deposit', 'amount' => 150.00, 'note' => 'شحن رصيد']);
//        Transaction::create(['user_id' => $sara->id, 'type' => 'purchase', 'amount' => 60.00, 'note' => 'شراء بطاقة - شهري 8 ميجا']);
//        Transaction::create(['user_id' => $omar->id, 'type' => 'deposit', 'amount' => 250.00, 'note' => 'شحن رصيد']);
//        Transaction::create(['user_id' => $omar->id, 'type' => 'purchase', 'amount' => 5.00, 'note' => 'شراء بطاقة - يومي 2 ميجا']);
//        Transaction::create(['user_id' => $omar->id, 'type' => 'withdraw', 'amount' => 45.00, 'note' => 'سحب رصيد']);
//
//        // ===== Recharge Requests =====
//        RechargeRequest::create(['user_id' => $ahmed->id, 'amount' => 100.00, 'note' => 'شحن عبر التحويل البنكي', 'status' => 'approved', 'handled_by' => $admin->id]);
//        RechargeRequest::create(['user_id' => $sara->id, 'amount' => 50.00, 'note' => '', 'status' => 'pending']);
//        RechargeRequest::create(['user_id' => $omar->id, 'amount' => 200.00, 'note' => 'شحن عاجل', 'status' => 'pending']);
//        RechargeRequest::create(['user_id' => $mona->id, 'amount' => 30.00, 'note' => '', 'status' => 'rejected', 'reject_reason' => 'لم يتم التحويل', 'handled_by' => $admin->id]);
//
//        // ===== Tickets =====
//        Ticket::create(['user_id' => $ahmed->id, 'title' => 'البطاقة لا تعمل', 'description' => 'اشتريت بطاقة ولم تعمل معي', 'priority' => 'high', 'status' => 'open']);
//        Ticket::create(['user_id' => $sara->id, 'title' => 'استفسار عن الباقات', 'description' => 'أريد معرفة الفرق بين الباقات', 'priority' => 'low', 'status' => 'resolved']);
//        Ticket::create(['user_id' => $omar->id, 'title' => 'مشكلة في الرصيد', 'description' => 'الرصيد لم يتم إضافته بعد الشحن', 'priority' => 'medium', 'status' => 'in-progress']);
    }
}
