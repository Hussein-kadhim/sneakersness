<?php

namespace Tests\Feature;

use App\Models\ContactPerVerkoper;
use App\Models\Contactpersoon;
use App\Models\User;
use App\Models\Verkoper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactpersoonKoppelenTest extends TestCase
{
    use RefreshDatabase;

    private function getOrganisator(): User
    {
        $organisator = User::where('email', 'organisator@sneakerness.com')->first();
        if (!$organisator) {
            $organisator = User::factory()->create([
                'role' => 'organisator',
                'email' => 'organisator@sneakerness.com',
            ]);
        }
        return $organisator;
    }

    /**
     * Test Happy Scenario: Organisator bezoekt het koppelingsscherm,
     * selecteert een contactpersoon en een verkoper en slaat de koppeling op.
     * De koppeling wordt opgeslagen in de database (ContactPerVerkoper),
     * er verschijnt een succesmelding en de gebruiker wordt teruggestuurd naar het overzicht.
     */
    public function test_happy_scenario_organisator_can_link_contactpersoon_to_verkoper_successfully(): void
    {
        $organisator = $this->getOrganisator();

        $verkoper = Verkoper::create([
            'Naam' => 'Sneaker District Rotterdam',
            'VerkooptSoort' => 'Sneakers',
            'IsActief' => 1,
        ]);

        $contactpersoon = Contactpersoon::create([
            'Naam' => 'Lars van der Berg',
            'Emailadres' => 'lars@sneakerdistrict.nl',
            'Telefoonnummer' => '06-11223344',
            'IsActief' => 1,
        ]);

        // 1. Scherm bezoeken
        $response = $this->actingAs($organisator)->get('/contactpersonen/koppelen');
        $response->assertStatus(200);
        $response->assertSee('Contactpersoon aan verkoper koppelen');
        $response->assertSee('Koppeling opslaan');

        // 2. Koppeling opslaan
        $postResponse = $this->actingAs($organisator)->post('/contactpersonen/koppelen', [
            'contactpersoon_id' => $contactpersoon->Id,
            'verkoper_id'       => $verkoper->Id,
            'rol'               => 'Standmanager',
            'is_primair'        => '1',
        ]);

        // 3. Redirect en succesmelding verifiëren
        $postResponse->assertRedirect('/contactpersonen');
        $postResponse->assertSessionHas('success', 'Contactpersoon succesvol gekoppeld aan de verkoper');

        // 4. Controleren in database
        $this->assertDatabaseHas('ContactPerVerkoper', [
            'VerkoperId'       => $verkoper->Id,
            'ContactpersoonId' => $contactpersoon->Id,
            'IsActief'         => 1,
        ]);

        // 5. Controleren op overzichtspagina
        $followResponse = $this->actingAs($organisator)->get('/contactpersonen');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Contactpersoon succesvol gekoppeld aan de verkoper');
    }

    /**
     * Test Unhappy Scenario: Organisator verstuurt het formulier zonder
     * verkoper of contactpersoon te selecteren.
     * Er worden foutmeldingen getoond en er wordt niets opgeslagen.
     */
    public function test_unhappy_scenario_validation_error_when_selection_is_missing(): void
    {
        $organisator = $this->getOrganisator();

        $response = $this->actingAs($organisator)->post('/contactpersonen/koppelen', [
            'contactpersoon_id' => '',
            'verkoper_id'       => '',
        ]);

        $response->assertSessionHasErrors(['general', 'contactpersoon_id', 'verkoper_id']);
        $this->assertEquals(0, ContactPerVerkoper::count());
    }

    /**
     * Test Beveiliging: Gastgebruiker (niet ingelogd) kan het scherm niet bezoeken.
     */
    public function test_guest_cannot_access_koppelen_page(): void
    {
        $response = $this->get('/contactpersonen/koppelen');
        $response->assertRedirect('/login');
    }
}
