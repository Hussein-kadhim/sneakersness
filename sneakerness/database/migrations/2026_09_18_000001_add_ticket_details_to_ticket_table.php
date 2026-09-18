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
        Schema::table('Ticket', function (Blueprint $table) {
            $table->string('TicketCode', 50)->nullable()->after('Id');
            $table->string('Bestelnummer', 50)->nullable()->after('TicketCode');
            $table->string('TicketType', 100)->nullable()->after('AantalTickets');
            $table->string('Tijdslot', 100)->nullable()->after('TicketType');
            $table->string('ZaalToegang', 100)->nullable()->after('Tijdslot');
            $table->string('Status', 50)->default('Geldig')->after('ZaalToegang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Ticket', function (Blueprint $table) {
            $table->dropColumn([
                'TicketCode',
                'Bestelnummer',
                'TicketType',
                'Tijdslot',
                'ZaalToegang',
                'Status',
            ]);
        });
    }
};
