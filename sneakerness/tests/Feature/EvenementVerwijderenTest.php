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
