<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'Ticket';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

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
        'Datum' => 'date',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    public function bezoeker(): BelongsTo
    {
        return $this->belongsTo(Bezoeker::class, 'BezoekerId', 'Id');
    }

    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class, 'EvenementId', 'Id');
    }

    public function prijs(): BelongsTo
    {
        return $this->belongsTo(Prijs::class, 'PrijsId', 'Id');
    }
}
