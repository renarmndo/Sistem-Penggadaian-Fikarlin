<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMutation;
use App\Models\Item;
use App\Services\InventoryMutationService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $statusScope = $request->input('status_scope', 'aktif');
        $query = Item::with(['pawnTransaction.customer', 'purchaseTransaction.customer']);

        // Filter berdasarkan Status Inventaris
        if ($statusScope === 'aktif') {
            // Default: Hanya barang yang fisiknya masih berada di toko / gudang
            $query->whereIn('status', [
                Item::STATUS_TERSIMPAN,
                Item::STATUS_DIPERPANJANG,
                Item::STATUS_SIAP_LELANG,
                Item::STATUS_STOK_ETALASE
            ]);
        } elseif ($statusScope === 'keluar') {
            $query->whereIn('status', [Item::STATUS_LUNAS, Item::STATUS_TERJUAL]);
        } elseif ($statusScope !== 'semua' && !empty($statusScope)) {
            $query->where('status', $statusScope);
        }

        // Filter berdasarkan Lokasi Fisik
        if ($location = $request->input('location')) {
            $query->where('location', $location);
        }

        // Filter berdasarkan Asal Transaksi (Gadai / Beli Bekas)
        if ($sourceType = $request->input('source_type')) {
            $query->where('source_type', $sourceType);
        }

        // Pencarian Teks
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhereHas('pawnTransaction.customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('identity_number', 'like', "%{$search}%");
                  });
            });
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        // Ringkasan KPI Stok Fisik Gudang
        $stats = [
            'total_aktif' => Item::whereIn('status', [Item::STATUS_TERSIMPAN, Item::STATUS_DIPERPANJANG, Item::STATUS_SIAP_LELANG, Item::STATUS_STOK_ETALASE])->count(),
            'rak_gudang' => Item::whereIn('status', [Item::STATUS_TERSIMPAN, Item::STATUS_DIPERPANJANG])->where('location', Item::LOCATION_RAK_GUDANG)->count(),
            'rak_lelang' => Item::where('status', Item::STATUS_SIAP_LELANG)->count(),
            'etalase' => Item::where('status', Item::STATUS_STOK_ETALASE)->count(),
            'total_keluar' => Item::whereIn('status', [Item::STATUS_LUNAS, Item::STATUS_TERJUAL])->count(),
        ];

        return view('admin.warehouse.index', compact('items', 'statusScope', 'stats'));
    }

    public function updateLocation(Request $request, Item $item, InventoryMutationService $mutationService)
    {
        $validated = $request->validate([
            'location' => ['required', 'in:rak_gudang,rak_lelang,etalase'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldLocation = $item->location;
        $newLocation = $validated['location'];

        $item->update(['location' => $newLocation]);

        $mutationService->recordMutation(
            $item,
            $newLocation === Item::LOCATION_RAK_LELANG ? InventoryMutation::TYPE_PINDAH_RAK_LELANG : InventoryMutation::TYPE_MASUK_RAK_GUDANG,
            auth()->id(),
            "Pemindahan lokasi fisik oleh Admin dari {$oldLocation} ke {$newLocation}. " . ($validated['notes'] ?? '')
        );

        return redirect()->back()->with('success', "Lokasi fisik barang {$item->item_code} berhasil diperbarui menjadi {$newLocation}.");
    }
}
