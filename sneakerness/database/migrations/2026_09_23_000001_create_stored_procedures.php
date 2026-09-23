<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Criterium: Stored Procedures & Joins
     * Maakt alle stored procedures aan in MySQL voor stands, verkopers, tickets, contactpersonen en evenementen.
     */
    public function up(): void
    {
        // Alleen uitvoeren op MySQL / MariaDB (niet op SQLite in-memory test database)
        if (DB::getDriverName() === 'mysql') {
            // 1. sp_GetStandsMetVerkoper (INNER JOIN)
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetStandsMetVerkoper`;");
            DB::unprepared("
                CREATE PROCEDURE `sp_GetStandsMetVerkoper`()
                BEGIN
                    SELECT 
                        s.Id AS StandId,
                        s.StandType,
                        s.Prijs,
                        s.AantalDagen,
                        s.VerhuurdStatus,
                        s.Opmerking AS StandLocatie,
                        v.Id AS VerkoperId,
                        v.Naam AS VerkoperNaam,
                        v.VerkooptSoort,
                        v.Logo
                    FROM `Stand` s
                    INNER JOIN `Verkoper` v ON s.VerkoperId = v.Id
                    WHERE s.IsActief = 1
                    ORDER BY s.Id ASC;
                END
            ");

            // 2. sp_GetStandStatistieken
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetStandStatistieken`;");
            DB::unprepared("
                CREATE PROCEDURE `sp_GetStandStatistieken`()
                BEGIN
                    SELECT 
                        COUNT(*) AS TotaalStands,
                        SUM(CASE WHEN StandType = 'AA+' THEN 1 ELSE 0 END) AS AantalAAPlus,
                        SUM(CASE WHEN StandType = 'AA' THEN 1 ELSE 0 END) AS AantalAA,
                        SUM(CASE WHEN StandType = 'A' THEN 1 ELSE 0 END) AS AantalA,
                        SUM(CASE WHEN VerhuurdStatus = 1 THEN 1 ELSE 0 END) AS VerhuurdAantal,
                        SUM(CASE WHEN VerhuurdStatus = 0 THEN 1 ELSE 0 END) AS BeschikbaarAantal
                    FROM `Stand`
                    WHERE IsActief = 1;
                END
            ");

            // 3. sp_GetTicketsMetDetails (Meervoudige INNER JOIN)
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetTicketsMetDetails`;");
            DB::unprepared("
                CREATE PROCEDURE `sp_GetTicketsMetDetails`()
                BEGIN
                    SELECT 
                        t.Id AS TicketId,
                        t.TicketCode,
                        t.Bestelnummer,
                        t.TicketType,
                        t.ZaalToegang,
                        t.Status,
                        t.Opmerking,
                        b.Naam AS BezoekerNaam,
                        b.Email AS BezoekerEmail,
                        e.Naam AS EvenementNaam,
                        e.Datum AS EvenementDatum,
                        e.Locatie AS EvenementLocatie,
                        p.Tarief AS TicketTarief,
                        p.Tijdslot AS TicketTijdslot
                    FROM `Ticket` t
                    INNER JOIN `Bezoeker` b ON t.BezoekerId = b.Id
                    INNER JOIN `Evenement` e ON t.EvenementId = e.Id
                    INNER JOIN `Prijs` p ON t.PrijsId = p.Id
                    WHERE t.IsActief = 1
                    ORDER BY t.TicketCode ASC;
                END
            ");

            // 4. sp_GetVerkopersMetDetails (LEFT JOINs)
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetVerkopersMetDetails`;");
            DB::unprepared("
                CREATE PROCEDURE `sp_GetVerkopersMetDetails`()
                BEGIN
                    SELECT 
                        v.Id AS VerkoperId,
                        v.Naam AS VerkoperNaam,
                        v.SpecialeStatus,
                        v.VerkooptSoort,
                        v.Logo,
                        cp.Id AS ContactpersoonId,
                        cp.Naam AS ContactpersoonNaam,
                        cp.Email AS ContactpersoonEmail,
                        cp.Telefoonnummer AS ContactpersoonTelefoon
                    FROM `Verkoper` v
                    LEFT JOIN `ContactPerVerkoper` cpv ON v.Id = cpv.VerkoperId AND cpv.IsActief = 1
                    LEFT JOIN `Contactpersoon` cp ON cpv.ContactpersoonId = cp.Id AND cp.IsActief = 1
                    WHERE v.IsActief = 1
                    ORDER BY v.Id ASC;
                END
            ");

            // 5. sp_GetContactpersonenMetVerkoper (LEFT JOINs)
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetContactpersonenMetVerkoper`;");
            DB::unprepared("
                CREATE PROCEDURE `sp_GetContactpersonenMetVerkoper`()
                BEGIN
                    SELECT 
                        cp.Id AS ContactpersoonId,
                        cp.Naam AS ContactpersoonNaam,
                        cp.Email AS ContactpersoonEmail,
                        cp.Telefoonnummer,
                        cp.Opmerking AS ContactRol,
                        v.Id AS VerkoperId,
                        v.Naam AS VerkoperNaam,
                        v.VerkooptSoort
                    FROM `Contactpersoon` cp
                    LEFT JOIN `ContactPerVerkoper` cpv ON cp.Id = cpv.ContactpersoonId AND cpv.IsActief = 1
                    LEFT JOIN `Verkoper` v ON cpv.VerkoperId = v.Id AND v.IsActief = 1
                    WHERE cp.IsActief = 1
                    ORDER BY cp.Id ASC;
                END
            ");

            // 6. sp_GetEvenementenMetOrganisator (INNER JOIN)
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetEvenementenMetOrganisator`;");
            DB::unprepared("
                CREATE PROCEDURE `sp_GetEvenementenMetOrganisator`()
                BEGIN
                    SELECT 
                        e.Id AS EvenementId,
                        e.Naam AS EvenementNaam,
                        e.Datum,
                        e.Locatie,
                        e.AantalTicketsPerTijdslot,
                        e.BeschikbareStands,
                        o.Id AS OrganisatorId,
                        o.Naam AS OrganisatorNaam
                    FROM `Evenement` e
                    INNER JOIN `Organisator` o ON e.OrganisatorId = o.Id
                    WHERE e.IsActief = 1
                    ORDER BY e.Datum ASC;
                END
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetStandsMetVerkoper`;");
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetStandStatistieken`;");
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetTicketsMetDetails`;");
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetVerkopersMetDetails`;");
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetContactpersonenMetVerkoper`;");
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_GetEvenementenMetOrganisator`;");
        }
    }
};
