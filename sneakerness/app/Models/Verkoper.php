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
        'StandType',
        'Dagen',
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
        if (! empty($this->Opmerking) && in_array($this->Opmerking, ['Actief', 'Bevestigd', 'In behandeling', 'Inactief'])) {
            return $this->Opmerking;
        }

        if (! $this->IsActief) {
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

    /**
     * Wijzigt de gegevens van de verkoper, inclusief gekoppelde contactpersoon en stand.
     */
    public function updateVerkoper(array $data): bool
    {
        $isActief = in_array(strtolower($data['status'] ?? 'actief'), ['actief', '1', 'bevestigd'], true);
        $specialeStatus = strtolower($data['verkoopt_soort'] ?? '') === 'partners' ? 1 : ($this->SpecialeStatus ? 1 : 0);

        $this->Naam = $data['naam'] ?? $this->Naam;
        $this->VerkooptSoort = $data['verkoopt_soort'] ?? $this->VerkooptSoort;
        $this->SpecialeStatus = $specialeStatus;
        if (isset($data['stand_type'])) {
            $this->StandType = $data['stand_type'];
        }
        if (isset($data['dagen'])) {
            $this->Dagen = $data['dagen'];
        }
        $this->IsActief = $isActief ? 1 : 0;
        if (isset($data['status'])) {
            $this->Opmerking = $data['status'];
        }

        $saved = $this->save();

        // Gekoppelde contactpersoon bijwerken
        if (! empty($data['contactpersoon_naam']) || ! empty($data['contactpersoon_email']) || ! empty($data['contactpersoon_telefoon'])) {
            $contact = $this->primary_contact;
            if ($contact) {
                if (! empty($data['contactpersoon_naam'])) {
                    $contact->Naam = $data['contactpersoon_naam'];
                }
                if (! empty($data['contactpersoon_email'])) {
                    $contact->Emailadres = $data['contactpersoon_email'];
                }
                if (! empty($data['contactpersoon_telefoon'])) {
                    $contact->Telefoonnummer = $data['contactpersoon_telefoon'];
                }
                $contact->save();
            } elseif (! empty($data['contactpersoon_naam']) && ! empty($data['contactpersoon_email'])) {
                $newContact = Contactpersoon::create([
                    'Naam' => $data['contactpersoon_naam'],
                    'Emailadres' => $data['contactpersoon_email'],
                    'Telefoonnummer' => $data['contactpersoon_telefoon'] ?? '',
                    'IsActief' => 1,
                    'Opmerking' => 'Contactpersoon '.$this->Naam,
                ]);
                $this->contactpersonen()->attach($newContact->Id, [
                    'IsActief' => 1,
                    'Opmerking' => 'Gekoppeld bij wijzigen verkoper',
                ]);
            }
        }

        // Gekoppelde stand bijwerken
        if (isset($data['stand_type']) || isset($data['stand_nummer']) || isset($data['opmerking'])) {
            $stand = $this->primary_stand;
            $prijsMap = [
                'AA+' => 450.00,
                'AA' => 300.00,
                'A' => 175.00,
            ];

            $standNummer = $data['stand_nummer'] ?? '';
            $extraOpmerking = $data['opmerking'] ?? '';
            $standOpmerking = $standNummer;
            if ($extraOpmerking !== '') {
                $standOpmerking = $standNummer !== '' ? ($standNummer.' • '.$extraOpmerking) : $extraOpmerking;
            }

            if ($stand) {
                if (isset($data['stand_type'])) {
                    $stand->StandType = $data['stand_type'];
                    $stand->Prijs = $prijsMap[$data['stand_type']] ?? $stand->Prijs;
                }
                if ($standOpmerking !== '') {
                    $stand->Opmerking = $standOpmerking;
                }
                $stand->VerhuurdStatus = $isActief ? 1 : 0;
                $stand->save();
            } elseif (! empty($data['stand_type'])) {
                Stand::create([
                    'VerkoperId' => $this->Id,
                    'StandType' => $data['stand_type'],
                    'Prijs' => $prijsMap[$data['stand_type']] ?? 250.00,
                    'VerhuurdStatus' => $isActief ? 1 : 0,
                    'IsActief' => 1,
                    'Opmerking' => $standOpmerking ?: 'Stand toewijzing',
                ]);
            }
        }

        return $saved;
    }

    /**
     * Controleert of de verkoper nog gekoppeld is aan één of meerdere contactpersonen of stands.
     */
    public function isGekoppeld(): bool
    {
        return $this->contactpersonen()->exists() || $this->stands()->exists();
    }

    /**
     * Verwijder een verkoper uit de administratie.
     * Retourneert false als de verkoper nog gekoppeld is en niet geforceerd mag worden.
     */
    public function deleteVerkoper(bool $force = false): bool
    {
        if (! $force && $this->isGekoppeld()) {
            return false;
        }

        // Koppelingen met contactpersonen en stands ontkoppelen/verwijderen
        $this->contactpersonen()->detach();
        $this->stands()->delete();

        return (bool) $this->delete();
    }
}
