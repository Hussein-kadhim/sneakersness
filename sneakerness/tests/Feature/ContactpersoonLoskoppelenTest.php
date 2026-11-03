<?php

namespace Tests\Feature;

use App\Models\Contactpersoon;
use App\Models\ContactpersoonModel;
use App\Models\ContactPerVerkoper;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactpersoonLoskoppelenTest extends TestCase
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
     * Happy scenario: Contactpersoon succesvol loskoppelen
     *
     * Gegeven ik ben ingelogd en bekijk een contactpersoon die gekoppeld is aan een verkoper
     * View naam: contactpersoon_detail.php (of detail.php binnen de map views/contactpersoon/)
     * Controller: ContactpersoonController, Methode: unlink() of loskoppelen()
     * Model: ContactpersoonModel, Methode: unlinkVerkoper() of loskoppelVerkoper()
     * Wanneer ik op "Loskoppelen" klik en de bevestigingsdialoog accepteer
     * Dan wordt de koppeling met de verkoper verwijderd
     * En blijft de contactpersoon los bewaard in het systeem
     * En zie ik de melding "Contactpersoon succesvol losgekoppeld"
     */
    public function test_happy_scenario_organisator_can_unlink_contactpersoon_successfully(): void
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

        // Koppel contactpersoon aan verkoper
        ContactPerVerkoper::create([
            'VerkoperId' => $verkoper->Id,
            'ContactpersoonId' => $contactpersoon->Id,
            'IsActief' => 1,
            'Opmerking' => 'Standmanager (Hoofdcontactpersoon)',
        ]);

        $this->assertEquals(1, $contactpersoon->verkopers()->count());

        // 1. Gegeven: Ik ben ingelogd en bekijk een contactpersoon die gekoppeld is aan een verkoper
        $detailResponse = $this->actingAs($organisator)->get(route('contactpersonen.show', $contactpersoon->Id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Mark de Vries');
        $detailResponse->assertSee('Kicks Rotterdam');
        $detailResponse->assertSee('Loskoppelen');
        $detailResponse->assertSee('Contactpersoon loskoppelen');
        $detailResponse->assertSee('Let op: De contactpersoon blijft bewaard in het systeem, maar is niet langer gekoppeld aan deze verkoper.');

        // 2. Wanneer: Ik op "Loskoppelen" klik en de bevestigingsdialoog accepteer
        $postResponse = $this->actingAs($organisator)->delete(
            route('contactpersonen.loskoppelen', $contactpersoon->Id),
            ['verkoper_id' => $verkoper->Id]
        );

        // 3. Dan: Wordt de koppeling met de verkoper verwijderd
        $postResponse->assertRedirect(route('contactpersonen.index'));
        $postResponse->assertSessionHas('success', 'Contactpersoon succesvol losgekoppeld');

        $this->assertEquals(0, $contactpersoon->fresh()->verkopers()->count());
        $this->assertDatabaseMissing('ContactPerVerkoper', [
            'ContactpersoonId' => $contactpersoon->Id,
            'VerkoperId' => $verkoper->Id,
        ]);

        // 4. En: Blijft de contactpersoon los bewaard in het systeem
        $this->assertDatabaseHas('Contactpersoon', [
            'Id' => $contactpersoon->Id,
            'Naam' => 'Mark de Vries',
            'Emailadres' => 'mark@kicksrdam.nl',
            'Telefoonnummer' => '06-12345678',
        ]);

        // 5. En: Zie ik de melding "Contactpersoon succesvol losgekoppeld"
        $followResponse = $this->actingAs($organisator)->get(route('contactpersonen.index'));
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Contactpersoon succesvol losgekoppeld');
        $followResponse->assertSee('Mark de Vries');
    }

    /**
     * Unhappy scenario: Serverfout tijdens loskoppelen
     *
     * Gegeven ik ben ingelogd en wil een koppeling verbreken
     * Wanneer er tijdens het loskoppelen een netwerk- of serverfout optreedt
     * Dan blijft de koppeling intact
     * En zie ik de foutmelding "Loskoppelen mislukt, probeer het later opnieuw"
     */
    public function test_unhappy_scenario_server_error_during_unlinking(): void
    {
        $organisator = $this->getOrganisator();

        $verkoper = Verkoper::create([
            'Naam' => 'SoleMate Amsterdam',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Sophie Jansen',
            'Emailadres' => 'sophie@solemate.nl',
            'Telefoonnummer' => '06-87654321',
            'Opmerking' => 'Eigenaar',
            'IsActief' => 1,
        ]);

        ContactPerVerkoper::create([
            'VerkoperId' => $verkoper->Id,
            'ContactpersoonId' => $contactpersoon->Id,
            'IsActief' => 1,
            'Opmerking' => 'Eigenaar',
        ]);

        // 1. Gegeven: Ik ben ingelogd en wil een koppeling verbreken
        // 2. Wanneer: Er tijdens het loskoppelen een netwerk- of serverfout optreedt (gesimuleerd via ?error=1)
        $response = $this->actingAs($organisator)->delete(
            route('contactpersonen.loskoppelen', ['contactpersoon' => $contactpersoon->Id, 'error' => 1]),
            ['verkoper_id' => $verkoper->Id]
        );

        // 3. Dan: Blijft de koppeling intact
        $this->assertEquals(1, $contactpersoon->fresh()->verkopers()->count());
        $this->assertDatabaseHas('ContactPerVerkoper', [
            'ContactpersoonId' => $contactpersoon->Id,
            'VerkoperId' => $verkoper->Id,
        ]);

        // 4. En: Zie ik de foutmelding "Loskoppelen mislukt, probeer het later opnieuw"
        $response->assertSessionHas('error', 'Loskoppelen mislukt, probeer het later opnieuw');
    }

    /**
     * Test Model methoden: unlinkVerkoper() en loskoppelVerkoper()
     * Model: ContactpersoonModel
     */
    public function test_model_methods_unlink_verkoper_and_loskoppel_verkoper(): void
    {
        $verkoper = Verkoper::create([
            'Naam' => 'Streetwear RTM',
            'VerkooptSoort' => 'Kleding',
            'IsActief' => 1,
        ]);

        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Bilal El Amrani',
            'Emailadres' => 'bilal@streetwear.nl',
            'Telefoonnummer' => '06-55443322',
            'Opmerking' => 'Sales',
            'IsActief' => 1,
        ]);

        ContactPerVerkoper::create([
            'VerkoperId' => $verkoper->Id,
            'ContactpersoonId' => $contactpersoon->Id,
            'IsActief' => 1,
            'Opmerking' => 'Sales',
        ]);

        $model = ContactpersoonModel::findOrFail($contactpersoon->Id);
        $this->assertEquals(1, $model->verkopers()->count());

        // Test loskoppelVerkoper()
        $result = $model->loskoppelVerkoper($verkoper->Id);
        $this->assertTrue($result);
        $this->assertEquals(0, $model->fresh()->verkopers()->count());

        // Koppel opnieuw
        ContactPerVerkoper::create([
            'VerkoperId' => $verkoper->Id,
            'ContactpersoonId' => $contactpersoon->Id,
            'IsActief' => 1,
            'Opmerking' => 'Sales',
        ]);

        // Test unlinkVerkoper()
        $result2 = $model->unlinkVerkoper($verkoper->Id);
        $this->assertTrue($result2);
        $this->assertEquals(0, $model->fresh()->verkopers()->count());
    }
}

