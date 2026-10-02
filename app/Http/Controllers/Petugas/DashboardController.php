<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\PawnTransaction;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayPawnCount = PawnTransaction::whereDate('created_at', $today)->count();
        $todayPurchaseCount = PurchaseTransaction::whereDate('created_at', $today)->count();
        
        $recentTransactions = PawnTransaction::with(['customer', 'item'])
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact('todayPawnCount', 'todayPurchaseCount', 'recentTransactions'));
    }
}
