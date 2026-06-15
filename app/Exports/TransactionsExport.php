<?php

namespace App\Exports;

use App\Models\GoldTransaction;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(
        private array $filters = [],
    ) {}

    public function query()
    {
        $query = GoldTransaction::with('member');

        if (isset($this->filters['from'])) {
            $query->where('transaction_date', '>=', $this->filters['from']);
        }

        if (isset($this->filters['to'])) {
            $query->where('transaction_date', '<=', $this->filters['to']);
        }

        if (isset($this->filters['member_id'])) {
            $query->where('member_id', $this->filters['member_id']);
        }

        if (isset($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        return $query->latest('transaction_date');
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Anggota',
            'Tipe',
            'Gram',
            'Harga per Gram',
            'Total',
            'Deskripsi',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date,
            $transaction->member?->name ?? '-',
            match ($transaction->type) {
                'BUY' => 'Beli',
                'SELL' => 'Jual',
                'TRANSFER_IN' => 'Transfer Masuk',
                'TRANSFER_OUT' => 'Transfer Keluar',
                'ADJUSTMENT' => 'Penyesuaian',
                default => $transaction->type,
            },
            number_format((float) $transaction->gram, 4, ',', '.'),
            'Rp ' . number_format((float) $transaction->price_per_gram, 2, ',', '.'),
            'Rp ' . number_format((float) $transaction->total_amount, 2, ',', '.'),
            $transaction->description ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'B8860B']]],
        ];
    }
}
