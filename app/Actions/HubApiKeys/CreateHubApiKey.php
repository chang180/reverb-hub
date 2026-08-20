<?php

namespace App\Actions\HubApiKeys;

use App\Enums\HubApiKeyPreset;
use App\Models\HubApiKey;
use App\Models\User;

class CreateHubApiKey
{
    /**
     * @return array{model: HubApiKey, plain: string}
     */
    public function handle(User $user, string $name, HubApiKeyPreset $preset): array
    {
        $token = HubApiKey::generateToken();

        $model = $user->hubApiKeys()->create([
            'name' => $name,
            'prefix' => $token['prefix'],
            'token_hash' => $token['hash'],
            'abilities' => $preset->abilities(),
        ]);

        return [
            'model' => $model,
            'plain' => $token['plain'],
        ];
    }
}
