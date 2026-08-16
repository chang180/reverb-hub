<?php

namespace Tests\Feature;

use App\Livewire\Applications\Index;
use App\Models\ReverbApplication;
use App\Models\User;
use App\Reverb\DatabaseApplicationProvider;
use App\Services\ReverbApiClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Reverb\Exceptions\InvalidApplication;
use Livewire\Livewire;
use Tests\TestCase;

class ReverbApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_applications(): void
    {
        $this->get(route('applications.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_create_an_application(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('name', 'Hostinger Shop')
            ->set('allowedOrigins', 'https://shop.example.com')
            ->call('create')
            ->assertHasNoErrors();

        $application = ReverbApplication::query()->first();

        $this->assertNotNull($application);
        $this->assertSame('Hostinger Shop', $application->name);
        $this->assertSame(['https://shop.example.com'], $application->allowed_origins);
        $this->assertTrue($application->enabled);
        $this->assertNotSame('', $application->key);
    }

    public function test_database_provider_only_returns_enabled_apps(): void
    {
        $enabled = ReverbApplication::factory()->create();
        ReverbApplication::factory()->disabled()->create();

        $provider = new DatabaseApplicationProvider;

        $this->assertCount(1, $provider->all());
        $this->assertSame($enabled->app_id, $provider->findById($enabled->app_id)->id());
        $this->assertSame($enabled->key, $provider->findByKey($enabled->key)->key());

        $this->expectException(InvalidApplication::class);
        $provider->findById('missing');
    }

    public function test_channel_page_lists_occupancy_from_reverb_api(): void
    {
        $user = User::factory()->create();
        $application = ReverbApplication::factory()->create();

        $this->mock(ReverbApiClient::class, function ($mock) use ($application): void {
            $mock->shouldReceive('channels')
                ->once()
                ->withArgs(fn (ReverbApplication $app): bool => $app->is($application))
                ->andReturn([
                    'orders' => ['subscription_count' => 3],
                    'presence-chat' => ['user_count' => 2],
                ]);
        });

        $this->actingAs($user)
            ->get(route('applications.show', $application))
            ->assertOk()
            ->assertSee('orders')
            ->assertSee('presence-chat');
    }
}
