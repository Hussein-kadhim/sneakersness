<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de Stand tabel
return new class extends Migration
{
    // Tabel 'Stand' aanmaken
    public function up(): void
    {
        Schema::create('Stand', function (Blueprint $table) {
            // Primaire sleutel
            $table->increments('Id');

            // Gekoppelde verkoper
            $table->unsignedInteger('VerkoperId');

            // Type stand (bijv. AA+, AA of A) en verhuurdetails
            $table->string('StandType', 10);
            $table->decimal('Prijs', 6, 2);
            $table->tinyInteger('AantalDagen');
            $table->boolean('VerhuurdStatus')->default(false);

            // Status en opmerkingen
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Tijdstempels
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();

            // Foreign key relatie met Verkoper
            $table->foreign('VerkoperId')->references('Id')->on('Verkoper')->onDelete('cascade');
        });
    }

    // Tabel 'Stand' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('Stand');
    }
};
