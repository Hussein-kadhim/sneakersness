<?php

namespace Tests\Feature;

use App\Models\Stand;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandToevoegenTest extends TestCase
{
    use RefreshDatabase;

    private function createOrganisatorUser(): User
    {
        return User::factory()->create([
            'role' => 'organisator',
            'email' => 'organisator@sneakerness.com',
        ]);
    }

    private function createTestVerkoper(): Verkoper
    {
        return Verkoper::create([
            'Naam' => 'Sneaker Vault RTM',
            'SpecialeStatus' => 0,
            'VerkooptSoort' => 'Exclusieve Sneakers',
            'IsActief' => true,
            'Opmerking' => 'Test verkoper',
        ]);
    }

    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $response = $this->get('/stands/create');

        $response->assertRedirect('/login');
    }

    public function test_geauthenticeerde_gebruiker_kan_stand_toevoegen_pagina_openen(): void
    {
        $user = $this->createOrganisatorUser();
        $verkoper = $this->createTestVerkoper();

        $response = $this->actingAs($user)->get('/stands/create');

        $response->assertStatus(200);
        $response->assertSee('Nieuwe Stand Toevoegen');
        $response->assertSee('Stand Type');
        $response->assertSee('Gekoppelde Verkoper');
        $response->assertSee('Prijs');
        $response->assertSee('Sneaker Vault RTM');
    }

    public function test_stand_toevoegen_valideert_verplichte_velden(): void
    {
        $user = $this->createOrganisatorUser();

        $response = $this->actingAs($user)->post('/stands', []);

        $response->assertSessionHasErrors([
            'StandType',
            'VerkoperId',
            'Prijs',
            'AantalDagen',
            'VerhuurdStatus',
        ]);
    }

    public function test_stand_kan_succesvol_worden_aangemaakt(): void
    {
        $user = $this->createOrganisatorUser();
        $verkoper = $this->createTestVerkoper();

        $payload = [
            'StandType' => 'AA+',
            'VerkoperId' => $verkoper->Id,
            'Prijs' => 850.00,
            'AantalDagen' => 2,
            'VerhuurdStatus' => 0,
            'Opmerking' => 'Stand #AA-999 • Hal 1',
        ];

        $response = $this->actingAs($user)->post('/stands', $payload);

        $response->assertRedirect('/stands');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('Stand', [
            'StandType' => 'AA+',
            'VerkoperId' => $verkoper->Id,
            'Prijs' => 850.00,
            'AantalDagen' => 2,
            'VerhuurdStatus' => 0,
            'Opmerking' => 'Stand #AA-999 • Hal 1',
            'IsActief' => 1,
        ]);
    }

    public function test_unhappy_scenario_stand_toevoegen_pagina_bij_gesimuleerde_database_storing(): void
    {
        $user = $this->createOrganisatorUser();

        $response = $this->actingAs($user)->get('/stands/create?unhappy=1');

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
    }

    public function test_unhappy_scenario_stand_opslaan_bij_gesimuleerde_database_storing(): void
    {
        $user = $this->createOrganisatorUser();

        $response = $this->actingAs($user)->post('/stands?db_error=1', [
            'StandType' => 'AA+',
            'VerkoperId' => 1,
            'Prijs' => 500.00,
            'AantalDagen' => 1,
            'VerhuurdStatus' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Verbindingsfout');
        $response->assertSee('Database is momenteel niet beschikbaar');
    }
}
