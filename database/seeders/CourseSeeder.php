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
        $data = Course::create([
            'name' => 'Bali National Golf Club',
            'location' => 'bali',
            'par' => 72,
            'total_holes' => 18,
        ]);

        $data2 = Course::create([
            'name' => 'DAGO HERITAGE 1917',
            'location' => 'bandung',
            'par' => 71,
            'total_holes' => 18,
        ]);

        $data->holes()->createMany($this->createHole());
        $data2->holes()->createMany($this->createHole());
    }

    public function createHole () {
        $data = [];

        foreach (range(1, 18) as $index) {
            $data[] = [
                'number' => $index,
                'allowed_time' => Carbon::createFromFormat('H:i', '00:10')->addMinutes($index * 1)->format('H:i'),
            ];
        }

        return $data;
    }
}
