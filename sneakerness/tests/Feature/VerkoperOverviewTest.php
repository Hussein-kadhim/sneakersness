<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerkoperOverviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Happy Scenario: overzicht toont verkopers, stat cards en niet-werkende actieknoppen.
     */
    public function test_happy_scenario_organisator_sees_verkopers_list_and_stats(): void
    {
        $this->seed(\Database\Seeders\SneakernessSeeder::class);

        $organisator = User::where('email', 'organisator@sneakerness.com')->first();
        if (!$organisator) {
            $organisator = User::factory()->create([
                'role' => 'organisator',
                'email' => 'organisator@sneakerness.com',
            ]);
        }

        $response = $this->actingAs($organisator)->get('/verkopers');

        $response->assertStatus(200);
        $response->assertSee('Verkopers Overzicht');
        $response->assertSee('Kicks Rotterdam');
        $response->assertSee('SoleMate Amsterdam');
        $response->assertSee('Streetwear RTM');
        $response->assertSee('Sneaker Art Studio');
        $response->assertSee('Vintage Kicks');
        $response->assertSee('Barber & Cuts Lounge');

        // Contactgegevens en standtypes
        $response->assertSee('Mark de Vries');
        $response->assertSee('Sophie Jansen');
        $response->assertSee('Bilal El Amrani');
        $response->assertSee('Emma Bakker');
        $response->assertSee('Daan van Leeuwen');
        $response->assertSee('Jesse van Dijk');

        // Stat cards
        $response->assertSee('48');
        $response->assertSee('18× AA+');
        $response->assertSee('8 Partners');

        // Niet-werkende knoppen conform verzoek
        $response->assertSee('onclick="return false;"', false);
    }

    /**
     * Test Unhappy Scenario: wanneer er geen verkopers zijn (of via empty=1),
     * toont het overzicht "Geen verkopers gevonden" en de toevoegoptie.
     */
    public function test_unhappy_scenario_shows_empty_message_and_add_button(): void
    {
        $response = $this->get('/verkopers?empty=1');

        $response->assertStatus(200);
        $response->assertSee('Geen verkopers gevonden');
        $response->assertSee('Verkoper toevoegen');
        $response->assertSee('onclick="return false;"', false);
    }

    /**
     * Test Unhappy Scenario via zoekfilter die niets oplevert.
     */
    public function test_unhappy_scenario_shows_empty_on_unknown_search(): void
    {
        $response = $this->get('/verkopers?q=onbestaandebedrijfsnaamxyz123');

        $response->assertStatus(200);
        $response->assertSee('Geen verkopers gevonden');
        $response->assertSee('Verkoper toevoegen');
    }

    /**
     * Test Unhappy Scenario: wanneer database offline/fout is,
     * toont het overzicht een rode melding en foutboodschap.
     */
    public function test_unhappy_scenario_shows_red_database_error_when_database_is_offline(): void
    {
        $response = $this->get('/verkopers?db_error=1');

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
        $response->assertSee('de verkopers konden niet worden geladen');
        $response->assertSee('sn-db-alert');
    }

    /**
     * Test Inloggen pagina retourneert 200 en Sneakerness stijl.
     */
    public function test_login_page_renders_with_sneakerness_branding(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Inloggen bij Sneakerness');
        $response->assertSee('E-mailadres');
        $response->assertSee('Wachtwoord');
    }

    /**
     * Test Registreren pagina retourneert 200 en Sneakerness stijl.
     */
    public function test_register_page_renders_with_sneakerness_branding(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Account aanmaken');
        $response->assertSee('Selecteer je rol');
        $response->assertSee('Bezoeker');
        $response->assertSee('Verkoper');
        $response->assertSee('Organisator');
    }
}
