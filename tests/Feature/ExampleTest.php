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
        // Ajuste: la ruta '/' redirige a /admin, por lo que esperamos 302
        $response->assertStatus(302);
        $response->assertRedirect('/admin');
    }
}
