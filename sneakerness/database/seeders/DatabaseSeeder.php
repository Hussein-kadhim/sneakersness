<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    public function run(): void
    {
        // Alle tabellen legen via SneakernessSeeder
        $this->call([
            SneakernessSeeder::class,
        ]);

        // Alleen de 3 inloggebruikers aanmaken
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $password = Hash::make('password');

        // Organisator-account
        User::create([
            'name'              => 'Organisator',
            'email'             => 'organisator@sneakerness.com',
            'role'              => 'organisator',
            'password'          => $password,
            'email_verified_at' => now(),
        ]);

        // Verkoper-account
        User::create([
            'name'              => 'Verkoper',
            'email'             => 'verkoper@sneakerness.com',
            'role'              => 'verkoper',
            'password'          => $password,
            'email_verified_at' => now(),
        ]);

        // Bezoeker-account
        User::create([
            'name'              => 'Bezoeker',
            'email'             => 'bezoeker@sneakerness.com',
            'role'              => 'bezoeker',
            'password'          => $password,
            'email_verified_at' => now(),
        ]);
    }
}
