<?php

namespace App\Http\Middleware;

use App\Models\HubApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHubApiAbility
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $apiKey = $request->attributes->get('hub_api_key');

        if (! $apiKey instanceof HubApiKey || ! $apiKey->hasAbility($ability)) {
            return response()->json([
                'message' => 'Forbidden.',
            ], 403);
        }

        return $next($request);
    }
}
