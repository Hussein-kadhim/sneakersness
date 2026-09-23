<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class SneakernessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('ContactPerVerkoper')->truncate();
        DB::table('Contactpersoon')->truncate();
        DB::table('Stand')->truncate();
        DB::table('Ticket')->truncate();
        DB::table('Prijs')->truncate();
        DB::table('Evenement')->truncate();
        DB::table('Bezoeker')->truncate();
        DB::table('Verkoper')->truncate();
        DB::table('Organisator')->truncate();

        Schema::enableForeignKeyConstraints();

        // 1. Organisatoren
        DB::table('Organisator')->insert([
            [
                'Id' => 1,
                'Naam' => 'Sneakerness Events BV',
                'Gebruikersnaam' => 'sneakerness.events',
                'Wachtwoord' => Hash::make('wachtwoord123'),
                'IsActief' => 1,
                'Opmerking' => 'Hoofdorganisator Sneakerness Rotterdam',
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ],
            [
                'Id' => 2,
                'Naam' => 'Urban Culture Nederland',
                'Gebruikersnaam' => 'urban.culture',
                'Wachtwoord' => Hash::make('wachtwoord123'),
                'IsActief' => 1,
                'Opmerking' => 'Co-organisator side events',
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ],
        ]);

        // 2. Bezoekers
        DB::table('Bezoeker')->insert([
            ['Id' => 1, 'Naam' => 'Lisa Jansen', 'Email' => 'lisa.jansen@gmail.com', 'IsActief' => 1, 'Opmerking' => 'VIP bezoeker', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'Naam' => 'Ahmed El Mansouri', 'Email' => 'ahmed.elmansouri@outlook.com', 'IsActief' => 1, 'Opmerking' => 'Reguliere bezoeker', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'Naam' => 'Emma van der Berg', 'Email' => 'emma.vdberg@hotmail.com', 'IsActief' => 1, 'Opmerking' => 'Reguliere bezoeker', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'Naam' => 'Tom Bakker', 'Email' => 'tom.bakker@gmail.com', 'IsActief' => 1, 'Opmerking' => 'Sneaker collector', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        // 3. Events, nadat de organisatoren bestaan vanwege de foreign key.
        $this->call(EvenementSeeder::class);

        // 4. Prijzen
        DB::table('Prijs')->insert([
            ['Id' => 1, 'EvenementId' => 1, 'Datum' => '2026-10-24', 'Tijdslot' => '10:00:00', 'Tarief' => 25.00, 'IsActief' => 1, 'Opmerking' => 'Early Bird Vroege Toegang', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'EvenementId' => 1, 'Datum' => '2026-10-24', 'Tijdslot' => '12:00:00', 'Tarief' => 20.00, 'IsActief' => 1, 'Opmerking' => 'Middagsessie 1', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'EvenementId' => 1, 'Datum' => '2026-10-24', 'Tijdslot' => '14:30:00', 'Tarief' => 20.00, 'IsActief' => 1, 'Opmerking' => 'Middagsessie 2', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        // 5. Tickets
        DB::table('Ticket')->insert([
            ['Id' => 1, 'BezoekerId' => 1, 'EvenementId' => 1, 'PrijsId' => 1, 'AantalTickets' => 2, 'Datum' => '2026-09-01', 'IsActief' => 1, 'Opmerking' => 'Vroege toegang tickets', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'BezoekerId' => 2, 'EvenementId' => 1, 'PrijsId' => 2, 'AantalTickets' => 1, 'Datum' => '2026-09-02', 'IsActief' => 1, 'Opmerking' => 'Middag ticket', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'BezoekerId' => 3, 'EvenementId' => 1, 'PrijsId' => 3, 'AantalTickets' => 4, 'Datum' => '2026-09-03', 'IsActief' => 1, 'Opmerking' => 'Groepsticket vrienden', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        // 6. Verkopers (48 totaal, 8 partners)
        $verkopersData = [
            // Top 6 uit Figma mockup
            [1, 'Kicks Rotterdam', 1, 'Sneakers', 'kicks_rtm.png', 1, 'Actief'],
            [2, 'SoleMate Amsterdam', 0, 'Sneakers', 'solemate.png', 1, 'Bevestigd'],
            [3, 'Streetwear RTM', 0, 'Streetwear', 'streetwear_rtm.png', 1, 'In behandeling'],
            [4, 'Sneaker Art Studio', 1, 'Customizer', 'sneaker_art.png', 1, 'Actief'],
            [5, 'Vintage Kicks', 0, 'Sneakers', 'vintage_kicks.png', 1, 'Bevestigd'],
            [6, 'Barber & Cuts Lounge', 0, 'Tattoo & Barbershop', 'barber_cuts.png', 1, 'Actief'],

            // Overige partners (Partners totaal = 8: 1, 4, 7, 8, 9, 10, 11, 12)
            [7, 'Solebox Official', 1, 'Sneakers', 'solebox.png', 1, 'Actief'],
            [8, 'Overkill Berlin', 1, 'Sneakers & Apparel', 'overkill.png', 1, 'Actief'],
            [9, 'Patta Amsterdam', 1, 'Streetwear', 'patta.png', 1, 'Actief'],
            [10, 'Woei Rotterdam', 1, 'Sneakers', 'woei.png', 1, 'Actief'],
            [11, 'Sneakersnstuff', 1, 'Sneakers', 'sns.png', 1, 'Actief'],
            [12, 'BSTN Store', 1, 'Streetwear', 'bstn.png', 1, 'Actief'],

            // Overige deelnemende verkopers
            [13, 'Outsole NL', 0, 'Vintage Sneakers', null, 1, 'Bevestigd'],
            [14, 'Deadstock District', 0, 'Sneakers', null, 1, 'Actief'],
            [15, 'Laces Out Supplies', 0, 'Accessoires', null, 1, 'Actief'],
            [16, 'Crep Protect Hub', 0, 'Sneaker Care', null, 1, 'Actief'],
            [17, 'Sneaker Cleaners RTM', 0, 'Cleaning Service', null, 1, 'Actief'],
            [18, 'Heat on Feet', 0, 'Sneakers', null, 1, 'Bevestigd'],
            [19, 'Grail Finder', 0, 'Exclusive Kicks', null, 1, 'In behandeling'],
            [20, 'Urban Threads', 0, 'Streetwear', null, 1, 'Actief'],
            [21, 'Daily Paper Archive', 0, 'Streetwear', null, 1, 'Actief'],
            [22, 'Off the Hook', 0, 'Apparel & Kicks', null, 1, 'Bevestigd'],
            [23, 'Prime Kicks Store', 0, 'Sneakers', null, 1, 'Actief'],
            [24, 'Rotterdam Custom Lab', 0, 'Customizer', null, 1, 'Actief'],
            [25, 'Sole Food BBQ', 0, 'Eten en Drinken', null, 1, 'Actief'],
            [26, 'Sneaker Bites Coffee', 0, 'Eten en Drinken', null, 1, 'Actief'],
            [27, 'The Good Will Out', 0, 'Sneakers', null, 1, 'Bevestigd'],
            [28, 'Afew Store Pop-up', 0, 'Sneakers & Streetwear', null, 1, 'Actief'],
            [29, 'Sneaker Freaker Mag', 0, 'Media & Books', null, 1, 'Actief'],
            [30, 'Shoe Surgeon Academy', 0, 'Workshop & Customizer', null, 1, 'Actief'],
            [31, 'Archive DNA', 0, 'Rare Collectibles', null, 1, 'In behandeling'],
            [32, 'Dunk Master NL', 0, 'Sneakers', null, 1, 'Actief'],
            [33, 'Air Max Heaven', 0, 'Sneakers', null, 1, 'Actief'],
            [34, 'Yeezy Supply Club', 0, 'Sneakers', null, 1, 'Bevestigd'],
            [35, 'Retro Jordan Vault', 0, 'Sneakers', null, 1, 'Actief'],
            [36, 'Sneaker Sock Lab', 0, 'Accessoires', null, 1, 'Actief'],
            [37, 'Pins & Patches Co.', 0, 'Accessoires', null, 1, 'Actief'],
            [38, 'Ink & Lace Tattoo Studio', 0, 'Tattoo & Barbershop', null, 1, 'Actief'],
            [39, 'Fade Masters RTM', 0, 'Tattoo & Barbershop', null, 1, 'Actief'],
            [40, 'Kids Sneaker Corner', 0, 'Kids Corner', null, 1, 'Actief'],
            [41, 'Mini Hypebeast NL', 0, 'Kids Corner', null, 1, 'Actief'],
            [42, 'Street Food Burgers', 0, 'Eten en Drinken', null, 1, 'Actief'],
            [43, 'Churros & Sweets Hub', 0, 'Eten en Drinken', null, 1, 'Actief'],
            [44, 'Sneaker Display Cases', 0, 'Accessoires', null, 1, 'Bevestigd'],
            [45, 'Rope Lace Supply', 0, 'Accessoires', null, 1, 'Actief'],
            [46, 'Sole Revival Studio', 0, 'Restoration', null, 1, 'Actief'],
            [47, 'Urban Graffiti Workshop', 0, 'Art & Culture', null, 1, 'Actief'],
            [48, 'Sneakerness Rotterdam Merch', 0, 'Merchandise', null, 1, 'Actief'],
        ];

        $verkopersInsert = [];
        foreach ($verkopersData as $item) {
            $verkopersInsert[] = [
                'Id' => $item[0],
                'Naam' => $item[1],
                'SpecialeStatus' => $item[2],
                'VerkooptSoort' => $item[3],
                'Logo' => $item[4],
                'IsActief' => $item[5],
                'Opmerking' => $item[6],
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ];
        }
        DB::table('Verkoper')->insert($verkopersInsert);

        // 7. Contactpersonen (54 stuks, 48 gekoppeld, 6 ongekoppeld)
        $contactenData = [
            [1, 'Mark de Vries', '06-12345678', 'mark@kicksrdam.nl', 1, 'Hoofdcontactpersoon'],
            [2, 'Sophie Jansen', '06-87654321', 'contact@solemate.nl', 2, 'Eigenaar'],
            [3, 'Bilal El Amrani', '06-22446688', 'info@streetwear-rtm.nl', 3, 'Standhouder'],
            [4, 'Emma Bakker', '06-99887766', 'art@sneakerstudio.nl', 4, 'Eigenaar & Artiest'],
            [5, 'Jesse van Dijk', '06-11223344', 'jesse@barberandcuts.nl', 6, 'Teamleider'],
            [6, 'Daan van Leeuwen', '06-33557799', 'daan@kicks.nl', null, 'Assistent'],

            // Gekoppelde contactpersonen voor verkopers 5, 7 t/m 48 (totaal 48 gekoppeld)
            [7, 'Hikmet Sugoer', '06-11223301', 'contact@solebox.com', 7],
            [8, 'Marc Leuschner', '06-11223302', 'info@overkill.de', 8],
            [9, 'Edson Sabajo', '06-11223303', 'edson@patta.nl', 9],
            [10, 'Woei Tjin', '06-11223304', 'woei@woei.nl', 10],
            [11, 'Peter Jansson', '06-11223305', 'peter@sns.se', 11],
            [12, 'Chris Bosz', '06-11223306', 'chris@bstn.com', 12],
            [13, 'Sander Meijer', '06-44556677', 'sander@outsole.nl', 13],
            [14, 'Lars de Jong', '06-55667788', 'lars@deadstock.nl', 14],
            [15, 'Robin Visser', '06-66778899', 'robin@lacesout.nl', 15],
            [16, 'Rizwan Ahmed', '06-77889900', 'riz@crepprotect.nl', 16],
            [17, 'Kevin Smeets', '06-88990011', 'kevin@cleanersrtm.nl', 17],
            [18, 'Tim Brouwer', '06-99001122', 'tim@heatonfeet.nl', 18],
            [19, 'Sven Kuipers', '06-10203040', 'sven@grailfinder.nl', 19],
            [20, 'Noah van Dijk', '06-20304050', 'noah@urbanthreads.nl', 20],
            [21, 'Jefferson Osei', '06-30405060', 'jeff@dailypaper.nl', 21],
            [22, 'David Cohen', '06-40506070', 'david@othook.nl', 22],
            [23, 'Lucas Vos', '06-50607080', 'lucas@primekicks.nl', 23],
            [24, 'Milan de Groot', '06-60708090', 'milan@customlab.nl', 24],
            [25, 'Marco Rossi', '06-70809001', 'marco@solefoodbbq.nl', 25],
            [26, 'Anouk Veenstra', '06-80900112', 'anouk@sneakerbites.nl', 26],
            [27, 'Alex Keller', '06-90011223', 'alex@tgwo.de', 27],
            [28, 'Marco Biergen', '06-01122334', 'marco@afew.de', 28],
            [29, 'Woody Simon', '06-12233445', 'woody@sneakerfreaker.nl', 29],
            [30, 'Dominic Ciambrone', '06-23344556', 'dominic@shoesurgeon.nl', 30],
            [31, 'Ryan Chang', '06-34455667', 'ryan@archivedna.nl', 31],
            [32, 'Stefan de Wit', '06-45566778', 'stefan@dunkmaster.nl', 32],
            [33, 'Dennis Scholten', '06-56677889', 'dennis@airmaxheaven.nl', 33],
            [34, 'Samir Bakkali', '06-67788990', 'samir@yeezysupply.nl', 34],
            [35, 'Bram Hendriks', '06-78899001', 'bram@retrojordan.nl', 35],
            [36, 'Kelly van Loon', '06-89900112', 'kelly@socks.nl', 36],
            [37, 'Iris Verschoor', '06-90011224', 'iris@pinsnl.nl', 37],
            [38, 'Boris van Dam', '06-01122336', 'boris@inklace.nl', 38],
            [39, 'Kareem Said', '06-12233448', 'kareem@fademasters.nl', 39],
            [40, 'Laura Mulder', '06-23344560', 'laura@kidscorner.nl', 40],
            [41, 'Bas van Beek', '06-34455672', 'bas@minihypebeast.nl', 41],
            [42, 'Giovanni Smit', '06-45566784', 'gio@streetfood.nl', 42],
            [43, 'Maria Santos', '06-56677896', 'maria@churros.nl', 43],
            [44, 'Jeroen Koster', '06-67788008', 'jeroen@displaycases.nl', 44],
            [45, 'Daniël Post', '06-78899120', 'daniel@ropelaces.nl', 45],
            [46, 'Floris Zeeman', '06-89900232', 'floris@solerevival.nl', 46],
            [47, 'Tyrell Jones', '06-90011344', 'tyrell@graffiti.nl', 47],
            [48, 'Organisatie Sneakerness', '06-01122456', 'merch@sneakerness.com', 48],
            [49, 'Kasper van Leeuwen', '06-33557788', 'kasper@vintagekicks.nl', 5],

            // Overige 5 ongekoppelde contactpersonen (totaal 6 ongekoppeld)
            [50, 'Anouk Jansen', '06-11224455', 'anouk@freelance.nl', null],
            [51, 'Rick van Dam', '06-22335566', 'rick@consultant.nl', null],
            [52, 'Chloe de Boer', '06-33446677', 'chloe@eventhost.nl', null],
            [53, 'Liam Visser', '06-44557788', 'liam@eventcrew.nl', null],
            [54, 'Sarah Bakker', '06-55668899', 'sarah@sneakerhead.nl', null],
        ];

        $contactenInsert = [];
        $cpvInsert = [];
        $cpvId = 1;
        foreach ($contactenData as $c) {
            $contactenInsert[] = [
                'Id' => $c[0],
                'Naam' => $c[1],
                'Telefoonnummer' => $c[2],
                'Email' => $c[3],
                'IsActief' => 1,
                'Opmerking' => $c[5] ?? ($c[4] ? 'Hoofdcontactpersoon' : 'Assistent'),
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ];
            if ($c[4] !== null) {
                $cpvInsert[] = [
                    'Id' => $cpvId++,
                    'VerkoperId' => $c[4],
                    'ContactpersoonId' => $c[0],
                    'IsActief' => 1,
                    'Opmerking' => $c[0] <= 42 ? 'Hoofdcontactpersoon' : 'Secundair contact',
                    'DatumAangemaakt' => now(),
                    'DatumGewijzigd' => now(),
                ];
            }
        }
        DB::table('Contactpersoon')->insert($contactenInsert);
        DB::table('ContactPerVerkoper')->insert($cpvInsert);

        // 8. Stands (18x AA+, 20x AA, 10x A, plus 7 extra voor 52 verhuurd / 55 totaal)
        $standsData = [
            // Top 6 uit Figma design
            [1, 1, 'AA+', 950.00, 2, 1, 'Stand #AA-101 • Hal 1'],
            [2, 2, 'AA', 600.00, 1, 1, 'Stand #AA-104 • Hal 1'],
            [3, 3, 'A', 450.00, 2, 0, 'Stand #A-208 • Hal 2'],
            [4, 4, 'AA+', 950.00, 2, 1, 'Stand #AA-112 • Hal 1'],
            [5, 5, 'AA', 650.00, 2, 1, 'Stand #A-201 • Hal 2'],
            [6, 6, 'A', 400.00, 2, 1, 'Stand #EX-05 • Hal 1 Lounge'],

            // 18x AA+ stands
            [7, 7, 'AA+', 950.00, 2, 1, 'Stand #AA-102 • Hal 1'],
            [8, 8, 'AA+', 950.00, 2, 1, 'Stand #AA-103 • Hal 1'],
            [9, 9, 'AA+', 950.00, 2, 1, 'Stand #AA-105 • Hal 1'],
            [10, 10, 'AA+', 950.00, 2, 1, 'Stand #AA-106 • Hal 1'],
            [11, 11, 'AA+', 950.00, 2, 1, 'Stand #AA-107 • Hal 1'],
            [12, 12, 'AA+', 950.00, 2, 1, 'Stand #AA-108 • Hal 1'],
            [13, 13, 'AA+', 950.00, 2, 1, 'Stand #AA-109 • Hal 1'],
            [14, 14, 'AA+', 950.00, 2, 1, 'Stand #AA-110 • Hal 1'],
            [15, 15, 'AA+', 900.00, 2, 1, 'Stand #AA-111 • Hal 1'],
            [16, 16, 'AA+', 900.00, 2, 1, 'Stand #AA-113 • Hal 1'],
            [17, 17, 'AA+', 900.00, 2, 1, 'Stand #AA-114 • Hal 1'],
            [18, 18, 'AA+', 900.00, 2, 1, 'Stand #AA-115 • Hal 1'],
            [19, 19, 'AA+', 900.00, 2, 1, 'Stand #AA-116 • Hal 1'],
            [20, 20, 'AA+', 900.00, 2, 1, 'Stand #AA-117 • Hal 1'],
            [21, 21, 'AA+', 900.00, 2, 1, 'Stand #AA-118 • Hal 1'],
            [22, 22, 'AA+', 900.00, 2, 1, 'Stand #AA-119 • Hal 1'],

            // 20x AA stands
            [23, 23, 'AA', 650.00, 2, 1, 'Stand #AA-201 • Hal 2'],
            [24, 24, 'AA', 650.00, 2, 1, 'Stand #AA-202 • Hal 2'],
            [25, 25, 'AA', 600.00, 2, 1, 'Stand #AA-203 • Hal 2 Food Area'],
            [26, 26, 'AA', 600.00, 2, 1, 'Stand #AA-204 • Hal 2 Food Area'],
            [27, 27, 'AA', 650.00, 2, 1, 'Stand #AA-205 • Hal 2'],
            [28, 28, 'AA', 650.00, 2, 1, 'Stand #AA-206 • Hal 2'],
            [29, 29, 'AA', 600.00, 2, 1, 'Stand #AA-207 • Hal 2 Media'],
            [30, 30, 'AA', 650.00, 2, 1, 'Stand #AA-209 • Hal 2'],
            [31, 31, 'AA', 600.00, 1, 1, 'Stand #AA-210 • Hal 2'],
            [32, 32, 'AA', 650.00, 2, 1, 'Stand #AA-211 • Hal 2'],
            [33, 33, 'AA', 650.00, 2, 1, 'Stand #AA-212 • Hal 2'],
            [34, 34, 'AA', 650.00, 2, 1, 'Stand #AA-213 • Hal 2'],
            [35, 35, 'AA', 650.00, 2, 1, 'Stand #AA-214 • Hal 2'],
            [36, 36, 'AA', 600.00, 2, 1, 'Stand #AA-215 • Hal 2'],
            [37, 37, 'AA', 600.00, 2, 1, 'Stand #AA-216 • Hal 2'],
            [38, 38, 'AA', 650.00, 2, 1, 'Stand #AA-217 • Hal 2'],
            [39, 39, 'AA', 600.00, 2, 1, 'Stand #AA-218 • Hal 2'],
            [40, 40, 'AA', 600.00, 2, 1, 'Stand #AA-219 • Hal 2 Kids'],

            // 10x A stands
            [41, 41, 'A', 400.00, 2, 1, 'Stand #A-202 • Hal 2 Kids'],
            [42, 42, 'A', 450.00, 2, 1, 'Stand #A-203 • Hal 2 Food'],
            [43, 43, 'A', 400.00, 2, 1, 'Stand #A-204 • Hal 2 Sweets'],
            [44, 44, 'A', 400.00, 2, 1, 'Stand #A-205 • Hal 2'],
            [45, 45, 'A', 350.00, 1, 1, 'Stand #A-206 • Hal 2'],
            [46, 46, 'A', 400.00, 2, 1, 'Stand #A-207 • Hal 2'],
            [47, 47, 'A', 400.00, 2, 1, 'Stand #A-209 • Hal 2 Art'],
            [48, 48, 'A', 500.00, 2, 1, 'Stand #A-01 • Hal 1 Main Entrance'],

            // Extra stands voor verhuurd: 52 / 55 totaal
            [49, 1, 'AA+', 950.00, 2, 1, 'Stand #AA-120 • Hal 1 Extra'],
            [50, 4, 'AA+', 950.00, 2, 1, 'Stand #AA-121 • Hal 1 Extra'],
            [51, 7, 'AA+', 950.00, 2, 1, 'Stand #AA-122 • Hal 1 Extra'],
            [52, 9, 'AA+', 950.00, 2, 1, 'Stand #AA-123 • Hal 1 Extra'],
            [53, 1, 'A', 350.00, 1, 0, 'Stand #A-210 • Hal 2 Beschikbaar'],
            [54, 2, 'A', 350.00, 1, 0, 'Stand #A-211 • Hal 2 Beschikbaar'],
            [55, 3, 'A', 350.00, 1, 0, 'Stand #A-212 • Hal 2 Beschikbaar'],
        ];

        $standsInsert = [];
        foreach ($standsData as $s) {
            $standsInsert[] = [
                'Id' => $s[0],
                'VerkoperId' => $s[1],
                'StandType' => $s[2],
                'Prijs' => $s[3],
                'AantalDagen' => $s[4],
                'VerhuurdStatus' => $s[5],
                'IsActief' => 1,
                'Opmerking' => $s[6],
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ];
        }
        DB::table('Stand')->insert($standsInsert);
    }
}
