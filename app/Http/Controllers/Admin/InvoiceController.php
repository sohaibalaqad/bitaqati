<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['user', 'package', 'card'])->latest()->get();
        return view('admin.invoices', compact('invoices'));
    }
}
