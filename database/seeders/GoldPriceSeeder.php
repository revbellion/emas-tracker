<?php

namespace Database\Seeders;

use App\Models\GoldPrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GoldPriceSeeder extends Seeder
{
    public function run(): void
    {
        $baseBuyPrice = 1_200_000;
        $baseSellPrice = 1_150_000;

        // Seed daily prices for the past 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $fluctuation = fake()->randomFloat(0, -15_000, 15_000);

            GoldPrice::create([
                'type' => 'BUY',
                'price' => $baseBuyPrice + $fluctuation,
                'provider' => 'Manual',
                'price_date' => $date,
            ]);

            GoldPrice::create([
                'type' => 'SELL',
                'price' => $baseSellPrice + $fluctuation,
                'provider' => 'Manual',
                'price_date' => $date,
            ]);

            // Slight trend for next day
            $baseBuyPrice += fake()->randomFloat(0, -3_000, 5_000);
            $baseSellPrice += fake()->randomFloat(0, -3_000, 5_000);
        }
    }
}
