<?php

namespace Database\Factories;

use App\Enums\HubApiKeyPreset;
use App\Models\HubApiKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HubApiKey>
 */
class HubApiKeyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $token = HubApiKey::generateToken();
        $preset = HubApiKeyPreset::Basic;

        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'prefix' => $token['prefix'],
            'token_hash' => $token['hash'],
            'abilities' => $preset->abilities(),
        ];
    }

    public function preset(HubApiKeyPreset $preset): static
    {
        return $this->state(fn (array $attributes) => [
            'abilities' => $preset->abilities(),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'revoked_at' => now(),
        ]);
    }
}
