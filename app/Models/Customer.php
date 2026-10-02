<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'identity_number',
        'name',
        'phone',
        'address',
        'notes',
    ];

    /**
     * Scope query untuk pencarian cepat berdasarkan KTP, nama, atau nomor HP.
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('identity_number', 'like', "%{$keyword}%")
              ->orWhere('name', 'like', "%{$keyword}%")
              ->orWhere('phone', 'like', "%{$keyword}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke transaksi gadai yang dimiliki oleh nasabah.
     */
    public function pawnTransactions(): HasMany
    {
        return $this->hasMany(PawnTransaction::class, 'customer_id');
    }

    /**
     * Relasi ke transaksi pembelian barang bekas yang dijual oleh nasabah/penjual.
     */
    public function purchaseTransactions(): HasMany
    {
        return $this->hasMany(PurchaseTransaction::class, 'customer_id');
    }
}
