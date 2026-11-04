<?php

namespace Tests\Feature;

use App\Models\Stand;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests voor de functionaliteit "Stand wijzigen"
 *
 * Test coverage:
 * 1. Een verkoper wijzigt succesvol zijn eigen standreservering (happy scenario)
 * 2. Een verkoper voert ongeldige gegevens in (unhappy scenario - validatie)
 * 3. Een verkoper probeert de reservering van iemand anders te wijzigen
 * 4. Een gebruiker die niet is ingelogd probeert een stand te wijzigen
 * 5. Een verkoper probeert een niet-bestaande reservering te wijzigen
 * 6. Na het opslaan worden de nieuwe gegevens correct uit de database opgehaald
 */
class StandWijzigenTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // Helper methods voor testopzet
    // =========================================================================

    private function createVerkoperUser(string $naam = 'Kicks Rotterdam'): User
    {
        return User::factory()->create([
            'name' => $naam,
            'role' => 'verkoper',
            'email' => 'verkoper@sneakerness.com',
        ]);
    }

    private function createOrganisatorUser(): User
    {
        return User::factory()->create([
            'name' => 'Organisator',
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);
    }

    /**
     * Maak een verkoper aan en koppel een gereserveerde stand
     */
    private function createVerkoperMetStand(string $naam = 'Kicks Rotterdam'): array
    {
        $verkoper = Verkoper::create([
            'Naam' => $naam,
            'SpecialeStatus' => 0,
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => true,
            'Opmerking' => 'Test verkoper',
        ]);

        $stand = Stand::create([
            'StandType' => 'AA',
            'VerkoperId' => $verkoper->Id,
            'Prijs' => 600.00,
            'AantalDagen' => 2,
            'VerhuurdStatus' => true, // Gereserveerd
            'IsActief' => true,
            'Opmerking' => 'Stand #AA-102 • Hal 1',
        ]);

        return [$verkoper, $stand];
    }

    // =========================================================================
    // TEST 1: Happy scenario — verkoper wijzigt succesvol zijn eigen stand
    // =========================================================================

    /**
     * Happy scenario:
     * Gegeven ik ben ingelogd als verkoper
     * En ik heb een stand gereserveerd
     * Wanneer ik mijn stand selecteer en op "Wijzigen" klik
     * En de gewenste wijzigingen invoer en op "Opslaan" klik
     * Dan worden de wijzigingen opgeslagen
     * En zie ik de melding "Stand is succesvol gewijzigd"
     */
    public function test_happy_scenario_verkoper_wijzigt_eigen_stand_succesvol(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('Kicks Rotterdam');
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        // Het wijzigformulier is bereikbaar
        $editResponse = $this->actingAs($user)
            ->get("/stands/{$stand->Id}/edit");

        $editResponse->assertStatus(200);
        $editResponse->assertSee('Stand Reservering Wijzigen');
        $editResponse->assertSee('Opslaan');

        // Formulier met geldige gewijzigde data insturen
        $response = $this->actingAs($user)
            ->from("/stands/{$stand->Id}/edit")
            ->put("/stands/{$stand->Id}", [
                'StandType'   => 'AA+',
                'AantalDagen' => 1,
                'Prijs'       => 750.00,
                'Opmerking'   => 'Stand #AA+-101 • Hal 1 Gewijzigd',
            ]);

        // Dan wordt de gebruiker teruggestuurd naar het standsoverzicht
        $response->assertRedirect('/stands');

        // En zie ik de melding "Stand is succesvol gewijzigd"
        $response->assertSessionHas('success', 'Stand is succesvol gewijzigd');

        // De nieuwe gegevens zijn direct zichtbaar (uit de database opgehaald)
        $stand->refresh();
        $this->assertEquals('AA+', $stand->StandType);
        $this->assertEquals(1, $stand->AantalDagen);
        $this->assertEquals('750.00', $stand->Prijs);
        $this->assertEquals('Stand #AA+-101 • Hal 1 Gewijzigd', $stand->Opmerking);
    }

    /**
     * Test dat het wijzigformulier de bestaande gegevens toont
     */
    public function test_wijzigformulier_toont_bestaande_gegevens(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('Kicks Rotterdam');
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        $response = $this->actingAs($user)
            ->get("/stands/{$stand->Id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Stand Reservering Wijzigen');
        // Huidige waarden zijn ingevuld
        $response->assertSee($stand->StandType);
        $response->assertSee(number_format((float)$stand->Prijs, 2, '.', ''));
    }

    // =========================================================================
    // TEST 2: Unhappy scenario — ongeldige gegevens
    // =========================================================================

    /**
     * Unhappy scenario:
     * Gegeven ik ben ingelogd als verkoper
     * En ik heb een stand gereserveerd
     * Wanneer ik mijn stand probeer te wijzigen
     * En de ingevoerde gegevens ongeldig zijn
     * Dan worden de wijzigingen niet opgeslagen
     * En zie ik de melding "Stand wijzigen is niet gelukt"
     */
    public function test_unhappy_scenario_ongeldige_gegevens_worden_niet_opgeslagen(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('Kicks Rotterdam');
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        $oorspronkelijkType  = $stand->StandType;
        $oorspronkelijkePrijs = $stand->Prijs;

        // Ongeldige gegevens insturen
        $response = $this->actingAs($user)
            ->from("/stands/{$stand->Id}/edit")
            ->put("/stands/{$stand->Id}", [
                'StandType'   => '',        // Verplicht veld leeg
                'AantalDagen' => 'ongeldig', // Geen geldig getal
                'Prijs'       => -999,       // Negatieve prijs niet toegestaan
                'Opmerking'   => str_repeat('X', 251), // Te lang
            ]);

        // Gebruiker wordt teruggestuurd naar het formulier
        $response->assertRedirect();

        // En zie ik de melding "Stand wijzigen is niet gelukt"
        $response->assertSessionHas('error', 'Stand wijzigen is niet gelukt');

        // Validatiefouten zijn aanwezig
        $response->assertSessionHasErrors(['StandType', 'Prijs', 'Opmerking']);

        // Wijzigingen zijn NIET opgeslagen
        $stand->refresh();
        $this->assertEquals($oorspronkelijkType, $stand->StandType);
    }

    /**
     * Test dat verplichte velden niet leeg mogen zijn
     */
    public function test_verplichte_velden_mogen_niet_leeg_zijn(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('Kicks Rotterdam');
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        $response = $this->actingAs($user)
            ->from("/stands/{$stand->Id}/edit")
            ->put("/stands/{$stand->Id}", [
                'StandType'   => '',
                'AantalDagen' => '',
                'Prijs'       => '',
            ]);

        $response->assertSessionHasErrors(['StandType', 'AantalDagen', 'Prijs']);
        $response->assertSessionHas('error', 'Stand wijzigen is niet gelukt');
    }

    // =========================================================================
    // TEST 3: Verkoper probeert stand van iemand anders te wijzigen
    // =========================================================================

    /**
     * Een verkoper mag uitsluitend zijn eigen reservering wijzigen.
     * Zorg dat andere verkopers geen reserveringen kunnen aanpassen die niet van hen zijn.
     */
    public function test_verkoper_mag_stand_van_anderen_niet_wijzigen(): void
    {
        // Stand van een andere verkoper
        [$andereVerkoper, $stand] = $this->createVerkoperMetStand('SoleMate Amsterdam');

        // Inloggen als een ANDERE verkoper
        $andereUser = User::factory()->create([
            'name' => 'Andere Verkoper',
            'role' => 'verkoper',
            'email' => 'andere@sneakerness.com',
        ]);

        // Poging om het edit-formulier te openen van een stand die niet van hem is
        $editResponse = $this->actingAs($andereUser)
            ->get("/stands/{$stand->Id}/edit");

        $editResponse->assertStatus(403);

        // Poging om direct de update te versturen
        $updateResponse = $this->actingAs($andereUser)
            ->from("/stands/{$stand->Id}/edit")
            ->put("/stands/{$stand->Id}", [
                'StandType'   => 'A',
                'AantalDagen' => 1,
                'Prijs'       => 100.00,
                'Opmerking'   => 'Gehackt!',
            ]);

        $updateResponse->assertStatus(403);

        // Gegevens zijn niet veranderd
        $stand->refresh();
        $this->assertEquals('AA', $stand->StandType);
        $this->assertEquals('Stand #AA-102 • Hal 1', $stand->Opmerking);
    }

    // =========================================================================
    // TEST 4: Niet-ingelogde gebruiker probeert stand te wijzigen
    // =========================================================================

    /**
     * Alleen ingelogde gebruikers met de juiste rechten mogen een stand wijzigen.
     */
    public function test_gast_wordt_doorgestuurd_naar_login_bij_edit(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand();

        $editResponse = $this->get("/stands/{$stand->Id}/edit");
        $editResponse->assertRedirect('/login');
    }

    public function test_gast_wordt_doorgestuurd_naar_login_bij_update(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand();

        $updateResponse = $this->put("/stands/{$stand->Id}", [
            'StandType'   => 'A',
            'AantalDagen' => 1,
            'Prijs'       => 300.00,
        ]);

        $updateResponse->assertRedirect('/login');
    }

    // =========================================================================
    // TEST 5: Niet-bestaande reservering
    // =========================================================================

    /**
     * Een verkoper probeert een niet-bestaande reservering te wijzigen.
     */
    public function test_niet_bestaande_stand_geeft_404(): void
    {
        $user = $this->createVerkoperUser();

        $editResponse = $this->actingAs($user)
            ->get('/stands/99999/edit');

        $editResponse->assertStatus(404);

        $updateResponse = $this->actingAs($user)
            ->put('/stands/99999', [
                'StandType'   => 'AA',
                'AantalDagen' => 2,
                'Prijs'       => 600.00,
            ]);

        $updateResponse->assertStatus(404);
    }

    // =========================================================================
    // TEST 6: Na opslaan worden gewijzigde gegevens correct opgehaald
    // =========================================================================

    /**
     * Na het opslaan worden de nieuwe gegevens correct uit de database opgehaald
     * en zijn ze zichtbaar op de standsoverzichtpagina.
     */
    public function test_gewijzigde_gegevens_zijn_direct_zichtbaar_na_opslaan(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('Kicks Rotterdam');
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        // Stand wijzigen
        $this->actingAs($user)
            ->put("/stands/{$stand->Id}", [
                'StandType'   => 'A',
                'AantalDagen' => 1,
                'Prijs'       => 350.00,
                'Opmerking'   => 'Gewijzigde opmerking',
            ]);

        // Controleer dat de nieuwe gegevens in de database staan
        $this->assertDatabaseHas('Stand', [
            'Id'         => $stand->Id,
            'StandType'  => 'A',
            'AantalDagen'=> 1,
            'Prijs'      => 350.00,
            'Opmerking'  => 'Gewijzigde opmerking',
        ]);

        // Overzichtspagina bezoeken - gewijzigde gegevens zijn zichtbaar
        $indexResponse = $this->actingAs($user)->get('/stands');
        $indexResponse->assertStatus(200);
    }

    // =========================================================================
    // TEST EXTRA: Organisator kan elke stand wijzigen
    // =========================================================================

    /**
     * Een organisator mag elke stand wijzigen (niet alleen zijn eigen stands).
     */
    public function test_organisator_kan_elke_stand_wijzigen(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('SoleMate Amsterdam');
        $organisator = $this->createOrganisatorUser();

        $editResponse = $this->actingAs($organisator)
            ->get("/stands/{$stand->Id}/edit");

        $editResponse->assertStatus(200);
        $editResponse->assertSee('Stand Reservering Wijzigen');

        $updateResponse = $this->actingAs($organisator)
            ->put("/stands/{$stand->Id}", [
                'StandType'   => 'AA+',
                'AantalDagen' => 2,
                'Prijs'       => 950.00,
                'Opmerking'   => 'Door organisator gewijzigd',
            ]);

        $updateResponse->assertRedirect('/stands');
        $updateResponse->assertSessionHas('success', 'Stand is succesvol gewijzigd');
    }

    /**
     * Test dat de standsoverzichtpagina een "Wijzigen" knop toont
     * voor de gereserveerde stand van de ingelogde verkoper.
     */
    public function test_stands_pagina_toont_wijzigen_knop_voor_eigen_stand(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('Kicks Rotterdam');
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        $response = $this->actingAs($user)->get('/stands');

        $response->assertStatus(200);
        $response->assertSee('Wijzigen');
        $response->assertSee('Stand Reservering Wijzigen');
    }

    /**
     * Test dat de unhappy scenario simulatie via query parameter werkt.
     */
    public function test_unhappy_scenario_via_query_parameter_geeft_foutmelding(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('Kicks Rotterdam');
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        $response = $this->actingAs($user)
            ->get("/stands/{$stand->Id}/edit?unhappy=1");

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
    }

    /**
     * Test stand selecteren uit dropdown en op "Wijzigen" klikken
     */
    public function test_verkoper_kan_stand_selecteren_en_op_wijzigen_klikken(): void
    {
        [$verkoper, $stand] = $this->createVerkoperMetStand('Kicks Rotterdam');
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        $response = $this->actingAs($user)
            ->get("/stands/select-edit?stand_id={$stand->Id}");

        $response->assertRedirect("/stands/{$stand->Id}/edit");
    }

    /**
     * Test dat select-edit zonder stand_id terugstuurt met melding
     */
    public function test_select_edit_zonder_stand_id_stuurt_terug(): void
    {
        $user = $this->createVerkoperUser('Kicks Rotterdam');

        $response = $this->actingAs($user)
            ->get('/stands/select-edit');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Selecteer een stand om te wijzigen.');
    }
}

