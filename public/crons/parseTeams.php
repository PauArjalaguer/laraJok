<?php
error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);
include("cnx/c.php");
/* <tr class="team_6423 team_5207 team_class" gamedate="20230922">		
				1<td width="25"></td>
				3<td width="75" class="tabla_standard_less"><div style="text-align:left; padding-left: 5px;">22/09/2023</div></td>
				5<td width="45" class="tabla_standard_less">20:45</td>
				7<td class="tabla_standard_less" style="padding-right: 20px;"  width="120">
					<div style="text-align:left; padding-left: 5px; font-size: 10px;">CLUB D ESPORTS VENDRELL</div>
				</td>
				9<td class="jor_in_games" style="display: none;" width="100">JORNADA 1				</td>
                11<td width="25"><img src='https://ns3104249.ip-54-37-85.eu/fecapa/images//logos_clubes/332.png' width='18'  class='team_logo'></td>
				13<td width="300"><div class="no_mobile nombre_junto_logo">CE VENDRELL</div></td>
				15<td width="25"><img src='https://ns3104249.ip-54-37-85.eu/fecapa/images//logos_clubes/321.png' width='18'  class='team_logo'></td>
				17<td width="300"><div class="no_mobile nombre_junto_logo">CHP MCDONALD'S AMPOSTA</div></td>
				19<td></td>
				21<td width="15"></td>
                23<td class="web_link_td" style="text-align: center;" width="40">4 - 4</td>
				25<td width="15"></td>
				27<td><a href="#" id="fichapartido_56168" class="link_ficha_partido"><span class="dashicons dashicons-search"></span></a></td>
		        29<td width="25" class="tabla_standard_links web_link_td"><i class="fa fa-search game_report" idp="56168" idc="2277" idm="1" topbar_title="2Âª CATALANA GRUP A 2023/2024 -  - 20:45" aria-hidden="true" title="Ficha Partido"></i>
									</td>
				31<td width="25"></td>	
								</tr> */

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

  // $mysqli->query("update clubs set clubImage='".$clubImage."' where idClub=".$idClub."");
  return $idClub;
}

function prepareDate($matchDate)
{
  $d = explode("/", $matchDate);
  $matchDate = $d[2] . "-" . $d[1] . "-" . $d[0];
  return $matchDate;
}




function parseTeam($idTeam, $mysqli)
{
  $result = $mysqli->query("SELECT idSeason,c.idCategory FROM matches m
	JOIN leagues l ON l.idLeague=m.idLeague
	JOIN categories c ON c.idCategory=l.idCategory WHERE (idLocal=$idTeam OR idvisitor=$idTeam)
	LIMIT 0,1
 ");
  $row = mysqli_fetch_array($result);

  if ($row['idCategory']) {
    $mysqli->query('update teams set idSeason=' . $row['idSeason'] . ', idCategory=' . $row['idCategory'] . ' where idTeam=' . $idTeam);
    echo '<br />update teams set idSeason=' . $row['idSeason'] . ', idCategory=' . $row['idCategory'] . ' where idTeam=' . $idTeam;
  }
}
$result = $mysqli->query("select idTeam from teams ");
while ($row = mysqli_fetch_array($result)) {
  //echo $row['idLeague']." - ";
  parseTeam($row['idTeam'], $mysqli);
}

//parseLeague(2228, $mysqli);
