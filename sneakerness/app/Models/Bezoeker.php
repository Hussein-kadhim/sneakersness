<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model voor bezoekers van het evenement
class Bezoeker extends Model
{
    use HasFactory;

    protected $table = 'Bezoeker';
    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'Naam',
        'Emailadres',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'IsActief'        => 'boolean',
        'DatumAangemaakt' => 'datetime',
        'DatumGewijzigd'  => 'datetime',
    ];

    // Relatie: een bezoeker kan meerdere tickets hebben
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'BezoekerId', 'Id');
    }
}
