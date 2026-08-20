<?php

namespace App\Actions\HubApiKeys;

use App\Models\HubApiKey;
use Illuminate\Support\Carbon;

class RevokeHubApiKey
{
    public function handle(HubApiKey $apiKey): HubApiKey
    {
        $apiKey->revoked_at = Carbon::now();
        $apiKey->save();

        return $apiKey;
    }
}
