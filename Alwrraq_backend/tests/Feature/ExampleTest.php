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

        $response
            ->assertStatus(200)
            ->assertSee('rel="canonical"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('max-image-preview:large', false)
            ->assertSee('جميع مناطق المملكة', false)
            ->assertSee('RedBox', false);
    }

    public function test_the_public_sitemap_is_available(): void
    {
        $response = $this->get('/sitemap.xml');

        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false)
            ->assertSee(route('public.home'), false);
    }
}
