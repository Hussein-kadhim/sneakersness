<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model voor de stands op de beursvloer
class Stand extends Model
{
    use HasFactory;

    // Database tabel en primaire sleutel instellen
    protected $table = 'Stand';
    protected $primaryKey = 'Id';

    // Aangepaste kolomnamen voor timestamps
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // In te vullen velden
    protected $fillable = [
        'VerkoperId',
        'StandType',
        'Prijs',
        'AantalDagen',
        'VerhuurdStatus',
        'IsActief',
        'Opmerking',
    ];

    // Data types converteren
    protected $casts = [
        'Prijs' => 'decimal:2',
        'AantalDagen' => 'integer',
        'VerhuurdStatus' => 'boolean',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Relatie naar de verkoper die deze stand huurt
    public function verkoper(): BelongsTo
    {
        return $this->belongsTo(Verkoper::class, 'VerkoperId', 'Id');
    }

    // Volledige tekstweergave voor de huurperiode (bijv. 'Zaterdag' of 'Weekend')
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

    // Korte notatie van de dagen voor compacte weergave in tabellen
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

    // Locatielabel of standnummer samenstellen (bijv. 'Stand #AA-004')
    public function getLocationDisplayAttribute(): string
    {
        if (!empty($this->Opmerking)) {
            return $this->Opmerking;
        }
        return 'Stand #' . $this->StandType . '-' . str_pad($this->Id, 3, '0', STR_PAD_LEFT);
    }

    // Status tekst: verhuurd of nog beschikbaar
    public function getStatusLabelAttribute(): string
    {
        return $this->VerhuurdStatus ? 'Verhuurd' : 'Beschikbaar';
    }

    // CSS styling voor de status badge
    public function getStatusColorClassAttribute(): string
    {
        return $this->VerhuurdStatus
            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
            : 'bg-amber-50 text-amber-700 border border-amber-200';
    }

    // Kleurstelling per type stand (AA+, AA of standaard)
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->StandType) {
            'AA+' => 'bg-purple-50 text-purple-700 border border-purple-200',
            'AA'  => 'bg-blue-50 text-blue-700 border border-blue-200',
            default => 'bg-slate-100 text-slate-700 border border-slate-200',
        };
    }

    // Nette weergave van het bedrag in euro's (bijv. € 250,00)
    public function getFormattedPriceAttribute(): string
    {
        return '€ ' . number_format((float)$this->Prijs, 2, ',', '.');
    }
}
