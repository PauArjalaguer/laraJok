<?php
/**
 * RESET I TEST COMPLET DELS CRONS FECAPA
 * =======================================
 * idSeason = 39 (temporada passada, dades completes)
 * 1. Truncate: player_match, classifications, matches, phases, leagues
 * 2. Crida els endpoints en ordre
 * 3. Mostra resultats i temps
 */
$SEASON = 39;

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$startTotal = microtime(true);

function printSection(string $title): void {
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "  {$title}\n";
    echo str_repeat('=', 60) . "\n";
}

function printInfo(string $msg): void {
    echo "  -> {$msg}\n";
}

function callApi(string $endpoint, string $label): array {
    $start = microtime(true);
    $url   = "http://larajok.test/api/fecapa/{$endpoint}";
    echo "\n  [RUN] {$label}\n";
    echo "        {$url}\n";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 180);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $elapsed  = round(microtime(true) - $start, 2);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        echo "  [ERR] HTTP {$httpCode} ({$elapsed}s)\n";
        if ($response) echo "        " . substr($response, 0, 300) . "\n";
        return [];
    }

    $json = json_decode($response, true) ?? [];
    echo "  [OK]  " . ($json['message'] ?? 'ok') . " ({$elapsed}s)\n";
    if (isset($json['processed'])) echo "        Processats: {$json['processed']}\n";
    if (isset($json['log']) && is_array($json['log'])) {
        foreach (array_slice($json['log'], 0, 5) as $entry) {
            if (isset($entry['idLeague'])) {
                $matches = $entry['matches_processed'] ?? ($entry['rows_processed'] ?? '?');
                $skip = $entry['skipped'] ?? null;
                $err  = $entry['error'] ?? null;
                if ($err)  echo "        [L{$entry['idLeague']}] ERR: {$err}\n";
                elseif ($skip) echo "        [L{$entry['idLeague']}] skip: {$skip}\n";
                else echo "        [L{$entry['idLeague']}] {$matches} registres\n";
            }
        }
        if (count($json['log']) > 5) echo "        ... i " . (count($json['log']) - 5) . " mes\n";
    }
    return $json;
}

function countTable(string $table): int {
    $row = DB::selectOne("SELECT COUNT(*) as n FROM {$table}");
    return (int)($row->n ?? 0);
}

// ─────────────────────────────────────────────────────────────
// PAS 1: TRUNCATE
// ─────────────────────────────────────────────────────────────
printSection('PAS 1: TRUNCATE TAULES');

echo "\n  Registres actuals:\n";
printInfo("leagues:         " . countTable('leagues'));
printInfo("phases:          " . countTable('phases'));
printInfo("matches:         " . countTable('matches'));
printInfo("classifications: " . countTable('classifications'));
printInfo("player_match:    " . countTable('player_match'));
printInfo("teams:           " . countTable('teams') . " (no es truncara)");

echo "\n  Fent truncate...\n";
DB::statement("SET FOREIGN_KEY_CHECKS=0");
DB::statement("TRUNCATE TABLE player_match");   printInfo("player_match OK");
DB::statement("TRUNCATE TABLE classifications"); printInfo("classifications OK");
DB::statement("TRUNCATE TABLE matches");        printInfo("matches OK");
DB::statement("TRUNCATE TABLE phases");         printInfo("phases OK");
DB::statement("TRUNCATE TABLE leagues");        printInfo("leagues OK");
DB::statement("SET FOREIGN_KEY_CHECKS=1");

echo "\n  Verificant buit:\n";
printInfo("leagues: "         . countTable('leagues'));
printInfo("matches: "         . countTable('matches'));
printInfo("classifications: " . countTable('classifications'));
printInfo("player_match: "    . countTable('player_match'));

// ─────────────────────────────────────────────────────────────
// PAS 2: CRONS EN ORDRE
// ─────────────────────────────────────────────────────────────
printSection('PAS 2: EXECUCIO DELS CRONS');

// CRON 1: Lligues
callApi('gestio_lligues', '02 - Insereix lligues de FECAPA');
$totalLligues = DB::selectOne("SELECT COUNT(*) as n FROM leagues WHERE idSeason = $SEASON")->n;
printInfo("leagues ara: " . countTable('leagues') . " total | {$totalLligues} de season {$SEASON}");

