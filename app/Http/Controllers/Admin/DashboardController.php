<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PawnTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalGudang = Item::where('location', Item::LOCATION_RAK_GUDANG)
            ->whereIn('status', [Item::STATUS_TERSIMPAN, Item::STATUS_DIPERPANJANG])
            ->count();

        $nearDueDateCount = PawnTransaction::whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG])
            ->whereBetween('due_date', [$today, $today->copy()->addDays(3)])
            ->count();

        $overdueCount = PawnTransaction::whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG])
            ->where('due_date', '<', $today)
            ->count();

        $siapLelangCount = Item::where('status', Item::STATUS_SIAP_LELANG)->count();

        $itemsActionNeeded = Item::with('pawnTransaction')
            ->whereIn('status', [Item::STATUS_SIAP_LELANG])
            ->orWhere(function ($q) use ($today) {
                $q->where('location', Item::LOCATION_RAK_GUDANG)
                  ->whereHas('pawnTransaction', function ($pt) use ($today) {
                      $pt->where('due_date', '<', $today);
                  });
            })
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalGudang',
            'nearDueDateCount',
            'overdueCount',
            'siapLelangCount',
            'itemsActionNeeded'
        ));
    }
}
