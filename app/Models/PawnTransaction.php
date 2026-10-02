<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PawnTransaction extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_TERSIMPAN = 'tersimpan';
    public const STATUS_DIPERPANJANG = 'diperpanjang';
    public const STATUS_LUNAS = 'lunas';
    public const STATUS_SIAP_LELANG = 'siap_lelang';
    public const STATUS_TERJUAL = 'terjual';

    protected $fillable = [
        'ticket_number',
        'barcode_code',
        'customer_id',
        'item_id',
        'user_id',
        'estimated_value',
        'loan_amount',
        'interest_rate',
        'interest_amount',
        'tenor_days',
        'penalty_amount',
        'total_amount',
        'pawn_date',
        'due_date',
        'settled_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'loan_amount' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'pawn_date' => 'date',
            'due_date' => 'date',
            'settled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PawnPayment::class, 'pawn_transaction_id')->latest();
    }
}
