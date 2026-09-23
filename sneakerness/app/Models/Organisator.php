<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor de organisatoren van het Sneakerness platform
class Organisator extends Model
{
    use HasFactory;

    // Database tabel en primaire sleutel instellen
    protected $table = 'Organisator';
    protected $primaryKey = 'Id';

    // Aangepaste kolomnamen voor timestamps
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Toegestane invoervelden
    protected $fillable = [
        'Naam',
        'Gebruikersnaam',
        'Wachtwoord',
        'IsActief',
        'Opmerking',
    ];

    // Wachtwoord nooit tonen in arrays of JSON responses
    protected $hidden = [
        'Wachtwoord',
    ];

    // Kolommen automatisch naar het juiste type casten
    protected $casts = [
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Relatie: een organisator kan meerdere evenementen organiseren
    public function evenementen(): HasMany
    {
        return $this->hasMany(Evenement::class, 'OrganisatorId', 'Id');
    }
}
