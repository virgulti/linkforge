<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'short_code' => $this->faker->unique()->regexify('[0-9a-zA-Z]{6}'),
            'original_url' => $this->faker->url(),
            'user_id' => null,
            'expires_at' => $this->faker->boolean(25) ? now()->addDays(rand(1, 30)) : null,
        ];
    }
}
