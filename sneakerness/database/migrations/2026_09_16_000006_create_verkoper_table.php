<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de Verkoper tabel
return new class extends Migration
{
    // Tabel 'Verkoper' aanmaken
    public function up(): void
    {
        Schema::create('Verkoper', function (Blueprint $table) {
            // Primaire sleutel
            $table->increments('Id');

            // Bedrijfsnaam en gegevens van de standhouder
            $table->string('Naam', 100);

            // SpecialeStatus: BIT-veld (bijv. 1 = partner)
            $table->boolean('SpecialeStatus')->default(false);

            // Soort verkoper: Sneakers, Eten en Drinken, Kids Corner, etc.
            $table->string('VerkooptSoort', 100);

            // Standtype: A, AA of AA+
            $table->string('StandType', 10)->nullable();

            // Aanwezigheidsdagen: Eén dag of Twee dagen
            $table->string('Dagen', 20)->nullable();

            // Logo: optioneel, alleen voor partners
            $table->string('Logo', 255)->nullable();

            // Status en opmerkingen
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Tijdstempels
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();
        });
    }

    // Tabel 'Verkoper' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('Verkoper');
    }
};
