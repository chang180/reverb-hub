<?php

namespace Database\Factories;

use App\Models\ReverbApplication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ReverbApplication>
 */
class ReverbApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'app_id' => (string) fake()->unique()->numberBetween(100_000, 999_999),
            'key' => Str::lower(Str::random(20)),
            'secret' => Str::lower(Str::random(32)),
            'allowed_origins' => ['*'],
            'max_connections' => null,
            'ping_interval' => 60,
            'activity_timeout' => 30,
            'max_message_size' => 10_000,
            'enabled' => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (): array => [
            'enabled' => false,
        ]);
    }
}
