<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_unhappy_scenario_stands_create_handles_database_query_exception_gracefully(): void
    {
        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $response = $this->actingAs($user)->get('/stands/create?db_error=1');

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
    }

    public function test_unhappy_scenario_event_create_shows_database_error_instead_of_form(): void
    {
        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $response = $this->actingAs($user)->get('/events/create?db_error=1');

        $response->assertOk();
        $response->assertSee('Event toevoegen');
        $response->assertSee('Verbindingsfout');
        $response->assertSee('de gegevens voor het aanmaken van het event konden niet worden geladen');
        $response->assertDontSee('name="OrganisatorId"', false);
        $response->assertDontSee('action="'.route('events.store').'"', false);
    }

    public function test_event_create_shows_form_when_database_is_available(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);

        DB::table('Organisator')->insert([
            'Naam' => 'Test Organisator',
            'Gebruikersnaam' => 'test-organisator',
            'Wachtwoord' => 'test',
            'IsActief' => true,
        ]);

        $response = $this->actingAs($user)->get('/events/create');

        $response->assertOk();
        $response->assertSee('name="OrganisatorId"', false);
        $response->assertSee('action="'.route('events.store').'"', false);
        $response->assertDontSee('Verbindingsfout');
    }
}
