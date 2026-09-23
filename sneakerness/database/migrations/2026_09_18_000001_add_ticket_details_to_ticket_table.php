<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migratie om extra ticketgegevens toe te voegen aan de Ticket tabel
return new class extends Migration
{
    // Extra kolommen toevoegen voor tickets
    public function up(): void
    {
        Schema::table('Ticket', function (Blueprint $table) {
            // Unieke ticket- en bestelreferenties
            $table->string('TicketCode', 50)->nullable()->after('Id');
            $table->string('Bestelnummer', 50)->nullable()->after('TicketCode');

            // Type ticket, tijdslot en zaalindeling
            $table->string('TicketType', 100)->nullable()->after('AantalTickets');
            $table->string('Tijdslot', 100)->nullable()->after('TicketType');
            $table->string('ZaalToegang', 100)->nullable()->after('Tijdslot');

            // Huidige status van het ticket (bijv. Geldig, Gescand of Aandacht nodig)
            $table->string('Status', 50)->default('Geldig')->after('ZaalToegang');
        });
    }

    // Toegevoegde kolommen verwijderen bij rollback
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
