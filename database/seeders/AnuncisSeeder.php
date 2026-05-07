<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnuncisSeeder extends Seeder
{
    public function run(): void
    {
        // ── Marques ──────────────────────────────────────────────────────────
        $marques = [
            'Bauer', 'CCM', 'Salming', 'Sher-Wood', 'Rickter',
            'Reno', 'Borelli', 'Gens', 'Jofa', 'Vic',
            'Alkali', 'Oxdog', 'Fat Pipe', 'Unihoc', 'TK',
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
        $midesSamarreta = ['3XS', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'Junior 8', 'Junior 10', 'Junior 12', 'Junior 14'];
        foreach ($midesSamarreta as $mida) {
            DB::table('anuncismides')->insert(['nom_mida' => $mida, 'tipus_mida' => 'samarreta', 'created_at' => now(), 'updated_at' => now()]);
        }

        $midesCalcat = ['30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47'];
        foreach ($midesCalcat as $mida) {
            DB::table('anuncismides')->insert(['nom_mida' => $mida, 'tipus_mida' => 'calcat', 'created_at' => now(), 'updated_at' => now()]);
        }

        // ── Tipus ────────────────────────────────────────────────────────────
        $tipus = [
            ['nom_tipus' => 'Casc',     'icona_fa' => 'fa-helmet-safety'],
            ['nom_tipus' => 'Sticks',   'icona_fa' => 'fa-hockey-sticks'],
            ['nom_tipus' => 'Patins',   'icona_fa' => 'fa-person-skating'],
            ['nom_tipus' => 'Pilotes',  'icona_fa' => 'fa-circle'],
            ['nom_tipus' => 'Tèxtil',   'icona_fa' => 'fa-shirt'],
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
            'Venc casc {marca} molt poc usat',
            'Stick {marca} per dretà, en bon estat',
            'Equipació completa {marca} talla {mida}',
            'Pilotes oficials {marca} (paquet de 6)',
            'Patins infantils {marca} talla {mida}',
            'Casc {marca} amb careta inclosa',
            'Canyelleres {marca} talla {mida}',
            'Guants de porter {marca}',
            'Samarreta oficial {marca} talla {mida}',
            'Espatlleres {marca} quasi noves',
            'Stick {marca} per esquerrà',
            'Patins {marca} a estrenar, talla {mida}',
            'Kit complet iniciació {marca}',
            'Pantalons {marca} talla {mida}',
        ];

        $descripcionsPatrons = [
            'Material en excel·lent estat, pràcticament sense ús. Venut per canvi de talla. Recollida a Mollet del Vallès o enviament a càrrec del comprador.',
            'Usat durant dues temporades, bon estat general. Alguns cops però res que afecti al funcionament. Preu negociable.',
            'Comprat fa un any, usat molt poc. Perfecte per a jugadors que comencen. Inclou tots els elements originals.',
            'Venc per haver deixat de jugar. Material revisat i net. Possibilitat de veure\'l personalment a Granollers.',
            'Molt bon estat. Usat durant una temporada. Es pot recollir a Barcelona zona Eixample o s\'envia per Correus.',
            'Material quasi nou, comprat i poc usat per canvi de posició. Ideal per iniciar-se en l\'hoquei patins.',
            'Equip complet en molt bon estat. Venut per canvi de talla del fill. Preu per aviat venda.',
            'Busco comprador ràpid, me\'n vaig de viatge. Preu inamovible. Recollida a Vic o enviament contractat pel comprador.',
            'Usat una temporada, molt ben conservat. Revisat per tècnic especialitzat. Garantia de funcionament.',
            'Adquirit el passat novembre, sortit poc a la pista. Totes les peces en perfecte estat, casc, protectors i guants.',
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
