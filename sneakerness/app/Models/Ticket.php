<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model voor de toegangstickets
class Ticket extends Model
{
    use HasFactory;

    protected $table = 'Ticket';

    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';

    const UPDATED_AT = 'DatumGewijzigd';

    // Exact de kolommen uit de entiteit specificatie inclusief ticket details
    protected $fillable = [
        'TicketCode',
        'Bestelnummer',
        'BezoekerId',
        'EvenementId',
        'PrijsId',
        'AantalTickets',
        'TicketType',
        'Tijdslot',
        'ZaalToegang',
        'Status',
        'Datum',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'Datum' => 'date',
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Relatie naar de bezoeker die het ticket heeft gekocht
    public function bezoeker(): BelongsTo
    {
        return $this->belongsTo(Bezoeker::class, 'BezoekerId', 'Id');
    }

    // Relatie naar het evenement waar het ticket voor geldt
    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class, 'EvenementId', 'Id');
    }

    // Relatie naar het gekozen tarief
    public function prijs(): BelongsTo
    {
        return $this->belongsTo(Prijs::class, 'PrijsId', 'Id');
    }

    // Nette weergave van de ticketcode met hekje (bijv. #TKT-84920)
    public function getTicketCodeDisplayAttribute(): string
    {
        $code = $this->TicketCode ?: ('TKT-'.(84919 + ($this->Id ?? 1)));

        return str_starts_with($code, '#') ? $code : '#'.$code;
    }

    // Nette weergave van het bestelnummer
    public function getBestelnummerDisplayAttribute(): string
    {
        $order = $this->Bestelnummer ?: ('SN-2024-'.(84919 + ($this->Id ?? 1)));

        return 'Bestelnr #'.ltrim($order, '#');
    }

    // Status label bepalen (standaard 'Geldig')
    public function getStatusLabelAttribute(): string
    {
        return $this->Status ?: ($this->IsActief ? 'Geldig' : 'Inactief');
    }

    // Badge kleur bepalen op basis van de ticketstatus
    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status_label) {
            'Geldig' => 'bg-emerald-50 text-emerald-700',
            'Aandacht nodig' => 'bg-amber-50 text-amber-700',
            'Wijziging verzocht' => 'bg-blue-50 text-blue-700',
            'Gescand' => 'bg-teal-50 text-teal-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    // Bepaalt welke actieknop getoond wordt
    public function getActionSecondaryLabelAttribute(): string
    {
        return 'Wijzigen';
    }

    // Styling voor de actieknop in de tabel
    public function getActionSecondaryColorClassAttribute(): string
    {
        return 'text-orange-500 hover:text-orange-600 font-semibold';
    }

    /**
     * Werkt het ticket en optioneel de bezoekergegevens bij in de database.
     */
    public function updateTicket(array $data): bool
    {
        // 1. Bezoeker bijwerken indien aanwezig
        if ($this->bezoeker && (isset($data['bezoeker_naam']) || isset($data['bezoeker_email']) || isset($data['naam']) || isset($data['email']))) {
            $this->bezoeker->update([
                'Naam' => $data['bezoeker_naam'] ?? $data['naam'] ?? $this->bezoeker->Naam,
                'Emailadres' => $data['bezoeker_email'] ?? $data['email'] ?? $this->bezoeker->Emailadres,
            ]);
        }

        // 2. Ticket attributen bijwerken
        $attributes = [];

        if (isset($data['ticket_type'])) {
            $attributes['TicketType'] = $data['ticket_type'];
        }
        if (isset($data['tijdslot'])) {
            $attributes['Tijdslot'] = $data['tijdslot'];
        }
        if (isset($data['zaal_toegang']) || isset($data['zaal'])) {
            $attributes['ZaalToegang'] = $data['zaal_toegang'] ?? $data['zaal'];
        }
        if (isset($data['status'])) {
            $attributes['Status'] = $data['status'];
            $attributes['IsActief'] = $data['status'] !== 'Inactief';
        }
        if (isset($data['aantal_tickets'])) {
            $attributes['AantalTickets'] = (int) $data['aantal_tickets'];
        }
        if (isset($data['evenement_id'])) {
            $attributes['EvenementId'] = (int) $data['evenement_id'];
        }
        if (isset($data['datum'])) {
            $attributes['Datum'] = $data['datum'];
        }
        if (isset($data['opmerking'])) {
            $attributes['Opmerking'] = $data['opmerking'];
        }

        return $this->update($attributes);
    }
}
