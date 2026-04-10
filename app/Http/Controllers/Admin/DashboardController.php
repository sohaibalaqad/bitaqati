<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Card;
use App\Models\Package;
use App\Models\Invoice;
use App\Models\RechargeRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalCards' => Card::available()->count(),
            'totalUserBalance' => User::where('role', 'client')->sum('balance'),
            'salesToday' => Invoice::paid()->whereDate('created_at', today())->sum('amount'),
            'activeUsers' => User::where('role', 'client')->where('status', 'active')->count(),
            'packages' => Package::withCount(['cards as available_count' => fn($q) => $q->where('status', 'available')])->get(),
            'pendingShipping' => RechargeRequest::pending()->count(),
            'recentSales' => Card::sold()->with(['package', 'buyer'])->latest('sold_at')->take(10)->get(),
        ];
        return view('admin.dashboard', $data);
    }
}
