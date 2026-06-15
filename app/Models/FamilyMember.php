<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FamilyMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'relationship',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GoldTransaction::class, 'member_id');
    }

    public function buyTransactions(): HasMany
    {
        return $this->hasMany(GoldTransaction::class, 'member_id')->where('type', 'BUY');
    }

    public function sellTransactions(): HasMany
    {
        return $this->hasMany(GoldTransaction::class, 'member_id')->where('type', 'SELL');
    }

    public function getGoldBalanceAttribute(): float
    {
        static $cache = [];

        if (isset($cache[$this->id])) {
            return $cache[$this->id];
        }

        $result = $this->transactions()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'BUY' THEN gram ELSE 0 END), 0) as buy,
                COALESCE(SUM(CASE WHEN type = 'ADJUSTMENT' AND gram > 0 THEN gram ELSE 0 END), 0) as buy_adj,
                COALESCE(SUM(CASE WHEN type IN ('SELL','TRANSFER_OUT') THEN gram ELSE 0 END), 0) as sell,
                COALESCE(SUM(CASE WHEN type = 'ADJUSTMENT' AND gram < 0 THEN gram ELSE 0 END), 0) as sell_adj,
                COALESCE(SUM(CASE WHEN type = 'TRANSFER_IN' THEN gram ELSE 0 END), 0) as transfer_in
            ")
            ->first();

        $balance = round(
            (float) $result->buy + (float) $result->buy_adj + (float) $result->transfer_in
            - abs((float) $result->sell) - abs((float) $result->sell_adj),
            4
        );

        $cache[$this->id] = $balance;

        return $balance;
    }

    public function getTotalCapitalAttribute(): float
    {
        return (float) $this->transactions()
            ->whereIn('type', ['BUY', 'ADJUSTMENT'])
            ->where('gram', '>', 0)
            ->sum('total_amount');
    }
}
