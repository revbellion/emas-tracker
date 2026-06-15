<?php

namespace Database\Seeders;

use App\Models\FamilyMember;
use App\Models\GoldTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GoldTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $members = FamilyMember::all();

        if (!$user) {
            return;
        }

        $transactions = [
            // Budi - pembelian rutin
            ['member' => 'Budi Santoso', 'type' => 'BUY', 'gram' => 10, 'price' => 1_185_000, 'date' => Carbon::today()->subDays(60), 'desc' => 'Pembelian awal'],
            ['member' => 'Budi Santoso', 'type' => 'BUY', 'gram' => 5, 'price' => 1_200_000, 'date' => Carbon::today()->subDays(30), 'desc' => 'Tabungan bulanan'],
            ['member' => 'Budi Santoso', 'type' => 'BUY', 'gram' => 3, 'price' => 1_210_000, 'date' => Carbon::today()->subDays(7), 'desc' => 'Top up tabungan'],

            // Siti - pembelian kecil rutin
            ['member' => 'Siti Rahmawati', 'type' => 'BUY', 'gram' => 5, 'price' => 1_190_000, 'date' => Carbon::today()->subDays(45), 'desc' => 'Tabungan bulanan'],
            ['member' => 'Siti Rahmawati', 'type' => 'BUY', 'gram' => 2, 'price' => 1_205_000, 'date' => Carbon::today()->subDays(14), 'desc' => 'Tabungan mingguan'],

            // Ahmad - pendidikan
            ['member' => 'Ahmad Prasetyo', 'type' => 'BUY', 'gram' => 20, 'price' => 1_180_000, 'date' => Carbon::today()->subDays(90), 'desc' => 'Tabungan pendidikan'],
            ['member' => 'Ahmad Prasetyo', 'type' => 'BUY', 'gram' => 10, 'price' => 1_195_000, 'date' => Carbon::today()->subDays(20), 'desc' => 'Top up pendidikan'],

            // Hj. Fatimah - dana pensiun
            ['member' => 'Hj. Fatimah', 'type' => 'BUY', 'gram' => 25, 'price' => 1_175_000, 'date' => Carbon::today()->subDays(120), 'desc' => 'Investasi pensiun'],
            ['member' => 'Hj. Fatimah', 'type' => 'SELL', 'gram' => 5, 'price' => 1_200_000, 'date' => Carbon::today()->subDays(15), 'desc' => 'Penjualan parsial untuk kebutuhan'],
        ];

        foreach ($transactions as $t) {
            $member = $members->firstWhere('name', $t['member']);
            if (!$member) {
                continue;
            }

            GoldTransaction::create([
                'member_id' => $member->id,
                'type' => $t['type'],
                'gram' => $t['gram'],
                'price_per_gram' => $t['price'],
                'total_amount' => $t['gram'] * $t['price'],
                'transaction_date' => $t['date'],
                'description' => $t['desc'],
                'created_by' => $user->id,
            ]);
        }
    }
}
