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
        Schema::create('ContactPerVerkoper', function (Blueprint $table) {
            $table->increments('Id');
            $table->unsignedInteger('VerkoperId');
            $table->unsignedInteger('ContactpersoonId');
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();

            $table->foreign('VerkoperId')->references('Id')->on('Verkoper')->onDelete('cascade');
            $table->foreign('ContactpersoonId')->references('Id')->on('Contactpersoon')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ContactPerVerkoper');
    }
};
