<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('Evenement', 'OrganisatorId')) {
            return;
        }

        Schema::table('Evenement', function (Blueprint $table) {
            $table->unsignedInteger('OrganisatorId');
            $table->foreign('OrganisatorId')
                ->references('Id')
                ->on('Organisator')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('Evenement', 'OrganisatorId')) {
            return;
        }

        Schema::table('Evenement', function (Blueprint $table) {
            $table->dropForeign(['OrganisatorId']);
            $table->dropColumn('OrganisatorId');
        });
    }
};
