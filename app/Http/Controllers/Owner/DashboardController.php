<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PawnPayment;
use App\Models\PawnTransaction;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Total Piutang Aktif (Total Plafon Pinjaman Gadai yang Belum Lunas)
        $totalPiutangAktif = PawnTransaction::whereIn('status', [
            PawnTransaction::STATUS_TERSIMPAN,
            PawnTransaction::STATUS_DIPERPANJANG
        ])->sum('loan_amount');

        // 2. Total Barang di Gudang
        $totalBarangGudang = Item::where('location', Item::LOCATION_RAK_GUDANG)
            ->whereIn('status', [Item::STATUS_TERSIMPAN, Item::STATUS_DIPERPANJANG])
            ->count();

        // 3. Estimasi Bunga Masuk Bulan Ini
        $bungaBulanIni = PawnPayment::whereBetween('payment_date', [$startOfMonth, Carbon::now()])
            ->sum('interest_amount');

        $dendaBulanIni = PawnPayment::whereBetween('payment_date', [$startOfMonth, Carbon::now()])
            ->sum('penalty_amount');

        // Total Kas Keluar (Gadai Baru Loan + Beli Bekas)
        $kasKeluarBeliBekas = PurchaseTransaction::whereBetween('purchase_date', [$startOfMonth, Carbon::now()])
            ->sum('purchase_price');

        // Recent Transactions Overview
        $recentPawns = PawnTransaction::with(['customer', 'item', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('owner.dashboard', compact(
            'totalPiutangAktif',
            'totalBarangGudang',
            'bungaBulanIni',
            'dendaBulanIni',
            'kasKeluarBeliBekas',
            'recentPawns'
        ));
    }
}
