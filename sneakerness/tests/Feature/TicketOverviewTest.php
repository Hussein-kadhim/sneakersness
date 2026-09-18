<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SneakernessSeeder;
use Database\Seeders\TicketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketOverviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Happy scenario:
     * Gegeven ik ben ingelogd
     * Wanneer ik naar het tickets overzicht ga
     * Dan worden de beschikbare tickets weergegeven
     * En zie ik per ticket de relevante informatie, zoals onderwerp, status en datum
     * En kan ik een ticket selecteren om de details te bekijken
     */
    public function test_happy_scenario_logged_in_user_sees_tickets_with_subject_status_date_and_details(): void
    {
        $this->seed(SneakernessSeeder::class);
        $this->seed(TicketSeeder::class);

        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $response = $this->actingAs($user)->get('/tickets');

        $response->assertStatus(200);
        $response->assertSee('Tickets Overzicht');

        // Beschikbare tickets worden weergegeven (Ticket codes)
        $response->assertSee('#TKT-84920');
        $response->assertSee('#TKT-84921');
        $response->assertSee('#TKT-84922');

        // Relevante informatie: Onderwerp / Tickettype
        $response->assertSee('Early Bird VIP');
        $response->assertSee('Regulier Slot 1');
        $response->assertSee('Weekend Passe-Partout');

        // Relevante informatie: Status
        $response->assertSee('Geldig');
        $response->assertSee('Aandacht nodig');
        $response->assertSee('Wijziging verzocht');

        // Relevante informatie: Datum en tijdslot
        $response->assertSee('Zaterdag 16 nov (10:00 - 18:00)');
        $response->assertSee('Zaterdag 16 nov (10:00 - 14:00)');

        // Details selectie knop en modal aanwezig
        $response->assertSee('Details');
        $response->assertSee('x-data="{ selectedTicket: null, showModal: false }"', false);
        $response->assertSee('x-show="showModal"', false);
    }

    /**
     * Unhappy scenario:
     * Gegeven ik ben ingelogd
     * Wanneer ik naar het tickets overzicht ga
     * En de database is niet beschikbaar
     * Dan krijg ik een foutmelding:
     * "Database is momenteel niet beschikbaar, de tickets konden niet worden geladen. Probeer het later opnieuw."
     * En worden er geen tickets weergegeven.
     */
    public function test_unhappy_scenario_database_unavailable_shows_exact_error_and_no_tickets(): void
    {
        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        // Simuleer database storing via parameter
        $response = $this->actingAs($user)->get('/tickets?db_error=1');

        $response->assertStatus(200);

        // Exact vereiste foutmelding
        $response->assertSee('Database is momenteel niet beschikbaar, de tickets konden niet worden geladen. Probeer het later opnieuw.');

        // Geen tickets worden weergegeven
        $response->assertDontSee('#TKT-84920');
        $response->assertDontSee('#TKT-84921');
        $response->assertDontSee('#TKT-84922');
        $response->assertDontSee('#TKT-84923');
        $response->assertDontSee('#TKT-84924');
        $response->assertDontSee('#TKT-84925');
    }
}
