<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Authenticatiemodel voor ingelogde gebruikers (Organisator, Verkoper of Bezoeker)
#[Fillable(['name', 'email', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Automatische typecasting voor datum en gehasht wachtwoord
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Controleert of de ingelogde gebruiker een organisator is
    public function isOrganisator(): bool
    {
        return $this->role === 'organisator';
    }

    // Controleert of de ingelogde gebruiker een verkoper / standhouder is
    public function isVerkoper(): bool
    {
        return $this->role === 'verkoper';
    }

    // Controleert of de ingelogde gebruiker een reguliere bezoeker is
    public function isBezoeker(): bool
    {
        return $this->role === 'bezoeker';
    }
}
