<?php

namespace Tests\Feature;

use App\Models\Contactpersoon;
use App\Models\ContactpersoonModel;
use App\Models\ContactPerVerkoper;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactpersoonWijzigenTest extends TestCase
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

    /**
     * Happy scenario: Contactpersoon succesvol bijwerken
     *
     * Gegeven ik ben ingelogd en open het bewerkscherm van een bestaande contactpersoon
     * Wanneer ik een veld (zoals het telefoonnummer) aanpas met geldige invoer en op "Opslaan" klik
     * Dan worden de gewijzigde gegevens opgeslagen in de database
     * En zie ik de melding "Gegevens succesvol bijgewerkt"
     */
    public function test_happy_scenario_organisator_can_edit_contactpersoon_successfully(): void
    {
        $organisator = $this->getOrganisator();

        $verkoper = Verkoper::create([
            'Naam' => 'Kicks Rotterdam',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Mark de Vries',
            'Emailadres' => 'mark@oudeadres.nl',
            'Telefoonnummer' => '06-11112222',
            'Opmerking' => 'Standmanager',
            'IsActief' => 1,
        ]);

        // 1. Gegeven: Ik ben ingelogd en open het bewerkscherm van een bestaande contactpersoon
        $response = $this->actingAs($organisator)->get(route('contactpersonen.edit', $contactpersoon->Id));
        $response->assertStatus(200);
        $response->assertSee('Contactpersoon wijzigen');
        $response->assertSee('Mark');
        $response->assertSee('de Vries');
        $response->assertSee('Opslaan');

        // 2. Wanneer: Ik een veld (zoals het telefoonnummer) aanpas met geldige invoer en op "Opslaan" klik
        $updatedData = [
            'voornaam' => 'Mark',
            'achternaam' => 'de Vries',
            'emailadres' => 'mark@kicksrdam.nl',
            'telefoonnummer' => '06-12345678',
            'functie' => 'Standmanager',
            'verkoper_id' => $verkoper->Id,
            'is_primair' => '1',
            'status' => 'Actief',
        ];

        $putResponse = $this->actingAs($organisator)->put(
            route('contactpersonen.update', $contactpersoon->Id),
            $updatedData
        );

        // 3. Dan: Worden de gewijzigde gegevens opgeslagen in de database
        $putResponse->assertRedirect(route('contactpersonen.index'));
        $putResponse->assertSessionHas('success', 'Gegevens succesvol bijgewerkt');

        $this->assertDatabaseHas('Contactpersoon', [
            'Id' => $contactpersoon->Id,
            'Naam' => 'Mark de Vries',
            'Emailadres' => 'mark@kicksrdam.nl',
            'Telefoonnummer' => '06-12345678',
            'Opmerking' => 'Standmanager',
            'IsActief' => 1,
        ]);

        // Controleer koppeling aan verkoper
        $this->assertDatabaseHas('ContactPerVerkoper', [
            'ContactpersoonId' => $contactpersoon->Id,
            'VerkoperId' => $verkoper->Id,
        ]);

        // 4. En: Zie ik de melding "Gegevens succesvol bijgewerkt" op de pagina
        $followResponse = $this->actingAs($organisator)->get(route('contactpersonen.index'));
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Gegevens succesvol bijgewerkt');
        $followResponse->assertSee('06-12345678');
    }

    /**
     * Unhappy scenario: Verplicht veld leeglaten bij bewerken
     *
     * Gegeven ik ben ingelogd op het bewerkscherm van een contactpersoon
     * Wanneer ik een verplicht veld (zoals de achternaam) leegmaak en op "Opslaan" klik
     * Dan worden de wijzigingen niet doorgevoerd
     * En zie ik de foutmelding "Dit veld is verplicht"
     */
    public function test_unhappy_scenario_validation_error_when_required_field_empty(): void
    {
        $organisator = $this->getOrganisator();

        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Mark de Vries',
            'Emailadres' => 'mark@kicksrdam.nl',
            'Telefoonnummer' => '06-12345678',
            'Opmerking' => 'Standmanager',
            'IsActief' => 1,
        ]);

        // 1. Gegeven: Ik ben ingelogd op het bewerkscherm van een contactpersoon
        $response = $this->actingAs($organisator)->get(route('contactpersonen.edit', $contactpersoon->Id));
        $response->assertStatus(200);

        // 2. Wanneer: Ik een verplicht veld (zoals de achternaam) leegmaak en op "Opslaan" klik
        $putResponse = $this->actingAs($organisator)->put(
            route('contactpersonen.update', $contactpersoon->Id),
            [
                'voornaam' => 'Mark',
                'achternaam' => '', // Verplicht veld leeggemaakt
                'emailadres' => 'mark@kicksrdam.nl',
                'telefoonnummer' => '06-99999999', // Gewijzigd telefoonnummer
            ]
        );

        // 3. Dan: Worden de wijzigingen niet doorgevoerd
        $putResponse->assertSessionHasErrors(['achternaam']);
        $errors = session('errors')->get('achternaam');
        $this->assertStringContainsString('Dit veld is verplicht', $errors[0]);

        $this->assertDatabaseHas('Contactpersoon', [
            'Id' => $contactpersoon->Id,
            'Naam' => 'Mark de Vries', // Oude naam blijft behouden
            'Telefoonnummer' => '06-12345678',   // Oude telefoonnummer blijft behouden
        ]);

        // En: Zie ik de foutmelding "Dit veld is verplicht" op het formulier
        $formFollow = $this->actingAs($organisator)->get(route('contactpersonen.edit', $contactpersoon->Id));
        $formFollow->assertStatus(200);
    }

    /**
     * Test 5. Contactpersoon aan verkoper loskoppelen
     *
     * Controller: ContactpersoonController, Methode: delete()
     * Model: ContactpersoonModel, Methode: deleteContactpersoon()
     */
    public function test_organisator_can_unlink_contactpersoon_from_verkoper(): void
    {
        $organisator = $this->getOrganisator();

        $verkoper = Verkoper::create([
            'Naam' => 'Solebox Rotterdam',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Sophie Jansen',
            'Emailadres' => 'sophie@solebox.nl',
            'Telefoonnummer' => '06-87654321',
            'Opmerking' => 'Sales',
            'IsActief' => 1,
        ]);

        // Koppel aan verkoper
        ContactPerVerkoper::create([
            'VerkoperId' => $verkoper->Id,
            'ContactpersoonId' => $contactpersoon->Id,
            'IsActief' => 1,
            'Opmerking' => 'Sales',
        ]);

        $this->assertEquals(1, $contactpersoon->verkopers()->count());

        // Test loskoppelen via ContactpersoonController@delete
        $deleteResponse = $this->actingAs($organisator)->delete(
            route('contactpersonen.loskoppelen', $contactpersoon->Id),
            ['verkoper_id' => $verkoper->Id]
        );

        $deleteResponse->assertRedirect(route('contactpersonen.index'));
        $deleteResponse->assertSessionHas('success', 'Contactpersoon succesvol losgekoppeld');

        // Koppeling is verwijderd
        $this->assertEquals(0, $contactpersoon->fresh()->verkopers()->count());

        // Test direct Model: ContactpersoonModel methode: deleteContactpersoon()
        ContactPerVerkoper::create([
            'VerkoperId' => $verkoper->Id,
            'ContactpersoonId' => $contactpersoon->Id,
            'IsActief' => 1,
            'Opmerking' => 'Sales',
        ]);
        $this->assertEquals(1, $contactpersoon->fresh()->verkopers()->count());

        $model = ContactpersoonModel::findOrFail($contactpersoon->Id);
        $result = $model->deleteContactpersoon($verkoper->Id);
        $this->assertTrue($result);
        $this->assertEquals(0, $contactpersoon->fresh()->verkopers()->count());
    }
}
