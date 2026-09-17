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
        Schema::create('Ticket', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('BezoekerId');
            $table->unsignedInteger('EvenementId');
            $table->unsignedInteger('PrijsId');
            $table->integer('AantalTickets');
            $table->date('Datum');
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();

            $table->foreign('BezoekerId')->references('Id')->on('Bezoeker')->onDelete('cascade');
            $table->foreign('EvenementId')->references('Id')->on('Evenement')->onDelete('cascade');
            $table->foreign('PrijsId')->references('Id')->on('Prijs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Ticket');
    }
};
