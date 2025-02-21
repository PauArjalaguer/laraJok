<?php

include("../cnx/fcf_cnx.php");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

function quitarAcentos($string) {
    $acentos = ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
                'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
                'ñ' => 'n', 'Ñ' => 'N'];
    return strtr($string, $acentos);
}
function buscaEquipsiLLigues($clubUrl)
{
    echo "buscaEquipsiLLigues $clubUrl";
    // Get the HTML content from the URL
    $html = file_get_contents($clubUrl);

    if ($html === false) {
        return "Error fetching content";
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Suppress warnings from malformed HTML
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // Find all tables with the class 'fcftable w-100 mb-15'
    $tables = $xpath->query("//table[contains(@class, 'fcftable') and contains(@class, 'w-100') and contains(@class, 'mb-15')]");

    foreach ($tables as $table) {
        $pdo = new PDO('mysql:host=localhost;dbname=fcfcat;charset=utf8mb4', 'fcfcat', 'Arn@u1b3rt@');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $teamName = $xpath->evaluate("string(.//tr[1]/td[1]/a)", $table);
        $teamUrl = $xpath->evaluate("string(.//tr[1]/td[1]/a/@href)", $table);
        $leagueNameOriginal = trim(strtolower($xpath->evaluate("string(.//tr[1]/td[2])", $table)));
        $groupName = trim(strtolower($xpath->evaluate("string(.//tr[1]/td[2]/span)", $table)));
        $leagueName = trim(str_replace($groupName, "", $leagueNameOriginal));

        $leagueName = preg_replace('/\s+/', '-', $leagueName);
        $groupName = preg_replace('/\s+/', '-', $groupName);
        $groupName = str_replace(['(', ')'], '', $groupName);

        $leagueUrl = quitarAcentos(mb_strtolower("https://www.fcf.cat/calendari/2425/futbol-sala/" . $leagueName . '/' . $groupName));

        $stmt = $pdo->prepare("SELECT id_team FROM teams WHERE id_team = ?");
        $acron = explode("/",$teamUrl)[5];
        $stmt->execute([$teamUrl]);
        $exisitingTeam = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$exisitingTeam) {
            $stmt = $pdo->prepare("INSERT INTO teams (id_team,teamname,teamacronym) VALUES (?,?,?)");
            $stmt->execute([
                $teamUrl,
                $teamName,
                $acron
               
            ]);
        }

        $stmt = $pdo->prepare("SELECT id_league FROM leagues WHERE id_league = ?");

        $stmt->execute([$leagueUrl]);
        $exisitingLeague = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$exisitingLeague) {
            $stmt = $pdo->prepare("INSERT INTO leagues (id_league,leaguename, id_season,updateddate) VALUES (?,?,?,?)");
            $stmt->execute([
                $leagueUrl,
                ucwords($leagueNameOriginal),20,date('Y-m-d H:i:s')
               
            ]);
        }
    }
}
/* $url = "https://www.fcf.cat/club/2425/espardenya-masquefa-fs/pbs";
buscaEquipsiLLigues($url, 1);
 */

 $pdo = new PDO('mysql:host=localhost;dbname=fcfcat;charset=utf8mb4', 'fcfcat', 'Arn@u1b3rt@');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare("SELECT id_club FROM clubs");
$stmt->execute();
$id_clubs = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($id_clubs as $id_club) {
    if (buscaEquipsiLLigues($id_club)) {
        echo "<hr />Dades actualitzades correctament";
      
    };
}
