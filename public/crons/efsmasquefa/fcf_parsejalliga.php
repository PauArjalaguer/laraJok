<?php

include("../cnx/fcf_cnx.php");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

function parsejaCalendari($url)
{
    $a = file_get_contents($url);
    $dom   = new DOMDocument('1.0');
    $html = $dom->loadHTML($a);

    $dom->preserveWhiteSpace = true;
    $xpath = new DomXPath($dom);

    $tables = $xpath->query("//table[contains(@class, 'calendaritable')]");
    $pdo = new PDO('mysql:host=localhost;dbname=fcfcat;charset=utf8mb4', 'fcfcat', 'Arn@u1b3rt@');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $matches = [];
    foreach ($tables as $table) {
        $idRound = str_replace("Jornada", "", $xpath->evaluate("string(.//thead/tr/th[1])", $table));
        $matchdate = $xpath->evaluate("string(.//thead/tr/th[2])", $table);
        $matchdate = date('Y-m-d', strtotime($matchdate));
        $matchhour = "09:00";
        $rows = $xpath->query(".//tbody/tr", $table);
        foreach ($rows as $row) {
            $localTeamNode = $xpath->query(".//td[1]/a", $row)->item(0);
            $localTeamName = $localTeamNode ? trim($localTeamNode->textContent) : '';
            $localTeamUrl = $localTeamNode ? $localTeamNode->getAttribute('href') : '';
            $localImg = $xpath->evaluate("string(.//td[2]/a/img/@src)", $row);

            $localScore = $xpath->evaluate("string(.//td[3])", $row);
            $visitorScore = $xpath->evaluate("string(.//td[5])", $row);

            $visitorImg = $xpath->evaluate("string(.//td[6]/a/img/@src)", $row);

            $visitorTeamNode = $xpath->query(".//td[7]/a", $row)->item(0);
            $visitorTeamName = $visitorTeamNode ? trim($visitorTeamNode->textContent) : '';
            $visitorTeamUrl = $visitorTeamNode ? $visitorTeamNode->getAttribute('href') : '';

            $matchUrlNode = $xpath->query(".//td[contains(@class, 'calendaritablehover')]/a", $row)->item(0);
            $matchUrl = $matchUrlNode ? $matchUrlNode->getAttribute('href') : '';
            $stmt = $pdo->prepare("SELECT id_match FROM matches WHERE leagueurl = ? AND id_round = ? AND localurl = ? AND visitorurl = ?");
            
            $stmt->execute([$url, $idRound, $localTeamUrl, $visitorTeamUrl]);
            $existingMatch = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($localTeamName != "" && $visitorTeamName != "") {
                if ($existingMatch) {               
                    $stmt = $pdo->prepare("UPDATE matches SET localresult = ?, visitorresult = ? WHERE id_match = ?");
                    $stmt->execute([
                        is_numeric($localScore) ? (int)$localScore : null,
                        is_numeric($visitorScore) ? (int)$visitorScore : null,
                        $existingMatch['id_match']
                    ]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO matches (id_round,localname, localimage, visitorname, visitorimage, matchdate, matchhour, localresult, visitorresult, id_league, id_group, leagueurl, localurl, visitorurl,matchurl) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
                    $stmt->execute([
                        $idRound,
                        $localTeamName,
                        $localImg,
                        $visitorTeamName,
                        $visitorImg,
                        $matchdate,
                        $matchhour,
                        is_numeric($localScore) ? (int)$localScore : null,
                        is_numeric($visitorScore) ? (int)$visitorScore : null,
                        1, // Dummy league ID, replace as needed
                        1, // Dummy group ID, replace as needed
                        $url,
                        $localTeamUrl,
                        $visitorTeamUrl,
                        $matchUrl
                    ]);
                }
            }
            /*  $matches[] = [
            'jornada' => $jornada,
            'date' => $date,
            'local_team' => [
                'name' => $localTeamName,
                'url' => $localTeamUrl,
                'img' => $localImg,
            ],
            'local_score' => $localScore,
            'visitor_score' => $visitorScore,
            'visitor_team' => [
                'name' => $visitorTeamName,
                'url' => $visitorTeamUrl,
                'img' => $visitorImg,
            ],
            'match_url' => $matchUrl,
        ]; */
        }
    }
    return 1;
}
function convertirCalendariAEquip($url)
{
    // Comprovar si la URL conté "calendari-equip"
    $b = explode("/", $url);

    return $b[count($b) - 1]; // Retorna false si la URL no coincideix amb el format esperat
}
function buscaHoraris($url)
{   echo "<hr /><h1>Buscant horaris per a la lliga " . $url ." </h1>";
    $pdo = new PDO('mysql:host=localhost;dbname=fcfcat;charset=utf8mb4', 'fcfcat', 'Arn@u1b3rt@');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT DISTINCT id_round FROM matches WHERE leagueurl = ? AND (localresult IS NULL OR visitorresult IS NULL)");
    $stmt->execute([$url]);
    $idRounds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($idRounds as $idRound) {

        $scheduleurl = str_replace("calendari", "resultats", $url) . "/jornada-" . $idRound;
        echo "<hr />Buscant horaris per a la jornada " . $idRound . " a " . $scheduleurl;

        $a = file_get_contents($scheduleurl);
        $dom   = new DOMDocument('1.0');
        $html = $dom->loadHTML($a);

        $dom->preserveWhiteSpace = true;
        $xpath = new DomXPath($dom);
        $tables = $xpath->query("//table[contains(@class, 'table_resultats')]");

        foreach ($tables as $table) {
         
            $rows = $xpath->query(".//tr", $table);
           
            foreach ($rows as $row) { //echo "row";
                $localTeamNode = $xpath->query(".//td[2]/a", $row)->item(0);
                $localTeamName = $localTeamNode ? trim($localTeamNode->textContent) : '';
                $localTeamUrl = $localTeamNode ? $localTeamNode->getAttribute('href') : '';

                $visitorTeamNode = $xpath->query(".//td[4]/a", $row)->item(0);
                $visitorTeamName = $visitorTeamNode ? trim($visitorTeamNode->textContent) : '';
                $visitorTeamUrl = $visitorTeamNode ? $visitorTeamNode->getAttribute('href') : '';

                $matchDate = trim($xpath->evaluate("string(.//td[3]/a/div[1])", $row));
                $matchHour = trim($xpath->evaluate("string(.//td[3]/a/div[2])", $row));
                $matchDateMod = implode("-", array_reverse(explode("-", $matchDate)));

                $locationNode = $xpath->query(".//td[6]/a", $row)->item(0);
                $location = $locationNode ? trim($locationNode->textContent) : '';

                $coordsNode = $xpath->query(".//td[8]/a", $row)->item(0);
                $coords = $locationNode ? trim($coordsNode->getAttribute('href')) : '';

                $matchHour = date('H:i:s', strtotime($matchHour));
                $localTeamUrl = convertirCalendariAEquip($localTeamUrl);
                $visitorTeamUrl = convertirCalendariAEquip($visitorTeamUrl);
                if ($matchDate != "ACTA TANCADA") {
                   // echo "-&bull; " . $matchDate . " ($matchDateMod) " . $matchHour . " " . $localTeamUrl . " vs " . $visitorTeamUrl . " a " . $location . " " . $coords . "|" . $url . "<br>";
                    // Update match information in the database
                    $visitorTeamUrl = "%" . $visitorTeamUrl . "%";
                    $localTeamUrl = "%" . $localTeamUrl . "%";
                    $stmt = $pdo->prepare("UPDATE matches SET matchdate = ?, matchhour = ?, location = ?, coords = ? WHERE localurl like  ?  AND visitorurl like ?  AND leagueurl = ?");
                   
                   try{ $stmt->execute([$matchDateMod, $matchHour, $location, $coords, $localTeamUrl, $visitorTeamUrl, $url]); // Replace 'your_league_url' as needed
                   } catch (Exception $e) {
                       echo $e->getMessage();}
                    $query = "UPDATE matches SET matchdate = ?, matchhour = ?, location = ?, coords = ? WHERE localurl LIKE ? AND visitorurl LIKE ? AND leagueurl = ?";
                   /*  $debugQuery = vsprintf(str_replace("?", "'%s'", $query), [$matchDateMod, $matchHour, $location, $coords, $localTeamUrl, $visitorTeamUrl, $url]);
                    echo $debugQuery;
                    echo "<br>"; */
                }
            }
        }
    }
}

$pdo = new PDO('mysql:host=localhost;dbname=fcfcat;charset=utf8mb4', 'fcfcat', 'Arn@u1b3rt@');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare("SELECT id_league FROM leagues");
$stmt->execute();
$id_leagues = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($id_leagues as $id_league) {
    if (parsejaCalendari($id_league)) {
        echo "<hr />Dades actualitzades correctament";
        buscaHoraris($id_league);
    };
}

