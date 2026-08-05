<?php

namespace Tests\Feature;

use Tests\TestCase;

class LanguageTest extends TestCase
{
    public function test_can_switch_language_to_english()
    {
        $response = $this->get('/lang/en');
        $response->assertSessionHas('locale', 'en');

        $this->withSession(['locale' => 'en'])
            ->get('/')
            ->assertSee('English')
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
}
