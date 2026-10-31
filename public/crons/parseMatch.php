<html>

<head>
    <meta http-equiv="refresh" content="15">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <title>Cron de dades de partits</title>
</head>
<?php
function cleanName($s)
{
    $s = trim($s); // treu espais davant i darrere
    $s = str_replace("\xC2\xA0", " ", $s); // canvia NBSP per espai normal
    $s = preg_replace('/\s+/u', ' ', $s); // col·lapsa espais múltiples
    return $s;
}

error_reporting(0);
ini_set('display_errors', 1);
include("curl.php");
include("cnx/c.php");

/**
 * Descarrega la fitxa de temporada d'un jugador a FECAPA.
 * Ex: https://www.server2.sidgad.es/fecapa/profiles/fecapa_profileseason_62764_1_41.php
 * Important: sense la capçalera Origin el servidor retorna buit.
 */
function getPlayerProfile($idPlayer, $idSeason)
{
    $url = "https://www.server2.sidgad.es/fecapa/profiles/fecapa_profileseason_" . intval($idPlayer) . "_1_" . intval($idSeason) . ".php";
    $response = getCurl($url);
    return $response ?: null;
}

/**
 * Acta buida (problema de FECAPA): recorrem els jugadors del partit i
 * agafem gols i targetes de la seva fitxa de temporada.
 * De pas, guardem la data de naixement del jugador.
 */
