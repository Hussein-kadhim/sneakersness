<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\SneakernessSeeder;
use Database\Seeders\TicketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAnnulerenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Happy scenario: Ticket succesvol annuleren
     * Gegeven ik ben ingelogd en sta in het ticketoverzicht
     * Wanneer ik een ticket selecteer en op annuleren druk
     * Dan wordt de status gewijzigd naar 'Geannuleerd' en IsActief naar false
     * En zie ik een bevestigingsmelding
     * En staat het ticket als geannuleerd in het overzicht
     */
    public function test_happy_scenario_user_can_cancel_ticket(): void
    {
        $this->seed(SneakernessSeeder::class);
        $this->seed(TicketSeeder::class);

        $user = User::factory()->create([
            'name' => 'Organisator User',
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $ticket = Ticket::first();
        $ticketCode = $ticket->ticket_code_display;

        // Controleer dat ticket actief is voor annulering
        $this->assertDatabaseHas('Ticket', ['Id' => $ticket->Id, 'IsActief' => true]);

        // Annuleer het ticket via POST request
        $response = $this->actingAs($user)->post("/tickets/{$ticket->Id}/annuleren");

        $response->assertRedirect('/tickets');
        $response->assertSessionHas('success', 'Het ticket is succesvol geannuleerd.');

        // Controleer dat het ticket geannuleerd is in de database
        $this->assertDatabaseHas('Ticket', [
            'Id' => $ticket->Id,
            'Status' => 'Geannuleerd',
            'IsActief' => false,
        ]);

        // Controleer dat overzichtspagina de melding toont
        $followResponse = $this->actingAs($user)->get('/tickets');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Het ticket is succesvol geannuleerd.');
    }

    /**
     * Unhappy scenario: Ticket annuleren terwijl database niet beschikbaar is
     * Gegeven ik ben ingelogd
     * Wanneer ik een ticket probeer te annuleren en de database is niet beschikbaar
     * Dan krijg ik een foutmelding
     * En wordt het ticket niet geannuleerd
     */
    public function test_unhappy_scenario_cancel_fails_when_database_unavailable(): void
    {
        $this->seed(SneakernessSeeder::class);
        $this->seed(TicketSeeder::class);

        $user = User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);

        $ticket = Ticket::first();
        $originalStatus = $ticket->Status;

        // Simuleer database storing tijdens annuleren
        $response = $this->actingAs($user)->post("/tickets/{$ticket->Id}/annuleren?db_error=1");

        $response->assertRedirect('/tickets');
        $response->assertSessionHas('error', 'Database is momenteel niet beschikbaar, het ticket kon niet worden geannuleerd. Probeer het later opnieuw.');

        // Ticket is niet geannuleerd - status en IsActief zijn ongewijzigd
        $this->assertDatabaseHas('Ticket', [
            'Id' => $ticket->Id,
            'Status' => $originalStatus,
            'IsActief' => true,
        ]);

        $followResponse = $this->actingAs($user)->get('/tickets');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Database is momenteel niet beschikbaar, het ticket kon niet worden geannuleerd. Probeer het later opnieuw.');
    }
}

