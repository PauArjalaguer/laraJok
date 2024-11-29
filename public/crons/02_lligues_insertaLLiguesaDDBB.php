<!doctype html>
<html>

<head>
  <title>Parseja lligues</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

  <div class='w-3/4 mx-auto my-2 bg-slate-100 rounded-xl p-2'>
    <h1 class="text-4xl font-bold p-5">Lligues</h1>
    <?php
    error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);

    include("cnx/c.php");
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
      echo "\n<div class='flex'>";
      echo "<div class='bg-slate-100 border-l border-t border-slate-700 w-1/12 p-2 text-center'>$idSeason</div>";
      echo "<div class='bg-slate-100 border-l border-t border-slate-700 w-1/12 p-2 text-center'>$idLliga</div>";
      echo "<div class='bg-slate-100 border-l border-t border-slate-700 w-6/12 p-2'>$nomLliga</div>";
      $idCategory = 0;
      foreach ($categories as $category) {
        //echo mb_strtolower($category['categoryName']) . " - " . mb_strtolower($nomLliga) . " (" . strpos(mb_strtolower($nomLliga), mb_strtolower($category['categoryName'])) . ")<br />";
        if (strlen(strpos(mb_strtolower($nomLliga), mb_strtolower($category['categoryName']))) > 0) {
          $idCategory = $category['idCategory'];
          echo "<div class='bg-slate-100 border-l border-r border-t border-slate-700 w-4/12 p-2'>" . ucwords(mb_strtolower($category['categoryName'])) . " (" . $idCategory . ")</div>";
        }
      
      }
      echo "</div>";
      $mysqli->query("insert into leagues (idLeague, leagueName, idSeason) values (" . $idLliga . ",'" . $nomLliga . "'," . $idSeason . ") ON DUPLICATE KEY UPDATE leagueName='$nomLliga', idSeason=$idSeason, idCategory=$idCategory");
    }
    $mysqli->close();


    ?>
  </div>
</body>

</html>

<script> setTimeout(()=> window.location.replace("03_lligues_copiaArxiudeLaLliga.php"),15000);</script>