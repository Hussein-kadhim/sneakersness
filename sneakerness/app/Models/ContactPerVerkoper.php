<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactPerVerkoper extends Model
{
    use HasFactory;

    protected $table = 'ContactPerVerkoper';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'VerkoperId',
        'ContactpersoonId',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    public function verkoper(): BelongsTo
    {
        return $this->belongsTo(Verkoper::class, 'VerkoperId', 'Id');
    }

    public function contactpersoon(): BelongsTo
    {
        return $this->belongsTo(Contactpersoon::class, 'ContactpersoonId', 'Id');
    }
}
