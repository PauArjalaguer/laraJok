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
function parseLeague($idGroup, $idLliga, $mysqli)
{

    $idLliga=str_replace("calendari","classificacio",$idLliga);
echo "<h1>$idLliga</h1>";
    $a = file_get_contents($idLliga);

    $dom   = new DOMDocument('1.0');
    $html = $dom->loadHTML($a);

    $dom->preserveWhiteSpace = true;
    $xpath = new DomXPath($dom);
    $tbody = $xpath->query("//tbody");

    foreach ($tbody[0]->childNodes as $tr) {
        if ($tr->tagName == "tr") {
            $position = $tr->childNodes[1]->nodeValue;
            $url = $tr->childNodes[3]->childNodes[0]->childNodes[0]->attributes[0]->nodeValue;
            $teamName = trim($tr->childNodes[5]->nodeValue);
            $teamImage = $tr->childNodes[3]->childNodes[0]->childNodes[0]->childNodes[0]->attributes[2]->nodeValue;
            $teamSlug = explode("/", $url);

            $cells = $xpath->query('.//td[@class="tc"]', $tr);
           
            $points=$cells[0]->nodeValue;
            $p=explode(" ",$points);
            $points=$p[0];
            if(count($cells)==4){
            $gf=$cells[2]->nodeValue;
            $gc=$cells[3]->nodeValue;
            }else{
                $gf=$cells[1]->nodeValue;
                $gc=$cells[2]->nodeValue;
            }

            $cells = $xpath->query('.//td[@class="tc resumida"]', $tr);
           
            $j=$cells[0]->nodeValue;
            $w=$cells[1]->nodeValue;
            $d=$cells[2]->nodeValue;
            $l=$cells[2]->nodeValue;

            $idTeam = sentenceToSum($teamName . $teamSlug[5] . $url . $teamImage);
         
            echo "<pre>";
            //  print_r($j);
            print_r($position . " " . $teamName . " " . $url . " " . $idTeam . " " . $teamImage . " PUNTS:" . $points . " Jugats: $j  W: " . $w . " D: " . $d . " L: " . $l . " GF:" . $gf . " GC:" . $gc);
            echo "</pre>";


            $url ="https://efsmasquefa-jokcat.turso.io";
            $token="eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3MzQwMjk1NzEsImlkIjoiYzA2ZThhYjctYWJhYi00NzQwLWE2MzMtZWQzZWZhYzRmNWQwIn0.yNzV7Ptv3KQP3LIi-WxV5scl4Cb_jx5ayNb4wPOXJmKmvHQ2_XmwQb79Z1KANORdbdS6eo27s3k7TZG_e79gCQ";
            
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
            /*   try {
            // Execute the request
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                throw new Exception('cURL Error: ' . curl_error($ch));
            }

            // Check HTTP status code
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                throw new Exception("HTTP Error: $httpCode - $response");
            }

            // Decode and log success
            $data = json_decode($response, true);
            error_log("Match inserted or replaced successfully: " . print_r($data, true));
            return $data;
        } catch (Exception $e) {
            error_log('Error inserting or replacing match: ' . $e->getMessage());
            throw $e;
        } finally {
            // Always close cURL handle
            curl_close($ch);
        } */
        }
    }
}
$url = 'https://jokcatfs-pauarjalaguer.turso.io/';
$token = "eyJhbGciOiJFZERTQSIsInR5cCI6IkpXVCJ9.eyJhIjoicnciLCJpYXQiOjE3MjQ1MTcwNjYsImlkIjoiZjI3NmQ3NmUtMjA1My00ZmJhLWI2MTgtMGQyZGZkN2E3NDEzIn0.vGKIODWyeqUw-YY-XdW6jEUeRUSyFdevSdimkQ0bpIIghhEbrXsHUVdDMXUBWwCHFHYtBwWixlv_JqQVzuDoCQ";

$query = "select * from groups";

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
foreach($data[0]['results']['rows'] as $r){
   parseLeague($r[0],$r[4],$mysqli);
}
?>

<script>
    //setTimeout(() => window.location.replace("01_lligues_copiaIndexdeLligues.php"), 15000);
</script>