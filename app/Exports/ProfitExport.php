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

class ProfitExport implements FromArray, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function array(): array
    {
        $latestSellPrice = GoldPrice::where('type', 'SELL')->latest('price_date')->first()?->price ?? 0;
        $members = FamilyMember::where('is_active', true)->get();

        return $members->map(function ($member) use ($latestSellPrice) {
            $gram = $member->gold_balance;
            $capital = $member->total_capital;
            $value = $gram * (float) $latestSellPrice;

            return [
                'member_name' => $member->name,
                'gram' => $gram,
                'capital' => $capital,
                'current_value' => round($value, 2),
                'profit' => round($value - $capital, 2),
                'profit_percentage' => $capital > 0 ? round((($value - $capital) / $capital) * 100, 2) : 0,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Anggota',
            'Gram',
            'Modal',
            'Nilai Saat Ini',
            'Laba/Rugi',
            'Persentase',
        ];
    }

    public function map($row): array
    {
        return [
            $row['member_name'],
            number_format((float) $row['gram'], 4, ',', '.'),
            'Rp ' . number_format((float) $row['capital'], 2, ',', '.'),
            'Rp ' . number_format((float) $row['current_value'], 2, ',', '.'),
            'Rp ' . number_format((float) $row['profit'], 2, ',', '.'),
            $row['profit_percentage'] . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'B8860B']]],
        ];
    }
}
