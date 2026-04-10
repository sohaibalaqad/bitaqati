<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;

class BalanceController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'client')->get();
        $totalBalance = $users->sum('balance');
        $transactions = Transaction::with('user')->latest()->take(30)->get();
        return view('admin.balances', compact('users', 'totalBalance', 'transactions'));
    }
}
