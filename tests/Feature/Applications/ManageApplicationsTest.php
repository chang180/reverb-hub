<?php

use App\Livewire\Applications\Index;
use App\Models\ReverbApplication;
use App\Models\User;
use Livewire\Livewire;

test('authenticated users can view the applications page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('applications.index'))
        ->assertOk()
        ->assertSee('Create application');
});

test('creating an application requires a name', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Index::class)
        ->set('name', '')
        ->set('allowedOrigins', '*')
        ->call('create')
        ->assertHasErrors(['name']);

    expect(ReverbApplication::query()->exists())->toBeFalse();
});

test('blank allowed origins default to any origin', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Index::class)
        ->set('name', 'Wildcard App')
        ->set('allowedOrigins', '  ,  ')
        ->call('create')
        ->assertHasNoErrors();

    $application = ReverbApplication::query()->first();

    expect($application)->not->toBeNull()
        ->and($application->allowed_origins)->toBe(['*']);
});

test('comma separated origins are stored as a list', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Index::class)
        ->set('name', 'Shop')
        ->set('allowedOrigins', 'https://a.test, https://b.test')
        ->call('create')
        ->assertHasNoErrors();

    $application = ReverbApplication::query()->first();

    expect($application->allowed_origins)->toBe([
        'https://a.test',
        'https://b.test',
    ]);
});

test('an application can be toggled off and on', function () {
    $this->actingAs(User::factory()->create());

    $application = ReverbApplication::factory()->create(['enabled' => true]);

    Livewire::test(Index::class)
        ->call('toggle', $application->id);

    expect($application->refresh()->enabled)->toBeFalse();

    Livewire::test(Index::class)
        ->call('toggle', $application->id);

    expect($application->refresh()->enabled)->toBeTrue();
});

test('rotating credentials replaces the key and flashes the new secret', function () {
    $this->actingAs(User::factory()->create());

    $application = ReverbApplication::factory()->create();
    $originalKey = $application->key;
    $originalAppId = $application->app_id;

    Livewire::test(Index::class)
        ->call('rotate', $application->id)
        ->assertSee('Copy this secret now');

    $application->refresh();

    expect($application->key)->not->toBe($originalKey)
        ->and($application->app_id)->not->toBe($originalAppId);

});

test('an application can be deleted', function () {
    $this->actingAs(User::factory()->create());

    $application = ReverbApplication::factory()->create();

    Livewire::test(Index::class)
        ->call('delete', $application->id);

    $this->assertModelMissing($application);
});
