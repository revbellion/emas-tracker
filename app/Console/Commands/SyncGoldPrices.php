<?php

namespace App\Console\Commands;

use App\Services\GoldPriceSyncService;
use App\Services\PriceProviders\AntamProvider;
use App\Services\PriceProviders\PegadaianProvider;
use Illuminate\Console\Command;

class SyncGoldPrices extends Command
{
    protected $signature = 'gold:sync {provider? : Provider name (pegadaian, antam)}';

    protected $description = 'Sync gold prices from external providers';

    public function handle(GoldPriceSyncService $syncService): int
    {
        $syncService->registerProvider(new PegadaianProvider);
        $syncService->registerProvider(new AntamProvider);

        $provider = $this->argument('provider');

        if ($provider) {
            $this->info("Syncing from {$provider}...");

            try {
                $result = $syncService->syncFromProvider($provider);

                if (isset($result['buy'])) {
                    $this->info("Buy price: Rp " . number_format($result['buy']->price, 0, ',', '.'));
                }
                if (isset($result['sell'])) {
                    $this->info("Sell price: Rp " . number_format($result['sell']->price, 0, ',', '.'));
                }

                if (empty($result)) {
                    $this->warn("No prices retrieved from {$provider}. Provider might be unavailable.");
                }

                return self::SUCCESS;
            } catch (\InvalidArgumentException $e) {
                $this->error($e->getMessage());

                $this->info('Available providers: ' . implode(', ', array_keys($syncService->getProviders())));

                return self::FAILURE;
            }
        }

        $this->info('Syncing from all providers...');
        $results = $syncService->syncAll();

        foreach ($results as $name => $result) {
            if (isset($result['error'])) {
                $this->error("[{$name}] {$result['error']}");
            } elseif (isset($result['buy']) || isset($result['sell'])) {
                $this->info("[{$name}] Buy: Rp " . number_format($result['buy']?->price ?? 0, 0, ',', '.') . " | Sell: Rp " . number_format($result['sell']?->price ?? 0, 0, ',', '.'));
            } else {
                $this->warn("[{$name}] No data retrieved");
            }
        }

        return self::SUCCESS;
    }
}
