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
            'start_interval' => $this->faker->time(),
            'morning' => $this->faker->time(),
            'afternoon' => $this->faker->time(),
            'crossover_one' => $this->faker->time(),
            'crossover_ten' => $this->faker->time(),
            'ball' => rand(2, 4),
            'transportation' => $transfortationOptions[rand(0, 2)],
        ];
    }
}
