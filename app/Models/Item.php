<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_TERSIMPAN = 'tersimpan';
    public const STATUS_DIPERPANJANG = 'diperpanjang';
    public const STATUS_SIAP_LELANG = 'siap_lelang';
    public const STATUS_TERJUAL = 'terjual';
    public const STATUS_LUNAS = 'lunas';
    public const STATUS_STOK_ETALASE = 'stok_etalase';

    public const LOCATION_RAK_GUDANG = 'rak_gudang';
    public const LOCATION_RAK_LELANG = 'rak_lelang';
    public const LOCATION_ETALASE = 'etalase';

    public const SOURCE_GADAI = 'gadai';
    public const SOURCE_BELI_BEKAS = 'beli_barang_bekas';

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'brand',
        'model_type',
        'condition_notes',
        'photo_path',
        'status',
        'location',
        'source_type',
        'estimated_value',
        'selling_price',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    public function pawnTransaction(): HasOne
    {
        return $this->hasOne(PawnTransaction::class, 'item_id');
    }

    public function purchaseTransaction(): HasOne
    {
        return $this->hasOne(PurchaseTransaction::class, 'item_id');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(InventoryMutation::class, 'item_id')->latest();
    }
}
