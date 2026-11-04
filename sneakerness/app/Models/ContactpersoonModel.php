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

    /**
     * Ontkoppelt een contactpersoon van een verkoper.
     * Methode conform specificatie: unlinkVerkoper() of loskoppelVerkoper()
     */
    public function unlinkVerkoper(?int $verkoperId = null): bool
    {
        return parent::unlinkVerkoper($verkoperId);
    }

    public function loskoppelVerkoper(?int $verkoperId = null): bool
    {
        return parent::loskoppelVerkoper($verkoperId);
    }

    /**
     * Ondersteunt ook statische aanroep conform diverse MVC patronen.
     */
    public static function __callStatic($method, $parameters)
    {
        if (in_array($method, ['deleteContactpersoon', 'unlinkVerkoper', 'loskoppelVerkoper'])) {
            $contactId = $parameters[0] ?? null;
            $verkoperId = $parameters[1] ?? null;
            if (is_numeric($contactId)) {
                $contact = self::findOrFail($contactId);

                return $contact->$method($verkoperId);
            }
        }

        return parent::__callStatic($method, $parameters);
    }
}
