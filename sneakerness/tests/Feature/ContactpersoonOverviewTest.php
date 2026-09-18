<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactpersoonOverviewTest extends TestCase
{
    use RefreshDatabase;
    public function test_happy_scenario_organisator_sees_contactpersonen_list(): void
    {
        $this->seed(\Database\Seeders\SneakernessSeeder::class);

        $organisator = User::where('email', 'organisator@sneakerness.com')->first();
        if (!$organisator) {
            $organisator = User::factory()->create([
                'role' => 'organisator',
                'email' => 'organisator@sneakerness.com',
            ]);
        }

        $response = $this->actingAs($organisator)->get('/contactpersonen');

        $response->assertStatus(200);
        $response->assertSee('Contactpersonen Overzicht');

        $response->assertSee('Mark de Vries');
        $response->assertSee('Sophie Jansen');
        $response->assertSee('Bilal El Amrani');
        $response->assertSee('Emma Bakker');
        $response->assertSee('Jesse van Dijk');
        $response->assertSee('Daan van Leeuwen');

        $response->assertSee('Kicks Rotterdam');
        $response->assertSee('SoleMate Amsterdam');
        $response->assertSee('Geen verkoper gekoppeld');

        $response->assertSee('mark@kicksrdam.nl');
        $response->assertSee('06-12345678');

        $response->assertSee('54');
        $response->assertSee('48');
        $response->assertSee('Contactpersoon toevoegen');
    }

    public function test_unhappy_scenario_shows_empty_message_and_add_button(): void
    {
        $organisator = User::where('email', 'organisator@sneakerness.com')->first();
        if (!$organisator) {
            $organisator = User::factory()->create([
                'role' => 'organisator',
                'email' => 'organisator@sneakerness.com',
            ]);
        }

        $response = $this->actingAs($organisator)->get('/contactpersonen?empty=1');

        $response->assertStatus(200);
        $response->assertSee('Geen contactpersonen gevonden');
        $response->assertSee('Contactpersoon toevoegen');
    }

    public function test_unhappy_scenario_shows_empty_on_unknown_search(): void
    {
        $organisator = User::where('email', 'organisator@sneakerness.com')->first();
        if (!$organisator) {
            $organisator = User::factory()->create([
                'role' => 'organisator',
                'email' => 'organisator@sneakerness.com',
            ]);
        }

        $response = $this->actingAs($organisator)->get('/contactpersonen?q=onbestaandenaamxyz123');

        $response->assertStatus(200);
        $response->assertSee('Geen contactpersonen gevonden');
        $response->assertSee('Contactpersoon toevoegen');
    }

    public function test_unhappy_scenario_shows_red_database_error_when_database_is_offline(): void
    {
        $organisator = User::where('email', 'organisator@sneakerness.com')->first();
        if (!$organisator) {
            $organisator = User::factory()->create([
                'role' => 'organisator',
                'email' => 'organisator@sneakerness.com',
            ]);
        }

        $response = $this->actingAs($organisator)->get('/contactpersonen?db_error=1');

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
        $response->assertSee('de contactpersonen konden niet worden geladen');
        $response->assertSee('sn-db-alert');
    }

    public function test_guests_cannot_access_contactpersonen_and_are_redirected_to_login(): void
    {
        $response = $this->get('/contactpersonen');
        $response->assertRedirect('/login');
    }
}
