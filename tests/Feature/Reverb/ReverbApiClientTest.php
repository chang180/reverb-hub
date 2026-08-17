<?php

use App\Services\ReverbApiClient;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['reverb-hub.api_url' => 'http://reverb.test']);

    Http::preventStrayRequests();
});

test('ping returns true when the reverb api responds', function () {
    Http::fake([
        'http://reverb.test' => Http::response('ok'),
    ]);

    expect((new ReverbApiClient)->ping())->toBeTrue();

    Http::assertSent(fn ($request): bool => $request->url() === 'http://reverb.test');
});

test('ping returns false when the reverb api cannot be reached', function () {
    Http::fake([
        'http://reverb.test' => Http::failedConnection(),
    ]);

    expect((new ReverbApiClient)->ping())->toBeFalse();
});
