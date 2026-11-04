<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('Stand') && ! Schema::hasColumn('Stand', 'AantalDagen')) {
            Schema::table('Stand', function (Blueprint $table) {
                $table->tinyInteger('AantalDagen')->default(1)->after('Prijs');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('Stand') && Schema::hasColumn('Stand', 'AantalDagen')) {
            Schema::table('Stand', function (Blueprint $table) {
                $table->dropColumn('AantalDagen');
            });
        }
    }
};

