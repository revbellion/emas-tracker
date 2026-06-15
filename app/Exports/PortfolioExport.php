<?php

namespace App\Exports;

use App\Models\FamilyMember;
use App\Models\GoldPrice;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PortfolioExport implements FromArray, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(
        private ?int $memberId = null,
    ) {}

    public function array(): array
    {
        $query = FamilyMember::where('is_active', true);

        if ($this->memberId) {
            $query->where('id', $this->memberId);
        }

        $members = $query->get();
        $latestSellPrice = GoldPrice::where('type', 'SELL')->latest('price_date')->first()?->price ?? 0;

        return $members->map(function ($member) use ($latestSellPrice) {
            $goldBalance = $member->gold_balance;
            $totalCapital = $member->total_capital;

            return [
                'name' => $member->name,
                'gold_balance' => $goldBalance,
                'average_price' => $goldBalance > 0 ? round($totalCapital / $goldBalance, 2) : 0,
                'total_capital' => $totalCapital,
                'current_value' => round($goldBalance * (float) $latestSellPrice, 2),
                'profit' => round(($goldBalance * (float) $latestSellPrice) - $totalCapital, 2),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Anggota',
            'Saldo Emas (Gram)',
            'Harga Rata-rata',
            'Total Modal',
            'Nilai Saat Ini',
            'Laba/Rugi',
        ];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            number_format((float) $row['gold_balance'], 4, ',', '.'),
            'Rp ' . number_format((float) $row['average_price'], 2, ',', '.'),
            'Rp ' . number_format((float) $row['total_capital'], 2, ',', '.'),
            'Rp ' . number_format((float) $row['current_value'], 2, ',', '.'),
            'Rp ' . number_format((float) $row['profit'], 2, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'B8860B']]],
        ];
    }
}
