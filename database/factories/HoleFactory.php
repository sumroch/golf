<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hole>
 */
class HoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'allowed_time' => $this->faker->time('H:i', '00:20'),
            'par' => 0,
            'course_id' => \App\Models\Course::factory(),
        ];
    }
}
