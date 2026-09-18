<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseDownUnhappyTest extends TestCase
{
    use RefreshDatabase;

    public function test_unhappy_scenario_contactpersonen_handles_database_query_exception_gracefully(): void
    {
        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $response = $this->actingAs($user)->get('/contactpersonen?db_error=1');

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
    }

    public function test_unhappy_scenario_verkopers_handles_database_query_exception_gracefully(): void
    {
        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $response = $this->actingAs($user)->get('/verkopers?db_error=1');

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
    }

    public function test_unhappy_scenario_stands_handles_database_query_exception_gracefully(): void
    {
        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $response = $this->actingAs($user)->get('/stands?unhappy=1');

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
    }

    public function test_unhappy_scenario_tickets_handles_database_query_exception_gracefully(): void
    {
        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $response = $this->actingAs($user)->get('/tickets?db_error=1');

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
    }
}
