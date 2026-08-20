<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HubApiKey;
use App\Services\ApiDocsBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HubApiController extends Controller
{
    public function __invoke(Request $request, ApiDocsBuilder $apiDocsBuilder): JsonResponse
    {
        if ($request->query('docs') !== '1') {
            return response()->json(['message' => 'Not found.'], 404);
        }

        /** @var HubApiKey $apiKey */
        $apiKey = $request->attributes->get('hub_api_key');

        return response()->json($apiDocsBuilder->build($request, $apiKey));
    }
}
