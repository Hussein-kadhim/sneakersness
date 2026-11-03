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
    }

    private function createVerkoperUser(string $name = 'Sole Vault Rotterdam'): User
    {
        return User::factory()->create([
            'name' => $name,
            'role' => 'verkoper',
            'email' => strtolower(str_replace(' ', '.', $name)) . '@sneakerness.com',
        ]);
    }

    private function createBezoekerUser(): User
    {
        return User::factory()->create([
            'name' => 'Bezoeker',
            'role' => 'bezoeker',
            'email' => 'bezoeker@sneakerness.com',
        ]);
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
    }
}
