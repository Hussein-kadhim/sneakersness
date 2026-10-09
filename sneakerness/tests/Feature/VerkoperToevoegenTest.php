<?php

namespace Tests\Feature;

use App\Models\Contactpersoon;
use App\Models\Stand;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerkoperToevoegenTest extends TestCase
{
    use RefreshDatabase;

    private function getOrganisator(): User
    {
        $organisator = User::where('email', 'organisator@sneakerness.com')->first();
        if (!$organisator) {
            $organisator = User::factory()->create([
                'role' => 'organisator',
                'email' => 'organisator@sneakerness.com',
            ]);
        }
        return $organisator;
    }

    /**
     * Test Happy Scenario: Organisator bezoekt het toevoegscherm,
     * vult alle verplichte velden correct in en slaat op.
     * De verkoper, contactpersoon en stand worden in de database opgeslagen
     * en er verschijnt een succesmelding in het verkopersoverzicht.
     */
    public function test_happy_scenario_organisator_can_add_new_verkoper_successfully(): void
    {
        $organisator = $this->getOrganisator();

        // 1. Scherm openen
        $response = $this->actingAs($organisator)->get('/verkopers/create');
        $response->assertStatus(200);
        $response->assertSee('Verkoper Toevoegen');
        $response->assertSee('Opslaan &amp; Verkoper toevoegen', false);

        // 2. Formulier invullen en versturen
        $formData = [
            'naam' => 'SoleBox Superior Rotterdam',
            'verkoopt_soort' => 'Sneakers',
            'contactpersoon_naam' => 'Dennis de Ridder',
            'contactpersoon_email' => 'dennis@solebox-superior.nl',
            'contactpersoon_telefoon' => '06-98765432',
            'stand_type' => 'AA+',
            'stand_nummer' => 'Stand #AA-201 • Hal 1',
            'dagen' => 'Weekend',
            'status' => 'Actief',
            'opmerking' => '2x 230V stroompunt vereist',
            'stuur_bevestiging' => '1',
        ];

        $postResponse = $this->actingAs($organisator)->post('/verkopers', $formData);

        // 3. Controleren dat er wordt geredirect naar het overzicht met succesmelding
        $postResponse->assertRedirect('/verkopers');
        $postResponse->assertSessionHas('success');

        // 4. Controleren dat verkoper is opgeslagen in de database
        $this->assertDatabaseHas('Verkoper', [
            'Naam' => 'SoleBox Superior Rotterdam',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        // 5. Controleren dat contactpersoon is opgeslagen in de database
        $this->assertDatabaseHas('Contactpersoon', [
            'Naam' => 'Dennis de Ridder',
            'Email' => 'dennis@solebox-superior.nl',
            'Telefoonnummer' => '06-98765432',
        ]);

        // 6. Controleren dat stand is aangemaakt en gekoppeld
        $verkoper = Verkoper::where('Naam', 'SoleBox Superior Rotterdam')->first();
        $this->assertNotNull($verkoper);

        $this->assertDatabaseHas('Stand', [
            'VerkoperId' => $verkoper->Id,
            'StandType' => 'AA+',
            'AantalDagen' => 2,
            'VerhuurdStatus' => 1,
        ]);

        // 7. Overzichtspagina bezoeken en succesmelding verifiëren
        $followResponse = $this->actingAs($organisator)->get('/verkopers');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('SoleBox Superior Rotterdam');
        $followResponse->assertSee('is succesvol toegevoegd');
    }

    /**
     * Test Unhappy Scenario: Organisator laat verplichte velden leeg
     * of voert ongeldige gegevens in. De verkoper wordt NIET opgeslagen
     * en er worden foutmeldingen getoond bij de desbetreffende velden.
     */
    public function test_unhappy_scenario_validation_errors_when_required_fields_empty(): void
    {
        $organisator = $this->getOrganisator();

        // Leeg formulier versturen
        $postResponse = $this->actingAs($organisator)->post('/verkopers', [
            'naam' => '',
            'verkoopt_soort' => '',
            'contactpersoon_naam' => '',
            'contactpersoon_email' => 'ongeldig-email-adres',
            'contactpersoon_telefoon' => '',
            'stand_type' => '',
            'stand_nummer' => '',
            'dagen' => '',
            'status' => '',
        ]);

        // Controleren dat gebruiker wordt teruggestuurd met validatiefouten
        $postResponse->assertSessionHasErrors([
            'naam',
            'verkoopt_soort',
            'contactpersoon_naam',
            'contactpersoon_email',
            'contactpersoon_telefoon',
            'stand_type',
            'stand_nummer',
            'dagen',
            'status',
        ]);

        // Controleren dat er GEEN verkoper met ongeldig email-adres is opgeslagen
        $this->assertDatabaseMissing('Contactpersoon', [
            'Email' => 'ongeldig-email-adres',
        ]);
    }

    /**
     * Test dat gastgebruikers (niet-ingelogd) geen toegang hebben tot het toevoegformulier.
     */
    public function test_guests_cannot_access_create_form_or_store_verkoper(): void
    {
        $response = $this->get('/verkopers/create');
        $response->assertRedirect('/login');

        $postResponse = $this->post('/verkopers', [
            'naam' => 'Test Verkoper',
        ]);
        $postResponse->assertRedirect('/login');
    }
}
