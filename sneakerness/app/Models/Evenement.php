<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor de Sneakerness evenementen
class Evenement extends Model
{
    use HasFactory;

    // De tabel en sleutel gebruiken hoofdletters en wijken daarmee af van Laravel-conventies.
    protected $table = 'Evenement';
    protected $primaryKey = 'Id';

    // Koppel Laravel-timestamps aan de kolomnamen van de evenemententabel.
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Velden die met create() en update() vanuit de evenementformulieren mogen worden opgeslagen.
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

    // Zet datum-, status- en tijdstempelwaarden automatisch om naar bruikbare PHP-types.
    protected $casts = [
        'Datum'           => 'date',
        'IsActief'        => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd'  => 'datetime',
    ];

    // Een evenement kan meerdere tickets hebben; EvenementId verwijst naar de Id van dit model.
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'EvenementId', 'Id');
    }
}
