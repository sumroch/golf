<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentRoundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $transfortationOptions = ['cart', 'combine', 'walk'];

        return [
            'tee_area' => json_encode(['blue', 'white']),
            'start_interval' => $this->faker->time(),
            'morning_one' => $this->faker->time(),
            'morning_ten' => $this->faker->time(),
            'afternoon_one' => $this->faker->time(),
            'afternoon_ten' => $this->faker->time(),
            'crossover_one' => $this->faker->time(),
            'crossover_ten' => $this->faker->time(),
            'ball' => rand(2, 4),
            'transportation' => $transfortationOptions[rand(0, 2)],
        ];
    }
}
