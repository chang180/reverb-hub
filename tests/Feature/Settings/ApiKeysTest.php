<?php

use App\Actions\HubApiKeys\CreateHubApiKey;
use App\Enums\HubApiKeyPreset;
use App\Livewire\Settings\ApiKeys;
use App\Models\HubApiKey;
use App\Models\User;
use Livewire\Livewire;

test('verified users can manage api keys in settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('api-keys.edit'))
        ->assertOk()
        ->assertSee('Create API key');
});

test('api keys settings page is translated for traditional chinese', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
            'locale' => 'zh_TW',
        ])
        ->get(route('api-keys.edit'))
        ->assertOk()
        ->assertSee('建立可程式化存取 Hub API 的金鑰')
        ->assertSee('權限')
        ->assertSee('基本')
        ->assertSee('可透過 API 建立 Reverb 應用程式。')
        ->assertSee('讀取')
        ->assertSee('管理')
        ->assertSee('建立 API key');
});

test('creating an api key shows the plain token once', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(ApiKeys::class)
        ->set('name', 'Deploy Bot')
        ->set('preset', HubApiKeyPreset::Read->value)
        ->call('create')
        ->assertSet('plainToken', fn (?string $token): bool => is_string($token) && str_starts_with($token, 'rh_'))
        ->assertSee('Copy this API key now');

    expect(HubApiKey::query()->where('name', 'Deploy Bot')->exists())->toBeTrue();
});

test('revoking an api key marks it revoked', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $created = app(CreateHubApiKey::class)->handle($user, 'Old Key', HubApiKeyPreset::Basic);

    Livewire::test(ApiKeys::class)
        ->call('revoke', $created['model']->id);

    expect($created['model']->refresh()->isRevoked())->toBeTrue();
});
