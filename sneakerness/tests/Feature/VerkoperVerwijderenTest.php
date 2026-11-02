<?php

namespace Tests\Feature;

use App\Models\Contactpersoon;
use App\Models\Stand;
use App\Models\User;
use App\Models\Verkoper;
use App\Models\VerkoperModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerkoperVerwijderenTest extends TestCase
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
     * Test Happy Scenario: Organisator verwijdert een verkoper en bevestigt dit.
     * De verkoper wordt verwijderd uit de administratie, er verschijnt een melding
     * en de verkoper staat niet meer in de lijst.
     */
    public function test_happy_scenario_organisator_can_delete_verkoper_successfully(): void
    {
        $organisator = $this->getOrganisator();

        // 1. Verkoper aanmaken die niet meer gekoppeld is
        $verkoper = Verkoper::create([
            'Naam' => 'Tijdelijke Pop-up Store',
            'VerkooptSoort' => 'Accessoires',
            'IsActief' => 1,
            'Opmerking' => 'Gestopte verkoper',
        ]);

        // 2. Verkopersoverzicht bezoeken en controleren dat de verkoper in de lijst staat
        $indexResponse = $this->actingAs($organisator)->get('/verkopers');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Tijdelijke Pop-up Store');

        // 3. Verwijderverzoek versturen (bevestigd via modal)
        $deleteResponse = $this->actingAs($organisator)->delete("/verkopers/{$verkoper->Id}");

        // 4. Controleren dat er wordt geredirect naar het overzicht met succesmelding
        $deleteResponse->assertRedirect('/verkopers');
        $deleteResponse->assertSessionHas('success');

        // 5. Controleren dat de verkoper uit de database is verwijderd
        $this->assertDatabaseMissing('Verkoper', [
            'Id' => $verkoper->Id,
            'Naam' => 'Tijdelijke Pop-up Store',
        ]);

        // 6. Overzichtspagina bezoeken en verifiëren dat verkoper weg is en melding toont
        $followResponse = $this->actingAs($organisator)->get('/verkopers');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('succesvol verwijderd');
        $followResponse->assertDontSee('Tijdelijke Pop-up Store');
    }

    /**
     * Test Unhappy Scenario: Organisator probeert een verkoper te verwijderen
     * die nog gekoppeld is aan een contactpersoon of stand.
     * De verkoper wordt NIET verwijderd en er verschijnt een foutmelding dat dit
     * niet kan omdat de verkoper nog gekoppeld is.
     */
    public function test_unhappy_scenario_cannot_delete_verkoper_that_is_still_linked(): void
    {
        $organisator = $this->getOrganisator();

        // 1. Verkoper aanmaken met gekoppelde contactpersoon
        $verkoper = Verkoper::create([
            'Naam' => 'Kicks Rotterdam',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $contact = Contactpersoon::create([
            'Naam' => 'Mark de Vries',
            'Emailadres' => 'mark@kicksrdam.nl',
            'Telefoonnummer' => '06-12345678',
            'IsActief' => 1,
        ]);

        $verkoper->contactpersonen()->attach($contact->Id, [
            'IsActief' => 1,
            'Opmerking' => 'Gekoppeld',
        ]);

        Stand::create([
            'VerkoperId' => $verkoper->Id,
            'StandType' => 'AA+',
            'Prijs' => 450.00,
            'VerhuurdStatus' => 1,
            'IsActief' => 1,
            'Opmerking' => 'Stand #AA-101 • Hal 1',
        ]);

        // 2. Poging doen om gekoppelde verkoper te verwijderen
        $deleteResponse = $this->actingAs($organisator)->delete("/verkopers/{$verkoper->Id}");

        // 3. Controleren op redirect naar overzicht met foutmelding
        $deleteResponse->assertRedirect('/verkopers');
        $deleteResponse->assertSessionHas('error');

        // 4. Controleren dat de verkoper NIET is verwijderd uit de database
        $this->assertDatabaseHas('Verkoper', [
            'Id' => $verkoper->Id,
            'Naam' => 'Kicks Rotterdam',
        ]);

        // 5. Overzichtspagina bezoeken en foutmelding verifiëren
        $followResponse = $this->actingAs($organisator)->get('/verkopers');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('omdat de verkoper nog gekoppeld is');
        $followResponse->assertSee('Kicks Rotterdam');
    }

    /**
     * Test dat gastgebruikers geen verkoper kunnen verwijderen.
     */
    public function test_guests_cannot_delete_verkoper(): void
    {
        $verkoper = Verkoper::create([
            'Naam' => 'Test Verkoper',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $response = $this->delete("/verkopers/{$verkoper->Id}");
        $response->assertRedirect('/login');

        $this->assertDatabaseHas('Verkoper', [
            'Id' => $verkoper->Id,
        ]);
    }

    /**
     * Test VerkoperModel klasse en deleteVerkoper() methode direct.
     */
    public function test_verkoper_model_delete_verkoper_method(): void
    {
        // Ongekoppelde verkoper via VerkoperModel
        $verkoper = Verkoper::create([
            'Naam' => 'Direct Delete Test',
            'VerkooptSoort' => 'Art',
            'IsActief' => 1,
        ]);

        $model = VerkoperModel::findOrFail($verkoper->Id);
        $this->assertFalse($model->isGekoppeld());

        $deleted = $model->deleteVerkoper();
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('Verkoper', [
            'Id' => $verkoper->Id,
        ]);

        // Gekoppelde verkoper via VerkoperModel weigert verwijderen
        $linkedVerkoper = Verkoper::create([
            'Naam' => 'Linked Delete Test',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $contact = Contactpersoon::create([
            'Naam' => 'Test Contact',
            'Emailadres' => 'contact@test.nl',
            'Telefoonnummer' => '06-00000000',
            'IsActief' => 1,
        ]);

        $linkedVerkoper->contactpersonen()->attach($contact->Id);

        $linkedModel = VerkoperModel::findOrFail($linkedVerkoper->Id);
        $this->assertTrue($linkedModel->isGekoppeld());

        $deletedLinked = $linkedModel->deleteVerkoper(force: false);
        $this->assertFalse($deletedLinked);
        $this->assertDatabaseHas('Verkoper', [
            'Id' => $linkedVerkoper->Id,
        ]);
    }
}
