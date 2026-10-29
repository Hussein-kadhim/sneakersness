<?php

namespace App\Models;

/**
 * Model VerkoperModel
 *
 * Ondersteunt de expliciet gevraagde naamgeving VerkoperModel
 * met behoud van alle Eloquent functionaliteit en methodes zoals updateVerkoper() en deleteVerkoper().
 */
class VerkoperModel extends Verkoper
{
    /**
     * Verwijdert een verkoper uit de administratie via VerkoperModel.
     */
    public function deleteVerkoper(bool $force = false): bool
    {
        return parent::deleteVerkoper($force);
    }
}
