<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evenement extends Model
{
    use HasFactory;

    protected $table = 'Evenement';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

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

    protected $casts = [
        'Datum' => 'date',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    public function organisator(): BelongsTo
    {
        return $this->belongsTo(Organisator::class, 'OrganisatorId', 'Id');
    }

    public function prijzen(): HasMany
    {
        return $this->hasMany(Prijs::class, 'EvenementId', 'Id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'EvenementId', 'Id');
    }
}
