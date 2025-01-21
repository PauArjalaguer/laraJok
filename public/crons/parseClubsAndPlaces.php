<?php
error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING);
include("cnx/c.php");
include("curl.php");
$dom   = new DOMDocument('1.0');
$url = "https://server2.sidgad.es/fecapa/00_fecapa_agenda_1.php";
$curled = getCurl($url);
$html = $dom->loadHTMLFile(mb_convert_encoding($url, 'HTML-ENTITIES', 'UTF-8'));

$html = $dom->loadHTML($curled);
$dom->preserveWhiteSpace = true;
$xpath = new DomXPath($dom);
$sel = $xpath->query("//select");

print_r($sel[1]->childNodes);
echo "CLUBS";
foreach ($sel[1]->childNodes as $clubs) {
    if ($clubs->tagName == 'option') {
        $idClub = $clubs->attributes[0]->nodeValue;
        $clubName = addslashes(utf8_decode($clubs->nodeValue));
        echo "<hr />$idClub $clubName";
        try{
        $mysqli->query("insert into clubs (idClub, clubName) values (" . $idClub . ",'" . $clubName . "') ON DUPLICATE KEY UPDATE clubName='$clubName'");
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}
echo "PLACES";
foreach ($sel[4]->childNodes as $clubs) {
  if ($clubs->tagName == 'option') {
      $idPlace = $clubs->attributes[0]->nodeValue;
      $placeName = addslashes(utf8_decode($clubs->nodeValue));
      echo "<hr />$idPlace $placeName";
      $mysqli->query("insert into places (idPlace, placeName) values (" . $idPlace . ",'" . $placeName . "') ON DUPLICATE KEY UPDATE placeName='$placeName'");

  }
}

/* 
  $llistaClubs =  $league->attributes[1]->nodeValue;

  $t = explode("temp_", $llistaClubs);
  $t1 = explode(" ", $t[1]);
  $idSeason = $t1[0];
  $nomLliga = addslashes(trim(utf8_decode($league->childNodes[3]->nodeValue)));
  $idLliga = $league->attributes[2]->nodeValue;
  $params = $league->attributes[6]->nodeValue;
  $pParams = explode(";", $params);
  $teamsList = explode(":", $pParams[0]); }*/

// $mysqli->query("insert into leagues (idLeague, leagueName, idSeason) values (" . $idLliga . ",'" . $nomLliga . "'," . $idSeason . ") ON DUPLICATE KEY UPDATE leagueName='$nomLliga', idSeason=$idSeason");

//$mysqli->close();
