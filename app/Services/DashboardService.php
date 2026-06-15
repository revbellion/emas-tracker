<?php

namespace App\Services;

use App\Models\FamilyMember;
use App\Models\GoldPrice;
use App\Models\GoldTransaction;

class DashboardService
{
    public function getOverview(): array
    {
        $latestSellPrice = GoldPrice::where('type', 'SELL')->latest('price_date')->first();
        $latestBuyPrice = GoldPrice::where('type', 'BUY')->latest('price_date')->first();

        $prevSellPrice = null;
        $prevBuyPrice = null;
        if ($latestSellPrice) {
            $prevSellPrice = GoldPrice::where('type', 'SELL')
                ->where('price_date', '<', $latestSellPrice->price_date)
                ->latest('price_date')
                ->first();
        }
        if ($latestBuyPrice) {
            $prevBuyPrice = GoldPrice::where('type', 'BUY')
                ->where('price_date', '<', $latestBuyPrice->price_date)
                ->latest('price_date')
                ->first();
        }

        $members = FamilyMember::where('is_active', true)->get();
        $totalGram = 0;
        $totalCapital = 0;

        foreach ($members as $member) {
            $totalGram += $member->gold_balance;
            $totalCapital += $member->total_capital;
        }

        $currentValue = $totalGram * (float) ($latestSellPrice?->price ?? 0);
        $profit = $currentValue - $totalCapital;
        $profitPercentage = $totalCapital > 0 ? round(($profit / $totalCapital) * 100, 2) : 0;

        return [
            'total_gram' => round($totalGram, 4),
            'total_capital' => round($totalCapital, 2),
            'current_value' => round($currentValue, 2),
            'profit' => round($profit, 2),
            'profit_percentage' => $profitPercentage,
            'latest_sell_price' => $latestSellPrice?->price ?? 0,
            'latest_buy_price' => $latestBuyPrice?->price ?? 0,
            'prev_sell_price' => $prevSellPrice?->price ?? 0,
            'prev_buy_price' => $prevBuyPrice?->price ?? 0,
            'price_date' => $latestSellPrice?->price_date,
        ];
    }

    public function getRecentTransactions(int $limit = 10)
    {
        return GoldTransaction::with('member')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getPortfolioGrowth(int $days = 30)
    {
        $startDate = now()->subDays($days);
        $latestSellPrice = GoldPrice::where('type', 'SELL')->latest('price_date')->first();
        $currentPrice = (float) ($latestSellPrice?->price ?? 0);

        $memberIds = FamilyMember::where('is_active', true)->pluck('id');
        $transactions = GoldTransaction::whereIn('member_id', $memberIds)
            ->orderBy('transaction_date')
            ->get(['transaction_date', 'gram', 'type']);

        $weeklyData = [];
        $cursor = $startDate->copy()->startOfWeek();
        $end = now()->endOfWeek();
        $cumulativeGram = 0;
        $txIndex = 0;

        while ($cursor <= $end) {
            $weekEnd = $cursor->copy()->endOfWeek();

            while ($txIndex < $transactions->count() &&
                   $transactions[$txIndex]->transaction_date->lte($weekEnd)) {
                $tx = $transactions[$txIndex];
                if (in_array($tx->type, ['BUY', 'TRANSFER_IN', 'ADJUSTMENT'])) {
                    $cumulativeGram += (float) $tx->gram;
                } elseif (in_array($tx->type, ['SELL', 'TRANSFER_OUT'])) {
                    $cumulativeGram -= abs((float) $tx->gram);
                }
                $txIndex++;
            }

            if ($cumulativeGram > 0 && $currentPrice > 0) {
                $weeklyData[] = [
                    'date' => $cursor->format('d M'),
                    'value' => round($cumulativeGram * $currentPrice, 2),
                ];
            }

            $cursor->addWeek();
        }

        return collect($weeklyData);
    }
}
