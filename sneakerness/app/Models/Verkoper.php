<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor de verkopers en standhouders op Sneakerness
class Verkoper extends Model
{
    use HasFactory;

    // Database tabel en primaire sleutel instellen
    protected $table = 'Verkoper';
    protected $primaryKey = 'Id';

    // Aangepaste kolomnamen voor timestamps
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Toegestane invoervelden
    protected $fillable = [
        'Naam',
        'SpecialeStatus',
        'VerkooptSoort',
        'Logo',
        'IsActief',
        'Opmerking',
    ];

    // Data types converteren
    protected $casts = [
        'SpecialeStatus' => 'boolean',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Relatie: een verkoper kan meerdere stands huren of bezitten
    public function stands(): HasMany
    {
        return $this->hasMany(Stand::class, 'VerkoperId', 'Id');
    }

    // Veel-op-veel relatie met contactpersonen via de koppeltabel
    public function contactpersonen(): BelongsToMany
    {
        return $this->belongsToMany(
            Contactpersoon::class,
            'ContactPerVerkoper',
            'VerkoperId',
            'ContactpersoonId',
            'Id',
            'Id'
        )->withPivot(['IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    // Handige helper: haalt de eerste stand van deze verkoper op
    public function getPrimaryStandAttribute(): ?Stand
    {
        return $this->stands->first();
    }

    // Handige helper: haalt de primaire contactpersoon op
    public function getPrimaryContactAttribute(): ?Contactpersoon
    {
        return $this->contactpersonen->first();
    }

    // Status label bepalen voor de weergave in tabellen en kaarten
    public function getStatusLabelAttribute(): string
    {
        if (!empty($this->Opmerking) && in_array($this->Opmerking, ['Actief', 'Bevestigd', 'In behandeling', 'Inactief'])) {
            return $this->Opmerking;
        }

        if (!$this->IsActief) {
            return 'Inactief';
        }

        $stand = $this->primary_stand;
        if ($stand && $stand->VerhuurdStatus) {
            return 'Actief';
        }

        return 'In behandeling';
    }

    // CSS badge kleurstelling voor de status van de verkoper
    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status_label) {
            'Actief' => 'bg-emerald-50 text-emerald-600',
            'Bevestigd' => 'bg-emerald-50 text-emerald-600',
            'In behandeling' => 'bg-amber-50 text-amber-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    // Badge styling toekennen op basis van het type assortiment (bijv. sneakers, kleding, custom)
    public function getCategoryColorClassAttribute(): string
    {
        $category = strtolower($this->VerkooptSoort ?? '');
        if (str_contains($category, 'sneaker')) {
            return 'bg-blue-100 text-blue-800';
        }
        if (str_contains($category, 'streetwear') || str_contains($category, 'apparel')) {
            return 'bg-indigo-100 text-indigo-800';
        }
        if (str_contains($category, 'custom')) {
            return 'bg-orange-100 text-orange-800';
        }
        if (str_contains($category, 'tattoo') || str_contains($category, 'barber')) {
            return 'bg-purple-100 text-purple-800';
        }
        return 'bg-slate-100 text-slate-700';
    }
}
