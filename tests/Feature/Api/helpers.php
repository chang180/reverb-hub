<?php

use App\Actions\HubApiKeys\CreateHubApiKey;
use App\Enums\HubApiKeyPreset;
use App\Models\HubApiKey;
use App\Models\User;

/**
 * @return array{model: HubApiKey, plain: string}
 */
function createHubApiKeyFor(User $user, HubApiKeyPreset $preset = HubApiKeyPreset::Basic, string $name = 'Test Key'): array
{
    return app(CreateHubApiKey::class)->handle($user, $name, $preset);
}

function hubApiHeaders(string $plainToken): array
{
    return [
        'Authorization' => 'Bearer '.$plainToken,
        'Accept' => 'application/json',
    ];
}
