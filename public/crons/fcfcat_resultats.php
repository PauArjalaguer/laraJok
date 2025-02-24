<?php
error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);
include("curl.php");
include("cnx/c.php");
function sentenceToSum($sentence)
{
    $sentence = strtolower($sentence);
    $sum = 0;

    // Loop through each character
    for ($i = 0; $i < strlen($sentence); $i++) {

        $charCode = ord($sentence[$i]);

        // Check if it's a lowercase letter (a-z)
        if ($charCode >= 97 && $charCode <= 122) {
            // Add position value (a=1, b=2, etc) to sum
            $sum += ($charCode - 96);
        }
    }

    return $sum;
}
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
function updateMatch($localUrl, $visitorUrl, $matchDate, $matchHour, $idGroup, $resultat)
{

    $resultat = explode("-", $resultat);
   // $numbersOnly = preg_replace('/[^0-9]/', '', $string);
    $localResult = preg_replace('/[^0-9]/', '', $resultat[0]);
    $visitorResult = preg_replace('/[^0-9]/', '', $resultat[1]);
    $query = "UPDATE matches SET scheduleUpdate= DATETIME('now'), matchDate=?, matchhour=?, localResult=?, visitorResult=? WHERE idLocal=? and idVisitor=? and idGroup=?";
    $params = [trim($matchDate), trim($matchHour), trim($localResult), trim($visitorResult), $localUrl, $visitorUrl, $idGroup,];

    $url = 'https://jokcatfs-pauarjalaguer.turso.io/';
    $token = "eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3MjQ1MTcwNjYsImlkIjoiZjI3NmQ3NmUtMjA1My00ZmJhLWI2MTgtMGQyZGZkN2E3NDEzIn0.vGKIODWyeqUw-YY-XdW6jEUeRUSyFdevSdimkQ0bpIIghhEbrXsHUVdDMXUBWwCHFHYtBwWixlv_JqQVzuDoCQ";

    print_r($params);
    $requestData = [
        'statements' => [
            [
                'q' => $query,
                'params' => $params
            ]
        ]
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($requestData)
    ]);
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}
function parseLeague($url, $urlGroup, $idGroup, $mysqli)
{


    $u = explode("/", $url);
    $teamName = $u[count($u) - 1];
    $urlGroup = str_replace("calendari", "calendari-equip", $urlGroup);

    $url = $urlGroup . "/" . $teamName;
    echo "<h1>$url</h1>";
    // echo $url;
    $a = file_get_contents($url);

    $dom   = new DOMDocument('1.0');
    $html = $dom->loadHTML($a);

    $dom->preserveWhiteSpace = true;
    $xpath = new DomXPath($dom);
    $rows = $xpath->query('//tbody')[1];

    foreach ($rows->childNodes as $match) {
        if ($match->tagName == 'tr') {
            /* echo "<pre>";
            print_r($match->childNodes[9]->childNodes[0]->attributes[0]->nodeValue);
            echo "</pre>"; */
            $matchDate = explode("-",$match->childNodes[3]->nodeValue);
            $matchDate = $matchDate[2]."-".$matchDate[1]."-".$matchDate[0];
            $matchHour = $match->childNodes[5]->nodeValue;
            $localUrl = $match->childNodes[7]->childNodes[0]->attributes[0]->nodeValue;
            $visitorUrl = $match->childNodes[9]->childNodes[0]->attributes[0]->nodeValue;
            $resultat = $match->childNodes[11]->nodeValue;
         //   echo "<hr />" . $matchDate . " " . $matchHour . " " . $localUrl . " | " . $visitorUrl . " " . $resultat . " |  " . $idGroup;
            updateMatch($localUrl, $visitorUrl, $matchDate, $matchHour, $idGroup, $resultat);
        }

    }
}
$url = 'https://jokcatfs-pauarjalaguer.turso.io/';
$token = "eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3MjQ1MTcwNjYsImlkIjoiZjI3NmQ3NmUtMjA1My00ZmJhLWI2MTgtMGQyZGZkN2E3NDEzIn0.vGKIODWyeqUw-YY-XdW6jEUeRUSyFdevSdimkQ0bpIIghhEbrXsHUVdDMXUBWwCHFHYtBwWixlv_JqQVzuDoCQ";
/* $url ="https://efsmasquefa-jokcat.turso.io";
$token="eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3MzQwMjk1NzEsImlkIjoiYzA2ZThhYjctYWJhYi00NzQwLWE2MzMtZWQzZWZhYzRmNWQwIn0.yNzV7Ptv3KQP3LIi-WxV5scl4Cb_jx5ayNb4wPOXJmKmvHQ2_XmwQb79Z1KANORdbdS6eo27s3k7TZG_e79gCQ";
 */

$query = '
select distinct idLocal, groupUrl, m.idGroup from matches m
	join groups g on g.idGroup=m.idGroup
	 where idLocal like \'%masquefa%\'	
order by groupUrl limit 0,30';
$requestData = [
    'statements' => [
        [
            'q' => $query
        ]
    ]
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($requestData)
]);
$response = curl_exec($ch);
$data = json_decode($response, true);
foreach ($data[0]['results']['rows'] as $r) {
    //parseRound(leagues[0], leagues[1], leagues[2]);
    //$r[0] = 'https://www.fcf.cat/resultats/2425/futbol-sala/lliga-segona-divisi%C3%B3-infantil-futbol-sala/bcn-gr-6/jornada-1';
    parseLeague($r[0], $r[1], $r[2],  $mysqli);
}
?>

<script>
    //setTimeout(() => window.location.replace("01_lligues_copiaIndexdeLligues.php"), 15000);
</script>