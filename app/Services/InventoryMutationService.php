<?php

namespace App\Services;

use App\Models\InventoryMutation;
use App\Models\Item;

class InventoryMutationService
{
    /**
     * Catat mutasi inventaris barang.
     */
    public function recordMutation(
        Item $item,
        string $mutationType,
        ?int $userId = null,
        ?string $description = null
    ): InventoryMutation {
        $oldLocation = $item->location;
        $oldStatus = $item->status;

        // Tentukan lokasi dan status baru berdasarkan tipe mutasi
        [$newLocation, $newStatus] = match ($mutationType) {
            InventoryMutation::TYPE_MASUK_RAK_GUDANG => [Item::LOCATION_RAK_GUDANG, Item::STATUS_TERSIMPAN],
            InventoryMutation::TYPE_MASUK_ETALASE => [Item::LOCATION_ETALASE, Item::STATUS_STOK_ETALASE],
            InventoryMutation::TYPE_PINDAH_RAK_LELANG => [Item::LOCATION_RAK_LELANG, Item::STATUS_SIAP_LELANG],
            InventoryMutation::TYPE_KELUAR_DITEBUS => [$item->location, Item::STATUS_LUNAS],
            InventoryMutation::TYPE_KELUAR_TERJUAL => [$item->location, Item::STATUS_TERJUAL],
            default => [$oldLocation, $oldStatus],
        };

        // Update item status & location
        $item->update([
            'location' => $newLocation,
            'status' => $newStatus,
        ]);

        // Simpan log mutasi
        return InventoryMutation::create([
            'item_id' => $item->id,
            'user_id' => $userId ?? auth()->id(),
            'mutation_type' => $mutationType,
            'old_location' => $oldLocation,
            'new_location' => $newLocation,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'description' => $description,
        ]);
    }
}
