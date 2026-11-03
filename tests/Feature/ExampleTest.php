<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('property="og:site_name" content="Go Umroh"', false);
        $response->assertSee('go_umroh.svg', false);
    }

    public function test_packages_page_renders_seo_open_graph(): void
    {
        $response = $this->get('/packages/contoh-paket');

        $response->assertStatus(200);
        $response->assertSee('property="og:site_name" content="Go Umroh"', false);
        $response->assertSee('name="twitter:card"', false);
    }
}
