<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de Contactpersoon tabel
return new class extends Migration
{
    // Tabel 'Contactpersoon' aanmaken
    public function up(): void
    {
        Schema::create('Contactpersoon', function (Blueprint $table) {
            // Primaire sleutel
            $table->increments('Id');

            // Contactgegevens
            $table->string('Naam', 100);
            $table->string('Telefoonnummer', 20);
            $table->string('Email', 100);

            // Status en opmerkingen (zoals functie of rol)
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Tijdstempels
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();
        });
    }

    // Tabel 'Contactpersoon' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('Contactpersoon');
    }
};
