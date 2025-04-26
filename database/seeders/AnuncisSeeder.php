<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AnuncisSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Obtenim les dades de les altres taules
        $estats = DB::table('anuncis_estats')->pluck('id_estat')->toArray();
        $marques = DB::table('anuncis_marques')->pluck('id_marca')->toArray();
        $mides = DB::table('anuncis_mides')->pluck('id_mida')->toArray();
        $tipus = DB::table('anuncis_tipus')->pluck('id_tipus')->toArray();
        $usuaris = DB::table('users')->pluck('id')->toArray();

        // Crear 100 anuncis
        for ($i = 0; $i < 100; $i++) {
            DB::table('anuncis')->insert([
                'titol' => $faker->sentence(3),
                'descripcio' => $faker->paragraph(),
                'preu' => $faker->randomFloat(2, 10, 500), // Entre 10 i 500 €
                'id_estat' => $faker->randomElement($estats),
                'id_marca' => $faker->randomElement($marques),
                'id_mida' => $faker->randomElement($mides),
                'id_tipus' => $faker->randomElement($tipus),
                'id_usuari' => $faker->randomElement($usuaris),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}