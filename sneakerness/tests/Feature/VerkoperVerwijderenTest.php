<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Verkoper;
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
}
