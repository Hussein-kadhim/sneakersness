<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\EvenementSeeder;
use Database\Seeders\SneakernessSeeder;
use Database\Seeders\TicketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketToevoegenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Happy scenario
     * Gegeven ik ben op het tickets overzicht
     * Wanneer ik een nieuw ticket toevoeg
     * Dan wordt het ticket succesvol aangemaakt
     * En wordt het ticket opgeslagen
     * En verschijnt het nieuwe ticket in het tickets overzicht
     */
    public function test_happy_scenario_staff_can_add_ticket_and_sees_it_in_overview(): void
    {
        $this->seed(SneakernessSeeder::class);
        $this->seed(TicketSeeder::class);

        $user = User::factory()->create([
            'name' => 'Organisator Medewerker',
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        // Gegeven ik ben op het tickets overzicht
        $overviewResponse = $this->actingAs($user)->get('/tickets');
        $overviewResponse->assertStatus(200);
        $overviewResponse->assertSee('Ticket toevoegen');

        // Formulier openen
        $createFormResponse = $this->actingAs($user)->get('/tickets/create');
        $createFormResponse->assertStatus(200);
        $createFormResponse->assertSee('Ticket Toevoegen');

        $initialCount = Ticket::count();

        // Wanneer ik een nieuw ticket toevoeg
        $postData = [
            'ticket_code' => 'TKT-99991',
            'bestelnummer' => 'SN-2026-99991',
            'bezoeker_naam' => 'Klaas van der Berg',
            'bezoeker_email' => 'klaas@berg.nl',
            'evenement_id' => 1,
            'ticket_type' => 'Regulier Slot 1',
            'tijdslot' => 'Zaterdag 24 okt (10:00 - 14:00)',
            'zaal_toegang' => 'Hal 1 • Entree A',
            'aantal_tickets' => 2,
            'status' => 'Geldig',
            'opmerking' => 'Registratie via medewerker portaal',
        ];

        $response = $this->actingAs($user)->post('/tickets', $postData);

        // Dan wordt het ticket succesvol aangemaakt en geredirect
        $response->assertRedirect('/tickets');
        $response->assertSessionHas('success');

        // En wordt het ticket opgeslagen in de database
        $this->assertDatabaseCount('Ticket', $initialCount + 1);
        $this->assertDatabaseHas('Ticket', [
            'TicketCode' => 'TKT-99991',
            'Bestelnummer' => 'SN-2026-99991',
            'Status' => 'Geldig',
        ]);

        // En verschijnt het nieuwe ticket in het tickets overzicht
        $followResponse = $this->actingAs($user)->get('/tickets');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('succesvol toegevoegd en opgeslagen');
        $followResponse->assertSee('TKT-99991');
        $followResponse->assertSee('Klaas van der Berg');
    }

    /**
     * Unhappy scenario
     * Gegeven ik ben ingelogd
     * Wanneer ik een nieuw ticket toevoeg
     * En de database is niet beschikbaar
     * Dan krijg ik een foutmelding:
     * "Database is momenteel niet beschikbaar, het ticket kon niet worden opgeslagen. Probeer het later opnieuw."
     * En wordt het ticket niet opgeslagen
     * En verschijnt het ticket niet in het tickets overzicht.
     */
    public function test_unhappy_scenario_add_ticket_fails_when_database_unavailable(): void
    {
        $this->seed(SneakernessSeeder::class);
        $this->seed(TicketSeeder::class);

        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $initialCount = Ticket::count();

        $postData = [
            'ticket_code' => 'TKT-99992',
            'bestelnummer' => 'SN-2026-99992',
            'bezoeker_naam' => 'Foutieve Invoer',
            'bezoeker_email' => 'fout@voorbeeld.nl',
            'ticket_type' => 'Regulier Slot 1',
            'status' => 'Geldig',
        ];

        // Wanneer ik een nieuw ticket toevoeg en de database is niet beschikbaar
        $response = $this->actingAs($user)->post('/tickets?db_error=1', $postData);

        $response->assertRedirect('/tickets/create');
        $response->assertSessionHas('error', 'Database is momenteel niet beschikbaar, het ticket kon niet worden opgeslagen. Probeer het later opnieuw.');

        // En wordt het ticket niet opgeslagen
        $this->assertDatabaseCount('Ticket', $initialCount);
        $this->assertDatabaseMissing('Ticket', [
            'TicketCode' => 'TKT-99992',
        ]);

        // En verschijnt het ticket niet in het tickets overzicht
        $followResponse = $this->actingAs($user)->get('/tickets');
        $followResponse->assertStatus(200);
        $followResponse->assertDontSee('TKT-99992');
        $followResponse->assertDontSee('Foutieve Invoer');
    }
}
