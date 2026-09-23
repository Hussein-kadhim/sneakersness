<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor de Sneakerness evenementen
class Evenement extends Model
{
    use HasFactory;

    // Database tabel en primaire sleutel instellen
    protected $table = 'Evenement';
    protected $primaryKey = 'Id';

    // Aangepaste timestamp velden
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Toegestane kolommen voor invoer
    protected $fillable = [
        'OrganisatorId',
        'Naam',
        'Datum',
        'Locatie',
        'AantalTicketsPerTijdslot',
        'BeschikbareStands',
        'IsActief',
        'Opmerking',
    ];

    // Data types formatteren en casten
    protected $casts = [
        'Datum' => 'date',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Relatie naar de organisator die dit evenement beheert
    public function organisator(): BelongsTo
    {
        return $this->belongsTo(Organisator::class, 'OrganisatorId', 'Id');
    }

    // Relatie met de verschillende prijzen en tijdsloten per evenement
    public function prijzen(): HasMany
    {
        return $this->hasMany(Prijs::class, 'EvenementId', 'Id');
    }

    // Relatie met alle verkochte tickets voor dit evenement
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'EvenementId', 'Id');
    }
}
