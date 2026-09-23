<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisator extends Model
{
    use HasFactory;

    protected $table = 'Organisator';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'Naam',
        'Gebruikersnaam',
        'Wachtwoord',
        'IsActief',
        'Opmerking',
    ];

    protected $hidden = [
        'Wachtwoord',
    ];

    protected $casts = [
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    public function evenementen(): HasMany
    {
        return $this->hasMany(Evenement::class, 'OrganisatorId', 'Id');
    }
}
