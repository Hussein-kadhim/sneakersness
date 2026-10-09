<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie om de rolkolom toe te voegen aan de users tabel
return new class extends Migration
{
    // 'role' kolom toevoegen aan 'users'
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rol van de gebruiker (standaard 'bezoeker', kan ook 'organisator' of 'verkoper' zijn)
            $table->string('role', 30)->default('bezoeker')->after('email');
        });
    }

    // 'role' kolom verwijderen bij rollback
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
