<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Hole;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 5) as $index) {
            Course::factory()->count(1)->has(
                Hole::factory()->count(18)->sequence(fn ($sequence) => [
                    'number' => $sequence->index + 1,
                    'allowed_time' => Carbon::createFromFormat('H:i', '00:10')->addMinutes($sequence->index * 1)->format('H:i'),
                ])
            )->create();
        }
    }
}
