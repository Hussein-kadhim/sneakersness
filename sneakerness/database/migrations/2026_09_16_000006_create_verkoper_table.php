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
        Schema::create('Verkoper', function (Blueprint $table) {
            $table->increments('Id');
            $table->string('Naam', 100);
            $table->boolean('SpecialeStatus')->default(false);
            $table->string('VerkooptSoort', 100);
            $table->string('Logo', 255)->nullable();
            $table->boolean('IsActief')->default(true);
            $table->string('Opmerking', 250)->nullable();
            $table->dateTime('DatumAangemaakt', 6)->useCurrent();
            $table->dateTime('DatumGewijzigd', 6)->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Verkoper');
    }
};
