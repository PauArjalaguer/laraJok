<?php
/**
 * Mostra les categories disponibles i les lligues de la temporada actual (41)
 * per entendre el mismatch
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CATEGORIES A LA BD ===\n";
$cats = DB::select("SELECT idCategory, categoryName FROM categories ORDER BY idCategory");
foreach ($cats as $c) {
    echo "  [{$c->idCategory}] {$c->categoryName}\n";
}

echo "\n=== LLIGUES TEMPORADA 41 (actual) ===\n";
$lligues = DB::select("
    SELECT idLeague, leagueName, idCategory
    FROM leagues
    WHERE idSeason = 41
    ORDER BY idCategory, leagueName
");
foreach ($lligues as $l) {
    $cat = $l->idCategory > 0 ? "[{$l->idCategory}]" : "[0 ❌]";
    echo "  {$cat} [{$l->idLeague}] {$l->leagueName}\n";
}

echo "\n=== RESUM TEMPORADA 41 ===\n";
$total = count($lligues);
$amb_cat = count(array_filter($lligues, fn($l) => $l->idCategory > 0));
$sense_cat = $total - $amb_cat;
echo "  Total lligues: $total\n";
echo "  Amb categoria: $amb_cat\n";
echo "  Sense categoria (idCategory=0): $sense_cat\n";
