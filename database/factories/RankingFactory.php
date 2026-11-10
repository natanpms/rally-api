<?php

namespace Database\Factories;

use App\Models\Ranking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ranking>
 */
class RankingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour' => 'atp',
            'ranked_at' => now()->startOfWeek()->toDateString(),
            'position' => fake()->unique()->numberBetween(1, 900),
            'player_id' => fake()->unique()->numberBetween(1, 99999),
            'player_name' => fake()->name('male'),
            'country_name' => fake()->country(),
            'country_code' => mb_strtoupper(fake()->lexify('???')),
            'points' => fake()->numberBetween(1, 10000),
            'photo_url' => fake()->imageUrl(200, 300),
        ];
    }

    public function wta(): static
    {
        return $this->state(['tour' => 'wta', 'player_name' => fake()->name('female')]);
    }
}
