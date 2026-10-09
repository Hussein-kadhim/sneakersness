<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Koppeltabel (pivot) model tussen verkopers en contactpersonen
class ContactPerVerkoper extends Model
{
    use HasFactory;

    // Tabel en primaire sleutel
    protected $table = 'ContactPerVerkoper';
    protected $primaryKey = 'Id';

    // Aangepaste timestamp kolommen
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // In te vullen velden
    protected $fillable = [
        'VerkoperId',
        'ContactpersoonId',
        'IsActief',
        'Opmerking',
    ];

    // Automatische typecasting
    protected $casts = [
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Relatie terug naar de verkoper
    public function verkoper(): BelongsTo
    {
        return $this->belongsTo(Verkoper::class, 'VerkoperId', 'Id');
    }

    // Relatie terug naar de gekoppelde contactpersoon
    public function contactpersoon(): BelongsTo
    {
        return $this->belongsTo(Contactpersoon::class, 'ContactpersoonId', 'Id');
    }
}
