<?php

// URL a la que se va a hacer la solicitud
$url = "http://www.server2.sidgad.es/fecapa/fecapa_cal_idc_2214_1.php?idc=2214&site_lang=ca";

// Los datos POST que se van a enviar
$postData = [
    'idc' => 2214,
    'site_lang' => 'ca'
];

// Inicializar cURL
$ch = curl_init($url);

// Configurar cURL para que retorne el resultado como string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Configurar cURL para que haga una solicitud POST
curl_setopt($ch, CURLOPT_POST, true);

// Configurar cURL para que envíe las variables POST
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch,CURLOPT_REFERER,"http://www.hoqueipatins.fecapa.cat/");
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
// Configurar los encabezados HTTP
$headers = [
    'sec-ch-ua: "Google Chrome";v="125", "Chromium";v="125", "Not.A/Brand";v="24"',
    'Accept: text/html, q=0.01',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'Referer: http://www.hoqueipatins.fecapa.cat/',
    'sec-ch-ua-mobile: ?0',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
    'sec-ch-ua-platform: "Windows"'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
print_r($ch);
// Ejecutar la solicitud
$response = curl_exec($ch);

// Verificar si hubo algún error
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // Imprimir la respuesta
    print_r($response);
}

// Cerrar la sesión cURL
curl_close($ch);
