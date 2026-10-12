<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor de Sneakerness evenementen
class Evenement extends Model
{
    use HasFactory;

    protected $table = 'Evenement';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'Naam',
        'Datum',
        'Locatie',
        'AantalTicketsPerTijdslot',
        'BeschikbareStands',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'Datum'           => 'date',
        'IsActief'        => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd'  => 'datetime',
    ];

    // Relatie met alle tickets voor dit evenement
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'EvenementId', 'Id');
    }
}
