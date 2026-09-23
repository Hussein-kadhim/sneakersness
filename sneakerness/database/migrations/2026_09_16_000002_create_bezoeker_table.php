<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de Bezoeker tabel
return new class extends Migration
{
    // Tabel 'Bezoeker' aanmaken
    public function up(): void
    {
        Schema::create('Bezoeker', function (Blueprint $table) {
            // Primaire sleutel
            $table->increments('Id');

            // Persoonsgegevens van de bezoeker
            $table->string('Naam', 100);
            $table->string('Email', 100)->unique();

            // Status en opmerkingen
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Aanmaak- en bewerkingsdatum
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();
        });
    }

    // Tabel 'Bezoeker' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('Bezoeker');
    }
};
