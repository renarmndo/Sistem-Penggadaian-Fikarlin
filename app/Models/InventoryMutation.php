<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMutation extends Model
{
    use HasFactory;

    public const TYPE_MASUK_RAK_GUDANG = 'masuk_rak_gudang';
    public const TYPE_MASUK_ETALASE = 'masuk_etalase';
    public const TYPE_PINDAH_RAK_LELANG = 'pindah_rak_lelang';
    public const TYPE_KELUAR_DITEBUS = 'keluar_ditebus';
    public const TYPE_KELUAR_TERJUAL = 'keluar_terjual';

    protected $fillable = [
        'item_id',
        'user_id',
        'mutation_type',
        'old_location',
        'new_location',
        'old_status',
        'new_status',
        'description',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
