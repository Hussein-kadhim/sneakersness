<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'Ticket';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

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

    public function bezoeker(): BelongsTo
    {
        return $this->belongsTo(Bezoeker::class, 'BezoekerId', 'Id');
    }

    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class, 'EvenementId', 'Id');
    }

    public function prijs(): BelongsTo
    {
        return $this->belongsTo(Prijs::class, 'PrijsId', 'Id');
    }

    public function getTicketCodeDisplayAttribute(): string
    {
        $code = $this->TicketCode ?: ('TKT-' . (84919 + $this->Id));
        return str_starts_with($code, '#') ? $code : '#' . $code;
    }

    public function getBestelnummerDisplayAttribute(): string
    {
        $order = $this->Bestelnummer ?: ('SN-2024-' . (84919 + $this->Id));
        return 'Bestelnr #' . ltrim($order, '#');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->Status ?: 'Geldig';
    }

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

    public function getActionSecondaryLabelAttribute(): string
    {
        return in_array($this->status_label, ['Aandacht nodig', 'Wijziging verzocht'])
            ? 'Behandelen'
            : 'Wijzigen';
    }

    public function getActionSecondaryColorClassAttribute(): string
    {
        return in_array($this->status_label, ['Aandacht nodig', 'Wijziging verzocht'])
            ? 'text-orange-500 hover:text-orange-600 font-semibold'
            : 'text-slate-500 hover:text-slate-900';
    }
}
