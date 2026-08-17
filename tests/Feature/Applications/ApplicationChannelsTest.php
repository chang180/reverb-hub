<?php

use App\Livewire\Applications\Show;
use App\Models\ReverbApplication;
use App\Models\User;
use App\Services\ReverbApiClient;
use Livewire\Livewire;

test('guests cannot view an application channel page', function () {
    $application = ReverbApplication::factory()->create();

    $this->get(route('applications.show', $application))
        ->assertRedirect(route('login'));
});

test('inspecting a channel lists presence users from the reverb api', function () {
    $user = User::factory()->create();
    $application = ReverbApplication::factory()->create();

    $this->mock(ReverbApiClient::class, function ($mock) use ($application): void {
        $mock->shouldReceive('channels')
            ->andReturn([
                'presence-chat' => [
                    'subscription_count' => 2,
                    'user_count' => 2,
                ],
            ]);

        $mock->shouldReceive('channelUsers')
            ->once()
            ->withArgs(fn (ReverbApplication $app, string $channel): bool => $app->is($application) && $channel === 'presence-chat')
            ->andReturn([
                ['id' => 'user-42'],
            ]);
    });

    $this->actingAs($user);

    Livewire::test(Show::class, ['application' => $application])
        ->assertSee('presence-chat')
        ->call('inspect', 'presence-chat')
        ->assertSet('selectedChannel', 'presence-chat')
        ->assertSee('user-42');
});

test('channel page shows an error when reverb is unreachable', function () {
    $user = User::factory()->create();
    $application = ReverbApplication::factory()->create();

    $this->mock(ReverbApiClient::class, function ($mock): void {
        $mock->shouldReceive('channels')
            ->andThrow(new RuntimeException('connection refused'));
    });

    $this->actingAs($user);

    Livewire::test(Show::class, ['application' => $application])
        ->assertSet('error', 'connection refused')
        ->assertSet('channels', [])
        ->assertSee('Cannot reach Reverb')
        ->assertSee('connection refused');
});
