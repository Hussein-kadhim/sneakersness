<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de Evenement tabel
return new class extends Migration
{
    // Tabel 'Evenement' aanmaken
    public function up(): void
    {
        Schema::create('Evenement', function (Blueprint $table) {
            // Gebruik de Id-naam die ook in het model en de relaties wordt verwacht.
            $table->increments('Id');

            // Evenement details
            $table->string('Naam', 100);
            $table->date('Datum');
            $table->string('Locatie', 150);
            $table->integer('AantalTicketsPerTijdslot');
            $table->integer('BeschikbareStands');

            // Status en opmerkingen
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Laravel gebruikt deze aangepaste namen voor aanmaak- en wijzigmomenten.
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();
        });
    }

    // Tabel 'Evenement' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('Evenement');
    }
};
