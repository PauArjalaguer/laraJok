<?php
error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);
include("/home/faltesdi/domains/faltesdirectes.c1.is/public_html/cnx/c.php");

$categories = array();
$res = $mysqli->query("select * from categories order by idCategory asc");
while ($row = mysqli_fetch_assoc($res)) {
  $categories[] = $row;
}
/* echo "<pre>";
print_r($categories);
echo "</pre>"; */
$dom   = new DOMDocument('1.0');
$url = "http://clubolesapati.cat/crons/htmls/leagues/leagues_1.html";
$html = $dom->loadHTMLFile(mb_convert_encoding($url, 'HTML-ENTITIES', 'UTF-8'));

$dom->preserveWhiteSpace = true;
$xpath = new DomXPath($dom);
$a = $xpath->query("//a");
foreach ($a as $league) {
  $llistaClubs =  $league->attributes[1]->nodeValue;

  $t = explode("temp_", $llistaClubs);
  $t1 = explode(" ", $t[1]);
  $idSeason = $t1[0];
  $nomLliga = addslashes(trim(utf8_decode($league->childNodes[3]->nodeValue)));
  $idLliga = $league->attributes[2]->nodeValue;
  $params = $league->attributes[6]->nodeValue;
  $pParams = explode(";", $params);
  $teamsList = explode(":", $pParams[0]);
  echo "<br />";
  foreach ($categories as $category) {
    echo mb_strtolower($category['categoryName']) . " - " . mb_strtolower($nomLliga) . " (" . strpos(mb_strtolower($nomLliga), mb_strtolower($category['categoryName'])) . ")<br />";
    if (strlen(strpos(mb_strtolower($nomLliga), mb_strtolower($category['categoryName']))) > 0) {
      $idCategory = $category['idCategory'];
    }
  }

  $mysqli->query("insert into leagues (idLeague, leagueName, idSeason) values (" . $idLliga . ",'" . $nomLliga . "'," . $idSeason . ") ON DUPLICATE KEY UPDATE leagueName='$nomLliga', idSeason=$idSeason, idCategory=$idCategory");
}
$mysqli->close();
