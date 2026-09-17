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
        Schema::create('Stand', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('VerkoperId');
            $table->string('StandType', 10);
            $table->decimal('Prijs', 6, 2);
            $table->tinyInteger('AantalDagen');
            $table->boolean('VerhuurdStatus')->default(false);
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();

            $table->foreign('VerkoperId')->references('Id')->on('Verkoper')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Stand');
    }
};
