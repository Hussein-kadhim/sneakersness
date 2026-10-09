<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SneakernessSeeder extends Seeder
{
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
    }
}
