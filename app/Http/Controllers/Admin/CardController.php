<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Package;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::with(['package', 'buyer'])->latest()->get();
        $packages = Package::all();
        $users = User::where('role', 'client')->where('status', 'active')->get();
        return view('admin.cards-store', compact('cards', 'packages', 'users'));
    }

    public function activeCards()
    {
        $soldToday = Card::sold()->whereDate('sold_at', today())->count();
        $totalSold = Card::sold()->count();
        $remaining = Card::available()->count();
        $recentSold = Card::sold()->with(['package', 'buyer'])->latest('sold_at')->take(50)->get();
        return view('admin.active-cards', compact('soldToday', 'totalSold', 'remaining', 'recentSold'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'package_id' => 'required|exists:packages,id',
        ]);

        Card::create([
            'username' => $request->username,
            'password' => $request->password,
            'package_id' => $request->package_id,
            'status' => 'available',
        ]);

        return back()->with('success', 'تم إضافة البطاقة بنجاح');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        // Accept parsed_cards JSON string OR cards array directly
        $cardsData = [];
        if ($request->filled('parsed_cards')) {
            $cardsData = json_decode($request->input('parsed_cards'), true) ?? [];
        } elseif ($request->has('cards')) {
            $cardsData = $request->input('cards');
        }

        if (empty($cardsData)) {
            return back()->with('error', 'لم يتم العثور على بطاقات في الملف المرفوع. تأكد من تنسيق الملف.')->withInput();
        }

        $packageId = $request->package_id;
        $inserted = 0;
        $skipped = 0;

        foreach ($cardsData as $card) {
            $username = trim($card['username'] ?? '');
            $password = trim($card['password'] ?? '');
            if (!$username || !$password) { $skipped++; continue; }

            // Skip duplicates
            if (Card::where('username', $username)->where('package_id', $packageId)->exists()) {
                $skipped++;
                continue;
            }

            Card::create([
                'username'   => $username,
                'password'   => $password,
                'package_id' => $packageId,
                'status'     => 'available',
            ]);
            $inserted++;
        }

        \Illuminate\Support\Facades\Log::info("Bulk card upload: {$inserted} inserted, {$skipped} skipped", ['package_id' => $packageId]);
        return back()->with('success', "تم رفع {$inserted} بطاقة بنجاح" . ($skipped > 0 ? " (تم تجاهل {$skipped} مكررة أو فارغة)" : ''));
    }

    public function sell(Request $request, $id)
    {
        $card = Card::available()->findOrFail($id);
        $package = $card->package;

        $soldTo = $request->sold_to;
        $user = $soldTo ? User::findOrFail($soldTo) : null;

        if ($user) {
            if ($user->balance < $package->price) {
                return back()->with('error', 'رصيد المستخدم غير كافٍ');
            }
            $user->decrement('balance', $package->price);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => $package->price,
                'note' => 'شراء بطاقة - ' . $package->name,
            ]);
        }

        $card->update([
            'status' => 'sold',
            'sold_to' => $user?->id,
            'sold_at' => now(),
        ]);

        Invoice::create([
            'user_id' => $user?->id ?? auth()->id(),
            'package_id' => $package->id,
            'card_id' => $card->id,
            'amount' => $package->price,
            'status' => 'paid',
        ]);

        return back()->with('success', 'تم بيع البطاقة بنجاح');
    }

    public function destroy($id)
    {
        Card::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف البطاقة');
    }
}
