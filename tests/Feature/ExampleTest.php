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
        $user = \App\Models\User::factory()->create(); // Create a user
        $response = $this->actingAs($user)->get('/'); // Authenticate user

        $response->assertStatus(200);
    }
}
