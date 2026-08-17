<?php

use App\Models\ReverbApplication;
use App\Models\User;
use App\Services\ReverbApiClient;

test('the dashboard reports when reverb is online and lists applications', function () {
    $user = User::factory()->create();
    ReverbApplication::factory()->create(['name' => 'Hostinger Shop']);

    $this->mock(ReverbApiClient::class, function ($mock): void {
        $mock->shouldReceive('ping')->andReturn(true);
    });

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Reverb online')
        ->assertSee('Hostinger Shop');
});

test('the dashboard reports when reverb is unreachable', function () {
    $user = User::factory()->create();

    $this->mock(ReverbApiClient::class, function ($mock): void {
        $mock->shouldReceive('ping')->andReturn(false);
    });

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Reverb unreachable');
});
