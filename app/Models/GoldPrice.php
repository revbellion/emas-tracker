<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoldPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'price',
        'provider',
        'price_date',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_date' => 'date',
        ];
    }
}
