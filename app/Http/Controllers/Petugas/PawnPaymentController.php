<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\InventoryMutation;
use App\Models\PawnPayment;
use App\Models\PawnTransaction;
use App\Services\BarcodeGeneratorService;
use App\Services\InventoryMutationService;
use App\Services\PawnCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PawnPaymentController extends Controller
{
    public function search(Request $request, PawnCalculationService $calcService)
    {
        $code = $request->input('code');

        if (! $code) {
            return view('petugas.payments.search');
        }

        $transaction = PawnTransaction::with(['customer', 'item'])
            ->where('barcode_code', $code)
            ->orWhere('ticket_number', $code)
            ->first();

        if (! $transaction) {
            return back()->withErrors(['code' => 'Data Surat Bukti Gadai (SBG) / Barcode tidak ditemukan.']);
        }

        if (in_array($transaction->status, [PawnTransaction::STATUS_LUNAS, PawnTransaction::STATUS_TERJUAL])) {
            return back()->withErrors(['code' => "Transaksi ini sudah berstatus {$transaction->status}."]);
        }

        $penaltyInfo = $calcService->calculatePenalty(
            (float) $transaction->loan_amount,
            Carbon::parse($transaction->due_date)
        );

        return view('petugas.payments.process', compact('transaction', 'penaltyInfo'));
    }

    public function store(
        Request $request,
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService
    ) {
        $validated = $request->validate([
            'pawn_transaction_id' => ['required', 'exists:pawn_transactions,id'],
            'payment_type' => ['required', 'in:pelunasan,perpanjangan'],
            'extension_tenor_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'notes' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated, $request, $calcService, $barcodeService, $mutationService) {
            $transaction = PawnTransaction::with('item')->findOrFail($validated['pawn_transaction_id']);
            $paymentType = $validated['payment_type'];

            $loanAmount = (float) $transaction->loan_amount;
            $interestAmount = (float) $transaction->interest_amount;
            
            // Hitung denda server
            $penaltyData = $calcService->calculatePenalty(
                $loanAmount,
                Carbon::parse($transaction->due_date)
            );
            $penaltyAmount = $penaltyData['penalty_amount'];

            $paymentNumber = $barcodeService->generatePaymentNumber();

            if ($paymentType === PawnPayment::TYPE_PELUNASAN) {
                $totalPaid = $loanAmount + $interestAmount + $penaltyAmount;

                $payment = PawnPayment::create([
                    'payment_number' => $paymentNumber,
                    'pawn_transaction_id' => $transaction->id,
                    'user_id' => auth()->id(),
                    'payment_type' => PawnPayment::TYPE_PELUNASAN,
                    'principal_amount' => $loanAmount,
                    'interest_amount' => $interestAmount,
                    'penalty_amount' => $penaltyAmount,
                    'total_paid' => $totalPaid,
                    'old_due_date' => $transaction->due_date,
                    'new_due_date' => null,
                    'payment_date' => Carbon::now(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Update status transaksi & item menjadi LUNAS
                $transaction->update([
                    'status' => PawnTransaction::STATUS_LUNAS,
                    'settled_at' => Carbon::now(),
                    'penalty_amount' => $penaltyAmount,
                ]);

                if ($transaction->item) {
                    $mutationService->recordMutation(
                        $transaction->item,
                        InventoryMutation::TYPE_KELUAR_DITEBUS,
                        auth()->id(),
                        "Pelunasan gadai {$transaction->ticket_number} (Status LUNAS)"
                    );
                }

                return redirect()->route('petugas.payments.receipt', $payment->id)
                    ->with('success', "Pelunasan berhasil diproses. Status transaksi: LUNAS.");
            } else {
                // Perpanjangan (Hanya bayar bunga + denda, perbarui tanggal jatuh tempo sesuai tenor pilihan)
                $extensionDays = (int) ($validated['extension_tenor_days'] ?? $transaction->tenor_days ?? 30);
                $totalPaid = $interestAmount + $penaltyAmount;
                
                // Hitung jatuh tempo baru: jika sudah lewat hari ini, hitung dari hari ini + tenor, jika belum lewat, hitung dari due date lama + tenor
                $baseDate = Carbon::parse($transaction->due_date);
                if (Carbon::today()->gt($baseDate)) {
                    $baseDate = Carbon::today();
                }
                $newDueDate = $calcService->calculateDueDate($baseDate, $extensionDays);

                $payment = PawnPayment::create([
                    'payment_number' => $paymentNumber,
                    'pawn_transaction_id' => $transaction->id,
                    'user_id' => auth()->id(),
                    'payment_type' => PawnPayment::TYPE_PERPANJANGAN,
                    'principal_amount' => 0,
                    'interest_amount' => $interestAmount,
                    'penalty_amount' => $penaltyAmount,
                    'total_paid' => $totalPaid,
                    'old_due_date' => $transaction->due_date,
                    'new_due_date' => $newDueDate,
                    'payment_date' => Carbon::now(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Update tanggal jatuh tempo & status transaksi/item menjadi DIPERPANJANG
                $transaction->update([
                    'due_date' => $newDueDate,
                    'tenor_days' => $extensionDays,
                    'status' => PawnTransaction::STATUS_DIPERPANJANG,
                    'penalty_amount' => 0,
                ]);

                if ($transaction->item) {
                    $transaction->item->update([
                        'status' => \App\Models\Item::STATUS_DIPERPANJANG,
                    ]);
                }

                return redirect()->route('petugas.payments.receipt', $payment->id)
                    ->with('success', "Perpanjangan tenor {$extensionDays} hari berhasil diproses. Tanggal jatuh tempo baru: {$newDueDate->format('d/m/Y')}");
            }
        });
    }

    public function printReceipt(PawnPayment $pawnPayment)
    {
        $pawnPayment->load(['pawnTransaction.customer', 'pawnTransaction.item', 'user']);
        return view('petugas.payments.receipt', compact('pawnPayment'));
    }
}