function fixMatchFromPlayerProfiles($idMatch, $mysqli)
{
    $idMatch = intval($idMatch);
    $res = $mysqli->query("select l.idSeason from matches m join leagues l on l.idLeague=m.idLeague where m.idMatch=$idMatch");
    $row = $res ? mysqli_fetch_array($res) : null;
    if (!$row || !$row['idSeason']) {
        echo "<br />No trobo la temporada del partit $idMatch";
        return;
    }
    $idSeason = intval($row['idSeason']);
    echo "<br /><b>ACTA BUIDA al partit $idMatch -> recupero dades de les fitxes de jugador (temporada $idSeason)</b>";
    // Les dades del partit canvien: eliminem la crònica generada per IA
    $mysqli->query("update matches set cronica=NULL where idMatch=$idMatch");

    $players = $mysqli->query("select idPlayer from player_match where idMatch=$idMatch");
    while ($p = mysqli_fetch_array($players)) {
        $idPlayer = intval($p['idPlayer']);
        $response = getPlayerProfile($idPlayer, $idSeason);
        if (!$response) {
            echo "<br />&nbsp;&nbsp;Fitxa buida pel jugador $idPlayer";
            continue;
        }

        // Data de naixement
        if (preg_match('/Data de Naixement:?.*?(\d{2})\/(\d{2})\/(\d{4})/s', $response, $m)) {
            $birthDate = $m[3] . "-" . $m[2] . "-" . $m[1];
            try {
                $mysqli->query("update players set birthDate='$birthDate' where idPlayer=$idPlayer");
            } catch (Exception $ex) {
                echo " -> Algo ha passat al guardar la data de naixement del jugador $idPlayer";
            }
        }

        // Fila del partit: date, local, resultat, visitant, link(idp), G, As, FD, PE, BL, VER
        $dom = new DOMDocument('1.0');
        if (!@$dom->loadHTML('<?xml encoding="UTF-8">' . $response)) {
            continue;
        }
        $xpath = new DomXPath($dom);
        $links = $xpath->query("//a[contains(@class,'game_report') and @idp='$idMatch']");
        if ($links->length == 0) {
            echo "<br />&nbsp;&nbsp;El jugador $idPlayer no té el partit $idMatch a la fitxa";
            continue;
        }
        $tr = $links[0];
        while ($tr && $tr->nodeName != 'tr') {
            $tr = $tr->parentNode;
        }
        if (!$tr) {
            continue;
        }
        $tds = $tr->getElementsByTagName('td');
        if ($tds->length < 11) {
            continue;
        }
        $gols     = intval(trim($tds[5]->nodeValue));
        $directes = intval(trim($tds[7]->nodeValue));
        $penalti  = intval(trim($tds[8]->nodeValue));
        $blue     = intval(trim($tds[9]->nodeValue));
        $red      = intval(trim($tds[10]->nodeValue));

        echo "<br />&nbsp;&nbsp;Jugador $idPlayer: $gols gols, $blue blaves, $red vermelles";
        try {
            $mysqli->query("update player_match set goals=$gols, blue=$blue, red=$red, directes=$directes, penalti=$penalti, updated=now()
                where idPlayer=$idPlayer and idMatch=$idMatch");
        } catch (Exception $ex) {
            echo " -> Algo ha passat al actualitzar el jugador $idPlayer al partit $idMatch";
        }
    }
}

function parseMatch($idMatch, $idLeague, $mysqli)
{
    echo "select idLocal, idVisitor from matches where idMatch=" . $idMatch;
    $result =   $mysqli->query("select idLocal, idVisitor, localResult, visitorResult from matches where idMatch=" . $idMatch);
    $row = mysqli_fetch_array($result);
    $idLocal = $row['idLocal'];
    $idVisitor = $row['idVisitor'];
    $totalResult = intval($row['localResult']) + intval($row['visitorResult']);
    $totalGols = 0;
    $mysqli->query("delete from  player_match where idMatch=" . $idMatch);
    // Refem l'acta: eliminem la crònica generada per IA perquè es torni a generar amb les dades noves
    $mysqli->query("update matches set cronica=NULL where idMatch=" . intval($idMatch));
    $dom   = new DOMDocument('1.0');
    $url = "https://www.server2.sidgad.es/fecapa/fecapa_gr_" . $idMatch . "_1.php";
    $response = getCurl($url);

    if ($response) {
        $html = $dom->loadHTML($response);
        print_r($html);
        if (!$html) {
            $mysqli->query("update matches set error=1 where idMatch=" . $idMatch);
            echo "<br />La url  no existeix<br />";
        }
        if ($html) {
            $mysqli->query("update matches set error=0 where idMatch=" . $idMatch);
            $dom->preserveWhiteSpace = true;
            $xpath = new DomXPath($dom);


            /// $tbody = $xpath->query("//div[contains(@class, 'atag')]");
            $table = $xpath->query(("//table"));
            $faltesLocal = $table[1]->childNodes[1]->childNodes[5]->nodeValue;

            $faltesVisitant = $table[1]->childNodes[1]->childNodes[9]->nodeValue;
            echo "<br />faltes local :" . $faltesLocal . " faltes visitant:" . $faltesVisitant;

            $arbitre = utf8_decode(trim($table[2]->childNodes[1]->childNodes[1]->nodeValue));
            $a = explode("ARBITRATGE	", $arbitre);
            $arbitre = trim($a[1]);
            echo "<br />Arbitre: " . $arbitre;

            try {
                $mysqli->query("update matches set referee='$arbitre', localFaults=$faltesLocal, visitorFaults=$faltesVisitant where idMatch=$idMatch");
            } catch (Exception $ex) {
                echo " -> Algo ha passat al insertar l' arbitre";
            }
            $p = $xpath->query("//div[contains(@class, 'player_season_stats')]");
            foreach ($p as $players) {
                echo "<br /> Jugador -> ";
                $id = $players->attributes[0]->nodeValue;
                echo  $cognomPre = $players->nodeValue;
                $cognom = "";
                $c = explode(" ", $cognomPre);
                for ($a = 1; $a <= count($c); $a++) {
                    $cognom .= $c[$a] . " ";
                }
                $nom = $players->childNodes[3]->nodeValue;
                $cognomNet = str_replace($nom, "", $cognom);

                $playerName = trim(addslashes(utf8_decode($nom . " " . $cognomNet)));
                /*    echo "<pre>";
        print_r($players);
        echo "</pre>"; */
                echo "<br />INSERTO JUGADOR $playerName<br />";
                $sql = "insert into players (idPlayer, playerName) values (" . $id . ",'" . $playerName . "') ON DUPLICATE KEY UPDATE playerName='$playerName'";
                echo $sql;
                try {
                    $mysqli->query($sql);
                } catch (Exception $ex) {
                    echo " -> Algo ha passat al insertar jugador $playerName";
                }
            }
            //stats

            $tActa = $xpath->query("//table[contains(@class, 'tabla_acta_print')]");
            foreach ($tActa[3]->childNodes as $players) {
                $a = 0;
                if ($players->tagName == 'tr' && $players->childElementCount > 11) {
                    $capita = false;
                    $gols = 0;
                    $blue = 0;
                    $red = 0;
                    echo "<br />";
                    echo $dorsal = $players->childNodes[1]->nodeValue;
                    echo " ";
                    echo $capita = $players->childNodes[7]->nodeValue;
                    if (strlen($capita) > 0) {
                        $capita = true;
                        echo " CAPITA  ";
                    } else {
                        $capita = false;
                    }
                    echo $nom = addslashes(utf8_decode($players->childNodes[9]->nodeValue));

                    $gols = trim($players->childNodes[11]->nodeValue);
                    if (strlen($gols) > 0) {
                        $gols = $players->childNodes[11]->nodeValue;
                        $totalGols += intval($gols);
                        echo " HA MARCAT $gols GOLS ";
                    } else {
                        $gols = 0;
                    }

                    if ($players->childNodes[21]->nodeValue) {
                        $red = 1;
                        echo " HI HA UNA TARGETA VERMELLA";
                    };
                    if ($players->childNodes[15]->nodeValue) {
                        $blue = 1;
                        echo " HI HA $blue TARGETA BLAVA";
                    }
                    if ($players->childNodes[17]->nodeValue) {
                        $blue = 2;
                        echo " HI HA $blue TARGETA BLAVA";
                    }
                    if ($players->childNodes[19]->nodeValue) {
                        $blue = 3;
                        echo " HI HA $blue TARGETA BLAVA";
                    }

                    $n = explode(",", $nom);
                    $nom = $n[1] . " " . $n[0];
                    $nom = cleanName($nom);
                    $sql = "update players set number = $dorsal  where playerName='$nom'  ";
                    try {
                        $mysqli->query($sql);
                    } catch (Exception $ex) {
                        echo " -> Algo ha passat al insertar el jugador $nom al partit $idMatch";
                    }
                    $sql = "INSERT INTO player_match (idPlayer, idMatch,  goals, blue,red, idTeam) VALUES ((SELECT idPlayer FROM players where playerName='$nom' LIMIT 1), $idMatch, $gols, $blue,$red,$idLocal);";
                    //echo "<br />" . $sql;
                    if (strlen($nom) > 3) {
                        try {
                            $mysqli->query($sql);
                        } catch (Exception $ex) {
                            echo " -> Algo ha passat al insertar el jugador $nom al partit $idMatch";
                        }
                    }
                }
                $a++;
            }
            foreach ($tActa[6]->childNodes as $players) {
                $a = 0;
                if ($players->tagName == 'tr' && $players->childElementCount > 11) {
                    $capita = false;
                    $gols = 0;
                    $blue = 0;
                    $red = 0;
                    echo "<br />";
                    echo $dorsal = $players->childNodes[1]->nodeValue;
                    echo " ";
                    echo $capita = $players->childNodes[7]->nodeValue;
                    if (strlen($capita) > 0) {
                        $capita = true;
                        echo " CAPITA  ";
                    } else {
                        $capita = false;
                    }

                    echo $nom = addslashes(utf8_decode($players->childNodes[9]->nodeValue));
                     $gols = trim($players->childNodes[11]->nodeValue);
                      if (strlen($gols) > 0) {
                        $gols = $players->childNodes[11]->nodeValue;
                        $totalGols += intval($gols);
                        echo " HA MARCAT $gols GOLS ";
                    } else {
                        $gols = 0;
                    }

                     if ($players->childNodes[21]->nodeValue) {
                        $red =1;
                        echo " HI HA UNA TARGETA VERMELLA";
                    };
                    if ($players->childNodes[15]->nodeValue) {
                        $blue = 1;
                        echo " HI HA $blue TARGETA BLAVA";
                    }
                    if ($players->childNodes[17]->nodeValue) {
                        $blue = 2;
                        echo " HI HA $blue TARGETA BLAVA";
                    }
                    if ($players->childNodes[19]->nodeValue) {
                        $blue = 3;
                        echo " HI HA $blue TARGETA BLAVA";
                    }

                    $n = explode(",", $nom);
                    $nom = $n[1] . " " . $n[0];
                    $nom = cleanName($nom);
                    $sql = "update players set number = $dorsal  where playerName='$nom'  ";
                    try {
                        $mysqli->query($sql);
                    } catch (Exception $ex) {
                        echo " -> Algo ha passat al insertar el jugador $nom al partit $idMatch";
                    }
                    //$sql = "INSERT INTO player_match (idPlayer, idMatch, goals, blue,red, idTeam) VALUES ((SELECT idPlayer FROM players where playerName='$nom' LIMIT 1), $idMatch, $gols, $blue,$red,$idVisitor);";
                    $sql = "INSERT INTO player_match (idPlayer, idMatch, goals, blue, red, idTeam)
SELECT p.idPlayer, $idMatch, $gols, $blue, $red, $idVisitor
FROM players p
WHERE p.playerName = '$nom'
LIMIT 1;";
                    echo "<br />" . $sql;
                    if (strlen($nom) > 3) {
                        try {
                            $mysqli->query($sql);
                        } catch (Exception $ex) {
                            echo " -> Algo ha passat al insertar el jugador $nom al partit $idMatch";
                        }
                    }
                }
                $a++;
            }

            // Acta buida: cap gol ni cap falta però el resultat no és 0-0
            if ($totalGols == 0 && intval($faltesLocal) == 0 && intval($faltesVisitant) == 0 && $totalResult > 0) {
                fixMatchFromPlayerProfiles($idMatch, $mysqli);
            }
        }
    }
    $mysqli->query("update matches set updatedTries=updatedTries+1, updated=now() where idMatch=" . $idMatch);
}
// parseMatch(58169, $mysqli);

// Mode reparació: parseMatch.php?fixEmpty=1 -> partits ja guardats amb l'acta buida
if (isset($_GET['fixEmpty'])) {
    $result = $mysqli->query("select m.idMatch from matches m
        where m.matchDate<NOW()
          and m.localFaults=0 and m.visitorFaults=0
          and (m.localResult + m.visitorResult) > 0
          and exists (select 1 from player_match pm where pm.idMatch=m.idMatch)
          and (select coalesce(sum(goals),0) from player_match pm where pm.idMatch=m.idMatch)=0
          and not exists (select 1 from player_match pm where pm.idMatch=m.idMatch and pm.updated is not null)
        order by m.matchDate desc
        limit 0,15");
    while ($row = mysqli_fetch_array($result)) {
        fixMatchFromPlayerProfiles($row['idMatch'], $mysqli);
    }
    exit;
}

//$result = $mysqli->query("select idMatch from matches where idMatch not in(select idMatch from player_match) and idMatch not in (37857,82928,82916,82900,82944,82116,76210,83048,83065,83065,83085,83082) ORDER BY downloadedtime desc limit 0,50");

//$result =  $mysqli->query("select distinct idMatch from matches m where matchDate<now() and (localFaults=0 and visitorFaults=0 and length(referee)<2) and (select count(*) from player_match where idMatch =m.idMatch)<5 and length(m.idMatch)<7 and idMatch not in (82916,82900,82944,82116,76210,83048,83065,83065,83085,83082,82130,6206) limit 100");
$result =  $mysqli->query("select  idMatch, localFaults, visitorFaults, referee, matchDate, idLeague from matches m 
WHERE 
matchDate<NOW() 
-- and (localFaults=0 and visitorFaults=0 and (length(referee)<2 or referee is NULL)) 
  and (select count(*) from player_match where idMatch =m.idMatch)<5 
  and length(m.idMatch)<7 
 AND (ERROR!=1 OR ERROR IS NULL) 
   and updatedTries<10
 -- and idMatch=83177
order by updated asc, matchDate deSC
limit 0,50
");

while ($row = mysqli_fetch_array($result)) {
    echo $row['idLeague']." - ";
    parseMatch($row['idMatch'], $row['idLeague'], $mysqli);
}