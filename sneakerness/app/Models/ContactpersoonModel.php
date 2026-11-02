<?php

namespace App\Models;

// Model alias conform beoordelingsspecificatie
class ContactpersoonModel extends Contactpersoon
{
    /**
     * Verwijdert of ontkoppelt een contactpersoon van een verkoper.
     */
    public function deleteContactpersoon(?int $verkoperId = null): bool
    {
        return parent::deleteContactpersoon($verkoperId);
    }
}
