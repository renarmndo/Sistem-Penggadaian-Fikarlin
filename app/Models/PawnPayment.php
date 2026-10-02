<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PawnPayment extends Model
{
    use HasFactory;

    public const TYPE_PELUNASAN = 'pelunasan';
    public const TYPE_PERPANJANGAN = 'perpanjangan';

    protected $fillable = [
        'payment_number',
        'pawn_transaction_id',
        'user_id',
        'payment_type',
        'principal_amount',
        'interest_amount',
        'penalty_amount',
        'total_paid',
        'old_due_date',
        'new_due_date',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'principal_amount' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'old_due_date' => 'date',
            'new_due_date' => 'date',
            'payment_date' => 'datetime',
        ];
    }

    public function pawnTransaction(): BelongsTo
    {
        return $this->belongsTo(PawnTransaction::class, 'pawn_transaction_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
