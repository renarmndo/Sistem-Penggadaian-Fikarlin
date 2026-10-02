<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PawnPayment;
use App\Models\PawnTransaction;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        // 1. Transaksi Gadai Baru
        $pawnTransactions = PawnTransaction::with(['customer', 'item', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        // 2. Pembayaran Bunga / Pelunasan / Perpanjangan
        $pawnPayments = PawnPayment::with(['pawnTransaction.customer', 'user'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->latest()
            ->get();

        // 3. Transaksi Pembelian Barang Bekas
        $purchaseTransactions = PurchaseTransaction::with(['customer', 'item', 'user'])
            ->whereBetween('purchase_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->latest()
            ->get();

        // 4. Barang Lelang & Terjual
        $auctionItems = Item::whereIn('status', [Item::STATUS_SIAP_LELANG, Item::STATUS_TERJUAL])
            ->latest()
            ->get();

        // Kalkulasi Laba / Rugi Periode
        $totalBungaMasuk = $pawnPayments->sum('interest_amount');
        $totalDendaMasuk = $pawnPayments->sum('penalty_amount');
        $totalPenjualanLelang = Item::where('status', Item::STATUS_TERJUAL)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('selling_price');

        $totalPengeluaranBeli = $purchaseTransactions->sum('purchase_price');
        $totalPinjamanDicairkan = $pawnTransactions->sum('loan_amount');

        $labaKotor = ($totalBungaMasuk + $totalDendaMasuk + $totalPenjualanLelang) - $totalPengeluaranBeli;

        return view('owner.reports.index', compact(
            'startDate',
            'endDate',
            'pawnTransactions',
            'pawnPayments',
            'purchaseTransactions',
            'auctionItems',
            'totalBungaMasuk',
            'totalDendaMasuk',
            'totalPenjualanLelang',
            'totalPengeluaranBeli',
            'totalPinjamanDicairkan',
            'labaKotor'
        ));
    }

    public function print(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        $type = $request->input('type', 'laba_rugi');

        $pawnTransactions = PawnTransaction::with(['customer', 'item'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $pawnPayments = PawnPayment::with(['pawnTransaction.customer'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $purchaseTransactions = PurchaseTransaction::with(['customer', 'item'])
            ->whereBetween('purchase_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $auctionItems = Item::whereIn('status', [Item::STATUS_SIAP_LELANG, Item::STATUS_TERJUAL])->get();

        $totalBungaMasuk = $pawnPayments->sum('interest_amount');
        $totalDendaMasuk = $pawnPayments->sum('penalty_amount');
        $totalPenjualanLelang = Item::where('status', Item::STATUS_TERJUAL)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('selling_price');
        $totalPengeluaranBeli = $purchaseTransactions->sum('purchase_price');

        $labaKotor = ($totalBungaMasuk + $totalDendaMasuk + $totalPenjualanLelang) - $totalPengeluaranBeli;

        return view('owner.reports.print', compact(
            'startDate',
            'endDate',
            'type',
            'pawnTransactions',
            'pawnPayments',
            'purchaseTransactions',
            'auctionItems',
            'totalBungaMasuk',
            'totalDendaMasuk',
            'totalPenjualanLelang',
            'totalPengeluaranBeli',
            'labaKotor'
        ));
    }
}
