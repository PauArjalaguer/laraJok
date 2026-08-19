<?php
/**
 * Script de diagnosi i fix de idCategory = 0 a les lligues
 * 
 * Estrategia:
 * 1. Intenta fer match per nom exacte entre seasons anteriors
 * 2. Si no, intenta match per nom normalitzat (sense accents ni espais)
 * 3. Mostra les lligues que seguiran a 0 per revisió manual
 */

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// 1. Mostra les lligues amb idCategory = 0 de la temporada actual
$zeros = DB::select("
    SELECT idLeague, leagueName, idSeason, idCategory
    FROM leagues
    WHERE idCategory = 0
    ORDER BY idSeason DESC, leagueName ASC
    LIMIT 50
");

echo "=== LLIGUES AMB idCategory = 0 ===\n";
echo count($zeros) . " lligues sense categoria\n\n";

foreach ($zeros as $l) {
    echo "  [{$l->idLeague}] {$l->leagueName} (season {$l->idSeason})\n";
}

// 2. Intenta assignar idCategory per nom exacte d'una lliga anterior
echo "\n=== FIX PER NOM EXACTE (seasons anteriors) ===\n";
$fixedExact = DB::statement("
    UPDATE leagues l
    JOIN (
        SELECT leagueName, MAX(idCategory) AS idCategory
        FROM leagues
        WHERE idCategory > 0
        GROUP BY leagueName
    ) prev ON l.leagueName = prev.leagueName
    SET l.idCategory = prev.idCategory
    WHERE l.idCategory = 0
");
echo "Actualitzades per nom exacte.\n";

// Quantes queden a 0?
$remaining = DB::select("SELECT COUNT(*) as total FROM leagues WHERE idCategory = 0");
echo "Resten a 0: " . $remaining[0]->total . "\n";

// 3. Fix per LIKE sobre el nom de la categoria
echo "\n=== FIX PER LIKE SOBRE categoryName ===\n";
DB::statement("
    UPDATE leagues l
    JOIN categories c ON LOWER(l.leagueName) LIKE CONCAT('%', LOWER(c.categoryName), '%')
    SET l.idCategory = c.idCategory
    WHERE l.idCategory = 0
");
echo "Actualitzades per LIKE categoryName.\n";

$remaining2 = DB::select("SELECT COUNT(*) as total FROM leagues WHERE idCategory = 0");
echo "Resten a 0: " . $remaining2[0]->total . "\n";

// 4. Mostra les que queden a 0 amb la millor candidata possible (per similitud de nom)
echo "\n=== LLIGUES QUE SEGUEIXEN A 0 (revisar manualment) ===\n";
$stillZero = DB::select("
    SELECT l.idLeague, l.leagueName, l.idSeason
    FROM leagues l
    WHERE l.idCategory = 0
    ORDER BY l.idSeason DESC, l.leagueName
");
if (count($stillZero) === 0) {
    echo "  -> Cap lliga amb idCategory = 0. Tot corregit!\n";
} else {
    foreach ($stillZero as $l) {
        echo "  [{$l->idLeague}] {$l->leagueName} (season {$l->idSeason})\n";
        // Busca la lliga amb nom mes similar amb idCategory > 0
        $candidate = DB::selectOne("
            SELECT leagueName, idCategory
            FROM leagues
            WHERE idCategory > 0
              AND (
                LOWER(leagueName) LIKE CONCAT('%', SUBSTRING(LOWER(?), 1, 10), '%')
                OR LOWER(?) LIKE CONCAT('%', SUBSTRING(LOWER(leagueName), 1, 10), '%')
              )
            LIMIT 1
        ", [$l->leagueName, $l->leagueName]);
        if ($candidate) {
            echo "    -> Candidata: [{$candidate->idCategory}] {$candidate->leagueName}\n";
        } else {
            echo "    -> Sense candidata clara\n";
        }
    }
}

echo "\n=== RESUM FINAL ===\n";
$summary = DB::select("
    SELECT idCategory, COUNT(*) as total
    FROM leagues
    WHERE idSeason >= 40
    GROUP BY idCategory
    ORDER BY idCategory
");
foreach ($summary as $row) {
    echo "  idCategory={$row->idCategory}: {$row->total} lligues\n";
}

echo "\nFet!\n";
