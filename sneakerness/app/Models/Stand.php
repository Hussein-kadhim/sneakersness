<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stand extends Model
{
    use HasFactory;

    protected $table = 'Stand';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'VerkoperId',
        'StandType',
        'Prijs',
        'AantalDagen',
        'VerhuurdStatus',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'Prijs' => 'decimal:2',
        'AantalDagen' => 'integer',
        'VerhuurdStatus' => 'boolean',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    public function verkoper(): BelongsTo
    {
        return $this->belongsTo(Verkoper::class, 'VerkoperId', 'Id');
    }

    public function getDaysTextAttribute(): string
    {
        if ($this->AantalDagen == 1) {
            return 'Zaterdag';
        }
        if (in_array($this->VerkoperId, [3, 5])) {
            return 'Weekend';
        }
        return '2 dagen';
    }

    public function getDaysShortAttribute(): string
    {
        if ($this->AantalDagen == 1) {
            return 'Za';
        }
        if (in_array($this->VerkoperId, [3, 5])) {
            return 'Wknd';
        }
        return '2d';
    }

    public function getLocationDisplayAttribute(): string
    {
        if (!empty($this->Opmerking)) {
            return $this->Opmerking;
        }
        return 'Stand #' . $this->StandType . '-' . str_pad($this->Id, 3, '0', STR_PAD_LEFT);
    }
}
