<?php

namespace Tests\Feature;

use App\Models\Stand;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandReserverenTest extends TestCase
{
    use RefreshDatabase;

    private function createVerkoperUser(): User
    {
        return User::factory()->create([
            'name' => 'Sole Vault Rotterdam',
            'role' => 'verkoper',
            'email' => 'verkoper@sneakerness.com',
        ]);
    }

    private function createTestStand(bool $isVerhuurd = false): Stand
    {
        $verkoper = Verkoper::create([
            'Naam' => 'Bestaande Verkoper',
            'SpecialeStatus' => 0,
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => true,
            'Opmerking' => 'Test',
        ]);

        return Stand::create([
            'StandType' => 'AA',
            'VerkoperId' => $verkoper->Id,
            'Prijs' => 600.00,
            'AantalDagen' => 2,
            'VerhuurdStatus' => $isVerhuurd,
            'IsActief' => true,
            'Opmerking' => 'Stand #AA-102 • Hal 1',
        ]);
    }

    /**
     * Happy scenario:
     * Gegeven ik ben ingelogd als verkoper
     * En er is een stand beschikbaar
     * Wanneer ik de stand kies en op "Reserveren" klik
     * Dan wordt de stand voor mij gereserveerd
     * En zie ik de melding "Stand is succesvol gereserveerd"
     */
    public function test_happy_scenario_verkoper_kan_beschikbare_stand_reserveren(): void
    {
        $verkoperUser = $this->createVerkoperUser();
        $beschikbareStand = $this->createTestStand(isVerhuurd: false);

        $this->assertFalse((bool)$beschikbareStand->VerhuurdStatus);

        // Wanneer de verkoper de stand kiest en op "Reserveren" klikt
        $response = $this->actingAs($verkoperUser)
            ->from('/stands')
            ->post("/stands/{$beschikbareStand->Id}/reserveren");

        // Dan wordt de gebruiker teruggestuurd naar de stands pagina
        $response->assertRedirect('/stands');

        // En zie ik de melding "Stand is succesvol gereserveerd"
        $response->assertSessionHas('success', 'Stand is succesvol gereserveerd');

        // Dan wordt de stand voor mij gereserveerd
        $beschikbareStand->refresh();
        $this->assertTrue((bool)$beschikbareStand->VerhuurdStatus);

        // De gekoppelde verkoper is nu de ingelogde verkoper
        $this->assertEquals('Sole Vault Rotterdam', $beschikbareStand->verkoper->Naam);
    }

    /**
     * Unhappy scenario:
     * Gegeven ik ben ingelogd als verkoper
     * En de stand is al gereserveerd
     * Wanneer ik de stand probeer te reserveren
     * Dan wordt de stand niet gereserveerd
     * En zie ik de melding "Deze stand is al gereserveerd"
     */
    public function test_unhappy_scenario_verkoper_kan_reeds_gereserveerde_stand_niet_reserveren(): void
    {
        $verkoperUser = $this->createVerkoperUser();
        $gereserveerdeStand = $this->createTestStand(isVerhuurd: true);
        $oorspronkelijkeVerkoperId = $gereserveerdeStand->VerkoperId;

        $this->assertTrue((bool)$gereserveerdeStand->VerhuurdStatus);

        // Wanneer de verkoper de stand probeer te reserveren
        $response = $this->actingAs($verkoperUser)
            ->from('/stands')
            ->post("/stands/{$gereserveerdeStand->Id}/reserveren");

        // Dan wordt de gebruiker teruggestuurd naar de stands pagina
        $response->assertRedirect('/stands');

        // En zie ik de melding "Deze stand is al gereserveerd"
        $response->assertSessionHas('error', 'Deze stand is al gereserveerd');

        // Dan wordt de stand NIET opnieuw gereserveerd en blijft de oorspronkelijke verkoper behouden
        $gereserveerdeStand->refresh();
        $this->assertTrue((bool)$gereserveerdeStand->VerhuurdStatus);
        $this->assertEquals($oorspronkelijkeVerkoperId, $gereserveerdeStand->VerkoperId);
    }

    /**
     * Test reserveren via de formulierkeuze (dropdown selectie) - Happy scenario
     */
    public function test_verkoper_kan_stand_reserveren_via_formulierkeuze(): void
    {
        $verkoperUser = $this->createVerkoperUser();
        $beschikbareStand = $this->createTestStand(isVerhuurd: false);

        $response = $this->actingAs($verkoperUser)
            ->from('/stands')
            ->post('/stands/reserveren', [
                'stand_id' => $beschikbareStand->Id,
            ]);

        $response->assertRedirect('/stands');
        $response->assertSessionHas('success', 'Stand is succesvol gereserveerd');

        $beschikbareStand->refresh();
        $this->assertTrue((bool)$beschikbareStand->VerhuurdStatus);
    }

    /**
     * Test reserveren via de formulierkeuze (dropdown selectie) - Unhappy scenario
     */
    public function test_verkoper_krijgt_melding_bij_formulierkeuze_voor_reeds_gereserveerde_stand(): void
    {
        $verkoperUser = $this->createVerkoperUser();
        $gereserveerdeStand = $this->createTestStand(isVerhuurd: true);

        $response = $this->actingAs($verkoperUser)
            ->from('/stands')
            ->post('/stands/reserveren', [
                'stand_id' => $gereserveerdeStand->Id,
            ]);

        $response->assertRedirect('/stands');
        $response->assertSessionHas('error', 'Deze stand is al gereserveerd');
    }

    /**
     * Test dat gastgebruiker wordt omgeleid naar inlogscherm
     */
    public function test_gast_kan_geen_stand_reserveren(): void
    {
        $stand = $this->createTestStand(isVerhuurd: false);

        $response = $this->post("/stands/{$stand->Id}/reserveren");

        $response->assertRedirect('/login');
    }

    /**
     * Test dat de standsoverzicht pagina de 'Reserveren' knop toont voor verkopers
     */
    public function test_stands_pagina_toont_reserveren_actie_voor_verkoper(): void
    {
        $verkoperUser = $this->createVerkoperUser();
        $stand = $this->createTestStand(isVerhuurd: false);

        $response = $this->actingAs($verkoperUser)->get('/stands');

        $response->assertStatus(200);
        $response->assertSee('Stand Reserveren');
        $response->assertSee('Reserveren');
    }
}
