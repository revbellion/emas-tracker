<?php

namespace App\Services;

use App\Models\GoldPrice;
use App\Services\PriceProviders\PriceProviderInterface;
use Illuminate\Support\Facades\Log;

class GoldPriceSyncService
{
    private array $providers = [];

    public function registerProvider(PriceProviderInterface $provider): void
    {
        $this->providers[strtolower($provider->name())] = $provider;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getProvider(string $name): ?PriceProviderInterface
    {
        return $this->providers[strtolower($name)] ?? null;
    }

    public function syncFromProvider(string $providerName): array
    {
        $provider = $this->getProvider($providerName);

        if (!$provider) {
            throw new \InvalidArgumentException("Provider '{$providerName}' not registered");
        }

        $buyPrice = $provider->fetchBuyPrice();
        $sellPrice = $provider->fetchSellPrice();

        $result = [];

        if ($buyPrice !== null) {
            $result['buy'] = GoldPrice::create([
                'type' => 'BUY',
                'price' => $buyPrice,
                'provider' => $providerName,
                'price_date' => now()->toDateString(),
            ]);
        }

        if ($sellPrice !== null) {
            $result['sell'] = GoldPrice::create([
                'type' => 'SELL',
                'price' => $sellPrice,
                'provider' => $providerName,
                'price_date' => now()->toDateString(),
            ]);
        }

        app(ActivityLogService::class)->log(
            'SYNC_GOLD_PRICE',
            "Harga emas disinkronisasi dari {$providerName} — Beli: Rp " . number_format($buyPrice ?? 0, 0, ',', '.') . ", Jual: Rp " . number_format($sellPrice ?? 0, 0, ',', '.'),
        );

        Log::info("Gold price synced from {$providerName}", [
            'buy' => $buyPrice,
            'sell' => $sellPrice,
        ]);

        return $result;
    }

    public function syncAll(): array
    {
        $results = [];

        foreach ($this->providers as $name => $provider) {
            try {
                $results[$name] = $this->syncFromProvider($name);
            } catch (\Exception $e) {
                Log::error("Failed to sync from {$name}: {$e->getMessage()}");
                $results[$name] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }
}
