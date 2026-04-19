<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{
    public function index()
    {
        $cards    = Card::with(['package', 'buyer'])->latest()->get();
        $packages = Package::all();
        $users    = User::where('role', 'client')->where('status', 'active')->get();
        return view('admin.cards-store', compact('cards', 'packages', 'users'));
    }

    public function activeCards()
    {
        $soldToday  = Card::sold()->whereDate('sold_at', today())->count();
        $totalSold  = Card::sold()->count();
        $remaining  = Card::available()->count();
        $recentSold = Card::sold()->with(['package', 'buyer'])->latest('sold_at')->take(50)->get();
        return view('admin.active-cards', compact('soldToday', 'totalSold', 'remaining', 'recentSold'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'   => 'required|string|max:100',
            'password'   => 'required|string|max:255',
            'package_id' => 'required|exists:packages,id',
        ]);

        // Ensure the package belongs to the current tenant
        $this->authorizePackage($request->package_id);

        Card::create([
            'username'   => $request->username,
            'password'   => $request->password,
            'package_id' => $request->package_id,
            'status'     => 'available',
        ]);

        return back()->with('success', 'تم إضافة البطاقة بنجاح');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        // Ensure the package belongs to the current tenant — prevents cross-tenant card injection
        $this->authorizePackage($request->package_id);

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
        $inserted  = 0;
        $skipped   = 0;

        foreach ($cardsData as $card) {
            $username = trim($card['username'] ?? '');
            $password = trim($card['password'] ?? '');

            if (! $username || ! $password) {
                $skipped++;
                continue;
            }

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

        Log::info('Bulk card upload completed', [
            'admin_id'   => auth()->id(),
            'package_id' => $packageId,
            'inserted'   => $inserted,
            'skipped'    => $skipped,
        ]);

        return back()->with('success', "تم رفع {$inserted} بطاقة بنجاح" . ($skipped > 0 ? " (تم تجاهل {$skipped} مكررة أو فارغة)" : ''));
    }

    public function sell(Request $request, $id)
    {
        $card    = Card::available()->findOrFail($id);
        $package = $card->package;
        $soldTo  = $request->sold_to;
        $user    = null;

        if ($soldTo) {
            $user = User::findOrFail($soldTo);

            // Defense-in-depth: ensure the user belongs to the same tenant as the card
            if ($card->tenant_id && $user->tenant_id !== $card->tenant_id) {
                return back()->with('error', 'المستخدم لا ينتمي إلى هذه الشبكة');
            }

            if ($user->balance < $package->price) {
                return back()->with('error', 'رصيد المستخدم غير كافٍ');
            }

            $user->decrement('balance', $package->price);

            Transaction::create([
                'user_id' => $user->id,
                'type'    => 'purchase',
                'amount'  => $package->price,
                'note'    => 'شراء بطاقة - ' . $package->name,
            ]);
        }

        $card->update([
            'status'  => 'sold',
            'sold_to' => $user?->id,
            'sold_at' => now(),
        ]);

        Invoice::create([
            'user_id'    => $user?->id ?? auth()->id(),
            'package_id' => $package->id,
            'card_id'    => $card->id,
            'amount'     => $package->price,
            'status'     => 'paid',
        ]);

        return back()->with('success', 'تم بيع البطاقة بنجاح');
    }

    public function destroy($id)
    {
        Card::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف البطاقة');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /** Abort 403 if the package does not belong to the authenticated admin's tenant. */
    private function authorizePackage(int $packageId): void
    {
        $package = Package::findOrFail($packageId);
        if ((int) $package->tenant_id !== (int) auth()->user()->tenant_id) {
            abort(403, 'هذه الباقة لا تنتمي لشبكتك');
        }
    }
}
