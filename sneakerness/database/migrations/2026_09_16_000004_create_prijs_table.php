<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de Prijs tabel (tarieven per tijdslot)
return new class extends Migration
{
    // Tabel 'Prijs' aanmaken
    public function up(): void
    {
        Schema::create('Prijs', function (Blueprint $table) {
            // Primaire sleutel
            $table->increments('Id');

            // Datum, tijdstip en tarief
            $table->date('Datum');
            $table->time('Tijdslot');
            $table->decimal('Tarief', 6, 2);

            // Status en opmerkingen
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Tijdstempels
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();
        });
    }

    // Tabel 'Prijs' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('Prijs');
    }
};
