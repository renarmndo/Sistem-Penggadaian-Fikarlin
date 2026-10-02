<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\InventoryMutation;
use App\Models\Item;
use App\Models\PawnTransaction;
use App\Services\InventoryMutationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuctionSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::whereIn('status', [Item::STATUS_SIAP_LELANG, Item::STATUS_STOK_ETALASE]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        return view('petugas.auction.index', compact('items'));
    }

    public function processSale(Request $request, Item $item, InventoryMutationService $mutationService)
    {
        $validated = $request->validate([
            'selling_price' => ['required', 'numeric', 'min:1000'],
            'buyer_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated, $item, $mutationService) {
            $sellingPrice = (float) $validated['selling_price'];

            // Update item selling_price dan status TERJUAL
            $item->update([
                'selling_price' => $sellingPrice,
                'status' => Item::STATUS_TERJUAL,
            ]);

            // Jika barang berasal dari gadai, update pawn_transaction status ke TERJUAL
            if ($item->pawnTransaction) {
                $item->pawnTransaction->update([
                    'status' => PawnTransaction::STATUS_TERJUAL,
                ]);
            }

            // Catat mutasi keluar terjual
            $mutation = $mutationService->recordMutation(
                $item,
                InventoryMutation::TYPE_KELUAR_TERJUAL,
                auth()->id(),
                "Penjualan barang lelang/etalase kepada {$validated['buyer_name']} seharga Rp " . number_format($sellingPrice, 0, ',', '.')
            );

            session(['last_buyer_name' => $validated['buyer_name']]);

            return redirect()->route('petugas.auction.receipt', $item->id)
                ->with('success', "Barang {$item->name} ({$item->item_code}) berhasil terjual. Silakan unduh atau cetak kuitansi bukti penjualan.");
        });
    }

    public function printReceipt(Item $item)
    {
        $buyerName = session('last_buyer_name', 'Pembeli');
        return view('petugas.auction.receipt', compact('item', 'buyerName'));
    }
}
