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
function updateMatch($localUrl, $visitorUrl, $matchDate, $matchHour, $place, $gM, $idGroup, $idRound, $localName, $visitorName)
{
    //todo integrar la part de distancies
    if ($matchDate == "ACTA TANCADA") {
        //si l'acta esta tancada, enlloc de l'hora ens vé el resultat
        $result = explode("-", $matchHour);
        $localResult = trim($result[0]);
        $visitorResult = trim($result[1]);
        $query = "UPDATE matches SET scheduleUpdate= DATETIME('now'),  localResult=?, visitorResult=? WHERE idLocal=? and idVisitor=? and idGroup=? and idRound=?";
        echo $query;
        $params = [$localResult, $visitorResult, $localUrl, $visitorUrl, $idGroup, $idRound];
      
    } else {
        $query = "UPDATE matches SET scheduleUpdate= DATETIME('now'), matchDate=? , matchHour=?, place=?,  coordinates=?,  meteo='28° / 18° | http://clubolesapati.cat/content/images/meteo/1.png' WHERE idLocal=? and idVisitor=? and idGroup=? and idRound=?";
        $params = [$matchDate, $matchHour, $place, $gM,  $localUrl, $visitorUrl, $idGroup, $idRound];
      
    }
    $url ="https://efsmasquefa-jokcat.turso.io";
    $token="eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3MzQwMjk1NzEsImlkIjoiYzA2ZThhYjctYWJhYi00NzQwLWE2MzMtZWQzZWZhYzRmNWQwIn0.yNzV7Ptv3KQP3LIi-WxV5scl4Cb_jx5ayNb4wPOXJmKmvHQ2_XmwQb79Z1KANORdbdS6eo27s3k7TZG_e79gCQ";
    
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
function parseRound($url, $idGroup, $idRound, $mysqli)
{

    echo "<h1>$url</h1>";
    $a = file_get_contents($url);

    $dom   = new DOMDocument('1.0');
    $html = $dom->loadHTML($a);

    $dom->preserveWhiteSpace = true;
    $xpath = new DomXPath($dom);
    $rows = $xpath->query('//tr[@class="linia"]');
    echo "<table>";
    foreach ($rows as $match) {

        $localName = $match->childNodes[3]->textContent;
        $visitorName = $match->childNodes[7]->textContent;
        $localUrl = trim($match->childNodes[1]->childNodes[1]->attributes[0]->value);
        $matchDate = trim($match->childNodes[5]->childNodes[1]->childNodes[1]->textContent);
        $matchHour =  trim($match->childNodes[5]->childNodes[1]->childNodes[3]->textContent);
        $m=explode("-",$matchDate);
        if ($m[2]) {
            if (strlen($m[2]) > 3) {
                $matchDate = $m[2] . "-" . $m[1] ."-" . $m[0];
            } else { $matchDate = $m[0] . "-" . $m[1] . "-" . $m[2]; }
        }

        $visitorUrl = trim($match->childNodes[9]->childNodes[1]->attributes[0]->value);
        $place = $match->childNodes[11]->childNodes[1]->childNodes[0]->textContent;
        $map = $match->childNodes[15]->childNodes[1]->attributes[0] ?  $match->childNodes[15]->childNodes[1]->attributes[0]->value : 'http://maps.google.com/maps?z=12&t=m&q=loc:41.498741+1.813684';

        echo "<tr><td>" . $localName . "</td><td> " . $visitorName . "</td><td>" . "</td><td>" . $localUrl . "</td><td>" . $visitorUrl . "</td><td>" . $matchDate . "</td><td>" . $matchHour . "</td><td>" . $place . "</td><td>" . $map . "</td></tr>";
        updateMatch($localUrl, $visitorUrl, $matchDate, $matchHour, $place, $map, $idGroup, $idRound, $localName, $visitorName);
    }
    echo "</table>";
    /*
    foreach ($tbody[0]->childNodes as $tr) {
        if ($tr->tagName == "tr") {
            $position = $tr->childNodes[1]->nodeValue;
            $url = $tr->childNodes[3]->childNodes[0]->childNodes[0]->attributes[0]->nodeValue;
            $teamName = trim($tr->childNodes[5]->nodeValue);
            $teamImage = $tr->childNodes[3]->childNodes[0]->childNodes[0]->childNodes[0]->attributes[2]->nodeValue;
            $teamSlug = explode("/", $url);
            $points = trim($tr->childNodes[9]->nodeValue);
            $j =  trim($tr->childNodes[17]->nodeValue);
            $w = trim($tr->childNodes[19]->nodeValue);
            $d = trim($tr->childNodes[21]->nodeValue);
            $l = trim($tr->childNodes[23]->nodeValue);
            $idTeam = sentenceToSum($teamName . $teamSlug[5] . $url . $teamImage);
            $gf = trim($tr->childNodes[47]->nodeValue);
            $gc = trim($tr->childNodes[49]->nodeValue);
            echo "<pre>";
            //  print_r($j);
            print_r($position . " " . $teamName . " " . $url . " " . $idTeam . " " . $teamImage . " PUNTS:" . $points . " Jugats: $j  W: " . $w . " D: " . $d . " L: " . $l . " GF:" . $gf . " GC:" . $gc);
            echo "</pre>";


            $url = 'https://jokcatfs-pauarjalaguer.turso.io/';
            $token = "eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3MjQ1MTcwNjYsImlkIjoiZjI3NmQ3NmUtMjA1My00ZmJhLWI2MTgtMGQyZGZkN2E3NDEzIn0.vGKIODWyeqUw-YY-XdW6jEUeRUSyFdevSdimkQ0bpIIghhEbrXsHUVdDMXUBWwCHFHYtBwWixlv_JqQVzuDoCQ";

            $query = "INSERT OR REPLACE INTO classification 
        ('idLeague', 'groupName', 'idTeam', 'position', 'points', 'played', 
        'won', 'draw', 'lost', 'goalsMade', 'goalsReceived', 'goalAverage', 
        'updated', 'idClassification', 'isDeleted', 'teamName') 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,DATETIME('now'),?,?,?)";

            $requestData = [
                'statements' => [
                    [
                        'q' => $query,
                        'params' => [
                            $idGroup,
                            '',
                            $idTeam,
                            $position,
                            $points,
                            $j,
                            $w,
                            $d,
                            $l,
                            $gf,
                            $gc,
                            0,
                            $idGroup . $position,
                            0,
                            $teamName
                        ]
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
        }
    }*/
}
$url = 'https://jokcatfs-pauarjalaguer.turso.io/';
$token = "eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3MjQ1MTcwNjYsImlkIjoiZjI3NmQ3NmUtMjA1My00ZmJhLWI2MTgtMGQyZGZkN2E3NDEzIn0.vGKIODWyeqUw-YY-XdW6jEUeRUSyFdevSdimkQ0bpIIghhEbrXsHUVdDMXUBWwCHFHYtBwWixlv_JqQVzuDoCQ";
$query = 'select replace(concat(groupUrl,\'/jornada-\', idRound),\'calendari\',\'resultats\') as url, m.idGroup, idRound from matches m join groups g on g.idGroup=m.idGroup where  (m.scheduleUpdate < datetime("now", "-30 minutes") OR m.scheduleUpdate IS NULL) order by m.scheduleUpdate asc limit 0,1';
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
    parseRound($r[0], $r[1], $r[2], $mysqli);
}
?>

<script>
   // setTimeout(() => window.location.replace("fcfcat_horaris.php"), 4000);
</script>