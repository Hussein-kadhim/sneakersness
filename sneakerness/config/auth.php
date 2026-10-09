<?php

use App\Models\User;

return [

    // Standaard authenticatie-instellingen voor de applicatie
    // Guard bepaalt hoe gebruikers worden geauthenticeerd (sessies via 'web')
    // Passwords broker regelt het resetten van wachtwoorden
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    // Beschikbare authenticatie guards
    // De 'web' guard gebruikt browser sessies en het Eloquent User model
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    // User providers bepalen hoe gebruikersgegevens uit de database worden opgehaald
    // Hier gebruiken we Eloquent met het User model van de applicatie
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],
    ],

    // Configuratie voor het resetten van wachtwoorden
    // Opslag van tokens in de password_reset_tokens tabel met een geldigheid van 60 minuten
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    // Tijdslimiet (in seconden) waarna een gebruiker opnieuw zijn wachtwoord moet bevestigen
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
