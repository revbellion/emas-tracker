<?php

namespace App\Services\PriceProviders;

interface PriceProviderInterface
{
    public function name(): string;

    public function fetchBuyPrice(): ?float;

    public function fetchSellPrice(): ?float;

    public function isAvailable(): bool;
}
