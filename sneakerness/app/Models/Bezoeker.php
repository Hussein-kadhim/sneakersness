<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor bezoekers van het evenement
class Bezoeker extends Model
{
    use HasFactory;

    // Koppeling naar de tabel en primaire sleutel in de database
    protected $table = 'Bezoeker';
    protected $primaryKey = 'Id';

    // Aangepaste kolomnamen voor aanmaak- en bewerkdatum
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Velden die via mass-assignment ingevuld mogen worden
    protected $fillable = [
        'Naam',
        'Email',
        'IsActief',
        'Opmerking',
    ];

    // Data types automatisch casten
    protected $casts = [
        'IsActief' => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd' => 'datetime',
    ];

    // Relatie: een bezoeker kan meerdere tickets hebben
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'BezoekerId', 'Id');
    }
}
