<?php
error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING);
/* 
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
}  */

/* 
$taula = $xpath->query("//table[contains(@class, 'tabla_standard')]");
echo "<pre>";
print_r($taula[0]->childNodes[3]->childNodes);
echo "</pre>";
$partits = $taula[0]->childNodes[3]->childNodes;

foreach ($partits as $partit) {
$idLocal="";
  $idLocal = $partit->attributes[2]->value;
  $idVisitor = $partit->attributes[3]->value;
  $idPista = $partit->attributes[4]->value;
  $param = $partit->attributes[5]->value;
  if ($param) {
    $p = explode("_", $param);
  }
  $idGroup = $p[4];

  if(!empty($idLocal) && $idPista && $idGroup){
    echo $idLocal . " " . $idVisitor . " " . $idPista . " " . $idGroup . " <br />";
    $mysqli->query(
      "UPDATE matches m
      JOIN teams t ON m.idLocal = t.idTeam
      SET m.idplace = $idPista
      WHERE t.idClub = $idLocal
      AND m.idGroup = $idGroup;"
    );

  
  }
} */

$dsn = 'mysql:host=localhost;dbname=patinscat;charset=utf8';
$usuario = 'jok';
$clave = 'Arn@u1b3rt@';
try {
  $pdo = new PDO($dsn, $usuario, $clave, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  
  // Obtener todos los lugares sin dirección
  //$stmt = $pdo->query("SELECT idPlace, placeName, placeAddress FROM places WHERE placeAddress IS NULL OR placeAddress = '' limit 1,5");
  $stmt = $pdo->query("SELECT idPlace, placeName, placeAddress FROM places");
  $places = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  foreach ($places as $place) {
      $idPlace = $place['idPlace'];
      $placeName = urlencode($placeName);
      $placeAddress =urlencode($place['placeAddress']);
     // echo $placeName;
     //Llamada a la API de Nominatim
      $url = "https://nominatim.openstreetmap.org/search?format=json&q=$placeName";
      echo  "<hr />".$url;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
      
      $response = curl_exec($ch);
      curl_close($ch);
      
      $data = json_decode($response, true);
      echo "<pre>";print_r($data);echo "</pre>";
     
      if (!empty($data)) {
          $placeAddress = $data[0]['display_name'];
          $lat = $data[0]['lat'];
          $lon = $data[0]['lon'];
          // Actualizar la dirección en la base de datos
          $updateStmt = $pdo->prepare("UPDATE places SET placeAddress = :placeAddress, lat = :lat, lon = :lon WHERE idPlace = :idPlace");
          $updateStmt->execute([':placeAddress' => $placeAddress, ':idPlace' => $idPlace, ':lat' => $lat, ':lon' => $lon]);
          
          echo "Actualizado: $placeName -> $placeAddress\n";
      } else {
          echo "No se encontró dirección para: $placeName\n";
      }
    
      // Esperar 2 segundos antes de la siguiente petición
      sleep(2);
  }
} catch (PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
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
