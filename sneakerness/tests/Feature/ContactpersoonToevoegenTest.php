<?php

namespace Tests\Feature;

use App\Models\Contactpersoon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactpersoonToevoegenTest extends TestCase
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
     * vult geldige gegevens in (voornaam, achternaam, e-mailadres, telefoonnummer) en klikt op Opslaan.
     * De nieuwe contactpersoon wordt opgeslagen in de database, er verschijnt een succesmelding
     * "Contactpersoon succesvol toegevoegd" en de gebruiker wordt teruggestuurd naar het overzicht.
     */
    public function test_happy_scenario_organisator_can_add_new_contactpersoon_successfully(): void
    {
        $organisator = $this->getOrganisator();

        // 1. Gegeven: Ik ben ingelogd op de pagina "Contactpersoon toevoegen"
        $response = $this->actingAs($organisator)->get('/contactpersonen/create');
        $response->assertStatus(200);
        $response->assertSee('Contactpersoon Toevoegen');
        $response->assertSee('Opslaan');

        // 2. Wanneer: Ik geldige gegevens invul en op "Opslaan" klik
        $formData = [
            'voornaam'       => 'Dennis',
            'achternaam'     => 'de Ridder',
            'emailadres'     => 'dennis.deridder@sneakerness.com',
            'telefoonnummer' => '06-98765432',
            'opmerking'      => 'Standmanager',
        ];

        $postResponse = $this->actingAs($organisator)->post('/contactpersonen', $formData);

        // 3. Dan: Wordt ik teruggestuurd naar het overzicht met succesmelding
        $postResponse->assertRedirect('/contactpersonen');
        $postResponse->assertSessionHas('success', 'Contactpersoon succesvol toegevoegd');

        // 4. En: Wordt de nieuwe contactpersoon opgeslagen in de database
        $this->assertDatabaseHas('Contactpersoon', [
            'Naam'           => 'Dennis de Ridder',
            'Emailadres'     => 'dennis.deridder@sneakerness.com',
            'Telefoonnummer' => '06-98765432',
            'Opmerking'      => 'Standmanager',
            'IsActief'       => 1,
        ]);

        // 5. En: Zie ik een succesmelding "Contactpersoon succesvol toegevoegd" op de overzichtspagina
        $followResponse = $this->actingAs($organisator)->get('/contactpersonen');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Contactpersoon succesvol toegevoegd');
        $followResponse->assertSee('Dennis de Ridder');
    }

    /**
     * Test Unhappy Scenario: Organisator laat verplichte velden leeg
     * of vult een ongeldig e-mailadres in en klikt op "Opslaan".
     * De contactpersoon wordt NIET opgeslagen in de database
     * en er worden foutmeldingen getoond bij de betreffende velden met de reden van afkeuring.
     */
    public function test_unhappy_scenario_validation_errors_when_fields_invalid_or_empty(): void
    {
        $organisator = $this->getOrganisator();

        // Formulier versturen met lege verplichte velden en ongeldig e-mailadres
        $postResponse = $this->actingAs($organisator)->post('/contactpersonen', [
            'voornaam'       => '',
            'achternaam'     => '',
            'emailadres'     => 'ongeldig-email-formaat',
            'telefoonnummer' => '',
        ]);

        // Controleren dat gebruiker wordt teruggestuurd met validatiefouten
        $postResponse->assertSessionHasErrors([
            'voornaam',
            'achternaam',
            'emailadres',
            'telefoonnummer',
        ]);

        // Controleren dat de contactpersoon NIET is opgeslagen in de database
        $this->assertDatabaseMissing('Contactpersoon', [
            'Emailadres' => 'ongeldig-email-formaat',
        ]);
    }

    /**
     * Test dat gastgebruikers (niet-ingelogd) geen toegang hebben tot het toevoegformulier.
     */
    public function test_guests_cannot_access_create_form_or_store_contactpersoon(): void
    {
        $response = $this->get('/contactpersonen/create');
        $response->assertRedirect('/login');

        $postResponse = $this->post('/contactpersonen', [
            'voornaam'   => 'Dennis',
            'achternaam' => 'de Ridder',
        ]);
        $postResponse->assertRedirect('/login');
    }
}
