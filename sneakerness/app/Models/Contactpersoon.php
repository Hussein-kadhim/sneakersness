<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contactpersoon extends Model
{
    use HasFactory;

    protected $table = 'Contactpersoon';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'Naam',
        'Telefoonnummer',
        'Email',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

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

    public function getPrimaryVerkoperAttribute(): ?Verkoper
    {
        return $this->verkopers->first();
    }

    public function getSubtitleTagAttribute(): ?string
    {
        return match ($this->Id) {
            1 => '(Standmanager)',
            2 => '(Eigenaar)',
            3 => '(Sales)',
            4 => '(Customizer)',
            5 => '',
            6 => '(Barbier)',
            default => '',
        };
    }

    public function getRoleDisplayAttribute(): string
    {
        return match ($this->Id) {
            1 => 'Hoofdcontactpersoon',
            2 => 'Eigenaar',
            3 => 'Standhouder',
            4 => 'Eigenaar & Artiest',
            5 => 'Teamleider',
            6 => 'Assistent',
            default => $this->Opmerking ?: 'Vertegenwoordiger',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->Id === 2) {
            return 'Bevestigd';
        }
        if ($this->Id === 3 || $this->Id === 6) {
            return 'In behandeling';
        }
        if (!$this->IsActief) {
            return 'Inactief';
        }
        return 'Actief';
    }

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
