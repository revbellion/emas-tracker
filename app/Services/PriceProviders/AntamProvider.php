<?php

namespace App\Services\PriceProviders;

use Illuminate\Support\Facades\Http;

class AntamProvider implements PriceProviderInterface
{
    private const URL = 'https://harga-emas.org/';

    private ?array $cachedResult = null;

    public function name(): string
    {
        return 'Antam';
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
            // Row with "1" in first column gives the 1 gram price
            $tables = $xpath->query("//table");
            if ($tables->length >= 2) {
                $table1 = $tables->item(1); // Second table (index 1) = Antam/Pegadaian prices
                $rows = $xpath->query('.//tr', $table1);

                foreach ($rows as $row) {
                    $cells = $xpath->query('.//td', $row);
                    if ($cells->length >= 3) {
                        $gramLabel = trim($cells->item(0)->textContent);
                        if ($gramLabel === '1') {
                            // First data column = Antam price per gram
                            $buyPrice = $this->parsePrice(trim($cells->item(1)->textContent));
                            break;
                        }
                    }
                }

                // Find buyback price from the footnote text in the last row
                foreach ($rows as $row) {
                    $cells = $xpath->query('.//td', $row);
                    if ($cells->length >= 2) {
                        $text = $cells->item(1)->textContent;
                        if (preg_match('/pembelian kembali:?\s*Rp?\s*([\d.]+)\s*\/?grm?/i', $text, $m)) {
                            $sellPrice = $this->parsePrice($m[1]);
                            break;
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
