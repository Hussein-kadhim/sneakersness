<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EvenementOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisator_sees_event_name_date_location_and_status(): void
    {
        $organisator = User::factory()->create(['role' => 'organisator']);

        $organisatorId = DB::table('Organisator')->insertGetId([
            'Naam' => 'Test Organisator',
            'Gebruikersnaam' => 'test-organisator',
            'Wachtwoord' => 'test',
            'IsActief' => true,
        ]);

        DB::table('Evenement')->insert([
            'OrganisatorId' => $organisatorId,
            'Naam' => 'Sneakerness Rotterdam 2026',
            'Datum' => '2026-10-24',
            'Locatie' => 'Van Nelle Fabriek Rotterdam',
            'AantalTicketsPerTijdslot' => 100,
            'BeschikbareStands' => 10,
            'IsActief' => true,
        ]);

        $response = $this->actingAs($organisator)->get(route('events.index'));

        $response->assertOk();
        $response->assertSee('Events Overzicht');
        $response->assertSee('Sneakerness Rotterdam 2026');
        $response->assertSee('24-10-2026');
        $response->assertSee('Van Nelle Fabriek Rotterdam');
        $response->assertSee('Actief');
    }

    public function test_search_can_filter_events_by_location(): void
    {
        $organisator = User::factory()->create(['role' => 'organisator']);

        $organisatorId = DB::table('Organisator')->insertGetId([
            'Naam' => 'Test Organisator',
            'Gebruikersnaam' => 'test-organisator',
            'Wachtwoord' => 'test',
            'IsActief' => true,
        ]);

        DB::table('Evenement')->insert([
            ['OrganisatorId' => $organisatorId, 'Naam' => 'Rotterdam Event', 'Datum' => '2026-10-24', 'Locatie' => 'Van Nelle Fabriek', 'AantalTicketsPerTijdslot' => 100, 'BeschikbareStands' => 10, 'IsActief' => true],
            ['OrganisatorId' => $organisatorId, 'Naam' => 'Amsterdam Event', 'Datum' => '2026-11-14', 'Locatie' => 'RAI Amsterdam', 'AantalTicketsPerTijdslot' => 100, 'BeschikbareStands' => 10, 'IsActief' => true],
        ]);

        $response = $this->actingAs($organisator)->get(route('events.index', ['q' => 'RAI']));

        $response->assertOk();
        $response->assertSee('Amsterdam Event');
        $response->assertDontSee('Rotterdam Event');
    }

    public function test_organisator_can_open_event_creation_form(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);

        DB::table('Organisator')->insert([
            'Naam' => 'Test Organisator',
            'Gebruikersnaam' => 'test-organisator',
            'Wachtwoord' => 'test',
            'IsActief' => true,
        ]);

        $response = $this->actingAs($user)->get(route('events.create'));

        $response->assertOk();
        $response->assertSee('Event toevoegen');
        $response->assertSee('Test Organisator');
    }

    public function test_organisator_can_create_an_event(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);
        $organisatorId = DB::table('Organisator')->insertGetId([
            'Naam' => 'Test Organisator',
            'Gebruikersnaam' => 'test-organisator',
            'Wachtwoord' => 'test',
            'IsActief' => true,
        ]);

        $response = $this->actingAs($user)->post(route('events.store'), [
            'OrganisatorId' => $organisatorId,
            'Naam' => 'Nieuw Sneakerness Event',
            'Datum' => '2027-10-24',
            'Locatie' => 'Van Nelle Fabriek',
            'AantalTicketsPerTijdslot' => 750,
            'BeschikbareStands' => 55,
            'IsActief' => '1',
            'Opmerking' => 'Test omschrijving',
        ]);

        $response->assertRedirect(route('events.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('Evenement', [
            'OrganisatorId' => $organisatorId,
            'Naam' => 'Nieuw Sneakerness Event',
            'Datum' => '2027-10-24 00:00:00',
            'Locatie' => 'Van Nelle Fabriek',
            'AantalTicketsPerTijdslot' => 750,
            'BeschikbareStands' => 55,
            'IsActief' => 1,
            'Opmerking' => 'Test omschrijving',
        ]);
    }

    public function test_organisator_can_open_event_edit_form_with_existing_values(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);
        $eventId = $this->createEvent();

        $response = $this->actingAs($user)->get(route('events.edit', $eventId));

        $response->assertOk();
        $response->assertSee('Event wijzigen');
        $response->assertSee('Sneakerness Rotterdam 2026');
        $response->assertSee('Van Nelle Fabriek');
        $response->assertSee('value="Sneakerness Rotterdam 2026"', false);
        $response->assertSee('action="'.route('events.update', $eventId).'"', false);
        $response->assertSee('Wijzigingen opslaan');
    }

    public function test_organisator_can_update_an_event(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);
        $eventId = $this->createEvent();

        $response = $this->actingAs($user)->put(route('events.update', $eventId), [
            'OrganisatorId' => DB::table('Organisator')->value('Id'),
            'Naam' => 'Aangepast Sneakerness Event',
            'Datum' => '2027-10-25',
            'Locatie' => 'Nieuwe locatie',
            'AantalTicketsPerTijdslot' => 850,
            'BeschikbareStands' => 60,
            'IsActief' => '0',
            'Opmerking' => 'Bijgewerkte omschrijving',
        ]);

        $response->assertRedirect(route('events.index'));
        $response->assertSessionHas('success', 'Het event is gewijzigd.');
        $this->assertDatabaseHas('Evenement', [
            'Id' => $eventId,
            'Naam' => 'Aangepast Sneakerness Event',
            'Datum' => '2027-10-25 00:00:00',
            'Locatie' => 'Nieuwe locatie',
            'AantalTicketsPerTijdslot' => 850,
            'BeschikbareStands' => 60,
            'IsActief' => 0,
            'Opmerking' => 'Bijgewerkte omschrijving',
        ]);
    }

    public function test_database_error_during_event_update_keeps_existing_values_and_shows_message(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);
        $eventId = $this->createEvent();

        $response = $this->actingAs($user)->put(route('events.update', ['event' => $eventId, 'db_error' => 1]), [
            'OrganisatorId' => DB::table('Organisator')->value('Id'),
            'Naam' => 'Wijziging mag niet worden opgeslagen',
            'Datum' => '2027-10-25',
            'Locatie' => 'Nieuwe locatie',
            'AantalTicketsPerTijdslot' => 850,
            'BeschikbareStands' => 60,
            'IsActief' => '0',
            'Opmerking' => 'Bijgewerkte omschrijving',
        ]);

        $response->assertRedirect(route('events.edit', $eventId));
        $response->assertSessionHas('databaseError', true);
        $this->assertDatabaseHas('Evenement', [
            'Id' => $eventId,
            'Naam' => 'Sneakerness Rotterdam 2026',
            'Locatie' => 'Van Nelle Fabriek',
        ]);

        $editResponse = $this->get(route('events.edit', $eventId));
        $editResponse->assertOk();
        $editResponse->assertSee('Er is een probleem met de database. Probeer het later opnieuw.');
    }

    public function test_non_organisator_cannot_edit_or_update_an_event(): void
    {
        $user = User::factory()->create(['role' => 'bezoeker']);
        $eventId = $this->createEvent();

        $this->actingAs($user)->get(route('events.edit', $eventId))->assertForbidden();
        $this->actingAs($user)->put(route('events.update', $eventId), [])->assertForbidden();
    }

    public function test_non_organisator_cannot_create_an_event(): void
    {
        $user = User::factory()->create(['role' => 'bezoeker']);

        $response = $this->actingAs($user)->get(route('events.create'));

        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('events.store'), []);

        $response->assertForbidden();
    }

    public function test_event_creation_validates_required_fields(): void
    {
        $user = User::factory()->create(['role' => 'organisator']);

        $response = $this->actingAs($user)->post(route('events.store'), []);

        $response->assertSessionHasErrors([
            'OrganisatorId',
            'Naam',
            'Datum',
            'Locatie',
            'AantalTicketsPerTijdslot',
            'BeschikbareStands',
            'IsActief',
        ]);
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
            'Opmerking' => 'Sneakerness evenement',
        ]);
    }
}
