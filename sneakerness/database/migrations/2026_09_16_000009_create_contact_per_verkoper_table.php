<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de koppeltabel ContactPerVerkoper
return new class extends Migration
{
    // Tabel 'ContactPerVerkoper' aanmaken
    public function up(): void
    {
        Schema::create('ContactPerVerkoper', function (Blueprint $table) {
            // Primaire sleutel
            $table->increments('Id');

            // Koppelingen naar de verkoper en contactpersoon
            $table->unsignedInteger('VerkoperId');
            $table->unsignedInteger('ContactpersoonId');

            // Status en opmerkingen
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Tijdstempels
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();

            // Foreign key relaties met cascade bij verwijdering
            $table->foreign('VerkoperId')->references('Id')->on('Verkoper')->onDelete('cascade');
            $table->foreign('ContactpersoonId')->references('Id')->on('Contactpersoon')->onDelete('cascade');
        });
    }

    // Tabel 'ContactPerVerkoper' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('ContactPerVerkoper');
    }
};
