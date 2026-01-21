<?php

namespace Database\Seeders;

use App\Models\Hole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hole::factory()->count(50)->create();
    }
}
