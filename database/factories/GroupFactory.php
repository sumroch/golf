<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'time' => $this->faker->time(),
            'session' => $this->faker->randomElement(['morning', 'afternoon']),
            'tee' => $this->faker->numberBetween(0, 10),
            'tournament_round_id' => \App\Models\TournamentRound::factory(),
        ];
    }
}
