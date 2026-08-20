<?php

namespace App\Actions\HubApiKeys;

use App\Models\HubApiKey;

class RevokeHubApiKey
{
    public function handle(HubApiKey $apiKey): HubApiKey
    {
        $apiKey->revoked_at = now();
        $apiKey->save();

        return $apiKey;
    }
}
