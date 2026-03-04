<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timezoneOptions = ['Asia/Jakarta', 'Asia/Makasar', 'Asia/Jayapura'];
        return [
            'name' => $this->faker->word(),
            'location' => $this->faker->city(),
            'organizer' => $this->faker->name(),
            'date_start' => $this->faker->date(),
            'timezone' => $timezoneOptions[array_rand($timezoneOptions)],
            'round' => 4,
            'course_id' => \App\Models\Course::factory()->has(
                \App\Models\Hole::factory()->count(18)
            )->create()->id,
        ];
    }
}
