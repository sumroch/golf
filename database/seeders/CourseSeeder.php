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
        $items = [15, 18, 12, 15, 15, 15, 12, 18, 15, 18, 15, 12, 15, 12, 15, 15, 15, 18];

        $data = [];

        foreach ($items as $key => $item) {
            $data[] = [
                'number' => $key + 1,
                'par' => rand(3, 5),
                'allowed_time' => '00:' . $item . ':00',
            ];
        }

        return $data;
    }
}
