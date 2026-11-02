<?php

namespace Tests\Feature;

use App\Models\Contactpersoon;
use App\Models\Stand;
use App\Models\User;
use App\Models\Verkoper;
use App\Models\VerkoperModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerkoperWijzigenTest extends TestCase
{
    use RefreshDatabase;

    private function getOrganisator(): User
    {
        $organisator = User::where('email', 'organisator@sneakerness.com')->first();
        if (! $organisator) {
            $organisator = User::factory()->create([
                'role' => 'organisator',
                'email' => 'organisator@sneakerness.com',
            ]);
        }

        return $organisator;
    }

    private function createTestVerkoper(): Verkoper
    {
        $verkoper = Verkoper::create([
            'Naam' => 'Kicks Rotterdam',
            'SpecialeStatus' => 1,
            'VerkooptSoort' => 'Sneakers',
            'StandType' => 'AA+',
            'Dagen' => 'Weekend',
            'IsActief' => 1,
            'Opmerking' => 'Actief',
        ]);

        $contact = Contactpersoon::create([
            'Naam' => 'Mark de Vries',
            'Telefoonnummer' => '06-12345678',
            'Emailadres' => 'mark@kicksrdam.nl',
            'IsActief' => 1,
            'Opmerking' => 'Eigenaar Kicks Rotterdam',
        ]);

        $verkoper->contactpersonen()->attach($contact->Id, [
            'IsActief' => 1,
            'Opmerking' => 'Primaire contactpersoon',
        ]);

        Stand::create([
            'VerkoperId' => $verkoper->Id,
            'StandType' => 'AA+',
            'Prijs' => 450.00,
            'VerhuurdStatus' => 1,
            'IsActief' => 1,
            'Opmerking' => 'Stand #AA-101 • Hal 1 • 230V stroompunt vereist',
        ]);

        return $verkoper->fresh(['stands', 'contactpersonen']);
    }

    /**
     * Test Happy Scenario: Organisator opent het wijzigscherm van een bestaande verkoper,
     * past de gegevens aan en klikt op "Wijzigingen opslaan".
     * De nieuwe gegevens worden in de database opgeslagen en er verschijnt een succesmelding.
     */
    public function test_happy_scenario_organisator_can_update_verkoper_successfully(): void
    {
        $organisator = $this->getOrganisator();
        $verkoper = $this->createTestVerkoper();

        // 1. Wijzigscherm openen
        $response = $this->actingAs($organisator)->get("/verkopers/{$verkoper->Id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Verkoper Wijzigen');
        $response->assertSee('Kicks Rotterdam');
        $response->assertSee('Mark de Vries');
        $response->assertSee('mark@kicksrdam.nl');
        $response->assertSee('Wijzigingen opslaan');

        // 2. Gegevens aanpassen en formulier versturen
        $updateData = [
            'naam' => 'Kicks Rotterdam Flagship',
            'verkoopt_soort' => 'Sneakers',
            'contactpersoon_naam' => 'Mark de Vries Jr.',
            'contactpersoon_email' => 'mark.jr@kicksrdam.nl',
            'contactpersoon_telefoon' => '06-87654321',
            'stand_type' => 'AA+',
            'stand_nummer' => 'Stand #AA-102 • Hal 1',
            'dagen' => 'Weekend',
            'status' => 'Actief',
            'opmerking' => 'Extra stroompunt en 4 kledingrekken',
            'stuur_bevestiging' => '1',
        ];

        $putResponse = $this->actingAs($organisator)->put("/verkopers/{$verkoper->Id}", $updateData);

        // 3. Controleren op redirect naar overzicht met succes flash message
        $putResponse->assertRedirect('/verkopers');
        $putResponse->assertSessionHas('success');

        // 4. Verifiëren dat de gewijzigde gegevens in de database staan
        $this->assertDatabaseHas('Verkoper', [
            'Id' => $verkoper->Id,
            'Naam' => 'Kicks Rotterdam Flagship',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $this->assertDatabaseHas('Contactpersoon', [
            'Naam' => 'Mark de Vries Jr.',
            'Emailadres' => 'mark.jr@kicksrdam.nl',
            'Telefoonnummer' => '06-87654321',
        ]);

        // 5. Overzichtspagina bezoeken en succesmelding verifiëren
        $followResponse = $this->actingAs($organisator)->get('/verkopers');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Kicks Rotterdam Flagship');
        $followResponse->assertSee('is succesvol bijgewerkt');
    }

    /**
     * Test Unhappy Scenario: Organisator laat verplichte velden leeg bij bewerken.
     * De wijzigingen worden NIET opgeslagen en er verschijnen foutmeldingen.
     */
    public function test_unhappy_scenario_validation_errors_when_required_field_empty(): void
    {
        $organisator = $this->getOrganisator();
        $verkoper = $this->createTestVerkoper();

        // Formulier versturen met lege verplichte velden
        $putResponse = $this->actingAs($organisator)->put("/verkopers/{$verkoper->Id}", [
            'naam' => '', // Verplicht veld leeg
            'verkoopt_soort' => '',
            'contactpersoon_naam' => '',
            'contactpersoon_email' => 'ongeldig-email',
            'contactpersoon_telefoon' => '',
            'stand_type' => '',
            'stand_nummer' => '',
            'dagen' => '',
            'status' => '',
        ]);

        // Controleren dat gebruiker foutmeldingen ontvangt
        $putResponse->assertSessionHasErrors([
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

        // Verifiëren dat de originele gegevens NIET zijn overschreven
        $this->assertDatabaseHas('Verkoper', [
            'Id' => $verkoper->Id,
            'Naam' => 'Kicks Rotterdam',
        ]);
        $this->assertDatabaseMissing('Verkoper', [
            'Naam' => '',
        ]);
    }

    /**
     * Test dat gastgebruikers geen toegang hebben tot het wijzigscherm of update actie.
     */
    public function test_guests_cannot_access_edit_or_update_verkoper(): void
    {
        $verkoper = $this->createTestVerkoper();

        $response = $this->get("/verkopers/{$verkoper->Id}/edit");
        $response->assertRedirect('/login');

        $putResponse = $this->put("/verkopers/{$verkoper->Id}", [
            'naam' => 'Hack Attempt',
        ]);
        $putResponse->assertRedirect('/login');
    }

    /**
     * Test VerkoperModel klasse en updateVerkoper methode direct.
     */
    public function test_verkoper_model_and_update_verkoper_method(): void
    {
        $verkoper = $this->createTestVerkoper();

        $modelInstance = VerkoperModel::findOrFail($verkoper->Id);
        $this->assertInstanceOf(VerkoperModel::class, $modelInstance);

        $result = $modelInstance->updateVerkoper([
            'naam' => 'Direct Model Update',
            'verkoopt_soort' => 'Partners',
            'contactpersoon_naam' => 'Direct Contact',
            'contactpersoon_email' => 'direct@test.nl',
            'contactpersoon_telefoon' => '06-00000000',
            'stand_type' => 'AA',
            'stand_nummer' => 'Stand #AA-999 • Hal 2',
            'dagen' => 'Weekend',
            'status' => 'Actief',
            'opmerking' => 'Test faciliteit',
        ]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('Verkoper', [
            'Id' => $verkoper->Id,
            'Naam' => 'Direct Model Update',
            'SpecialeStatus' => 1,
        ]);
    }
}
