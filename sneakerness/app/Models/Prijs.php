<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prijs extends Model
{
    use HasFactory;

    protected $table = 'Prijs';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'EvenementId',
        'Datum',
        'Tijdslot',
        'Tarief',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'Datum' => 'date',
        'Tarief' => 'decimal:2',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class, 'EvenementId', 'Id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'PrijsId', 'Id');
    }
}
