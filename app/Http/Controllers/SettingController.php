<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManualPriceRequest;
use App\Services\GoldPriceService;
use App\Services\GoldPriceSyncService;
use App\Services\PriceProviders\AntamProvider;
use App\Services\PriceProviders\PegadaianProvider;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function __construct(
        private GoldPriceService $goldPriceService,
        private GoldPriceSyncService $syncService,
    ) {}

    public function index()
    {
        $this->syncService->registerProvider(new PegadaianProvider);
        $this->syncService->registerProvider(new AntamProvider);

        $latest = $this->goldPriceService->getLatestSellPrice();

        return Inertia::render('Settings/Index', [
            'providers' => array_keys($this->syncService->getProviders()),
            'latestBuyPrice' => $this->goldPriceService->getLatestBuyPrice()?->price ?? 0,
            'latestSellPrice' => $latest?->price ?? 0,
            'latestSyncInfo' => $latest ? ($latest->provider . ' - ' . $latest->price_date) : null,
        ]);
    }

    public function syncPrice(string $provider)
    {
        $this->syncService->registerProvider(new PegadaianProvider);
        $this->syncService->registerProvider(new AntamProvider);

        try {
            $result = $this->syncService->syncFromProvider($provider);
            $msg = "Sinkronisasi dari {$provider} berhasil";

            if (isset($result['buy'])) {
                $msg .= " — Harga Beli: Rp " . number_format($result['buy']->price, 0, ',', '.');
            }
            if (isset($result['sell'])) {
                $msg .= " — Harga Jual: Rp " . number_format($result['sell']->price, 0, ',', '.');
            }

            Cache::forget('latest_buy_price');
            Cache::forget('latest_sell_price');

            return redirect()->route('settings.index')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('settings.index')->withErrors(['error' => "Gagal sinkronisasi dari {$provider}: {$e->getMessage()}"]);
        }
    }

    public function setManualPrice(StoreManualPriceRequest $request)
    {
        $validated = $request->validated();

        $priceDate = $validated['price_date'] ?? now()->toDateString();

        $this->goldPriceService->create([
            'type' => 'BUY',
            'price' => $validated['buy_price'],
            'provider' => 'manual',
            'price_date' => $priceDate,
        ]);

        $this->goldPriceService->create([
            'type' => 'SELL',
            'price' => $validated['sell_price'],
            'provider' => 'manual',
            'price_date' => $priceDate,
        ]);

        Cache::forget('latest_buy_price');
        Cache::forget('latest_sell_price');

        return redirect()->route('settings.index')->with('success', 'Harga manual berhasil disimpan — Beli: Rp ' . number_format($validated['buy_price'], 0, ',', '.') . ', Jual: Rp ' . number_format($validated['sell_price'], 0, ',', '.'));
    }
}
