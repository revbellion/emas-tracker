<?php

namespace App\Services;

use App\Models\GoldPrice;
use Illuminate\Support\Facades\Cache;

class GoldPriceService
{
    public function getAll(array $filters = [])
    {
        $query = GoldPrice::query();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['from'])) {
            $query->where('price_date', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->where('price_date', '<=', $filters['to']);
        }

        return $query->latest('price_date')->paginate(10);
    }

    public function create(array $data): GoldPrice
    {
        $price = GoldPrice::create($data);

        app(ActivityLogService::class)->log(
            'CREATE_GOLD_PRICE',
            "Harga emas {$data['type']} Rp " . number_format($data['price'], 0, ',', '.') . " ditambahkan",
            $price
        );

        return $price;
    }

    public function getLatestSellPrice(): ?GoldPrice
    {
        $value = Cache::get('latest_sell_price');

        if ($value instanceof GoldPrice) {
            return $value;
        }

        $fresh = GoldPrice::where('type', 'SELL')->latest('price_date')->first();
        Cache::put('latest_sell_price', $fresh, 3600);

        return $fresh;
    }

    public function getLatestBuyPrice(): ?GoldPrice
    {
        $value = Cache::get('latest_buy_price');

        if ($value instanceof GoldPrice) {
            return $value;
        }

        $fresh = GoldPrice::where('type', 'BUY')->latest('price_date')->first();
        Cache::put('latest_buy_price', $fresh, 3600);

        return $fresh;
    }
}
