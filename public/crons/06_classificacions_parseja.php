<?php
error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);
include("curl.php");
include("cnx/c.php");
/* <tr>
	    1<td width="35"><div style="text-align: right; margin-right: 8px;">1</div></td>
		3<td width="35"><div class="logo_club_listado"><img src="https://ns3104249.ip-54-37-85.eu/fecapa/images//logos_clubes/333.png" width="20"></div></td>
		5<td><div class="no_mobile">HC LLEIDANET ALPICAT</div><div class="mobile" style="display: none;">HCA</div></td>
		7<td class="stats_table_special" width="50">31</td>
		9<td class="stats_table" width="50"><div align="center">14</div></td>
		11<td class="stats_table" width="50"><div align="center">10</div></td>
		13<td class="stats_table" width="50"><div align="center">1</div></td>
		15<td class="stats_table" width="50"><div align="center">3</div></td>
		17<td class="stats_table" width="50"><div align="center">55</div></td>
		19<td class="stats_table" width="50"><div align="center">31</div></td>
		21<td class="stats_table" width="50"><div align="center">24</div></td>
		23<td class="stats_table no_mobile" width="50"><div align="center"></div></td>
																	</tr> */
function findIdTeam($teamName, $idLeague, $idGroup, $mysqli)
{
    if ($idGroup) {
        $sql = "Select idTeam, teamName from teams t join matches m on m.idLocal=t.idTeam where idLeague=$idLeague and idGroup=$idGroup
    and teamName ='" . addslashes($teamName) . "' limit 0,1";
        echo "<br />$sql";
        $res = $mysqli->query($sql) or die(mysqli_error($mysqli));
        $r = mysqli_fetch_assoc($res);
        $idTeam = $r['idTeam'];

        return $idTeam;
    }
}
function parseLeague($idLliga, $mysqli)
{
    $dom   = new DOMDocument('1.0');
    $curled = getCurl("https://www.server2.sidgad.es/fecapa/fecapa_clasif_idc_" . $idLliga . "_1.php");
    //$html = $dom->loadHTML(mb_convert_encoding($curled, 'HTML-ENTITIES', 'UTF-8'));
    $html = $dom->loadHTML($curled);
    $dom->preserveWhiteSpace = true;
    $xpath = new DomXPath($dom);
    $tableList = $xpath->query("//table");
    foreach ($tableList as $classification) {
        if ($classification->previousElementSibling->attributes[0]->nodeValue == 'div_titulo_fase_idc') {
            $phaseName = utf8_decode($classification->previousElementSibling->nodeValue);
            $res =  $mysqli->query("select idGroup from phases where TRIM(groupName)='" . trim($phaseName) . "' and idLeague=$idLliga");
            echo "<br />select idGroup from phases where TRIM(groupName)='" . trim($phaseName) . "' and idLeague=$idLliga";
            $row = mysqli_fetch_assoc($res);
            echo "<br><br>Id Grup: " . $idGroup = $row['idGroup'];
        }
        foreach ($classification->childNodes[3]->childNodes as $tr) {
            if ($tr->tagName == 'tr') {
                $position = $tr->childNodes[1]->nodeValue;
                $teamName = utf8_decode($tr->childNodes[5]->childNodes[0]->nodeValue);
                $idTeam = findIdTeam($teamName, $idLliga, $idGroup, $mysqli);
                $points = $tr->childNodes[7]->nodeValue;
                $played = $tr->childNodes[9]->nodeValue;
                $won = $tr->childNodes[11]->nodeValue;
                $draw = $tr->childNodes[13]->nodeValue;
                $lost = $tr->childNodes[15]->nodeValue;
                $gA = $tr->childNodes[17]->nodeValue;
                $gC = $tr->childNodes[19]->nodeValue;
                echo "<br />" . $position . " " . $idTeam . " - " . $teamName . " " . $points . " " . $played . " Guanyat " . $won . " Empatat " . $draw . " Perdut  " . $lost . " " . $gA . " " . $gC;
                $idClassification = $idGroup . $idTeam;
                $sql2 = "insert into classifications (idClassification,idTeam,points,position,played,won, draw, lost, goalsMade, goalsReceived, idLeague, idGroup) values 
                ($idClassification,$idTeam,$points,$position,$played,$won, $draw, $lost, $gA, $gC, $idLliga, $idGroup) ON DUPLICATE KEY 
                UPDATE  position=$position, won=$won, draw=$draw, played=$played, lost=$lost, goalsMade=$gA, goalsReceived=$gC, points=$points, position=$position ";
                echo $sql2;
                if ($idTeam && $played>0) {
                    $mysqli->query($sql2);
                }
            }
            /* echo "\n<pre>";
            print_r($tr);
            echo "</pre>";  */
        }
    }
}
$result =$result = $mysqli->query("select idLeague from leagues where idSeason=39  order by lastupdated desc, idLeague desc limit 0,100");
while ($row = mysqli_fetch_array($result)) {
    //echo $row['idLeague']." - ";
    parseLeague($row['idLeague'], $mysqli);
}

//parseLeague(2228, $mysqli);
?>

<script> setTimeout(() => window.location.replace("01_lligues_copiaIndexdeLligues.php"), 1115000);</script>