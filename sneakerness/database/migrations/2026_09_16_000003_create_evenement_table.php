<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Evenement', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('OrganisatorId');
            $table->string('Naam', 100);
            $table->date('Datum');
            $table->string('Locatie', 150);
            $table->integer('AantalTicketsPerTijdslot');
            $table->integer('BeschikbareStands');
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();

            $table->foreign('OrganisatorId')->references('Id')->on('Organisator')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Evenement');
    }
};
