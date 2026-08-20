<?php

use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('guests cannot view the pulse dashboard', function () {
    $this->get('/pulse')->assertNotFound();
});

test('authenticated users can view the pulse dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/pulse')
        ->assertOk();
});

test('the pulse dashboard links back to the main dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/pulse')
        ->assertOk()
        ->assertSee(route('dashboard', absolute: false), escape: false)
        ->assertSee(__('Dashboard'));
});

test('the viewPulse gate only allows authenticated users', function () {
    expect(Gate::has('viewPulse'))->toBeTrue()
        ->and(Gate::allows('viewPulse'))->toBeFalse();

    $this->actingAs(User::factory()->create());

    expect(Gate::allows('viewPulse'))->toBeTrue();
});
