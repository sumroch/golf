<?php

namespace Database\Seeders;

use App\Models\Tournament;
use App\Models\TournamentRound;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tournament::factory()
            ->count(1)
            ->has(TournamentRound::factory()->count(4), 'rounds')
            ->create();
    }
}
