<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Card;
use App\Models\Package;

class SalesReportController extends Controller
{
    public function index()
    {
        $totalRevenue = Invoice::paid()->sum('amount');
        $todayRevenue = Invoice::paid()->whereDate('created_at', today())->sum('amount');
        $monthRevenue = Invoice::paid()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');
        $totalSold = Card::sold()->count();

        $packageStats = Package::withCount(['cards as sold_count' => fn($q) => $q->where('status', 'sold')])
            ->withSum(['cards as revenue' => fn($q) => $q->where('status', 'sold')], 'id')
            ->get()
            ->map(function ($package) {
                $package->revenue = Invoice::where('package_id', $package->id)->paid()->sum('amount');
                return $package;
            });

        $recentInvoices = Invoice::paid()->with(['user', 'package'])->latest()->take(20)->get();

        return view('admin.sales-reports', compact(
            'totalRevenue', 'todayRevenue', 'monthRevenue', 'totalSold',
            'packageStats', 'recentInvoices'
        ));
    }
}
