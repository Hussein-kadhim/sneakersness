<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor het aanmaken van de Ticket tabel
return new class extends Migration
{
    // Tabel 'Ticket' aanmaken
    public function up(): void
    {
        Schema::create('Ticket', function (Blueprint $table) {
            // Primaire sleutel
            $table->increments('Id');

            // Koppelingen naar bezoeker, evenement en prijs
            $table->unsignedInteger('BezoekerId');
            $table->unsignedInteger('EvenementId');
            $table->unsignedInteger('PrijsId');

            // Ticket specificaties
            $table->integer('AantalTickets');
            $table->date('Datum');

            // Status en opmerkingen
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();

            // Tijdstempels
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();

            // Foreign key relaties
            $table->foreign('BezoekerId')->references('Id')->on('Bezoeker')->onDelete('cascade');
            $table->foreign('EvenementId')->references('Id')->on('Evenement')->onDelete('cascade');
            $table->foreign('PrijsId')->references('Id')->on('Prijs')->onDelete('cascade');
        });
    }

    // Tabel 'Ticket' verwijderen bij rollback
    public function down(): void
    {
        Schema::dropIfExists('Ticket');
    }
};
