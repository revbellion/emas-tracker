<?php

namespace App\Services;

use App\Models\FamilyMember;
use App\Models\GoldTransaction;
use Illuminate\Support\Facades\DB;

class GoldTransactionService
{
    public function getAll(array $filters = [])
    {
        $query = GoldTransaction::with('member', 'creator');

        if (isset($filters['member_id'])) {
            $query->where('member_id', $filters['member_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['from'])) {
            $query->where('transaction_date', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->where('transaction_date', '<=', $filters['to']);
        }

        if (isset($filters['search'])) {
            $query->where('description', 'like', "%{$filters['search']}%");
        }

        return $query->latest()->paginate(10);
    }

    public function buy(array $data): GoldTransaction
    {
        return DB::transaction(function () use ($data) {
            $data['type'] = 'BUY';
            $data['created_by'] = auth()->id();
            $data['total_amount'] = $data['gram'] * $data['price_per_gram'];

            $transaction = GoldTransaction::create($data);

            app(ActivityLogService::class)->log(
                'BUY_GOLD',
                "Pembelian {$data['gram']} gram emas oleh {$transaction->member->name}",
                $transaction
            );

            return $transaction;
        });
    }

    public function sell(array $data): GoldTransaction
    {
        return DB::transaction(function () use ($data) {
            $member = FamilyMember::findOrFail($data['member_id']);
            $gram = (float) $data['gram'];

            if ($member->gold_balance < $gram) {
                throw new \Exception("Saldo emas {$member->name} tidak mencukupi. Tersedia: {$member->gold_balance} gram");
            }

            $data['type'] = 'SELL';
            $data['created_by'] = auth()->id();
            $data['gram'] = -abs($gram);
            $data['total_amount'] = $data['gram'] * $data['price_per_gram'];

            $transaction = GoldTransaction::create($data);

            app(ActivityLogService::class)->log(
                'SELL_GOLD',
                "Penjualan {$gram} gram emas oleh {$transaction->member->name}",
                $transaction
            );

            return $transaction;
        });
    }

    public function transfer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $fromMember = FamilyMember::findOrFail($data['from_member_id']);
            $toMember = FamilyMember::findOrFail($data['to_member_id']);
            $gram = (float) $data['gram'];

            if ($fromMember->gold_balance < $gram) {
                throw new \Exception("Saldo emas {$fromMember->name} tidak mencukupi. Tersedia: {$fromMember->gold_balance} gram");
            }

            $pricePerGram = $data['price_per_gram'] ?? 0;

            $out = GoldTransaction::create([
                'member_id' => $fromMember->id,
                'type' => 'TRANSFER_OUT',
                'gram' => -abs($gram),
                'price_per_gram' => $pricePerGram,
                'total_amount' => 0,
                'transaction_date' => $data['transaction_date'],
                'description' => $data['description'] ?? "Transfer ke {$toMember->name}",
                'created_by' => auth()->id(),
            ]);

            $in = GoldTransaction::create([
                'member_id' => $toMember->id,
                'type' => 'TRANSFER_IN',
                'gram' => abs($gram),
                'price_per_gram' => $pricePerGram,
                'total_amount' => 0,
                'transaction_date' => $data['transaction_date'],
                'description' => $data['description'] ?? "Transfer dari {$fromMember->name}",
                'reference_id' => $out->id,
                'created_by' => auth()->id(),
            ]);

            $out->update(['reference_id' => $in->id]);

            app(ActivityLogService::class)->log(
                'TRANSFER_GOLD',
                "Transfer {$gram} gram emas dari {$fromMember->name} ke {$toMember->name}",
                $out
            );

            return [$out, $in];
        });
    }

    public function adjustment(array $data): GoldTransaction
    {
        return DB::transaction(function () use ($data) {
            $data['type'] = 'ADJUSTMENT';
            $data['created_by'] = auth()->id();
            $data['total_amount'] = ($data['gram'] ?? 0) * ($data['price_per_gram'] ?? 0);

            $transaction = GoldTransaction::create($data);

            app(ActivityLogService::class)->log(
                'ADJUSTMENT_GOLD',
                "Penyesuaian {$data['gram']} gram emas untuk {$transaction->member->name}",
                $transaction
            );

            return $transaction;
        });
    }

    public function void(GoldTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $old = $transaction->toArray();
            $transaction->delete();

            app(ActivityLogService::class)->log(
                'VOID_TRANSACTION',
                "Transaksi emas dibatalkan",
                $transaction,
                $old
            );
        });
    }
}
