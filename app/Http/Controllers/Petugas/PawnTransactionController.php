<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InventoryMutation;
use App\Models\Item;
use App\Models\PawnTransaction;
use App\Services\BarcodeGeneratorService;
use App\Services\InventoryMutationService;
use App\Services\PawnCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PawnTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = PawnTransaction::with(['customer', 'item', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('barcode_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('identity_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        return view('petugas.pawn.index', compact('transactions'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->take(50)->get();
        return view('petugas.pawn.create', compact('customers'));
    }

    public function store(
        Request $request,
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService
    ) {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model_type' => ['nullable', 'string', 'max:100'],
            'condition_notes' => ['nullable', 'string'],
            'estimated_value' => ['required', 'numeric', 'min:1000'],
            'loan_amount' => ['required', 'numeric', 'min:1000'],
            'tenor_days' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated, $request, $calcService, $barcodeService, $mutationService) {
            $estimatedValue = (float) $validated['estimated_value'];
            $loanAmount = (float) $validated['loan_amount'];
            $tenorDays = (int) ($validated['tenor_days'] ?? 30);
            
            $interestAmount = $calcService->calculateInterest($loanAmount);
            $interestRate = (float) \App\Models\AppSetting::getValue(\App\Models\AppSetting::KEY_INTEREST_RATE, 10);
            $totalAmount = $loanAmount + $interestAmount;
            
            $pawnDate = Carbon::today();
            $dueDate = $calcService->calculateDueDate($pawnDate, $tenorDays);

            // 1. Buat data Barang (Item)
            $itemCode = $barcodeService->generateItemCode();
            $item = Item::create([
                'item_code' => $itemCode,
                'name' => $validated['item_name'],
                'category' => $validated['category'],
                'brand' => $validated['brand'] ?? null,
                'model_type' => $validated['model_type'] ?? null,
                'condition_notes' => $validated['condition_notes'] ?? null,
                'status' => Item::STATUS_TERSIMPAN,
                'location' => Item::LOCATION_RAK_GUDANG,
                'source_type' => Item::SOURCE_GADAI,
                'estimated_value' => $estimatedValue,
            ]);

            // 2. Generate Ticket & Barcode
            $ticketNumber = $barcodeService->generateTicketNumber();
            $barcodeCode = $barcodeService->generateBarcodeCode($ticketNumber);

            // 3. Buat Transaksi Gadai Baru
            $transaction = PawnTransaction::create([
                'ticket_number' => $ticketNumber,
                'barcode_code' => $barcodeCode,
                'customer_id' => $validated['customer_id'],
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'estimated_value' => $estimatedValue,
                'loan_amount' => $loanAmount,
                'interest_rate' => $interestRate,
                'interest_amount' => $interestAmount,
                'tenor_days' => $tenorDays,
                'penalty_amount' => 0,
                'total_amount' => $totalAmount,
                'pawn_date' => $pawnDate,
                'due_date' => $dueDate,
                'status' => PawnTransaction::STATUS_TERSIMPAN,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 4. Catat mutasi inventaris masuk Rak Gudang
            $mutationService->recordMutation(
                $item,
                InventoryMutation::TYPE_MASUK_RAK_GUDANG,
                auth()->id(),
                "Barang baru dari transaksi gadai {$ticketNumber}"
            );

            return redirect()->route('petugas.pawn.sbg', $transaction->id)
                ->with('success', "Transaksi Gadai {$ticketNumber} berhasil disimpan dengan status TERSIMPAN.");
        });
    }

    public function show(PawnTransaction $pawnTransaction)
    {
        $pawnTransaction->load(['customer', 'item', 'user', 'payments.user']);
        return view('petugas.pawn.show', compact('pawnTransaction'));
    }

    public function printSbg(PawnTransaction $pawnTransaction)
    {
        $pawnTransaction->load(['customer', 'item', 'user']);
        return view('petugas.pawn.sbg', compact('pawnTransaction'));
    }
}
