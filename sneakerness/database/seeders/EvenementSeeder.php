<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvenementSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Evenement')->insert([
            [
                'Id' => 1,
                'OrganisatorId' => 1,
                'Naam' => 'Sneakerness Rotterdam 2026',
                'Datum' => '2026-10-24',
                'Locatie' => 'Van Nelle Fabriek Rotterdam',
                'AantalTicketsPerTijdslot' => 750,
                'BeschikbareStands' => 55,
                'IsActief' => 1,
                'Opmerking' => 'Sneakerness Rotterdam Drop Hub Editie 2026',
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ],
            [
                'Id' => 2,
                'OrganisatorId' => 1,
                'Naam' => 'Sneakerness Amsterdam 2027',
                'Datum' => '2027-05-15',
                'Locatie' => 'Kromhouthal Amsterdam',
                'AantalTicketsPerTijdslot' => 900,
                'BeschikbareStands' => 60,
                'IsActief' => 0,
                'Opmerking' => 'Sneakerness Amsterdam Voorjaarseditie 2027',
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ],
            [
                'Id' => 3,
                'OrganisatorId' => 2,
                'Naam' => 'Sneakerness Londen 2027',
                'Datum' => '2027-09-25',
                'Locatie' => 'Truman Brewery Londen',
                'AantalTicketsPerTijdslot' => 1100,
                'BeschikbareStands' => 70,
                'IsActief' => 0,
                'Opmerking' => 'Sneakerness UK Flagship Convention',
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ],
            [
                'Id' => 4,
                'OrganisatorId' => 2,
                'Naam' => 'Sneakerness Parijs 2027',
                'Datum' => '2027-10-09',
                'Locatie' => 'Paris Expo Parijs',
                'AantalTicketsPerTijdslot' => 850,
                'BeschikbareStands' => 58,
                'IsActief' => 0,
                'Opmerking' => 'Sneakerness France Najaarsevent',
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ],
        ]);
    }
}