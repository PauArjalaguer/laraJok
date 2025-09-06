<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;


use \DOMDocument;
use \DOMXPath;

class ScrapingFCF extends Controller
{
    const ERROR_FETCH_CONTENT = 'Error fetching content';
    protected static function getWebContent($url, $userAgent = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return $html;
    }

    protected static function getDOM($html)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_NOERROR);
        return new DOMXPath($dom);
    }
    public static function scrapeTeams()
    {
        $baseUrl = 'https://www.fcf.cat/club';
        $url = $baseUrl . '/2526/espardenya-masquefa-fs/';
        $html = self::getWebContent($url);
        if (!$html) {
            return response()->json(['error' => self::ERROR_FETCH_CONTENT], 500);
        }

        $xpath = self::getDOM($html);


        // Find all news containers
        $teamsTables = $xpath->query("//table[contains(@class, 'fcftable') and contains(@class, 'mb-15')]");
        $hrefs = [];
        foreach ($teamsTables as $teamsTable) {
            $hrefNode = $xpath->query(".//a/@href", $teamsTable)->item(0);
            if ($hrefNode) {
                $hrefs[] = $hrefNode->nodeValue; // p.ex. https://www.fcf.cat/equip/2526/2cs/efs-masquefa-a
            }
        }
        dd($hrefs);
    }
}
