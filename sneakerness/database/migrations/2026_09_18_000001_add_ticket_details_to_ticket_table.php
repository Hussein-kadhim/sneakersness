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
        if (! Schema::hasTable('Ticket')) {
            return;
        }

        Schema::table('Ticket', function (Blueprint $table) {
            if (! Schema::hasColumn('Ticket', 'TicketCode')) {
                $table->string('TicketCode', 50)->nullable()->after('Id');
            }
            if (! Schema::hasColumn('Ticket', 'Bestelnummer')) {
                $table->string('Bestelnummer', 50)->nullable()->after('TicketCode');
            }
            if (! Schema::hasColumn('Ticket', 'TicketType')) {
                $table->string('TicketType', 100)->nullable()->after('AantalTickets');
            }
            if (! Schema::hasColumn('Ticket', 'Tijdslot')) {
                $table->string('Tijdslot', 100)->nullable()->after('TicketType');
            }
            if (! Schema::hasColumn('Ticket', 'ZaalToegang')) {
                $table->string('ZaalToegang', 100)->nullable()->after('Tijdslot');
            }
            if (! Schema::hasColumn('Ticket', 'Status')) {
                $table->string('Status', 50)->default('Geldig')->after('ZaalToegang');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('Ticket')) {
            return;
        }

        Schema::table('Ticket', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['TicketCode', 'Bestelnummer', 'TicketType', 'Tijdslot', 'ZaalToegang', 'Status'] as $col) {
                if (Schema::hasColumn('Ticket', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

