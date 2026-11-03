<?php

namespace Tests\Feature;

use App\Models\Stand;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandVerwijderenTest extends TestCase
{
    use RefreshDatabase;

    private function createOrganisatorUser(): User
    {
        return User::factory()->create([
            'name' => 'Organisator',
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);
        /**
     * Beveiliging / Unhappy scenario 1:
     * Verkoper probeert stand van een andere verkoper te verwijderen -> 403 Forbidden.
     */
    public function test_verkoper_kan_stand_van_andere_verkoper_niet_verwijderen(): void
    {
        $verkoperUser = $this->createVerkoperUser('Verkoper A');
        $andermansStand = $this->createTestStand('Verkoper B', isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->delete("/stands/{$andermansStand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $andermansStand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 2:
     * Bezoeker probeert een stand te verwijderen -> 403 Forbidden.
     */
    public function test_bezoeker_mag_geen_stand_verwijderen(): void
    {
        $bezoeker = $this->createBezoekerUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($bezoeker)
            ->delete("/stands/{$stand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 3:
     * Niet-ingelogde bezoeker/gast wordt doorgestuurd naar login.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $stand = $this->createTestStand();

        $response = $this->delete("/stands/{$stand->Id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
    /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    private function createVerkoperUser(string $name = 'Sole Vault Rotterdam'): User
    {
        return User::factory()->create([
            'name' => $name,
            'role' => 'verkoper',
            'email' => strtolower(str_replace(' ', '.', $name)) . '@sneakerness.com',
        ]);
        /**
     * Beveiliging / Unhappy scenario 1:
     * Verkoper probeert stand van een andere verkoper te verwijderen -> 403 Forbidden.
     */
    public function test_verkoper_kan_stand_van_andere_verkoper_niet_verwijderen(): void
    {
        $verkoperUser = $this->createVerkoperUser('Verkoper A');
        $andermansStand = $this->createTestStand('Verkoper B', isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->delete("/stands/{$andermansStand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $andermansStand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 2:
     * Bezoeker probeert een stand te verwijderen -> 403 Forbidden.
     */
    public function test_bezoeker_mag_geen_stand_verwijderen(): void
    {
        $bezoeker = $this->createBezoekerUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($bezoeker)
            ->delete("/stands/{$stand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 3:
     * Niet-ingelogde bezoeker/gast wordt doorgestuurd naar login.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $stand = $this->createTestStand();

        $response = $this->delete("/stands/{$stand->Id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
    /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    private function createBezoekerUser(): User
    {
        return User::factory()->create([
            'name' => 'Bezoeker',
            'role' => 'bezoeker',
            'email' => 'bezoeker@sneakerness.com',
        ]);
        /**
     * Beveiliging / Unhappy scenario 1:
     * Verkoper probeert stand van een andere verkoper te verwijderen -> 403 Forbidden.
     */
    public function test_verkoper_kan_stand_van_andere_verkoper_niet_verwijderen(): void
    {
        $verkoperUser = $this->createVerkoperUser('Verkoper A');
        $andermansStand = $this->createTestStand('Verkoper B', isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->delete("/stands/{$andermansStand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $andermansStand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 2:
     * Bezoeker probeert een stand te verwijderen -> 403 Forbidden.
     */
    public function test_bezoeker_mag_geen_stand_verwijderen(): void
    {
        $bezoeker = $this->createBezoekerUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($bezoeker)
            ->delete("/stands/{$stand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 3:
     * Niet-ingelogde bezoeker/gast wordt doorgestuurd naar login.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $stand = $this->createTestStand();

        $response = $this->delete("/stands/{$stand->Id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
    /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    private function createTestStand(string $verkoperNaam = 'Test Verkoper', bool $isVerhuurd = false): Stand
    {
        $verkoper = Verkoper::firstOrCreate(
            ['Naam' => $verkoperNaam],
            [
                'SpecialeStatus' => 0,
                'VerkooptSoort' => 'Sneakers',
                'IsActief' => true,
                'Opmerking' => 'Test Verkoper',
            ]
        );

        return Stand::create([
            'StandType' => 'AA',
            'VerkoperId' => $verkoper->Id,
            'Prijs' => 500.00,
            'AantalDagen' => 2,
            'VerhuurdStatus' => $isVerhuurd,
            'IsActief' => true,
            'Opmerking' => 'Stand #AA-100 • Hal 1',
        ]);
        /**
     * Beveiliging / Unhappy scenario 1:
     * Verkoper probeert stand van een andere verkoper te verwijderen -> 403 Forbidden.
     */
    public function test_verkoper_kan_stand_van_andere_verkoper_niet_verwijderen(): void
    {
        $verkoperUser = $this->createVerkoperUser('Verkoper A');
        $andermansStand = $this->createTestStand('Verkoper B', isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->delete("/stands/{$andermansStand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $andermansStand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 2:
     * Bezoeker probeert een stand te verwijderen -> 403 Forbidden.
     */
    public function test_bezoeker_mag_geen_stand_verwijderen(): void
    {
        $bezoeker = $this->createBezoekerUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($bezoeker)
            ->delete("/stands/{$stand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 3:
     * Niet-ingelogde bezoeker/gast wordt doorgestuurd naar login.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $stand = $this->createTestStand();

        $response = $this->delete("/stands/{$stand->Id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
    /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Happy scenario 1:
     * Organisator kan elke stand succesvol verwijderen.
     */
    public function test_organisator_kan_elke_stand_succesvol_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('success', 'Stand is succesvol verwijderd');

        $this->assertDatabaseMissing('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Beveiliging / Unhappy scenario 1:
     * Verkoper probeert stand van een andere verkoper te verwijderen -> 403 Forbidden.
     */
    public function test_verkoper_kan_stand_van_andere_verkoper_niet_verwijderen(): void
    {
        $verkoperUser = $this->createVerkoperUser('Verkoper A');
        $andermansStand = $this->createTestStand('Verkoper B', isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->delete("/stands/{$andermansStand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $andermansStand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 2:
     * Bezoeker probeert een stand te verwijderen -> 403 Forbidden.
     */
    public function test_bezoeker_mag_geen_stand_verwijderen(): void
    {
        $bezoeker = $this->createBezoekerUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($bezoeker)
            ->delete("/stands/{$stand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 3:
     * Niet-ingelogde bezoeker/gast wordt doorgestuurd naar login.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $stand = $this->createTestStand();

        $response = $this->delete("/stands/{$stand->Id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
    /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Happy scenario 2:
     * Verkoper kan zijn eigen stand succesvol verwijderen.
     */
    public function test_verkoper_kan_eigen_stand_succesvol_verwijderen(): void
    {
        $verkoperUser = $this->createVerkoperUser('Kicks R Us');
        $eigenStand = $this->createTestStand('Kicks R Us', isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->from('/stands')
            ->delete("/stands/{$eigenStand->Id}");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('success', 'Stand is succesvol verwijderd');

        $this->assertDatabaseMissing('Stand', [
            'Id' => $eigenStand->Id,
        ]);
        /**
     * Beveiliging / Unhappy scenario 1:
     * Verkoper probeert stand van een andere verkoper te verwijderen -> 403 Forbidden.
     */
    public function test_verkoper_kan_stand_van_andere_verkoper_niet_verwijderen(): void
    {
        $verkoperUser = $this->createVerkoperUser('Verkoper A');
        $andermansStand = $this->createTestStand('Verkoper B', isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->delete("/stands/{$andermansStand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $andermansStand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 2:
     * Bezoeker probeert een stand te verwijderen -> 403 Forbidden.
     */
    public function test_bezoeker_mag_geen_stand_verwijderen(): void
    {
        $bezoeker = $this->createBezoekerUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($bezoeker)
            ->delete("/stands/{$stand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 3:
     * Niet-ingelogde bezoeker/gast wordt doorgestuurd naar login.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $stand = $this->createTestStand();

        $response = $this->delete("/stands/{$stand->Id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
    /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
    /**
     * Beveiliging / Unhappy scenario 1:
     * Verkoper probeert stand van een andere verkoper te verwijderen -> 403 Forbidden.
     */
    public function test_verkoper_kan_stand_van_andere_verkoper_niet_verwijderen(): void
    {
        $verkoperUser = $this->createVerkoperUser('Verkoper A');
        $andermansStand = $this->createTestStand('Verkoper B', isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->delete("/stands/{$andermansStand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $andermansStand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 2:
     * Bezoeker probeert een stand te verwijderen -> 403 Forbidden.
     */
    public function test_bezoeker_mag_geen_stand_verwijderen(): void
    {
        $bezoeker = $this->createBezoekerUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($bezoeker)
            ->delete("/stands/{$stand->Id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}

    /**
     * Beveiliging / Unhappy scenario 3:
     * Niet-ingelogde bezoeker/gast wordt doorgestuurd naar login.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $stand = $this->createTestStand();

        $response = $this->delete("/stands/{$stand->Id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
        /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
    /**
     * Unhappy scenario 4:
     * Proberen een niet-bestaande stand te verwijderen geeft duidelijke foutmelding.
     */
    public function test_verwijderen_van_niet_bestaande_stand_geeft_foutmelding(): void
    {
        $organisator = $this->createOrganisatorUser();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete('/stands/999999');

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand bestaat niet.');
    }

    /**
     * Unhappy scenario 5:
     * Gesimuleerde database storing bij verwijderen -> foutmelding en stand blijft behouden.
     */
    public function test_unhappy_scenario_gesimuleerde_database_fout_bij_verwijderen(): void
    {
        $organisator = $this->createOrganisatorUser();
        $stand = $this->createTestStand();

        $response = $this->actingAs($organisator)
            ->from('/stands')
            ->delete("/stands/{$stand->Id}?unhappy=1");

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Stand verwijderen is niet gelukt');

        $this->assertDatabaseHas('Stand', [
            'Id' => $stand->Id,
        ]);
    }
}
