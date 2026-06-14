<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'services' => Product::where('type', 'service')->count(),
            'goods' => Product::where('type', 'good')->count(),
            'invoices' => Invoice::count(),
            'revenue' => Invoice::where('status', 'paid')->sum('total'),
        ];

        $recentInvoices = Invoice::with('items.product')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentInvoices'));
    }
}
