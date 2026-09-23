<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model voor de 'Stand' tabel in de database.
 */
class Stand extends Model
{
    use HasFactory;

    // Database tabel en primaire sleutel koppeling
    protected $table = 'Stand';
    protected $primaryKey = 'Id';

    // Aangepaste timestamp kolomnamen
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Velden die via mass assignment gevuld mogen worden
    protected $fillable = [
        'VerkoperId',
        'StandType',
        'Prijs',
        'AantalDagen',
        'VerhuurdStatus',
        'IsActief',
        'Opmerking',
    ];

    // Type-casting voor database attributen
    protected $casts = [
        'Prijs' => 'decimal:2',
        'AantalDagen' => 'integer',
        'VerhuurdStatus' => 'boolean',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    /**
     * Relatie: Een stand hoort bij een Verkoper (optioneel, kan leeg zijn als de stand nog niet verhuurd is).
     */
    public function verkoper(): BelongsTo
    {
        return $this->belongsTo(Verkoper::class, 'VerkoperId', 'Id');
    }

    /**
     * Accessor: Geeft een leesbare weergave van de dagen dat de stand bezet is.
     */
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

    /**
     * Accessor: Korte versie van het aantal dagen voor compacte weergaves (mobiel/badges).
     */
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

    /**
     * Accessor: Bepaalt de weer te geven standlocatie / omschrijving (bijv. hal/rij nummer).
     */
    public function getLocationDisplayAttribute(): string
    {
        if (!empty($this->Opmerking)) {
            return $this->Opmerking;
        }
        return 'Stand #' . $this->StandType . '-' . str_pad($this->Id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor: Geeft het tekstlabel voor de verhuurstatus ('Verhuurd' of 'Beschikbaar').
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->VerhuurdStatus ? 'Verhuurd' : 'Beschikbaar';
    }

    /**
     * Accessor: Geeft de CSS Tailwind badge-klassen terug op basis van de verhuurstatus.
     */
    public function getStatusColorClassAttribute(): string
    {
        return $this->VerhuurdStatus
            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
            : 'bg-amber-50 text-amber-700 border border-amber-200';
    }

    /**
     * Accessor: Geeft de CSS Tailwind badge-klassen terug voor het type stand (AA+, AA of A).
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->StandType) {
            'AA+' => 'bg-purple-50 text-purple-700 border border-purple-200',
            'AA'  => 'bg-blue-50 text-blue-700 border border-blue-200',
            default => 'bg-slate-100 text-slate-700 border border-slate-200',
        };
    }

    /**
     * Accessor: Formatteert de prijs netjes in eurovaluta (bijv. € 450,00).
     */
    public function getFormattedPriceAttribute(): string
    {
        return '€ ' . number_format((float)$this->Prijs, 2, ',', '.');
    }
}

