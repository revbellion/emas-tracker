<?php

namespace App\Services\PriceProviders;

use Illuminate\Support\Facades\Http;

class PegadaianProvider implements PriceProviderInterface
{
    private const URL = 'https://harga-emas.org/';

    private ?array $cachedResult = null;

    public function name(): string
    {
        return 'Pegadaian';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function fetchBuyPrice(): ?float
    {
        return $this->fetchPrices()['buy'] ?? null;
    }

    public function fetchSellPrice(): ?float
    {
        return $this->fetchPrices()['sell'] ?? null;
    }

    private function fetchPrices(): array
    {
        if ($this->cachedResult !== null) {
            return $this->cachedResult;
        }

        $this->cachedResult = $this->fetch();
        return $this->cachedResult;
    }

    private function fetch(): array
    {
        try {
            $response = Http::timeout(10)->withOptions(['allow_redirects' => true])->get(self::URL);

            if (!$response->successful()) {
                return [];
            }

            $html = $response->body();
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            $buyPrice = null;
            $sellPrice = null;

            // Table 1 contains Antam and Pegadaian prices per gram
            $tables = $xpath->query("//table");
            if ($tables->length >= 2) {
                $table1 = $tables->item(1); // Second table
                $rows = $xpath->query('.//tr', $table1);

                // Pegadaian is in third column (index 2)
                foreach ($rows as $row) {
                    $cells = $xpath->query('.//td', $row);
                    if ($cells->length >= 3) {
                        $gramLabel = trim($cells->item(0)->textContent);
                        if ($gramLabel === '1') {
                            $buyPrice = $this->parsePrice(trim($cells->item(2)->textContent));
                            break;
                        }
                    }
                }

                // Table 2 (index 2) has buyback prices: Jual (sell), Beli (buy)
                if ($tables->length >= 3) {
                    $table2 = $tables->item(2);
                    $rows2 = $xpath->query('.//tr', $table2);
                    foreach ($rows2 as $row) {
                        $cells = $xpath->query('.//td', $row);
                        if ($cells->length >= 3) {
                            $label = trim($cells->item(0)->textContent);
                            if ($label === '1') {
                                // Jual = dealer selling (our BUY), Beli = dealer buying (our SELL)
                                $sellPrice = $this->parsePrice(trim($cells->item(2)->textContent));
                                break;
                            }
                        }
                    }
                }
            }

            return [
                'buy' => $buyPrice,
                'sell' => $sellPrice,
            ];
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    private function parsePrice(string $value): ?float
    {
        $cleaned = preg_replace('/[^0-9,]/', '', $value);
        $cleaned = str_replace('.', '', $cleaned);
        $cleaned = str_replace(',', '.', $cleaned);

        return is_numeric($cleaned) ? (float) $cleaned : null;
    }
}
