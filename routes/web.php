<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\Petugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root berdasarkan status auth
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return match ($user->role) {
            \App\Models\User::ROLE_OWNER => redirect()->route('owner.dashboard'),
            \App\Models\User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            \App\Models\User::ROLE_PETUGAS => redirect()->route('petugas.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Role Route Group: Petugas Front-Office (FR-1.1 - FR-1.5, FR-2.4)
Route::middleware(['auth', 'role:petugas,admin,owner'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [Petugas\DashboardController::class, 'index'])->name('dashboard');

    // FR-1.1: Master Data Nasabah
    Route::get('/customers/search-json', [Petugas\CustomerController::class, 'searchJson'])->name('customers.search-json');
    Route::resource('customers', Petugas\CustomerController::class);

    // FR-1.2 & FR-1.5: Transaksi Gadai Baru & SBG
    Route::get('/pawn/{pawnTransaction}/sbg', [Petugas\PawnTransactionController::class, 'printSbg'])->name('pawn.sbg');
    Route::resource('pawn', Petugas\PawnTransactionController::class)->except(['destroy', 'edit', 'update']);

    // FR-1.3 & FR-1.5: Pelunasan & Perpanjangan (Scan SBG Barcode)
    Route::get('/payments/search', [Petugas\PawnPaymentController::class, 'search'])->name('payments.search');
    Route::post('/payments', [Petugas\PawnPaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{pawnPayment}/receipt', [Petugas\PawnPaymentController::class, 'printReceipt'])->name('payments.receipt');

    // FR-1.4 & FR-1.5: Transaksi Beli Barang Bekas
    Route::get('/purchases/{purchaseTransaction}/nota', [Petugas\PurchaseTransactionController::class, 'printNota'])->name('purchases.nota');
    Route::resource('purchases', Petugas\PurchaseTransactionController::class)->except(['destroy', 'edit', 'update']);

    // FR-2.4: Modul Penjualan Lelang Front-Office
    Route::get('/auction', [Petugas\AuctionSaleController::class, 'index'])->name('auction.index');
    Route::post('/auction/{item}/sell', [Petugas\AuctionSaleController::class, 'processSale'])->name('auction.sell');
    Route::get('/auction/{item}/receipt', [Petugas\AuctionSaleController::class, 'printReceipt'])->name('auction.receipt');

    // Buku Kas & Riwayat Transaksi Kasir Harian
    Route::get('/daily-transactions', [Petugas\DailyTransactionController::class, 'index'])->name('transactions.daily');
    Route::get('/daily-transactions/print', [Petugas\DailyTransactionController::class, 'printDaily'])->name('transactions.print-daily');
});

// Role Route Group: Admin Gudang (FR-2.1 - FR-2.4)
Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // FR-2.1: Kelola Stok & Lokasi Rak Gudang
    Route::get('/warehouse', [Admin\WarehouseController::class, 'index'])->name('warehouse.index');
    Route::patch('/warehouse/{item}/location', [Admin\WarehouseController::class, 'updateLocation'])->name('warehouse.update-location');

    // FR-2.2: Monitoring Kontrak Jatuh Tempo
    Route::get('/duedate', [Admin\DueDateController::class, 'index'])->name('duedate.index');

    // FR-2.3: Status Barang Macet -> Siap Lelang & Rak Lelang
    Route::get('/auction', [Admin\AuctionController::class, 'index'])->name('auction.index');
    Route::patch('/auction/{item}/mark-siap-lelang', [Admin\AuctionController::class, 'markSiapLelang'])->name('auction.mark-siap-lelang');
});

// Role Route Group: Owner Pemilik/Pengawas (FR-3.1 - FR-3.4)
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [Owner\DashboardController::class, 'index'])->name('dashboard');

    // FR-3.2: Parameter Sistem
    Route::get('/settings', [Owner\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [Owner\SettingController::class, 'update'])->name('settings.update');

    // FR-3.3: Kelola Akun Pengguna
    Route::resource('users', Owner\UserController::class);

    // FR-3.4: Laporan Transaksi, Lelang, & Laba/Rugi Real-Time
    Route::get('/reports', [Owner\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [Owner\ReportController::class, 'print'])->name('reports.print');
});
