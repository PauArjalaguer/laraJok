<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '30');
error_reporting(E_ALL);
include("curl.php");
?>
<!doctype html>
<html>

<head>
    <title>Parseja lligues</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>


    <div class='w-3/4 mx-auto my-2 bg-slate-100 rounded-xl p-2'>
        <h1 class="text-4xl font-bold p-5">Partits</h1>
        <?php
        $initTime = new DateTime();

        error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);
        include("cnx/c.php");

        function prepareTeamName($teamName)
        {
            return  utf8_decode(addslashes(trim($teamName)));
        }
        function prepareidClub($clubImage, $mysqli)
        {

            if (strpos($clubImage, "https://sidgad.cloud/fecapa/images//logos_clubes/") !== false) {
                $idClub = str_replace("https://sidgad.cloud/fecapa/images//logos_clubes/", "", strtolower(str_replace("\//", "\/", $clubImage)));
            } else {
                $idClub = str_replace("https://ns3104249.ip-54-37-85.eu/fecapa/images//logos_clubes/", "", strtolower(str_replace("\//", "\/", $clubImage)));
            }

            if ($clubImage == 'https://sidgad.cloud/shared/portales_files/images/no_logo.png') {
                $idClub = 0;
            }

            $idClub =  str_replace(".png", "", $idClub);
            $idClub =  str_replace(".gif", "", $idClub);
            $i = explode("_", $idClub);
            $idClub = $i[0];
            if ($idClub == 'no_logo') {
                $idClub = 0;
            }

            try {
                $mysqli->query("update clubs set clubImage='" . $clubImage . "' where idClub=" . $idClub . "");
            } catch (Exception $ex) {
                echo "Hi ha hagut algun problema al actualitzar el club $idClub";
            }
            return $idClub;
        }

        function prepareDate($matchDate)
        {
            $d = explode("/", $matchDate);
            $matchDate = $d[2] . "-" . $d[1] . "-" . $d[0];
            return $matchDate;
        }

        function parseLeague($idLliga, $mysqli, $idSeason = 37)
        {
            $dom   = new DOMDocument('1.0');
            $curled = getCurl("https://www.server2.sidgad.es/fecapa/fecapa_cal_idc_" . $idLliga . "_1.php");
            //$html = $dom->loadHTML(mb_convert_encoding($curled, 'HTML-ENTITIES', 'UTF-8'));
            $html = $dom->loadHTML($curled);
            $dom->preserveWhiteSpace = true;
            $xpath = new DomXPath($dom);

            $div = $xpath->query("//div");
            echo "<br />Data fecapa: " . $div[count($div) - 2]->nodeValue;

            $dateNode = strtotime(trim(str_replace("GMT", "", str_replace("Report Created on ", "", $div[count($div) - 2]->nodeValue))));
            // echo $dateNode;
            $todayAtMidnight = strtotime(date('Y-m-d'));
            echo "<br />Data de fa un quart d' hora:" . date('d-m-Y H:i:s', $todayAtMidnight);

            if (!$_GET['force']) {
                if ($dateNode < $todayAtMidnight) {
                    exit();
                }
            }

            $select = $xpath->query("//select");
            if ($select[0]->childNodes) {
                foreach ($select[0]->childNodes as $phase) {
                    if ($phase->tagName == 'option') {
                        $idPhase = $phase->attributes[0]->nodeValue;
                        $phaseName = addslashes(utf8_decode($phase->nodeValue));
                        echo "\n<div class='flex'>";
                        echo "<div class='bg-slate-100 border-l border-t border-slate-700 w-1/12 p-2 text-center'>$idPhase</div>";
                        echo "<div class='bg-slate-100 border-l border-t border-slate-700 w-11/12 p-2 text-center'>$phaseName</div>";
                        echo "</div>";
                        $sql = "insert into phases (idGroup, groupName, idLeague) values (" . $idPhase . ",'" . $phaseName . "','$idLliga') ON DUPLICATE KEY UPDATE groupName='$phaseName'";
                        // echo $sql;
                        $mysqli->query($sql);
                    }
                }
            } else {
                $idPhase = $idLliga;
                echo "\n<div class='flex'>";
                echo "<div class='bg-slate-100 border-l border-t border-slate-700 w-1/12 p-2 text-center'>$idLliga</div>";
                echo "<div class='bg-slate-100 border-l border-t border-slate-700 w-11/12 p-2 text-center'>No hi ha grups</div>";
                echo "</div>";
                $sql = "insert into phases (idGroup, groupName, idLeague) values (" . $idPhase . ",'Grup únic','$idLliga') ON DUPLICATE KEY UPDATE groupName='Grup únic'";
                $mysqli->query($sql);
            }
            $tableList = $xpath->query("//table[@id='my_calendar_table']");

            foreach ($tableList as $calendar) {
                echo "<hr >";
                $idGrup = $calendar->attributes[0]->nodeValue;
                $id = explode(" ", $idGrup);
                $idGrup = str_replace("content_fase_", "", $id[2]);

                if (!$idGrup) {
                    $idGrup = $idLliga;
                }
                $a = 1;

                foreach ($calendar->childNodes as $ch) {
                    if ($ch->tagName == "tbody") {
                        foreach ($ch->childNodes as $ch2) {
                            if ($ch2->tagName == 'tr') {
                                echo "\n<div class='flex'>";
                                $at = $ch2->attributes[0]->nodeValue;
                                if ($ch2->childNodes[3]->nodeValue && $ch2->childNodes[3]->nodeValue != '00/00/0000') {

                                    //data i hora del partit
                                    $matchDate = prepareDate($ch2->childNodes[3]->nodeValue);
                                    $matchHour = trim($ch2->childNodes[5]->nodeValue);
                                    echo "\n\t<div class='bg-slate-100 border-l border-t border-slate-700 w-2/12 p-2 text-center'>$idGrup $matchDate <br /> $matchHour</div>";

                                    //lloc
                                    $place = utf8_decode(trim($ch2->childNodes[7]->nodeValue));
                                    echo "\n\t<div class='bg-slate-100 border-l border-t border-slate-700 w-2/12 p-2 text-center'>$place</div>";
                                    $idPlace = 1;

                                    $a = explode(" ", $at);
                                    $idLocal = str_replace("team_", "", $a[0]);
                                    $idVisitor = str_replace("team_", "", $a[1]);

                                    $localImage = trim($ch2->childNodes[11]->childNodes[0]->attributes[0]->nodeValue);
                                    $visitorImage = trim($ch2->childNodes[15]->childNodes[0]->attributes[0]->nodeValue);

                                    $localTeam = prepareTeamName($ch2->childNodes[13]->childNodes[1]->nodeValue);
                                    $visitorTeam = prepareTeamName($ch2->childNodes[17]->childNodes[1]->nodeValue);

                                    $localClub = prepareidClub($localImage, $mysqli);
                                    $visitorClub = prepareidClub($visitorImage, $mysqli);


                                    echo "\n\t<div class='bg-slate-100 border-l border-t border-slate-700 w-8/12 p-2 text-center'><img class='inline w-1/12' src='$localImage' /> $localTeam $idLocal - $visitorTeam $idVisitor <img src=' $visitorImage'  class='inline w-1/12'  /></div>";

                                    $idMatch = str_replace("fichapartido_", "", $ch2->childNodes[27]->childNodes[1]->attributes[1]->nodeValue);



                                    if (!$idGrup) {
                                        $idGrup = $ch2->childNodes[29]->childNodes[1]->attributes[2]->nodeValue;
                                        $nomGrup = $ch2->childNodes[29]->childNodes[1]->attributes[4]->nodeValue;
                                        //echo "nom grup ".$nomGrup;
                                        $n = explode("-  -", $nomGrup);
                                        $no = explode(" ", $n[0]);
                                        array_pop($no);
                                        array_pop($no);


                                        $nomGrup = addslashes(utf8_decode(implode(" ", $no)));
                                        $sql = "insert into phases (idGroup, groupName, idLeague) values (" . $idGrup . ",'" . $nomGrup . "','$idLliga') ON DUPLICATE KEY UPDATE groupName='$nomGrup'";
                                        // echo $sql;

                                        try {
                                            $mysqli->query($sql);
                                        } catch (Exception $ex) {
                                            echo " -> Algo ha passat al insertar la lliga $idLliga";
                                        }
                                    }
                                    if (!$idGrup) {
                                        $idGrup = 0;
                                    }


                                    $resultat = utf8_decode(trim($ch2->childNodes[23]->nodeValue));
                                    $r = explode("-", $resultat);
                                    $localResult = trim($r[0]);
                                    if (strlen($localResult) < 1 or $localResult == 'NO JUGAT'  or $localResult == 'SUSPÈS') {
                                        $localResult = 'null';
                                    }
                                    $visitorResult = trim($r[1]);
                                    if (strlen($visitorResult) < 1  or $visitorResult == 'NO JUGAT' or $visitorResult == 'SUSPÈS') {
                                        $visitorResult = 'null';
                                    }
                                    $round = addslashes(str_replace("JORNADA ", "", utf8_decode(trim($ch2->childNodes[9]->nodeValue))));
                                    echo "\n\t<div class='bg-slate-100 border-l border-t border-slate-700 w-3/12 p-2 text-center'> $round $localResult - $visitorResult</div>";

                                    if ($localTeam && $visitorTeam) {
                                        $sql = "insert into teams (idTeam, teamName,idClub,idSeason) values (" . $idLocal . ",'" . $localTeam . "'," . $localClub . "," . $idSeason . ")  ON DUPLICATE KEY UPDATE teamName='$localTeam', idSeason=$idSeason";
                                        //echo "<br />$sql";
                                        $mysqli->query($sql);
                                        $sql = "insert into teams (idTeam, teamName,idClub,idSeason) values (" . $idVisitor . ",'" . $visitorTeam . "'," . $visitorClub . "," . $idSeason . ")  ON DUPLICATE KEY UPDATE teamName='$visitorTeam', idSeason=$idSeason";
                                        //echo "<br />$sql";
                                        $mysqli->query($sql);
                                        if (!$idMatch) {
                                            $idMatch = $idLliga . $idGrup . $idLocal . $idVisitor;
                                        } else {
                                            $mysqli->query("delete from matches where idMatch = " . $idLliga . $idGrup . $idLocal . $idVisitor);
                                        }

                                        $sql = "insert into matches (idMatch,idLocal, idVisitor, matchDate, matchHour, idPlace, idRound, localResult, visitorResult, idLeague, idGroup)
            values (" . $idMatch . "," . $idLocal . "," . $idVisitor . ",'" . $matchDate . "','" . $matchHour . "','" . $idPlace . "','" . $round . "'," . $localResult . "," . $visitorResult . "," . $idLliga . "," . $idGrup . ")  
                     ON DUPLICATE KEY UPDATE matchDate='$matchDate', matchHour='$matchHour', localResult=$localResult, visitorResult=$visitorResult, updated=now()";
                                        if ($idMatch) {
                                            $sql .= ", idMatchShort='$idMatch'";
                                        }
                                        echo "<br />$sql";

                                        $timestamp_fecha = strtotime($matchDate);
                                        $mysqli->query($sql);
                                        $mysqli->query("UPDATE leagues SET lastUpdated=now() where idLeague=$idLliga");
                                    }
                                }
                                echo "\n</div>";
                            }
                        }
                    }
                }
            }
        }
        if ($_GET['idLeague']) {
            $subquery = " and idLeague=" . $_GET['idLeague'] . " ";
        }
        $result = $mysqli->query("select idLeague from leagues where idSeason=37  $subquery order by lastupdated asc, idLeague desc limit 0,2");
        while ($row = mysqli_fetch_array($result)) {
            //echo $row['idLeague']." - ";
            parseLeague($row['idLeague'], $mysqli);
        }
        $endTime = new DateTime();

        try {
            $mysqli->query("UPDATE phases SET numberofmatches=(SELECT COUNT(*) FROM matches WHERE idGroup=phases.idGroup);");
            /*  $mysqli->query("UPDATE phases SET startdate=(SELECT matchDate FROM matches WHERE idGroup=phases.idGroup LIMIT 1);");
            $mysqli->query("UPDATE phases SET enddate=(SELECT matchDate FROM matches WHERE idGroup=phases.idGroup order by matchdate desc LIMIT 1);"); */
        } catch (Exception $ex) {
            echo "jopelines";
        }
        //parseLeague(2228, $mysqli);
        ?>

    </div>

</body>

</html>
<?php 
if($_GET['force']){
    echo "<script>  setTimeout(() => location.reload(), 45000); </script>";
}
?>