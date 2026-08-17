<?php

test('an unsupported session locale is ignored', function () {
    $this->withSession(['locale' => 'fr'])
        ->get(route('login'))
        ->assertOk();

    expect(app()->getLocale())->toBe('en');
});

test('users can switch back to english', function () {
    $this->from(route('login'))
        ->post(route('locale.update', 'en'))
        ->assertRedirect(route('login'));

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Log in to your account');
});
