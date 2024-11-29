<?php

function getCurl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    $postData = [
        'idc' => '3063',
        'tipo_stats' => 'plantillas',
        'site_lang' => 'ca'
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    // Set the headers
    $headers = [
        'Host: server2.sidgad.es',
        'Origin: http://server2.sidgad.es',
        'Referer: http://server2.sidgad.es',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Content-Type: text/html; charset=UTF-8'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        return $response;
    }
}
