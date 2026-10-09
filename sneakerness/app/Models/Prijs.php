<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor de toegangsprijzen en tarieven per tijdslot
class Prijs extends Model
{
    use HasFactory;

    protected $table = 'Prijs';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'Datum',
        'Tijdslot',
        'Tarief',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'Datum'           => 'date',
        'Tarief'          => 'decimal:2',
        'IsActief'        => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd'  => 'datetime',
    ];

    // Relatie naar alle tickets die voor dit tarief verkocht zijn
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'PrijsId', 'Id');
    }
}
