<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Gasten kunnen de homepage bekijken en zien alleen 'Home' in de navigatiebalk.
     */
    public function test_guest_can_view_homepage_and_only_sees_home_in_navbar(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sneakerness Rotterdam');
        $response->assertSee('Home');
        $response->assertSee('Inloggen');
        $response->assertSee('Registreren');

        // Navigatie-items voor beheerders/rollen mogen NIET zichtbaar zijn voor gasten
        $response->assertDontSee('>Tickets<', false);
        $response->assertDontSee('>Verkopers<', false);
        $response->assertDontSee('>Contactpersonen<', false);
        $response->assertDontSee('>Stand huren<', false);
        $response->assertDontSee('>Events<', false);
        $response->assertDontSee('>Bezoekers<', false);
    }

    /**
     * Gasten worden geredirect naar login wanneer ze naar beveiligde overzichten gaan.
     */
    public function test_guests_are_redirected_to_login_when_accessing_protected_routes(): void
    {
        $this->get('/tickets')->assertRedirect('/login');
        $this->get('/verkopers')->assertRedirect('/login');
        $this->get('/contactpersonen')->assertRedirect('/login');
    }

    /**
     * Ingelogde gebruikers zien de overzichtslinks wel in de navigatiebalk.
     */
    public function test_authenticated_user_sees_role_navigation_items(): void
    {
        $user = User::factory()->create([
            'role' => 'organisator',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Tickets');
        $response->assertSee('Verkopers');
        $response->assertSee('Contactpersonen');
        $response->assertSee('Uitloggen');
    }
}
