<?php

date_default_timezone_set('Europe/Madrid');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '30');
error_reporting(E_ALL);
include("curl.php");
error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);
include("cnx/c.php");

function parseLeague($idLeague, $idLocal, $idVisitor,$mysqli)
{
    $dom   = new DOMDocument('1.0');
    echo "<br />https://www.server2.sidgad.es/fecapa/fecapa_cal_idc_" . $idLeague . "_1.php";
    $curled = getCurl("https://www.server2.sidgad.es/fecapa/fecapa_cal_idc_" . $idLeague . "_1.php");
    //$html = $dom->loadHTML(mb_convert_encoding($curled, 'HTML-ENTITIES', 'UTF-8'));
    $html = $dom->loadHTML($curled);
    $dom->preserveWhiteSpace = true;
    $xpath = new DomXPath($dom);
    $div = $xpath->query("//div");
    echo "<br />Data fecapa: ".$div[count($div) - 2]->nodeValue;
  
    $dateNode = strtotime(trim(str_replace("GMT", "", str_replace("Report Created on ", "", $div[count($div) - 2]->nodeValue))));
    // echo $dateNode;
    $quarterAgoTimestamp = time() - 111900;
    echo "<br />Data de fa un quart d' hora:".date('d-m-Y H:i:s', $quarterAgoTimestamp);
    if ($dateNode > $quarterAgoTimestamp || $_GET['force']) {
        echo "<br />Fa menys d' un quart d' hora, busquem partits entre $idLocal i  $idVisitor.";
        $tbody = $xpath->query("//tbody");

        foreach ($tbody as $tb) {
            // echo "<pre>"; print_r($tb->childNodes); echo "</pre>";
            foreach ($tb->childNodes as $tr) {
                if ($tr->tagName == 'tr' && $tr->attributes[0]->value == "team_$idLocal team_$idVisitor team_class") {
                    echo "<pre>"; print_r($tr); echo "</pre>";
                    $result = explode("-", trim($tr->childNodes[23]->nodeValue));
                    print_r($result);
                    if (!empty($result[0])) {                       
                        $query = "update matches set localResult=" . trim($result[0]) . ", visitorResult=" . trim($result[1]) . ", updated=now() where idLocal=$idLocal and idVisitor=$idVisitor and idLeague=$idLeague";
                        echo "<br /><br /><br />" . $query;
                       $mysqli->query($query);
                       mail('pau.arjalaguer@gmail.com', 'Partit Enviat', "idlocal: $idLocal \nVisitor: $idVisitor\nLeague: $idLeague");
                    }
                }
            }
        }
        // echo "<pre>"; print_r($tbody->childNodes); echo "</pre>";

    }
    //echo date('Y-m-d H:i:s', $quarterAgoTimestamp);
}
if($_GET['force']){
    $result = $mysqli->query("   select  idLocal,idVisitor,idLeague  from matches where idmatch not in (select idmatch from player_match)  and length(idMatch)<7  ORDER BY RAND() limit 0,3");
 
}else{
$result = $mysqli->query("select idLocal,idVisitor,idLeague from matches where matchdate=curdate() and matchHour<=curtime() - INTERVAL 1 HOUR
and localresult is null  ORDER BY RAND() limit 0,3");
}
while ($row = mysqli_fetch_array($result)) {
    //echo $row['idLeague']." - ";
    parseLeague($row['idLeague'], $row['idLocal'], $row['idVisitor'], $mysqli);
}
if($_GET['force']){
    echo "<script>  setTimeout(() => location.reload(), 45000); </script>";
}