// CRON 2: Partits — loop fins que totes les lligues de la season tinguin partits
echo "\n  [LOOP] 04 - Parseja partits temporada {$SEASON} (limit=50 per passada)\n";
$passada    = 1;
$maxPassades = 8;
do {
    $prevMatches = countTable('matches');
    callApi("parseja_partits?force=1&idSeason={$SEASON}&limit=50", "Passada {$passada}");
    $nowMatches = countTable('matches');
    $lligues0   = (int)DB::selectOne("
        SELECT COUNT(*) as n FROM leagues l
        WHERE l.idSeason = {$SEASON}
          AND NOT EXISTS (SELECT 1 FROM matches m WHERE m.idLeague = l.idLeague)
    ")->n;
    printInfo("Partits totals: {$nowMatches} | Lligues sense partits: {$lligues0}");
    $passada++;
} while ($lligues0 > 0 && $nowMatches > $prevMatches && $passada <= $maxPassades);

printInfo("Resultat final: " . countTable('phases') . " phases, " . countTable('matches') . " matches");

// CRON 3: Equips
callApi('parseja_equips', 'parseTeams - Assigna idCategory/idSeason als equips');

// CRON 4: Classificacions (limit=200 per cobrir totes les lligues)
callApi("parseja_classificacions?idSeason={$SEASON}&limit=200", "06 - Parseja classificacions temporada {$SEASON}");
printInfo("classifications ara: " . countTable('classifications'));

// CRON 5: Acta de partits
callApi('parseja_acta_partits', 'parseMatch - Parseja actes de partits');
printInfo("player_match ara: " . countTable('player_match'));

// CRON 6: Post-cron cleanup
callApi('post_cron_cleanup', 'Post-cron cleanup - Recalcula idCategory, phases, etc.');

// ─────────────────────────────────────────────────────────────
// PAS 3: RESUM FINAL
// ─────────────────────────────────────────────────────────────
printSection('PAS 3: RESUM FINAL');

$elapsed_total = round(microtime(true) - $startTotal, 1);

$leagues_total = countTable('leagues');
$leagues_ok    = (int)DB::selectOne("SELECT COUNT(*) as n FROM leagues WHERE idCategory > 0")->n;
$leagues_zero  = $leagues_total - $leagues_ok;

echo "\n  Estat final:\n";
printInfo("leagues:         {$leagues_total} ({$leagues_ok} amb cat, {$leagues_zero} sense)");
printInfo("phases:          " . countTable('phases'));
printInfo("matches:         " . countTable('matches'));
printInfo("classifications: " . countTable('classifications'));
printInfo("player_match:    " . countTable('player_match'));
printInfo("teams:           " . countTable('teams'));

echo "\n  Lligues temporada {$SEASON}:\n";
$rows = DB::select("
    SELECT l.idLeague, l.leagueName, l.idCategory, 
           COUNT(DISTINCT m.idMatch) AS partits,
           (SELECT COUNT(*) FROM classifications c 
            JOIN phases p ON c.idGroup = p.idGroup 
            WHERE p.idLeague = l.idLeague) AS classif
    FROM leagues l
    LEFT JOIN matches m ON m.idLeague = l.idLeague
    WHERE l.idSeason = {$SEASON}
    GROUP BY l.idLeague
    HAVING partits > 0
    ORDER BY l.idCategory, partits DESC
");
foreach ($rows as $l) {
    $cat  = $l->idCategory > 0 ? "cat {$l->idCategory}" : "X cat 0";
    $cok  = $l->classif > 0 ? "OK {$l->classif} clf" : "X 0 clf";
    echo "    [{$l->idLeague}] {$l->leagueName} ({$cat}, {$l->partits}p, {$cok})\n";
}

echo "\n  Temps total: {$elapsed_total}s\n";
$ok = ($leagues_zero === 0 && countTable('classifications') > 0);
echo $ok ? "\n  TOT OK! La web hauria de funcionar.\n" : "\n  Revisa els errors anteriors.\n";
echo "\nFet!\n";
