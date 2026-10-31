<?php

function getCurl($url, $maxRetries = 4)
{
    // 1. Els teus 10 proxys privats de Webshare (amb usuari i contrasenya)
    $proxies = [
        ['addr' => '45.38.107.97:6014',   'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '64.137.96.74:6641',   'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '198.23.243.226:6361', 'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '38.154.185.97:6370',  'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '84.247.60.125:6095',  'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '191.96.254.138:6185', 'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '198.46.161.42:5092',  'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '64.137.10.153:5803',  'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '104.164.49.38:7693',  'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
        ['addr' => '23.26.71.145:5628',   'auth' => 'fykvhzyj:het4zaa84fv5', 'type' => CURLPROXY_HTTP],
    ];

    // 2. Afegim els 15 proxys més ràpids de GeoNode (sense autenticació)
    $geonodeProxies = getGeonodeProxies(15);
    $proxies = array_merge($proxies, $geonodeProxies);

    // Barregem tota la llista (Webshare + GeoNode)
    shuffle($proxies);

    $postData = http_build_query([
        'idc' => '3063',
        'tipo_stats' => 'plantillas',
        'site_lang' => 'ca'
    ]);

    $headers = [
        'Origin: http://server2.sidgad.es',
        'Referer: http://server2.sidgad.es/',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ];

    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        if (!isset($proxies[$attempt])) {
            break;
        }

        $current = $proxies[$attempt];
        $proxyAddr = $current['addr'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        // Configuració del proxy (suporta tant Webshare com GeoNode HTTP/SOCKS)
        curl_setopt($ch, CURLOPT_PROXY, $proxyAddr);
        curl_setopt($ch, CURLOPT_PROXYTYPE, $current['type']);

        if (!empty($current['auth'])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $current['auth']);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8); // Si un proxy públic no connecta en 8s, saltem ràpid al següent
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_errno($ch) ? curl_error($ch) : '';
        curl_close($ch);

        // Comprovem si ha fallat
        if ($curlError || $httpCode >= 400 || empty($response)) {
            $errorDetail = $curlError ?: "HTTP Code: $httpCode (Resposta buida o error)";
            echo "Error amb el proxy $proxyAddr: $errorDetail" . PHP_EOL;

            // NOMÉS enviem correu si el que falla és un dels teus proxys fixos de Webshare
            // (els públics de GeoNode fallen sovint i t'inundarien la bústia jok@jok.cat)
            if (!empty($current['auth'])) {
                enviarAlertaProxy($proxyAddr, $url, $errorDetail);
            }

            continue;
        }

        return $response;
    }

    return false;
}

/**
 * Obté els proxys de l'API de GeoNode i els guarda en cau durant 30 minuts
 */
function getGeonodeProxies($maxToPick = 15)
{
    $cacheFile = sys_get_temp_dir() . '/geonode_proxies_cache.json';
    $cacheTime = 1800; // 30 minuts en segons

    // Si el fitxer de cau no existeix o té més de 30 minuts, consultem l'API
    if (!file_exists($cacheFile) || (time() - filemtime($cacheFile) > $cacheTime)) {
        $apiUrl = 'https://proxylist.geonode.com/api/proxy-list?page=1&limit=100&sort_by=responseTime&sort_type=asc';
        
        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $json = @file_get_contents($apiUrl, false, $ctx);
        
        if ($json) {
            @file_put_contents($cacheFile, $json);
        }
    } else {
        $json = @file_get_contents($cacheFile);
    }

    if (!$json) {
        return [];
    }

    $data = json_decode($json, true);
    if (empty($data['data']) || !is_array($data['data'])) {
        return [];
    }

    $parsedProxies = [];
    foreach ($data['data'] as $item) {
        if (empty($item['ip']) || empty($item['port']) || empty($item['protocols'][0])) {
            continue;
        }

        $proto = strtolower($item['protocols'][0]);
        $curlType = CURLPROXY_HTTP;

        if ($proto === 'socks5') {
            // CURLPROXY_SOCKS5_HOSTNAME resol el DNS al proxy i és més segur
            $curlType = defined('CURLPROXY_SOCKS5_HOSTNAME') ? CURLPROXY_SOCKS5_HOSTNAME : CURLPROXY_SOCKS5;
        } elseif ($proto === 'socks4') {
            $curlType = CURLPROXY_SOCKS4;
        }

        $parsedProxies[] = [
            'addr' => $item['ip'] . ':' . $item['port'],
            'auth' => null,
            'type' => $curlType
        ];

        if (count($parsedProxies) >= $maxToPick) {
            break;
        }
    }

    return $parsedProxies;
}

function enviarAlertaProxy($proxy, $url, $error)
{
    $to = 'jok@jok.cat';
    $subject = "[JOK Cron] Fallada del proxy privat $proxy";
    $message = "Hola,\n\n"
             . "Un dels proxys privats de Webshare ha fallat durant l'execució del cron.\n\n"
             . "- Proxy: $proxy\n"
             . "- URL destí: $url\n"
             . "- Detall de l'error: $error\n"
             . "- Data i hora: " . date('Y-m-d H:i:s') . "\n";

    $mailHeaders = "From: noreply@jok.cat\r\n"
                 . "Reply-To: jok@jok.cat\r\n"
                 . "Content-Type: text/plain; charset=UTF-8\r\n";

    @mail($to, $subject, $message, $mailHeaders);
}