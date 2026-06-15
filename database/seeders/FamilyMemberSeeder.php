<?php

namespace Database\Seeders;

use App\Models\FamilyMember;
use Illuminate\Database\Seeder;

class FamilyMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'Budi Santoso', 'relationship' => 'Suami', 'notes' => 'Kepala keluarga'],
            ['name' => 'Siti Rahmawati', 'relationship' => 'Istri', 'notes' => null],
            ['name' => 'Ahmad Prasetyo', 'relationship' => 'Anak', 'notes' => 'Tabungan pendidikan'],
            ['name' => 'Dewi Lestari', 'relationship' => 'Anak', 'notes' => null],
            ['name' => 'Hj. Fatimah', 'relationship' => 'Ibu', 'notes' => 'Dana pensiun'],
        ];

        foreach ($members as $member) {
            FamilyMember::create($member);
        }
    }
}
