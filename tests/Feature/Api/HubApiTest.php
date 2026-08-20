<?php

use App\Actions\HubApiKeys\RevokeHubApiKey;
use App\Enums\HubApiKeyPreset;
use App\Models\ReverbApplication;
use App\Models\User;

require __DIR__.'/helpers.php';

test('docs cannot be accessed without a bearer token', function () {
    $response = $this->getJson('/api?docs=1');

    $response->assertUnauthorized()
        ->assertJson([
            'message' => 'Unauthenticated.',
            'docs_url' => '/api?docs=1',
        ])
        ->assertJsonMissing(['endpoints', 'workflow', 'client_env_mapping', 'key_abilities']);
});

test('invalid and revoked tokens return minimal unauthorized responses', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user);

    $this->withHeaders(hubApiHeaders('rh_invalidtoken'))
        ->getJson('/api?docs=1')
        ->assertUnauthorized()
        ->assertJsonMissing(['endpoints']);

    app(RevokeHubApiKey::class)->handle($created['model']);

    $this->withHeaders(hubApiHeaders($created['plain']))
        ->getJson('/api?docs=1')
        ->assertUnauthorized();
});

test('basic keys receive docs with create endpoint only', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user, HubApiKeyPreset::Basic);

    $response = $this->withHeaders(hubApiHeaders($created['plain']))
        ->getJson('/api?docs=1');

    $response->assertOk()
        ->assertJsonPath('key_abilities', HubApiKeyPreset::Basic->abilities())
        ->assertJsonStructure([
            'hub',
            'authentication',
            'workflow',
            'endpoints',
            'client_env_mapping',
        ]);

    $paths = collect($response->json('endpoints'))->pluck('path')->all();

    expect($paths)->toContain('/api/applications')
        ->and($paths)->not->toContain('/api/applications/{application}');
});

test('read preset docs include list endpoint', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user, HubApiKeyPreset::Read);

    $paths = collect(
        $this->withHeaders(hubApiHeaders($created['plain']))
            ->getJson('/api?docs=1')
            ->json('endpoints'),
    )->pluck('path')->all();

    expect($paths)->toContain('/api/applications')
        ->and(collect($paths)->filter(fn (string $path) => str_contains($path, '{application}'))->isEmpty())->toBeTrue();
});

test('manage preset docs include update rotate and delete endpoints', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user, HubApiKeyPreset::Manage);

    $paths = collect(
        $this->withHeaders(hubApiHeaders($created['plain']))
            ->getJson('/api?docs=1')
            ->json('endpoints'),
    )->pluck('path')->all();

    expect($paths)->toContain('/api/applications/{application}')
        ->and($paths)->toContain('/api/applications/{application}/rotate');
});

test('authenticated root without docs returns not found', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user);

    $this->withHeaders(hubApiHeaders($created['plain']))
        ->getJson('/api')
        ->assertNotFound();
});

test('ai workflow can create an application using docs only', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user, HubApiKeyPreset::Basic);
    $headers = hubApiHeaders($created['plain']);

    $docs = $this->withHeaders($headers)
        ->getJson('/api?docs=1')
        ->assertOk()
        ->json();

    $createEndpoint = collect($docs['endpoints'])
        ->first(fn (array $endpoint): bool => ($endpoint['method'] ?? null) === 'POST');

    expect($createEndpoint)->not->toBeNull();

    $path = $createEndpoint['path'];
    $nameField = $createEndpoint['request']['body']['name']['example'] ?? 'Docs App';
    $originsField = $createEndpoint['request']['body']['allowed_origins']['example'] ?? 'https://shop.test';

    $response = $this->withHeaders($headers)
        ->postJson($path, [
            'name' => $nameField,
            'allowed_origins' => $originsField,
        ]);

    $response->assertCreated()
        ->assertJsonStructure(['id', 'name', 'app_id', 'key', 'secret', 'allowed_origins', 'enabled']);

    expect(ReverbApplication::query()->where('name', $nameField)->exists())->toBeTrue();
});

test('basic keys cannot list applications', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user, HubApiKeyPreset::Basic);

    ReverbApplication::factory()->create();

    $this->withHeaders(hubApiHeaders($created['plain']))
        ->getJson('/api/applications')
        ->assertForbidden();
});

test('read keys can list applications without secrets', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user, HubApiKeyPreset::Read);
    $application = ReverbApplication::factory()->create();

    $response = $this->withHeaders(hubApiHeaders($created['plain']))
        ->getJson('/api/applications');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $application->id)
        ->assertJsonMissing(['secret']);
});

test('create response includes secret once and manage keys can rotate and delete', function () {
    $user = User::factory()->create();
    $created = createHubApiKeyFor($user, HubApiKeyPreset::Manage);
    $headers = hubApiHeaders($created['plain']);

    $createResponse = $this->withHeaders($headers)
        ->postJson('/api/applications', [
            'name' => 'Managed App',
            'allowed_origins' => 'https://managed.test',
        ])
        ->assertCreated();

    expect($createResponse->json('secret'))->not->toBeEmpty();

    $applicationId = $createResponse->json('id');

    $this->withHeaders($headers)
        ->getJson('/api/applications')
        ->assertOk()
        ->assertJsonMissing(['secret']);

    $rotateResponse = $this->withHeaders($headers)
        ->postJson("/api/applications/{$applicationId}/rotate")
        ->assertOk();

    expect($rotateResponse->json('secret'))->not->toBeEmpty()
        ->and($rotateResponse->json('key'))->not->toBe($createResponse->json('key'));

    $this->withHeaders($headers)
        ->patchJson("/api/applications/{$applicationId}", ['enabled' => false])
        ->assertOk()
        ->assertJsonPath('data.enabled', false);

    $this->withHeaders($headers)
        ->deleteJson("/api/applications/{$applicationId}")
        ->assertNoContent();

    expect(ReverbApplication::query()->find($applicationId))->toBeNull();
});
