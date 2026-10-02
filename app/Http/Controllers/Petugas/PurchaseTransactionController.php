<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InventoryMutation;
use App\Models\Item;
use App\Models\PurchaseTransaction;
use App\Services\BarcodeGeneratorService;
use App\Services\InventoryMutationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseTransaction::with(['customer', 'item', 'user']);

        if ($search = $request->input('search')) {
            $query->where('transaction_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('identity_number', 'like', "%{$search}%");
                  });
        }

        $purchases = $query->latest()->paginate(15)->withQueryString();

        return view('petugas.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->take(50)->get();
        return view('petugas.purchases.create', compact('customers'));
    }

    public function store(
        Request $request,
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
            'purchase_price' => ['required', 'numeric', 'min:1000'],
            'selling_price' => ['nullable', 'numeric', 'min:1000'],
            'notes' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated, $barcodeService, $mutationService) {
            $purchasePrice = (float) $validated['purchase_price'];

            // 1. Buat data barang bekas (Item: status=stok_etalase, location=etalase)
            $itemCode = $barcodeService->generateItemCode();
            $item = Item::create([
                'item_code' => $itemCode,
                'name' => $validated['item_name'],
                'category' => $validated['category'],
                'brand' => $validated['brand'] ?? null,
                'model_type' => $validated['model_type'] ?? null,
                'condition_notes' => $validated['condition_notes'] ?? null,
                'status' => Item::STATUS_STOK_ETALASE,
                'location' => Item::LOCATION_ETALASE,
                'source_type' => Item::SOURCE_BELI_BEKAS,
                'estimated_value' => $purchasePrice,
                'selling_price' => $validated['selling_price'] ?? null,
            ]);

            // 2. Buat transaksi pembelian
            $transactionNumber = $barcodeService->generatePurchaseNumber();
            $purchase = PurchaseTransaction::create([
                'transaction_number' => $transactionNumber,
                'customer_id' => $validated['customer_id'],
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'purchase_price' => $purchasePrice,
                'purchase_date' => Carbon::today(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // 3. Catat mutasi inventaris masuk etalase
            $mutationService->recordMutation(
                $item,
                InventoryMutation::TYPE_MASUK_ETALASE,
                auth()->id(),
                "Pembelian barang bekas {$transactionNumber} (Stok Etalase Toko)"
            );

            return redirect()->route('petugas.purchases.nota', $purchase->id)
                ->with('success', "Transaksi Pembelian {$transactionNumber} berhasil disimpan. Kas keluar tercatat.");
        });
    }

    public function printNota(PurchaseTransaction $purchaseTransaction)
    {
        $purchaseTransaction->load(['customer', 'item', 'user']);
        return view('petugas.purchases.nota', compact('purchaseTransaction'));
    }
}
