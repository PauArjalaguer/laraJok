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
//include("curl.php");
include("cnx/c.php");

function parseMatch($idMatch, $idLeague, $mysqli)
{
    echo "select idLocal, idVisitor from matches where idMatch=" . $idMatch;
    $result =   $mysqli->query("select idLocal, idVisitor from matches where idMatch=" . $idMatch);
    $row = mysqli_fetch_array($result);
    $idLocal = $row['idLocal'];
    $idVisitor = $row['idVisitor'];
    $mysqli->query("delete from  player_match where idMatch=" . $idMatch);
    $dom   = new DOMDocument('1.0');
    $url = "https://www.server2.sidgad.es/fecapa/fecapa_gr_" . $idMatch . "_1.php";
    //  $url="https://www.server2.sidgad.es/fecapa/fecapa_gr_83612_1.php";
    //$curled = getCurl("https://www.server2.sidgad.es/fecapa/fecapa_gr_".$idMatch."_1.php");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    $postData = [
        'idm' => 1,
        'idc' => $idLeague,
        'idp' => $idMatch,
        'tab' => 'tab_ficha_resumen'
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    // Set the headers
    $headers = [
        'Host: server2.sidgad.es',
        'Origin: http://server2.sidgad.es',
        'Referer: http://server2.sidgad.es',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Content-Type: text/html; charset=UTF-8'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    /*  if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        return $response;
    } */
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
                        echo " HA MARCAT $gols GOLS ";
                    } else {
                        $gols = 0;
                    }

                    if ($players->childNodes[21]->nodeValue) {
                        $red = $players->childNodes[21]->nodeValue;
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

                     if ($players->childNodes[21]->nodeValue) {
                        $red = $players->childNodes[21]->nodeValue;
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
        }
    }
    $mysqli->query("update matches set updatedTries=updatedTries+1, updated=now() where idMatch=" . $idMatch);
}
// parseMatch(58169, $mysqli);

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
    //echo $row['idLeague']." - ";
    parseMatch($row['idMatch'], $row['idLeague'], $mysqli);
}
