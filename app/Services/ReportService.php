<?php

namespace App\Services;

use App\Models\FamilyMember;
use App\Models\GoldPrice;
use App\Models\GoldTransaction;

class ReportService
{
    public function memberPortfolio(?int $memberId = null)
    {
        $query = FamilyMember::where('is_active', true);
        
        if ($memberId) {
            $query->where('id', $memberId);
        }

        $members = $query->get();
        $latestSellPrice = GoldPrice::where('type', 'SELL')->latest('price_date')->first()?->price ?? 0;

        return $members->map(function ($member) use ($latestSellPrice) {
            $goldBalance = $member->gold_balance;
            $totalCapital = $member->total_capital;

            return [
                'id' => $member->id,
                'name' => $member->name,
                'gold_balance' => $goldBalance,
                'total_capital' => $totalCapital,
                'average_price' => $goldBalance > 0 ? round($totalCapital / $goldBalance, 2) : 0,
                'current_value' => round($goldBalance * (float) $latestSellPrice, 2),
                'profit' => round(($goldBalance * (float) $latestSellPrice) - $totalCapital, 2),
            ];
        });
    }

    public function transactionHistory(array $filters = [])
    {
        $query = GoldTransaction::with('member');

        if (isset($filters['from'])) {
            $query->where('transaction_date', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->where('transaction_date', '<=', $filters['to']);
        }

        if (isset($filters['member_id'])) {
            $query->where('member_id', $filters['member_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('description', 'like', "%{$filters['search']}%")
                  ->orWhereHas('member', fn($q) => $q->where('name', 'like', "%{$filters['search']}%"));
            });
        }

        $sortField = $filters['sort'] ?? 'transaction_date';
        $sortDir = $filters['direction'] ?? 'desc';
        $allowedSorts = ['transaction_date', 'gram', 'total_amount', 'type'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest('transaction_date');
        }

        return $query->paginate(20);
    }

    public function goldPriceHistory(array $filters = [])
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

        $sortField = $filters['sort'] ?? 'price_date';
        $sortDir = $filters['direction'] ?? 'desc';
        $allowedSorts = ['price_date', 'price', 'type'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest('price_date');
        }

        return $query->paginate(20);
    }

    public function profitReport(array $filters = [])
    {
        $latestSellPrice = GoldPrice::where('type', 'SELL')->latest('price_date')->first()?->price ?? 0;
        $members = FamilyMember::where('is_active', true)->get();

        $totalGram = 0;
        $totalCapital = 0;

        $data = $members->map(function ($member) use ($latestSellPrice, &$totalGram, &$totalCapital) {
            $gram = $member->gold_balance;
            $capital = $member->total_capital;
            $value = $gram * (float) $latestSellPrice;

            $totalGram += $gram;
            $totalCapital += $capital;

            return [
                'member_name' => $member->name,
                'gram' => $gram,
                'capital' => $capital,
                'current_value' => round($value, 2),
                'profit' => round($value - $capital, 2),
                'profit_percentage' => $capital > 0 ? round((($value - $capital) / $capital) * 100, 2) : 0,
            ];
        });

        $totalValue = $totalGram * (float) $latestSellPrice;
        $totalProfit = $totalValue - $totalCapital;

        return [
            'details' => $data,
            'summary' => [
                'total_gram' => round($totalGram, 4),
                'total_capital' => round($totalCapital, 2),
                'total_value' => round($totalValue, 2),
                'total_profit' => round($totalProfit, 2),
                'total_profit_percentage' => $totalCapital > 0 ? round(($totalProfit / $totalCapital) * 100, 2) : 0,
                'latest_price' => $latestSellPrice,
            ],
        ];
    }
}
