<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMutation;
use App\Models\Item;
use App\Models\PawnTransaction;
use App\Services\InventoryMutationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Barang status SIAP LELANG atau gadai yang sudah melewati jatuh tempo
        $query = Item::with(['pawnTransaction.customer'])
            ->where(function ($q) use ($today) {
                $q->where('status', Item::STATUS_SIAP_LELANG)
                  ->orWhereHas('pawnTransaction', function ($pt) use ($today) {
                      $pt->whereIn('status', [PawnTransaction::STATUS_TERSIMPAN, PawnTransaction::STATUS_DIPERPANJANG])
                         ->where('due_date', '<', $today);
                  });
            });

        $items = $query->latest()->paginate(15)->withQueryString();

        return view('admin.auction.index', compact('items'));
    }

    public function markSiapLelang(Item $item, InventoryMutationService $mutationService)
    {
        return DB::transaction(function () use ($item, $mutationService) {
            $item->update([
                'status' => Item::STATUS_SIAP_LELANG,
                'location' => Item::LOCATION_RAK_LELANG,
            ]);

            if ($item->pawnTransaction) {
                $item->pawnTransaction->update([
                    'status' => PawnTransaction::STATUS_SIAP_LELANG,
                ]);
            }

            $mutationService->recordMutation(
                $item,
                InventoryMutation::TYPE_PINDAH_RAK_LELANG,
                auth()->id(),
                'Admin memverifikasi status barang macet menjadi SIAP LELANG dan memindahkan fisik ke Rak Lelang'
            );

            return redirect()->back()->with('success', "Status barang {$item->item_code} berhasil diubah menjadi SIAP LELANG (Rak Lelang).");
        });
    }
}
