<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EvenementVerwijderenTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisator_can_delete_an_event(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);
        $eventId = $this->createEvent();

        $overview = $this->actingAs($user)->get(route('events.index'));
        $overview->assertSee('data-delete-url="'.route('events.destroy', $eventId).'"', false);
        $overview->assertSee('Event verwijderen');
        $overview->assertSee('gekoppelde tickets worden ook verwijderd.');
        $overview->assertSee('Deze actie kan niet ongedaan worden gemaakt.');

        $response = $this->delete(route('events.destroy', $eventId));

        $response->assertRedirect(route('events.index'));
        $response->assertSessionHas('success', 'Het event is verwijderd.');
        $this->assertDatabaseMissing('Evenement', ['Id' => $eventId]);

        $overview = $this->get(route('events.index'));
        $overview->assertDontSee('Sneakerness Rotterdam 2026');
    }

    public function test_database_error_during_event_deletion_keeps_the_event_and_shows_message(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);
        $eventId = $this->createEvent();

        $response = $this->actingAs($user)->delete(route('events.destroy', [
            'event' => $eventId,
            'db_error' => 1,
        ]));

        $response->assertRedirect(route('events.index'));
        $response->assertSessionHas('databaseError', true);
        $this->assertDatabaseHas('Evenement', [
            'Id' => $eventId,
            'Naam' => 'Sneakerness Rotterdam 2026',
        ]);

        $overview = $this->get(route('events.index'));
        $overview->assertOk();
        $overview->assertSee('Er is een probleem met de database. Probeer het later opnieuw.');
    }

    public function test_non_organisator_cannot_delete_an_event(): void
    {
        $user = User::factory()->create(['role' => 'bezoeker']);
        $eventId = $this->createEvent();

        $this->actingAs($user)->delete(route('events.destroy', $eventId))->assertForbidden();
        $this->assertDatabaseHas('Evenement', ['Id' => $eventId]);
    }

    private function createEvent(): int
    {
        $organisatorId = DB::table('Organisator')->insertGetId([
            'Naam' => 'Test Organisator',
            'Gebruikersnaam' => 'test-organisator',
            'Wachtwoord' => 'test',
            'IsActief' => true,
        ]);

        return DB::table('Evenement')->insertGetId([
            'OrganisatorId' => $organisatorId,
            'Naam' => 'Sneakerness Rotterdam 2026',
            'Datum' => '2026-10-24',
            'Locatie' => 'Van Nelle Fabriek',
            'AantalTicketsPerTijdslot' => 100,
            'BeschikbareStands' => 10,
            'IsActief' => true,
        ]);
    }
}
