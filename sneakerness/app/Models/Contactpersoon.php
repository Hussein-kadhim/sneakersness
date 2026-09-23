<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Model voor de contactpersonen van verkopers en stands
class Contactpersoon extends Model
{
    use HasFactory;

    // Database tabel en primaire sleutel instellen
    protected $table = 'Contactpersoon';
    protected $primaryKey = 'Id';

    // Aangepaste timestamp velden
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Te bewerken kolommen
    protected $fillable = [
        'Naam',
        'Telefoonnummer',
        'Email',
        'IsActief',
        'Opmerking',
    ];

    // Data types converteren
    protected $casts = [
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Veel-op-veel relatie met verkopers via de tussenliggende koppeltabel
    public function verkopers(): BelongsToMany
    {
        return $this->belongsToMany(
            Verkoper::class,
            'ContactPerVerkoper',
            'ContactpersoonId',
            'VerkoperId',
            'Id',
            'Id'
        )->withPivot(['IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    // Handige helper: haalt de eerste gekoppelde verkoper op
    public function getPrimaryVerkoperAttribute(): ?Verkoper
    {
        return $this->verkopers->first();
    }

    // Voornaam isoleren uit de volledige naam voor weergave
    public function getVoornaamAttribute(): string
    {
        $delen = explode(' ', trim($this->Naam), 2);
        return $delen[0] ?? '';
    }

    // Achternaam isoleren uit de volledige naam
    public function getAchternaamAttribute(): string
    {
        $delen = explode(' ', trim($this->Naam), 2);
        return $delen[1] ?? '';
    }

    // Subtitel badge op basis van het ID van de contactpersoon
    public function getSubtitleTagAttribute(): ?string
    {
        $tags = [
            1 => '(Standmanager)',
            2 => '(Eigenaar)',
            3 => '(Sales)',
            4 => '(Customizer)',
            5 => '(Barbier)',
        ];

        return $tags[$this->Id] ?? '';
    }

    // Rolweergave: gebruikt de opmerking als functie, anders standaard vertegenwoordiger
    public function getRoleDisplayAttribute(): string
    {
        return $this->Opmerking ?: 'Vertegenwoordiger';
    }

    // Status tekst bepalen voor de badges in het dashboard
    public function getStatusLabelAttribute(): string
    {
        if ($this->Id === 2) {
            return 'Bevestigd';
        }

        if ($this->Id === 3 || $this->Id === 6 || !$this->IsActief) {
            return 'In behandeling';
        }

        return 'Actief';
    }

    // Tailwind CSS klassen toekennen op basis van de huidige status
    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status_label) {
            'Actief' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Bevestigd' => 'bg-teal-50 text-teal-700 border-teal-200',
            'In behandeling' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
