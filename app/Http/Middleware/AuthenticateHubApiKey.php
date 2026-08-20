<?php

namespace App\Http\Middleware;

use App\Models\HubApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateHubApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! is_string($token) || $token === '') {
            return $this->unauthenticatedResponse();
        }

        $apiKey = HubApiKey::findByPlainToken($token);

        if (! $apiKey instanceof HubApiKey) {
            return $this->unauthenticatedResponse();
        }

        $apiKey->forceFill(['last_used_at' => now()])->save();
        $request->attributes->set('hub_api_key', $apiKey);

        return $next($request);
    }

    private function unauthenticatedResponse(): Response
    {
        return response()->json([
            'message' => 'Unauthenticated.',
            'docs_url' => '/api?docs=1',
        ], 401);
    }
}
