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
}