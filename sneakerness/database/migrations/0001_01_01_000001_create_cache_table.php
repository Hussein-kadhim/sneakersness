<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie voor de database cache tabellen van Laravel
return new class extends Migration
{
    // Cache tabellen aanmaken
    public function up(): void
    {
        // Opslag van gecachte data (sleutel, waarde en verloopdatum)
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->bigInteger('expiration')->index();
        });

        // Tabel voor atomische cache locks (voorkomt gelijktijdige bewerkingen)
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });
    }

    // Cache tabellen verwijderen bij een rollback
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
