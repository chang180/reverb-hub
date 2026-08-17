<?php

use App\Models\User;

test('guests cannot view appearance settings', function () {
    $this->get(route('appearance.edit'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view appearance settings', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('appearance.edit'))
        ->assertOk()
        ->assertSee('Appearance');
});

test('settings redirects to the profile page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/settings')
        ->assertRedirect('/settings/profile');
});
