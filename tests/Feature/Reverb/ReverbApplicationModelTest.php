<?php

use App\Models\ReverbApplication;
use App\Reverb\DatabaseApplicationProvider;
use Illuminate\Support\Facades\DB;
use Laravel\Reverb\Exceptions\InvalidApplication;

test('new credentials are stored with an encrypted secret', function () {
    $application = new ReverbApplication;
    $credentials = $application->assignNewCredentials();
    $application->name = 'Encrypted App';
    $application->allowed_origins = ['*'];
    $application->enabled = true;
    $application->save();

    $this->assertModelExists($application);

    expect($application->app_id)->toBe($credentials['app_id'])
        ->and($application->key)->toBe($credentials['key'])
        ->and($application->secret)->toBe($credentials['secret']);

    $storedSecret = DB::table($application->getTable())
        ->where('id', $application->id)
        ->value('secret');

    expect($storedSecret)->not->toBe($credentials['secret']);
});

test('empty allowed origins become a wildcard for reverb', function () {
    $application = ReverbApplication::factory()->create([
        'allowed_origins' => [],
    ]);

    expect($application->toReverbApplication()->allowedOrigins())->toBe(['*'])
        ->and($application->toReverbApplication()->id())->toBe($application->app_id)
        ->and($application->toReverbApplication()->key())->toBe($application->key);
});

test('saving an application flushes the provider cache', function () {
    $application = ReverbApplication::factory()->create();
    $provider = new DatabaseApplicationProvider;

    expect($provider->all())->toHaveCount(1);

    $application->update(['enabled' => false]);

    expect($provider->all())->toHaveCount(0);
});

test('find by key rejects missing applications', function () {
    $provider = new DatabaseApplicationProvider;

    $provider->findByKey('missing');
})->throws(InvalidApplication::class);
