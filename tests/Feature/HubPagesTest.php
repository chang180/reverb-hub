<?php

use App\Models\User;

test('the home page shows hub branding and auth links', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(config('app.name'))
        ->assertSee('Log in')
        ->assertSee('Sign up');
});

test('authenticated visitors see a dashboard link on the home page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertDontSee('Log in');
});

test('the health endpoint is available', function () {
    $this->get('/up')->assertOk();
});

test('the passkey well-known endpoints document the security settings url', function () {
    $this->get(route('well-known.passkeys'))
        ->assertOk()
        ->assertJson([
            'enroll' => route('security.edit'),
            'manage' => route('security.edit'),
        ]);
});
