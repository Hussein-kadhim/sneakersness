<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor de toegangsprijzen en tarieven per tijdslot
class Prijs extends Model
{
    use HasFactory;

    // Database koppeling
    protected $table = 'Prijs';
    protected $primaryKey = 'Id';

    // Aangepaste timestamp velden
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // In te vullen velden
    protected $fillable = [
        'EvenementId',
        'Datum',
        'Tijdslot',
        'Tarief',
        'IsActief',
        'Opmerking',
    ];

    // Typen casten (bijv. bedrag naar 2 decimalen)
    protected $casts = [
        'Datum' => 'date',
        'Tarief' => 'decimal:2',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Relatie terug naar het bijbehorende evenement
    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class, 'EvenementId', 'Id');
    }

    // Relatie naar alle tickets die voor dit specifieke tarief verkocht zijn
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'PrijsId', 'Id');
    }
}
