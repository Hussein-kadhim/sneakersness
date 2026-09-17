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

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SneakernessSeeder::class,
        ]);

        // Alleen exact 3 gebruikers voor de 3 rollen
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $password = Hash::make('password');

        User::create([
            'name' => 'Organisator',
            'email' => 'organisator@sneakerness.com',
            'role' => 'organisator',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Verkoper',
            'email' => 'verkoper@sneakerness.com',
            'role' => 'verkoper',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Bezoeker',
            'email' => 'bezoeker@sneakerness.com',
            'role' => 'bezoeker',
            'password' => $password,
            'email_verified_at' => now(),
        ]);
    }
}
