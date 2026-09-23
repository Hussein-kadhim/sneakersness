<?php

namespace Database\Seeders;

use App\Models\Bezoeker;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ticketsData = [
            [
                'ticket_code' => 'TKT-84920',
                'order' => 'SN-2024-84920',
                'naam' => 'Jan Jansen',
                'email' => 'jan.jansen@voorbeeld.nl',
                'ticket_type' => 'Early Bird VIP',
                'tijdslot' => 'Zaterdag 16 nov (10:00 - 18:00)',
                'zaal' => 'Hal 1 & 2 • Entree A',
                'status' => 'Geldig',
            ],
            [
                'ticket_code' => 'TKT-84921',
                'order' => 'SN-2024-84921',
                'naam' => 'Sophie van Dijk',
                'email' => 'sophie.vd@outlook.com',
                'ticket_type' => 'Regulier Slot 1',
                'tijdslot' => 'Zaterdag 16 nov (10:00 - 14:00)',
                'zaal' => 'Hal 1 • Entree B',
                'status' => 'Aandacht nodig',
            ],
            [
                'ticket_code' => 'TKT-84922',
                'order' => 'SN-2024-84922',
                'naam' => 'Bilal El Amrani',
                'email' => 'bilal@kicksdaily.nl',
                'ticket_type' => 'Weekend Passe-Partout',
                'tijdslot' => '16 & 17 Nov (Volledig weekend)',
                'zaal' => 'Hal 1 & 2 • VIP Fast-lane',
                'status' => 'Wijziging verzocht',
            ],
            [
                'ticket_code' => 'TKT-84923',
                'order' => 'SN-2024-84923',
                'naam' => 'Emma Bakker',
                'email' => 'emma.art@gmail.com',
                'ticket_type' => 'Regulier Slot 2',
                'tijdslot' => 'Zondag 17 nov (14:00 - 18:00)',
                'zaal' => 'Hal 1 • Entree B',
                'status' => 'Geldig',
            ],
            [
                'ticket_code' => 'TKT-84924',
                'order' => 'SN-2024-84924',
                'naam' => 'Daan van Leeuwen',
                'email' => 'daanvl@vintagehub.nl',
                'ticket_type' => 'Early Bird VIP',
                'tijdslot' => 'Weekend (16 & 17 nov)',
                'zaal' => 'Hal 1 & 2 • VIP Fast-lane',
                'status' => 'Aandacht nodig',
            ],
            [
                'ticket_code' => 'TKT-84925',
                'order' => 'SN-2024-84925',
                'naam' => 'Jesse de Bruin',
                'email' => 'jesse@barberlounge.com',
                'ticket_type' => 'Regulier Slot 1',
                'tijdslot' => 'Zaterdag 16 nov (10:00 - 14:00)',
                'zaal' => 'Hal 1 • Entree A',
                'status' => 'Gescand',
            ],
        ];

        foreach ($ticketsData as $item) {
            $bezoeker = Bezoeker::firstOrCreate(
                ['Email' => $item['email']],
                [
                    'Naam' => $item['naam'],
                    'IsActief' => 1,
                    'Opmerking' => 'Ticket koper',
                ]
            );

            Ticket::updateOrCreate(
                ['TicketCode' => $item['ticket_code']],
                [
                    'Bestelnummer' => $item['order'],
                    'BezoekerId' => $bezoeker->Id,
                    'EvenementId' => 1,
                    'PrijsId' => 1,
                    'AantalTickets' => 1,
                    'TicketType' => $item['ticket_type'],
                    'Tijdslot' => $item['tijdslot'],
                    'ZaalToegang' => $item['zaal'],
                    'Status' => $item['status'],
                    'Datum' => '2024-11-16',
                    'IsActief' => 1,
                ]
            );
        }

        // Zorg dat eventuele oude tickets zonder code op pagina 2 belanden
        $remaining = Ticket::whereNull('TicketCode')->get();
        $counter = 84926;
        foreach ($remaining as $ticket) {
            $ticket->update([
                'TicketCode' => 'TKT-' . $counter,
                'Bestelnummer' => 'SN-2024-' . $counter,
                'TicketType' => 'Regulier Slot 1',
                'Tijdslot' => 'Zaterdag 16 nov (10:00 - 14:00)',
                'ZaalToegang' => 'Hal 1 • Entree B',
                'Status' => 'Geldig',
            ]);
            $counter++;
        }
    }
}
