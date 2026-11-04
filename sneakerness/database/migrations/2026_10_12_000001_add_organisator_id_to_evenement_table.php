<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Voorkom dat dezelfde foreign key bij een herhaalde migratie wordt toegevoegd.
        if (Schema::hasColumn('Evenement', 'OrganisatorId')) {
            return;
        }

        Schema::table('Evenement', function (Blueprint $table) {
            // Het datatype komt overeen met de Id-kolom in de organisatortabel.
            $table->unsignedInteger('OrganisatorId');
            // Verwijder een evenement automatisch als de gekoppelde organisator wordt verwijderd.
            $table->foreign('OrganisatorId')
                ->references('Id')
                ->on('Organisator')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Niets terugdraaien als de kolom ontbreekt, bijvoorbeeld na een gedeeltelijke rollback.
        if (! Schema::hasColumn('Evenement', 'OrganisatorId')) {
            return;
        }

        Schema::table('Evenement', function (Blueprint $table) {
            // Verwijder eerst de foreign key voordat de onderliggende kolom wordt verwijderd.
            $table->dropForeign(['OrganisatorId']);
            $table->dropColumn('OrganisatorId');
        });
    }
};
