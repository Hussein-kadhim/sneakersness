<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de Organisator tabel
return new class extends Migration
{
    // Tabel 'Organisator' aanmaken
    public function up(): void
    {
        Schema::create('Organisator', function (Blueprint $table) {
            // Primaire sleutel met auto-increment
            $table->increments('Id');

            // Naam en inloggegevens van de organisator
            $table->string('Naam', 100);
            $table->string('Gebruikersnaam', 100)->unique();
            $table->string('Wachtwoord', 255);

            // Status en eventuele opmerkingen
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Aanmaak- en bewerkingsdatum met microseconde precisie
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();
        });
    }

    // Tabel 'Organisator' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('Organisator');
    }
};
