<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_switch_language_to_english()
    {
        $response = $this->get('/lang/en');
        $response->assertSessionHas('locale', 'en');

        $this->withSession(['locale' => 'en'])
            ->get('/')
            ->assertSee('Home');
    }

    public function test_can_switch_language_to_arabic()
    {
        $response = $this->get('/lang/ar');
        $response->assertSessionHas('locale', 'ar');

        $this->withSession(['locale' => 'ar'])
            ->get('/')
            ->assertSee('الرئيسية');
    }

    public function test_invalid_locale_rejected()
    {
        $this->get('/lang/fr')->assertStatus(404);
    }
}
