<?php

namespace Tests\Feature;

use App\Models\Contactpersoon;
use App\Models\ContactpersoonModel;
use App\Models\ContactPerVerkoper;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactpersoonVerwijderenTest extends TestCase
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
     * Happy scenario: Contactpersoon succesvol verwijderen
     *
     * Gegeven ik ben ingelogd in het contactpersonenoverzicht
     * Wanneer ik bij een contactpersoon op "Verwijderen" klik en de bevestigingsvraag goedkeur
     * Dan wordt de contactpersoon definitief uit het systeem verwijderd
     * En zie ik de melding "Contactpersoon succesvol verwijderd"
     */
    public function test_happy_scenario_organisator_can_permanently_delete_contactpersoon(): void
    {
        $organisator = $this->getOrganisator();

        $verkoper = Verkoper::create([
            'Naam' => 'Kicks Rotterdam',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Mark de Vries',
            'Emailadres' => 'mark@kicksrdam.nl',
            'Telefoonnummer' => '06-12345678',
            'Opmerking' => 'Standmanager',
            'IsActief' => 1,
        ]);

        ContactPerVerkoper::create([
            'VerkoperId' => $verkoper->Id,
            'ContactpersoonId' => $contactpersoon->Id,
            'IsActief' => 1,
            'Opmerking' => 'Standmanager',
        ]);

        // 1. Gegeven: Ik ben ingelogd in het contactpersonenoverzicht
        $response = $this->actingAs($organisator)->get(route('contactpersonen.index'));
        $response->assertStatus(200);
        $response->assertSee('Mark de Vries');
        $response->assertSee('Contactpersoon verwijderen');
        $response->assertSee('Weet je zeker dat je contactpersoon');
        $response->assertSee('De koppeling met de verkoper wordt hierdoor ook automatisch beëindigd.');

        // 2. Wanneer: Ik bij een contactpersoon op "Verwijderen" klik en de bevestigingsvraag goedkeur
        $deleteResponse = $this->actingAs($organisator)->delete(
            route('contactpersonen.delete', $contactpersoon->Id)
        );

        // 3. Dan: Wordt de contactpersoon definitief uit het systeem verwijderd
        $deleteResponse->assertRedirect(route('contactpersonen.index'));
        $deleteResponse->assertSessionHas('success', 'Contactpersoon succesvol verwijderd');

        $this->assertDatabaseMissing('Contactpersoon', [
            'Id' => $contactpersoon->Id,
        ]);
        $this->assertDatabaseMissing('ContactPerVerkoper', [
            'ContactpersoonId' => $contactpersoon->Id,
        ]);

        // 4. En: Zie ik de melding "Contactpersoon succesvol verwijderd" op het overzicht
        $followResponse = $this->actingAs($organisator)->get(route('contactpersonen.index'));
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Contactpersoon succesvol verwijderd');
        $followResponse->assertDontSee('mark@kicksrdam.nl');
    }

    /**
     * Unhappy scenario: Verwijderen van contactpersoon annuleren
     *
     * Gegeven ik ben ingelogd en klik op "Verwijderen" bij een contactpersoon
     * Wanneer de bevestigingsvraag verschijnt en ik op "Annuleren" klik
     * Dan wordt de actie afgebroken
     * En blijft de contactpersoon ongewijzigd in de database staan
     */
    public function test_unhappy_scenario_canceling_delete_keeps_contactpersoon_intact(): void
    {
        $organisator = $this->getOrganisator();

        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Sophie Jansen',
            'Emailadres' => 'sophie@solemate.nl',
            'Telefoonnummer' => '06-87654321',
            'Opmerking' => 'Eigenaar',
            'IsActief' => 1,
        ]);

        // 1. Gegeven: Ik ben ingelogd en klik op "Verwijderen" bij een contactpersoon
        $response = $this->actingAs($organisator)->get(route('contactpersonen.index'));
        $response->assertStatus(200);
        $response->assertSee('Sophie Jansen');
        $response->assertSee('Annuleren');

        // 2. Wanneer: De bevestigingsvraag verschijnt en ik op "Annuleren" klik
        // Er wordt géén DELETE verzoek verstuurd.
        // 3. Dan: Blijft de contactpersoon ongewijzigd in de database staan
        $this->assertDatabaseHas('Contactpersoon', [
            'Id' => $contactpersoon->Id,
            'Naam' => 'Sophie Jansen',
            'Emailadres' => 'sophie@solemate.nl',
            'Telefoonnummer' => '06-87654321',
        ]);
    }

    /**
     * Test Model method: deleteContactpersoon()
     * Model: ContactpersoonModel
     */
    public function test_contactpersoon_model_delete_contactpersoon_method(): void
    {
        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Jesse van Dijk',
            'Emailadres' => 'jesse@barber.nl',
            'Telefoonnummer' => '06-11223344',
            'Opmerking' => 'Barbier',
            'IsActief' => 1,
        ]);

        $model = ContactpersoonModel::findOrFail($contactpersoon->Id);
        $result = $model->deleteContactpersoon();

        $this->assertTrue($result);
        $this->assertDatabaseMissing('Contactpersoon', [
            'Id' => $contactpersoon->Id,
        ]);
    }
}

