<?php

namespace App\Services;

use App\Models\HubApiKey;
use Illuminate\Http\Request;

class ApiDocsBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function build(Request $request, HubApiKey $apiKey): array
    {
        $baseUrl = rtrim($request->getSchemeAndHttpHost(), '/').'/api';

        return [
            'hub' => [
                'name' => 'Reverb Hub',
                'base_url' => $baseUrl,
                'version' => '1',
            ],
            'authentication' => [
                'type' => 'bearer',
                'header' => 'Authorization',
                'format' => 'Bearer {api_key}',
                'hint' => 'Use the rh_... key issued in Settings > API Keys',
            ],
            'key_abilities' => $apiKey->abilities,
            'workflow' => [
                'summary' => 'Create a Reverb application and copy credentials to the client Laravel app',
                'steps' => $this->workflowSteps($apiKey),
            ],
            'endpoints' => $this->endpointsFor($apiKey),
            'client_env_mapping' => [
                'REVERB_APP_ID' => 'response.app_id',
                'REVERB_APP_KEY' => 'response.key',
                'REVERB_APP_SECRET' => 'response.secret',
                'REVERB_HOST' => $request->getHost(),
                'REVERB_PORT' => $request->getPort() === 443 || $request->getPort() === 80
                    ? ($request->getScheme() === 'https' ? '443' : '80')
                    : (string) $request->getPort(),
                'REVERB_SCHEME' => $request->getScheme(),
            ],
        ];
    }

    /**
     * @return list<string>
     */
    private function workflowSteps(HubApiKey $apiKey): array
    {
        $steps = [];

        if ($apiKey->hasAbility('applications:create')) {
            $steps[] = 'POST /api/applications with name and allowed_origins';
            $steps[] = 'Save app_id, key, secret from response immediately (secret shown once)';
            $steps[] = 'Set REVERB_APP_ID, REVERB_APP_KEY, REVERB_APP_SECRET, REVERB_HOST in client .env';
        }

        if ($apiKey->hasAbility('applications:read')) {
            $steps[] = 'GET /api/applications to list applications without secrets';
        }

        if ($apiKey->hasAbility('applications:update')) {
            $steps[] = 'PATCH /api/applications/{id} with enabled to enable or disable an application';
            $steps[] = 'POST /api/applications/{id}/rotate to rotate credentials (secret shown once)';
        }

        if ($apiKey->hasAbility('applications:delete')) {
            $steps[] = 'DELETE /api/applications/{id} to remove an application';
        }

        return $steps;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function endpointsFor(HubApiKey $apiKey): array
    {
        $endpoints = [];

        if ($apiKey->hasAbility('applications:create')) {
            $endpoints[] = [
                'method' => 'POST',
                'path' => '/api/applications',
                'ability' => 'applications:create',
                'description' => 'Create a new Reverb application',
                'request' => [
                    'content_type' => 'application/json',
                    'body' => [
                        'name' => [
                            'type' => 'string',
                            'required' => true,
                            'max' => 255,
                            'example' => 'My Shop',
                        ],
                        'allowed_origins' => [
                            'type' => 'string',
                            'required' => true,
                            'description' => 'Comma or whitespace separated URLs; blank becomes *',
                            'example' => 'https://shop.test',
                        ],
                    ],
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Created; secret included once',
                        'example' => [
                            'id' => 1,
                            'name' => 'My Shop',
                            'app_id' => '123456',
                            'key' => 'abcdefghijklmnop',
                            'secret' => 'plain-secret-shown-once',
                            'allowed_origins' => ['https://shop.test'],
                            'enabled' => true,
                        ],
                    ],
                    '401' => ['description' => 'Missing or invalid API key'],
                    '403' => ['description' => 'Key lacks applications:create'],
                    '422' => ['description' => 'Validation error'],
                ],
            ];
        }

        if ($apiKey->hasAbility('applications:read')) {
            $endpoints[] = [
                'method' => 'GET',
                'path' => '/api/applications',
                'ability' => 'applications:read',
                'description' => 'List Reverb applications without secrets',
                'request' => [
                    'content_type' => null,
                    'body' => [],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Application list',
                        'example' => [
                            'data' => [
                                [
                                    'id' => 1,
                                    'name' => 'My Shop',
                                    'app_id' => '123456',
                                    'key' => 'abcdefghijklmnop',
                                    'allowed_origins' => ['https://shop.test'],
                                    'enabled' => true,
                                ],
                            ],
                        ],
                    ],
                    '401' => ['description' => 'Missing or invalid API key'],
                    '403' => ['description' => 'Key lacks applications:read'],
                ],
            ];
        }

        if ($apiKey->hasAbility('applications:update')) {
            $endpoints[] = [
                'method' => 'PATCH',
                'path' => '/api/applications/{application}',
                'ability' => 'applications:update',
                'description' => 'Enable or disable a Reverb application',
                'request' => [
                    'content_type' => 'application/json',
                    'body' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'required' => true,
                            'example' => false,
                        ],
                    ],
                ],
                'responses' => [
                    '200' => ['description' => 'Updated application'],
                    '401' => ['description' => 'Missing or invalid API key'],
                    '403' => ['description' => 'Key lacks applications:update'],
                    '404' => ['description' => 'Application not found'],
                    '422' => ['description' => 'Validation error'],
                ],
            ];

            $endpoints[] = [
                'method' => 'POST',
                'path' => '/api/applications/{application}/rotate',
                'ability' => 'applications:update',
                'description' => 'Rotate application credentials; plain secret returned once',
                'request' => [
                    'content_type' => null,
                    'body' => [],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Rotated credentials; secret included once',
                        'example' => [
                            'id' => 1,
                            'name' => 'My Shop',
                            'app_id' => '654321',
                            'key' => 'newkeyabcdefghij',
                            'secret' => 'new-plain-secret-shown-once',
                            'allowed_origins' => ['https://shop.test'],
                            'enabled' => true,
                        ],
                    ],
                    '401' => ['description' => 'Missing or invalid API key'],
                    '403' => ['description' => 'Key lacks applications:update'],
                    '404' => ['description' => 'Application not found'],
                ],
            ];
        }

        if ($apiKey->hasAbility('applications:delete')) {
            $endpoints[] = [
                'method' => 'DELETE',
                'path' => '/api/applications/{application}',
                'ability' => 'applications:delete',
                'description' => 'Delete a Reverb application',
                'request' => [
                    'content_type' => null,
                    'body' => [],
                ],
                'responses' => [
                    '204' => ['description' => 'Application deleted'],
                    '401' => ['description' => 'Missing or invalid API key'],
                    '403' => ['description' => 'Key lacks applications:delete'],
                    '404' => ['description' => 'Application not found'],
                ],
            ];
        }

        return $endpoints;
    }
}
