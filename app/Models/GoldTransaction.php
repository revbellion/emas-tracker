<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoldTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'type',
        'gram',
        'price_per_gram',
        'total_amount',
        'transaction_date',
        'description',
        'reference_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'gram' => 'decimal:4',
            'price_per_gram' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'transaction_date' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class, 'member_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reference_id');
    }
}
