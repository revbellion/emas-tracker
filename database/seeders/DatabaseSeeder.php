<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'hendrikadi08@gmail.com',
            'password' => bcrypt('H3s0y4m123'),
        ]);
    }
}
