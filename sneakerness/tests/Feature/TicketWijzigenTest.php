<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\EvenementSeeder;
use Database\Seeders\SneakernessSeeder;
use Database\Seeders\TicketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketWijzigenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Happy scenario: Ticket succesvol wijzigen
     * Gegeven ik ben ingelogd en sta in het ticketoverzicht
     * Wanneer ik een ticket open om te wijzigen, de gegevens aanpas en dit opsla
     * Dan worden de wijzigingen opgeslagen
     * En zie ik een melding dat het ticket is gewijzigd
     * En staan de nieuwe gegevens in het ticketoverzicht
     */
    public function test_happy_scenario_user_can_edit_ticket_and_sees_updated_data(): void
    {
        $this->seed(SneakernessSeeder::class);
        $this->seed(TicketSeeder::class);

        $user = User::factory()->create([
            'name' => 'Organisator User',
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $ticket = Ticket::first();

        // Gegeven ik ben op het tickets overzicht
        $overviewResponse = $this->actingAs($user)->get('/tickets');
        $overviewResponse->assertStatus(200);
        $overviewResponse->assertSee('Tickets Overzicht');
        $overviewResponse->assertSee('Wijzigen');

        // Wanneer ik een ticket open om te wijzigen
        $editFormResponse = $this->actingAs($user)->get("/tickets/{$ticket->Id}/edit");
        $editFormResponse->assertStatus(200);
        $editFormResponse->assertSee('Ticket Wijzigen');

        // De gegevens aanpas en dit opsla
        $updateData = [
            'ticket_code' => $ticket->ticket_code_display,
            'bestelnummer' => $ticket->bestelnummer_display,
            'bezoeker_naam' => 'Gewijzigde Bezoeker Naam',
            'bezoeker_email' => 'gewijzigd@voorbeeld.nl',
            'ticket_type' => 'VIP Toegang 2026',
            'tijdslot' => 'Zondag 25 okt (12:00 - 18:00)',
            'zaal_toegang' => 'Hal 2 • VIP Lounge',
            'status' => 'Geldig',
            'opmerking' => 'Gegevens succesvol geüpdatet',
        ];

        $response = $this->actingAs($user)->put("/tickets/{$ticket->Id}", $updateData);

        // Dan worden de wijzigingen opgeslagen en redirect naar overzicht
        $response->assertRedirect('/tickets');
        $response->assertSessionHas('success', 'Het ticket is gewijzigd.');

        // En staan de nieuwe gegevens in de database
        $this->assertDatabaseHas('Ticket', [
            'Id' => $ticket->Id,
            'TicketType' => 'VIP Toegang 2026',
            'Status' => 'Geldig',
        ]);

        $this->assertDatabaseHas('Bezoeker', [
            'Naam' => 'Gewijzigde Bezoeker Naam',
            'Emailadres' => 'gewijzigd@voorbeeld.nl',
        ]);

        // En zie ik een melding dat het ticket is gewijzigd
        // En staan de nieuwe gegevens in het ticketoverzicht
        $followResponse = $this->actingAs($user)->get('/tickets');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Het ticket is gewijzigd.');
        $followResponse->assertSee('VIP Toegang 2026');
        $followResponse->assertSee('Gewijzigde Bezoeker Naam');
    }

    /**
     * Unhappy scenario: Ticket wijzigen terwijl de database niet beschikbaar is
     * Gegeven ik ben ingelogd
     * Wanneer ik een ticket probeer te wijzigen en op opslaan druk
     * En de database is niet beschikbaar
     * Dan krijg ik een foutmelding: “Database is momenteel niet beschikbaar, uw ticket kon niet worden gewijzigd. Probeer het later opnieuw.”
     * En worden de wijzigingen niet opgeslagen
     * En blijft het ticket in het ticketoverzicht staan met de oude gegevens
     */
    public function test_unhappy_scenario_edit_fails_when_database_unavailable(): void
    {
        $this->seed(SneakernessSeeder::class);
        $this->seed(TicketSeeder::class);

        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $ticket = Ticket::first();
        $oldTicketType = $ticket->TicketType;

        $updateData = [
            'ticket_code' => $ticket->ticket_code_display,
            'bestelnummer' => $ticket->bestelnummer_display,
            'bezoeker_naam' => 'Nieuwe Mislukte Naam',
            'bezoeker_email' => 'mislukt@voorbeeld.nl',
            'ticket_type' => 'Nieuw Niet Opgeslagen Type',
            'status' => 'Geldig',
        ];

        // Wanneer ik een ticket probeer te wijzigen en de database is niet beschikbaar
        $response = $this->actingAs($user)->put("/tickets/{$ticket->Id}?db_error=1", $updateData);

        // Dan krijg ik een foutmelding
        $response->assertRedirect("/tickets/{$ticket->Id}/edit");
        $response->assertSessionHas('error', 'Database is momenteel niet beschikbaar, uw ticket kon niet worden gewijzigd. Probeer het later opnieuw.');

        // En worden de wijzigingen niet opgeslagen
        $this->assertDatabaseMissing('Ticket', [
            'Id' => $ticket->Id,
            'TicketType' => 'Nieuw Niet Opgeslagen Type',
        ]);

        $this->assertDatabaseMissing('Bezoeker', [
            'Naam' => 'Nieuwe Mislukte Naam',
        ]);

        // En blijft het ticket in het ticketoverzicht staan met de oude gegevens
        $followResponse = $this->actingAs($user)->get('/tickets');
        $followResponse->assertStatus(200);
        $followResponse->assertSee($oldTicketType);
        $followResponse->assertDontSee('Nieuw Niet Opgeslagen Type');
    }
}
