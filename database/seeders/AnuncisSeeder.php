<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnuncisSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('anuncismarques')->truncate();
        DB::table('anuncisestats')->truncate();
        DB::table('anuncismides')->truncate();
        DB::table('anuncistipus')->truncate();
        DB::table('anuncisfotos')->truncate();
        DB::table('anuncis')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── Marques ──────────────────────────────────────────────────────────

        $marques = [
            'Reno', 'Azemad', 'Sioux', 'Toor', 'Skater', 
            'Roll-Line', 'Genial', 'Crojet', 'Replic', 'Etisport', 
            'Queen Skate', 'Boiani', 'TVS', 'Meneghini', 'STD Skates', 'McRoller','Stika'
            'Altres'
        ];
        foreach ($marques as $m) {
            DB::table('anuncismarques')->insert(['nom_marca' => $m, 'created_at' => now(), 'updated_at' => now()]);
        }

        // ── Estats ───────────────────────────────────────────────────────────
        $estats = ['Nou', 'Usat', 'Molt usat', 'Per peces'];
        foreach ($estats as $e) {
            DB::table('anuncisestats')->insert(['nom_estat' => $e, 'created_at' => now(), 'updated_at' => now()]);
        }

        // ── Mides ────────────────────────────────────────────────────────────
        $midesSamarreta = ['3XS', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'Junior 8', 'Junior 10', 'Junior 12', 'Junior 14', 'Altres'];
        foreach ($midesSamarreta as $mida) {
            DB::table('anuncismides')->insert(['nom_mida' => $mida, 'tipus_mida' => 'samarreta', 'created_at' => now(), 'updated_at' => now()]);
        }

        $midesCalcat = ['30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', 'Altres'];
        foreach ($midesCalcat as $mida) {
            DB::table('anuncismides')->insert(['nom_mida' => $mida, 'tipus_mida' => 'calcat', 'created_at' => now(), 'updated_at' => now()]);
        }

        // ── Tipus ────────────────────────────────────────────────────────────
        $tipus = [
            ['nom_tipus' => 'Sticks',               'icona_fa' => 'fa-hockey-sticks'],
            ['nom_tipus' => 'Patins',               'icona_fa' => 'fa-person-skating'],
            ['nom_tipus' => 'Rodes i Recanvis',     'icona_fa' => 'fa-circle-dot'],
            ['nom_tipus' => 'Proteccions Jugador',  'icona_fa' => 'fa-shield-halved'],
            ['nom_tipus' => 'Proteccions Porter',   'icona_fa' => 'fa-user-shield'],
            ['nom_tipus' => 'Bosses',               'icona_fa' => 'fa-bag-shopping'],
            ['nom_tipus' => 'Cascs',                'icona_fa' => 'fa-helmet-safety'],
            ['nom_tipus' => 'Tèxtil',               'icona_fa' => 'fa-shirt'],
            ['nom_tipus' => 'ALTRES',               'icona_fa' => 'fa-ellipsis'],
        ];
        foreach ($tipus as $t) {
            DB::table('anuncistipus')->insert(array_merge($t, ['created_at' => now(), 'updated_at' => now()]));
        }


        // ── Usuari de prova ──────────────────────────────────────────────────
        // Agafem el primer usuari existent
        $idUsuari = DB::table('users')->value('id');
        if (!$idUsuari) {
            $idUsuari = DB::table('users')->insertGetId([
                'name'     => 'Usuari Prova',
                'email'    => 'prova@jok.cat',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── IDs de les taules lookup ─────────────────────────────────────────
        $marcaIds  = DB::table('anuncismarques')->pluck('id')->toArray();
        $estatIds  = DB::table('anuncisestats')->pluck('id')->toArray();
        $midaIds   = DB::table('anuncismides')->pluck('id')->toArray();
        $tipusIds  = DB::table('anuncistipus')->pluck('id')->toArray();

        // ── Títols de prova variats ──────────────────────────────────────────
        $titolsPatrons = [
            'Patins {marca} talla {mida} com nous',
            'Venc casc {marca} amb visor',
            'Stick {marca} de fusta, pràcticament nou',
            'Stick {marca} de composite talla {mida}',
            'Equipació de porter {marca} completa',
            'Rodes {marca} de 92A, pack de 8',
            'Guants {marca} per a jugador talla {mida}',
            'Genolleres {marca} d\'hoquei patins',
            'Espinilleres {marca} molt protegides',
            'Botes {marca} soles, talla {mida}',
            'Guantilles de porter {marca}',
            'Bossa porta-patins {marca}',
            'Protector bucal i mitges {marca}',
            'Mascareta de porter {marca}',
            'Frens {marca} de color blanc',
        ];

        $descripcionsPatrons = [
            'Material d\'hoquei patins en excel·lent estat. Venut per canvi de talla. Molt cuidat, ideal per a competició.',
            'Usat només una temporada. Les botes estan perfectes i les rodes tenen encara molta vida. Recollida a local social.',
            'Stick professional de fusta, molt bon tacte. No té cap esquerda, només les típiques rascades de l\'ús.',
            'Guants molt flexibles i amb molta protecció. Talla {mida}. Es poden enviar per correu.',
            'Material revisat. Les rodes són Roll-Line i els rodaments giren perfectament. Preu algo negociable.',
            'Kit complet per a principiants. Inclou patins, genolleres i guants. Tot de la marca {marca}.',
            'Equip de porter molt complet. Peto, guardes i mascareta. Molt poca utilització.',
        ];

        // ── Fotos de placeholder (picsum per categoria) ──────────────────────
        $fotosSeed = [
            // sport/hockey themed seeds
            100, 200, 300, 400, 500, 600, 700, 800,
            111, 222, 333, 444, 555, 666, 777, 888,
            101, 202, 303, 404, 505, 606, 707, 808,
        ];

        // ── Inserir 100 anuncis ──────────────────────────────────────────────
        $now = Carbon::now();

        for ($i = 1; $i <= 100; $i++) {
            $idMarca = $marcaIds[array_rand($marcaIds)];
            $idEstat = $estatIds[array_rand($estatIds)];
            $idMida  = $midaIds[array_rand($midaIds)];
            $idTipus = $tipusIds[array_rand($tipusIds)];

            $nomMarca = DB::table('anuncismarques')->where('id', $idMarca)->value('nom_marca');
            $nomMida  = DB::table('anuncismides')->where('id', $idMida)->value('nom_mida');

            $titolPatro = $titolsPatrons[array_rand($titolsPatrons)];
            $titol = str_replace(['{marca}', '{mida}'], [$nomMarca, $nomMida], $titolPatro);

            $descripcio = $descripcionsPatrons[array_rand($descripcionsPatrons)];

            // Preu: "Per peces" és l'estat id 4, posem null; resta random entre 15-350€
            $idEstatPerPeces = DB::table('anuncisestats')->where('nom_estat', 'Per peces')->value('id');
            $preu = ($idEstat == $idEstatPerPeces) ? null : rand(15, 350) + (rand(0, 9) * 0.5);

            $createdAt = $now->copy()->subDays(rand(0, 180));

            $idAnunci = DB::table('anuncis')->insertGetId([
                'titol'      => $titol,
                'descripcio' => $descripcio,
                'preu'       => $preu,
                'id_usuari'  => $idUsuari,
                'id_marca'   => $idMarca,
                'id_estat'   => $idEstat,
                'id_mida'    => $idMida,
                'id_tipus'   => $idTipus,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Entre 1 i 4 fotos per anunci
            $numFotos = rand(1, 4);
            for ($f = 0; $f < $numFotos; $f++) {
                $seed = $fotosSeed[($i + $f * 7) % count($fotosSeed)];
                // Usar picsum amb seed únic per anunci+foto
                $fotoUrl = "https://picsum.photos/seed/anunci{$idAnunci}foto{$f}/600/450";

                DB::table('anuncisfotos')->insert([
                    'id_anunci'  => $idAnunci,
                    'foto_ruta'  => $fotoUrl,
                    'ordre'      => $f,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
