<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PawnPayment;
use App\Models\PawnTransaction;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DailyTransactionController extends Controller
{
    public function index(Request $request)
    {
        $dateString = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($dateString);
        $typeFilter = $request->input('type', 'all');

        $data = $this->getTransactionsData($targetDate, $typeFilter);

        return view('petugas.transactions.daily', [
            'transactions' => $data['transactions'],
            'stats' => $data['stats'],
            'targetDate' => $targetDate,
            'typeFilter' => $typeFilter,
        ]);
    }

    public function printDaily(Request $request)
    {
        $dateString = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($dateString);
        $typeFilter = $request->input('type', 'all');

        $data = $this->getTransactionsData($targetDate, $typeFilter);

        return view('petugas.transactions.print-daily', [
            'transactions' => $data['transactions'],
            'stats' => $data['stats'],
            'targetDate' => $targetDate,
            'typeFilter' => $typeFilter,
        ]);
    }

    private function getTransactionsData(Carbon $date, string $typeFilter): array
    {
        $records = collect();
        $totalCashIn = 0;
        $totalCashOut = 0;

        // 1. Gadai Baru (Kas Keluar)
        if (in_array($typeFilter, ['all', 'gadai_baru'])) {
            $pawns = PawnTransaction::with(['customer', 'item', 'user'])
                ->whereDate('pawn_date', $date)
                ->get();

            foreach ($pawns as $p) {
                $loanAmount = (float) $p->loan_amount;
                $totalCashOut += $loanAmount;

                $records->push([
                    'id' => 'gadai-' . $p->id,
                    'time' => $p->created_at,
                    'doc_number' => $p->ticket_number,
                    'type_key' => 'gadai_baru',
                    'type_label' => 'Gadai Baru',
                    'party_name' => $p->customer->name ?? '-',
                    'party_phone' => $p->customer->phone ?? '-',
                    'item_desc' => ($p->item->name ?? '-') . ' (' . ($p->item->category ?? '-') . ')',
                    'cash_flow' => 'out',
                    'amount' => $loanAmount,
                    'notes' => 'Pencairan Plafon Pinjaman',
                    'user_name' => $p->user->name ?? 'Kasir',
                    'print_url' => route('petugas.pawn.sbg', $p->id),
                    'print_label' => 'SBG',
                ]);
            }
        }

        // 2. Pembayaran Pelunasan & Perpanjangan (Kas Masuk)
        if (in_array($typeFilter, ['all', 'pelunasan', 'perpanjangan'])) {
            $paymentsQuery = PawnPayment::with(['pawnTransaction.customer', 'pawnTransaction.item', 'user'])
                ->whereDate('payment_date', $date);

            if ($typeFilter === 'pelunasan') {
                $paymentsQuery->where('payment_type', PawnPayment::TYPE_PELUNASAN);
            } elseif ($typeFilter === 'perpanjangan') {
                $paymentsQuery->where('payment_type', PawnPayment::TYPE_PERPANJANGAN);
            }

            $payments = $paymentsQuery->get();

            foreach ($payments as $pay) {
                $paid = (float) $pay->total_paid;
                $totalCashIn += $paid;

                $isLunas = $pay->payment_type === PawnPayment::TYPE_PELUNASAN;
                $records->push([
                    'id' => 'payment-' . $pay->id,
                    'time' => $pay->created_at,
                    'doc_number' => $pay->payment_number,
                    'type_key' => $isLunas ? 'pelunasan' : 'perpanjangan',
                    'type_label' => $isLunas ? 'Pelunasan Gadai' : 'Perpanjangan Tenor',
                    'party_name' => $pay->pawnTransaction->customer->name ?? '-',
                    'party_phone' => $pay->pawnTransaction->customer->phone ?? '-',
                    'item_desc' => ($pay->pawnTransaction->item->name ?? '-') . ' (Ref SBG: ' . ($pay->pawnTransaction->ticket_number ?? '-') . ')',
                    'cash_flow' => 'in',
                    'amount' => $paid,
                    'notes' => $isLunas ? 'Pokok + Bunga + Denda' : 'Bunga + Denda',
                    'user_name' => $pay->user->name ?? 'Kasir',
                    'print_url' => route('petugas.payments.receipt', $pay->id),
                    'print_label' => 'Struk',
                ]);
            }
        }

        // 3. Pembelian Barang Bekas (Kas Keluar)
        if (in_array($typeFilter, ['all', 'beli_bekas'])) {
            $purchases = PurchaseTransaction::with(['customer', 'item', 'user'])
                ->whereDate('purchase_date', $date)
                ->get();

            foreach ($purchases as $purch) {
                $purchasePrice = (float) $purch->purchase_price;
                $totalCashOut += $purchasePrice;

                $records->push([
                    'id' => 'purchase-' . $purch->id,
                    'time' => $purch->created_at,
                    'doc_number' => $purch->transaction_number,
                    'type_key' => 'beli_bekas',
                    'type_label' => 'Beli Barang Bekas',
                    'party_name' => $purch->customer->name ?? '-',
                    'party_phone' => $purch->customer->phone ?? '-',
                    'item_desc' => ($purch->item->name ?? '-') . ' (' . ($purch->item->category ?? '-') . ')',
                    'cash_flow' => 'out',
                    'amount' => $purchasePrice,
                    'notes' => 'Pembelian Barang Bekas Toko',
                    'user_name' => $purch->user->name ?? 'Kasir',
                    'print_url' => route('petugas.purchases.nota', $purch->id),
                    'print_label' => 'Nota',
                ]);
            }
        }

        // 4. Penjualan Barang Lelang / Etalase (Kas Masuk)
        if (in_array($typeFilter, ['all', 'penjualan_lelang'])) {
            $sales = Item::with(['mutations'])
                ->where('status', Item::STATUS_TERJUAL)
                ->whereDate('updated_at', $date)
                ->get();

            foreach ($sales as $sale) {
                $sellingPrice = (float) ($sale->selling_price ?? 0);
                $totalCashIn += $sellingPrice;

                $records->push([
                    'id' => 'sale-' . $sale->id,
                    'time' => $sale->updated_at,
                    'doc_number' => $sale->item_code,
                    'type_key' => 'penjualan_lelang',
                    'type_label' => 'Penjualan Lelang/Etalase',
                    'party_name' => 'Pembeli Toko',
                    'party_phone' => '-',
                    'item_desc' => $sale->name . ' (' . $sale->category . ')',
                    'cash_flow' => 'in',
                    'amount' => $sellingPrice,
                    'notes' => 'Penjualan Barang Toko',
                    'user_name' => auth()->user()->name ?? 'Kasir',
                    'print_url' => route('petugas.auction.receipt', $sale->id),
                    'print_label' => 'Kuitansi',
                ]);
            }
        }

        // Sort records terbaru ke terlama
        $sortedRecords = $records->sortByDesc('time')->values();

        return [
            'transactions' => $sortedRecords,
            'stats' => [
                'cash_in' => $totalCashIn,
                'cash_out' => $totalCashOut,
                'net_flow' => $totalCashIn - $totalCashOut,
                'count' => $sortedRecords->count(),
            ]
        ];
    }
}
