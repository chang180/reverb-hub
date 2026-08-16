<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_includes_locale_switcher(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('EN')
            ->assertSee('繁中');
    }

    public function test_users_can_switch_to_traditional_chinese(): void
    {
        $this->from(route('login'))
            ->post(route('locale.update', 'zh_TW'))
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('登入你的帳號');
    }

    public function test_unknown_locales_are_rejected(): void
    {
        $this->post(route('locale.update', 'fr'))
            ->assertNotFound();
    }
}
