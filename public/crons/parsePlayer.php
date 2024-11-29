<html>

<head>
    <meta http-equiv="refresh" content="105">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <title>Cron de dades del jugador</title>
</head>
<?php

error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);
include("../cnx/c.php");

function parsePlayer($idPlayer, $mysqli)
{
    echo "<table>";

    $dom   = new DOMDocument('1.0');
    $url = "http://clubolesapati.cat/crons/htmls/player/player_" . $idPlayer . ".html";
    echo "<hr />$url<br />";
    $html = $dom->loadHTMLFile(mb_convert_encoding($url, 'HTML-ENTITIES', 'UTF-8'));
    if (!$html) {
        // $mysqli->query("update matches set error=1 where idMatch=" . $idMatch);
        // echo "<br />La url $url no existeix<br />";
    }
    if ($html) {
        $dom->preserveWhiteSpace = true;
        $xpath = new DomXPath($dom);

        $tbody = $xpath->query(("//tbody"));


        foreach ($tbody as $tb) {
            $ctr = 1;
            foreach ($tb->childNodes as $tr) {
                if ($ctr > 5 && $tr->tagName == 'tr' && $tr->childElementCount > 1) {
                    $blue = 0;
                    echo "<tr>";
                    echo "<td>" . $tr->childNodes[9]->childNodes[1]->attributes[2]->nodeValue . "</td>";
                    echo "<td>" . $tr->childNodes[1]->nodeValue . "</td>";
                    echo "<td>" . $tr->childNodes[3]->nodeValue . " - " . $tr->childNodes[7]->nodeValue . "</td>";
                    echo "<td>" . $tr->childNodes[5]->nodeValue . "</td>";
                    echo "<td>" . $tr->childNodes[11]->nodeValue . " gols</td>";
                    echo "<td>" . $tr->childNodes[15]->nodeValue . " directes</td>";
                    echo "<td>" . $tr->childNodes[17]->nodeValue . " penaltis</td>";
                    echo "<td>" . $tr->childNodes[19]->nodeValue . " blaves</td>";
                    echo "<td>" . $tr->childNodes[21]->nodeValue . " vermelles</td>";
                    $goals = intval($tr->childNodes[11]->nodeValue);
                    $directes =  intval($tr->childNodes[15]->nodeValue);
                    $penalti =  intval($tr->childNodes[17]->nodeValue);
                    $blue = intval($tr->childNodes[19]->nodeValue);
                    $red =  intval($tr->childNodes[21]->nodeValue);
                    $idMatch = intval($tr->childNodes[9]->childNodes[1]->attributes[2]->nodeValue);


                  //  $sql = "update player_match set goals=$goals, blue=$blue, red=$red  where idPlayer= $idPlayer and idMatch= $idMatch";
                  $mysqli->query("insert into player_match (idPlayer, idMatch, goals, blue,red, idTeam, directes, penalti) 
                  values (" . $idPlayer . "," . $idMatch . ", $goals,$blue,$red, 99999, $directes, $penalti) ON DUPLICATE KEY UPDATE updated=now(),goals=$goals, blue=$blue, red=$red, idTeam= ");

                  /*   echo "<td>$sql</td>";
                    $mysqli->query($sql); */
                    echo "</tr>";
                }
                $ctr++;
            }
        }
    }
    echo "</table>";
}
parsePlayer(58383, $mysqli);


/* 
<tr>
				1<td style="text-align: center;" width="14%" class="texto_gris_10">14/04/2024</td>
				3<td style="text-align: right;" width="10%">
					
					<span class="texto_gris_10" style="color: #000;">HOAA</span>
				</td>
				5<td style="text-align: center; font-weight: bold; letter-spacing: 1px; font-size: 12px;">
					3:0				</td>
			7	<td style="text-align: left;" width="10%">
					<span class="texto_gris_10" style="color: #000;">COPA</span>
				</td>
			9	<td width="25" style="text-align: center;">
										<a class="fa fa-search game_report" href="#" idp="76485" idc="0" idm="1" topbar_title="" aria-hidden="true" title="Ficha Partido" style="color: #18B;"></a>
									</td>
				
			11					<td class="stats_table"></td>
			13	<td class="stats_table"></td>
			15	<td class="stats_table">
									</td>
			17	<td class="stats_table">
									</td>
			19	<td class="stats_table">1</td>
			21	<td class="stats_table"></td>
			</tr>
 */