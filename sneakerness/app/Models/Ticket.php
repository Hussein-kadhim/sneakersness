<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model voor de toegangstickets
class Ticket extends Model
{
    use HasFactory;

    // Database tabel en primaire sleutel instellen
    protected $table = 'Ticket';
    protected $primaryKey = 'Id';

    // Aangepaste kolomnamen voor timestamps
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Velden die ingevuld mogen worden
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

    // Typen converteren
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

    // Relatie naar het gekozen tarief / prijstype
    public function prijs(): BelongsTo
    {
        return $this->belongsTo(Prijs::class, 'PrijsId', 'Id');
    }

    // Nette weergave van de ticketcode met hekje (bijv. #TKT-84920)
    public function getTicketCodeDisplayAttribute(): string
    {
        $code = $this->TicketCode ?: ('TKT-' . (84919 + $this->Id));
        return str_starts_with($code, '#') ? $code : '#' . $code;
    }

    // Nette weergave van het bestelnummer
    public function getBestelnummerDisplayAttribute(): string
    {
        $order = $this->Bestelnummer ?: ('SN-2024-' . (84919 + $this->Id));
        return 'Bestelnr #' . ltrim($order, '#');
    }

    // Status label bepalen (standaard 'Geldig')
    public function getStatusLabelAttribute(): string
    {
        return $this->Status ?: 'Geldig';
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

    // Bepaalt welke actieknop getoond wordt ('Behandelen' bij problemen, anders 'Wijzigen')
    public function getActionSecondaryLabelAttribute(): string
    {
        return in_array($this->status_label, ['Aandacht nodig', 'Wijziging verzocht'])
            ? 'Behandelen'
            : 'Wijzigen';
    }

    // Styling voor de actieknop in de tabel
    public function getActionSecondaryColorClassAttribute(): string
    {
        return in_array($this->status_label, ['Aandacht nodig', 'Wijziging verzocht'])
            ? 'text-orange-500 hover:text-orange-600 font-semibold'
            : 'text-slate-500 hover:text-slate-900';
    }
}
