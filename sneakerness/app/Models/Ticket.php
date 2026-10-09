<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model voor de toegangstickets
class Ticket extends Model
{
    use HasFactory;

    protected $table = 'Ticket';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Exact de kolommen uit de entiteit specificatie
    protected $fillable = [
        'BezoekerId',
        'EvenementId',
        'PrijsId',
        'AantalTickets',
        'Datum',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'Datum'           => 'date',
        'IsActief'        => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd'  => 'datetime',
    ];

    // Relatie naar de bezoeker die het ticket heeft gekocht
    public function bezoeker(): BelongsTo
    {
        return $this->belongsTo(Bezoeker::class, 'BezoekerId', 'Id');
    }

    // Relatie naar het evenement waar het ticket voor geldt
    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class, 'EvenementId', 'Id');
    }

    // Relatie naar het gekozen tarief
    public function prijs(): BelongsTo
    {
        return $this->belongsTo(Prijs::class, 'PrijsId', 'Id');
    }
}
