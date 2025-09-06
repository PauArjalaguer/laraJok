<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use \DOMDocument;
use \DOMXPath;


class ScrapingController extends Controller
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

    protected static function saveArticle($data)
    {

        try {
            if (!News::where('externalLink', $data['url'])->exists()) {
                $n = News::create([
                    'newsTitle' => $data['title'],
                    'newsDatetime' => $data['date'],
                    'newsContent' => $data['content'],
                    'newsImage' => $data['image'],
                    'externalLink' => $data['url']
                ]);
                Log::info("----------------------------------------------------------");
                Log::info($n);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error saving article: ' . $e->getMessage());
            return false;
        }
    }

    public static function scrapeFCBarcelona()
    {
        $baseUrl = 'https://www.fcbarcelona.cat';
        $url = $baseUrl . '/ca/hoquei-patins/primer-equip/noticies';
        $html = self::getWebContent($url);
        if (!$html) {
            return response()->json(['error' => self::ERROR_FETCH_CONTENT], 500);
        }

        $xpath = self::getDOM($html);
        $articles = [];

        // Find all news containers
        $newsDivs = $xpath->query("//div[contains(@class, 'feed__container') and contains(@class, 'js-feed-container')]");

        foreach ($newsDivs as $div) {
            $titleNode = $xpath->query(".//*[contains(@class, 'thumbnail__title')]", $div)->item(0);
            $linkNode = $xpath->query('.//a', $div)->item(0);
            $imageNode = $xpath->query('.//img', $div)->item(0);

            $image = $imageNode ? $imageNode->attributes[1]->value : '';
            $link = $linkNode ? $linkNode->getAttribute('href') : '';
            $fullUrl = $baseUrl . $link;

            $pattern = '/\/(\d{4})\/(\d{2})\/(\d{2})\//';

            preg_match($pattern, $image, $d);
            $date = $d[1] . "-" . $d[2] . "-" . $d[3];
            // Get article detail
            $detail = self::getFCBarcelonaDetail($fullUrl);

            $articleData = [
                'title' => $titleNode ? utf8_decode(trim($titleNode->textContent)) : '',
                'content' => utf8_decode($detail),
                'image' => $image,
                'url' => $fullUrl,
                'source' => 'FCBarcelona',
                'date' => $date . " 00:00:00"

            ];


            if (self::saveArticle($articleData)) {
                $articles[] = $articleData;
            }
        }

        return response()->json(['articles' => $articles]);
    }

    protected static function getFCBarcelonaDetail($url)
    {
        $html = self::getWebContent($url);
        if (!$html) {return '';}

        $xpath = self::getDOM($html);
        $textNoticia = "";

        $article = $xpath->query("//div[contains(@class, 'article__content') and contains(@class, 'js-article-body')]")->item(0);
        if ($article) {
            $paragraphs = $xpath->query('.//p', $article);
            foreach ($paragraphs as $paragraph) {
                $textNoticia .= $paragraph->textContent . " \n\n";
            }
        }

        return trim($textNoticia);
    }

    public static function scrapeReus()
    {
        $baseUrl = 'https://reusdeportiu.org';
        $url = $baseUrl . '/Not%C3%ADcies/seccions/hoquei-patins/';
        //https://reusdeportiu.org/Not%C3%ADcies/seccions/hoquei-patins/
        $html = self::getWebContent($url);
        if (!$html) {
            return response()->json(['error' => self::ERROR_FETCH_CONTENT], 500);
        }

        $xpath = self::getDOM($html);
        $articles = [];

        $newsDivs = $xpath->query("//article");

        foreach ($newsDivs as $div) {
            $titleNode = $xpath->query(".//h2", $div)->item(0);
            $link = $xpath->query('.//h2', $div)->item(0)->childNodes[1]->attributes[0]->nodeValue;
            $image = $xpath->query(".//div[@class='blog-meta']", $div)->item(0)->childNodes[0]->childNodes[0]->attributes[6]->nodeValue;
            $content = $xpath->query(".//div[@class='entry-content']", $div)->item(0)->nodeValue;

            $date =  $xpath->query(".//span[@class='av-structured-data']", $div)->item(3)->nodeValue;

            $articleData = [
                'title' => $titleNode ? trim($titleNode->nodeValue) : '',
                'content' => $content,
                'image' => $image,
                'url' => $link,
                'source' => 'Reus',
                'date' => $date

            ];


            if (self::saveArticle($articleData)) {
                $articles[] = $articleData;
            }
        }

        return response()->json(['articles' => $articles]);
    }
    protected static function getPalauDetail($url)
    {
        $html = self::getWebContent($url);
        if (!$html) {return '';}

        $xpath = self::getDOM($html);
        $textNoticia = "";

        $article = $xpath->query("//div[contains(@class, 'entry-content') and contains(@class, 'clearfix')]")->item(0);
        if ($article) {
            $paragraphs = $xpath->query('.//p', $article);
            foreach ($paragraphs as $paragraph) {
                $textNoticia .= $paragraph->textContent . " \n\n";
            }
        }

        return trim($textNoticia);
    }
    public static function scrapePalau()
    {
        $baseUrl = 'https://hcpalau.com';
        $url = $baseUrl . '/noticies/';
        $html = self::getWebContent($url);
        if (!$html) {
            return response()->json(['error' => self::ERROR_FETCH_CONTENT], 500);
        }

        $xpath = self::getDOM($html);
        $articles = [];
        $newsDivs = $xpath->query("//div[contains(@class, 'grid-item-inner')]");
        $contador = 0;
        set_time_limit(1300);
        foreach ($newsDivs as $key => $div) {
            if ($contador <= 5) {
                set_time_limit(1300);
                echo $contador;
                $link = $xpath->query(".//h3[@class='entry-title']", $div)->item(0)->childNodes[0]->attributes[0]->nodeValue;
                $title = $xpath->query(".//h3[@class='entry-title']", $div)->item(0)->nodeValue;
                $image = $xpath->query(".//div[@class='entry-featured']", $div)->item(0)->childNodes[1]->childNodes[0]->attributes->getNamedItem('src')->nodeValue;
                $date = str_replace("-", "_", $xpath->query(".//div[@class='entry-featured']", $div)->item(0)->childNodes[1]->childNodes[0]->attributes[6]->nodeValue);
                $date = explode("_", $date);
                $detail = self::getPalauDetail($link);
                if (isset($date[1]) && strlen($date[1]) == 8) {
                    try {
                        $date = Carbon::createFromFormat('Ymd', $date[1])->format("Y-m-d h:m:s");
                    } catch (\Exception $e) {
                        //$date = '1970-01-01 00:00:00'; 
                        $date = Carbon::now()->format('Y-m-d h:m:s');
                    }
                } else {
                    // $date = '1970-01-01 00:00:00';  
                    $date = Carbon::now()->format('Y-m-d h:m:s');
                }
                $articleData = [
                    'title' => $title,
                    'url' => $link,
                    'date' => $date,
                    'image' => $image,
                    'content' => $detail

                ];

                if (self::saveArticle($articleData)) {
                    $articles[] = $articleData;
                }
                $contador++;
            }
        }

        return response()->json(['articles' => $articles]);
    }
    protected static function getCerdanyolaDetail($url)
    {
        $html = self::getWebContent($url);
        if (!$html){ return '';}

        $xpath = self::getDOM($html);
        $textNoticia = "";

        $article = $xpath->query("//div[contains(@class, 'article-content')]")->item(0);
        if ($article) {
            $paragraphs = $xpath->query('.//p', $article);
            foreach ($paragraphs as $paragraph) {
                $textNoticia .= $paragraph->textContent . " \n\n";
            }
        }

        return trim($textNoticia);
    }
    public static function scrapeCerdanyola()
    {
        $baseUrl = 'https://www.cerdanyola.info';
        $url = $baseUrl . '/search?_token=5YitQbDLpnznBTPY6UpZkwT3wd5VPoBywFbNqwuw&q=hoquei';
        $html = self::getWebContent($url);
        if (!$html) {
            return response()->json(['error' => self::ERROR_FETCH_CONTENT], 500);
        }

        $xpath = self::getDOM($html);
        $articles = [];
        $newsDivs = $xpath->query("//div[contains(@class, 'post-wrapper')]");
        $contador = 0;
        set_time_limit(1300);

        foreach ($newsDivs as $div) {
            if ($contador <= 25) {
                set_time_limit(1300);

                $linkNode = $xpath->query(".//div[contains(@class, 'category-news-image')]//a", $div)->item(0);
                if (isset($linkNode)) {
                    $link = $baseUrl . $linkNode->getAttribute('href');
                    $imageNode = $xpath->query(".//img[contains(@class, 'cover')]", $div)->item(0);
                    $image = $imageNode ? $imageNode->getAttribute('src') : '';
                    $titleNode = $xpath->query(".//h3/a", $div)->item(0);
                    $title = $titleNode ? trim($titleNode->nodeValue) : '';

                    $dateNode = $xpath->query(".//p[contains(@class, 'text-muted')]", $div)->item(0);
                    $date = $dateNode ? str_replace("-", "_", trim($dateNode->nodeValue)) : '';
                    $date = str_replace("de ", "", $date);
                    $date = str_replace("d'", "", $date);
                    $d = explode(" ", $date);
                    $monthNames = [
                        'gener' => 1,
                        'febrer' => 2,
                        'març' => 3,
                        'abril' => 4,
                        'maig' => 5,
                        'juny' => 6,
                        'juliol' => 7,
                        'agost' => 8,
                        'setembre' => 9,
                        'octubre' => 10,
                        'novembre' => 11,
                        'desembre' => 12
                    ];

                    $day = $d[0];
                    $month = $monthNames[strtolower($d[1])] ?? null;
                    $year = $d[2];
                    $date =  $year . "-" . $month . "-" . $day;
                    $detail = self::getCerdanyolaDetail($link);

                    $articleData = [
                        'title' => $title,
                        'url' => $link,
                        'date' => $date,
                        'image' => $image,
                        'content' => $detail
                    ];

                    if (self::saveArticle($articleData)) {
                        $articles[] = $articleData;
                    }
                    $contador++;
                }
            }
        }

        return $articles;
    }
    protected static function getRegioDetail($url)
    {
        $html = self::getWebContent($url);
        if (!$html) {return '';}

        $xpath = self::getDOM($html);
        $textNoticia = "";

        $title =  $article = $xpath->query("//h1[contains(@class,'ft-title')]")->item(0) ? $article = $xpath->query("//h1[contains(@class,'ft-title')]")->item(0)->nodeValue : '';

        $article = $xpath->query("//div[contains(@class, 'ft-layout-grid-flex__colXs-12')]")->item(0);
        if ($article) {
            $paragraphs = $xpath->query('.//p', $article);
            foreach ($paragraphs as $paragraph) {
                $textNoticia .= $paragraph->textContent . " \n\n";
            }
        }

        return ['title' => $title, 'content' => trim($textNoticia)];
    }
    public static function scrapeRegio()
    {
        $baseUrl = 'https://www.regio7.cat';
        $url = $baseUrl . '/tags/hoquei-patins/';
        $html = self::getWebContent($url);
        if (!$html) {
            return response()->json(['error' => self::ERROR_FETCH_CONTENT], 500);
        }

        $xpath = self::getDOM($html);
        $articles = [];
        $newsDivs = $xpath->query("//article");
        $contador = 0;
        set_time_limit(1300);

        foreach ($newsDivs as $div) {
            if ($contador <= 5) {
                set_time_limit(1300);

                $linkNode = $xpath->query(".//a[contains(@class, 'new__headline')]", $div)->item(0);
                if (isset($linkNode)) {

                    $link = $linkNode ? $linkNode->getAttribute('href') : '';

                    $imageNode1 = $xpath->query(".//picture", $div)->item(0);
                    $image1 = $imageNode1 ? $imageNode1->getAttribute('data-iesrc') : '';

                    $imageNode2 = $xpath->query(".//img", $div)->item(0);
                    $image2 = $imageNode2 ? $imageNode2->getAttribute('src') : '';
                    $image = $image1 ? $image1 : $image2;
                    $titleNode = $xpath->query(".//h1[@itemprop='headline']", $div)->item(0);
                    $title = $titleNode ? trim($titleNode->nodeValue) : '';

                    $dateNode = $xpath->query(".//meta[@itemprop='datePublished']", $div)->item(0);
                    $date = $dateNode ?  $dateNode->getAttribute('content') : '';
                    $date = Carbon::parse($date)->format('Y-m-d H:i:s');
                    $detail = self::getRegioDetail($link);

                    $articleData = [
                        'title' => $detail['title'],
                        'url' => $link,
                        'date' => $date,
                        'image' => $image,
                        'content' => $detail['content']
                    ];

                    if (strlen($detail['title']) > 1) {
                        if (self::saveArticle($articleData)) {
                            $articles[] = $articleData;
                        }
                    }
                    $contador++;
                }
            }
        }

        return $articles;
    }

    public static function scrapeNoia()
    {
        $baseUrl = 'https://cenoia.com/';
        $url = $baseUrl . 'category/actualitat/';
        $html = self::getWebContent($url);
        if (!$html) {
            return response()->json(['error' => self::ERROR_FETCH_CONTENT], 500);
        }

        $xpath = self::getDOM($html);
        $articles = [];
        $newsDivs = $xpath->query(".//div[contains(@class, 'blog-post-repeat')]");
        $contador = 0;
        set_time_limit(1300);

        foreach ($newsDivs as $div) {
            if ($contador <= 15) {
                set_time_limit(1300);

                $title = $xpath->query(".//h3[contains(@class, 'post-title')]", $div)->item(0)->nodeValue;

                echo "\n$title";
                $link = $xpath->query(".//div[contains(@class, 'post-thumb')]", $div)->item(0)->childNodes[0]->getAttribute('href');
                $image = $xpath->query(".//div[contains(@class, 'post-thumb')]", $div)->item(0)->childNodes[0]->childNodes[0]->getAttribute('src');
            
                $date = $xpath->query(".//div[contains(@class, 'post-date')]", $div)->item(0)->nodeValue;

                $date = str_replace("de ", "", $date);
                $date = str_replace("d'", "", $date);
                $d = explode(" ", $date);

                $monthNames = [
                    'gener' => 1,
                    'febrer' => 2,
                    'març' => 3,
                    'abril' => 4,
                    'maig' => 5,
                    'juny' => 6,
                    'juliol' => 7,
                    'agost' => 8,
                    'setembre' => 9,
                    'octubre' => 10,
                    'novembre' => 11,
                    'desembre' => 12
                ];

                $day = $d[0];
                $month = $monthNames[strtolower($d[1])] ?? null;
                $year = $d[2];
                $date =  $year . "-" . $month . "-" . $day;



                $detail = self::getNoiaDetail($link);

                $articleData = [
                    'title' => $title,
                    'url' => $link,
                    'date' => $date,
                    'image' => $image,
                    'content' => $detail['content']
                ];

                if (strlen($title) > 1) {
                    if (self::saveArticle($articleData)) {
                        $articles[] = $articleData;
                    }
                }
                $contador++;
            }
        }

        return $articles;
    }

    protected static function getNoiaDetail($url)
    {
        $html = self::getWebContent($url);
        if (!$html) {
            return '';
        }

        $xpath = self::getDOM($html);
        $textNoticia = "";


        $article = $xpath->query("//div[contains(@class, 'entry-content')]")->item(0);
        if ($article) {
            $paragraphs = $xpath->query('.//p', $article);
            foreach ($paragraphs as $paragraph) {
                $textNoticia .= $paragraph->textContent . " \n\n";
            }
        }

        return ['content' => trim($textNoticia)];
    }
    public static function scrapeFecapaResults()
    {
        $games = [];
        $url = 'https://server2.sidgad.es/fecapa/00_fecapa_agenda_1.php';

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Origin: http://www.hoqueipatins.fecapa.cat',
            'Referer: http://www.hoqueipatins.fecapa.cat/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'your_form_data_here');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $htmlContent = curl_exec($ch);
        curl_close($ch);


        // Use DOMDocument to parse the HTML
        $dom = new \DOMDocument();
        @$dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $rows = $dom->getElementsByTagName('tr');

        foreach ($rows as $row) {
            // Check if the row has the 'fila_agenda' class
            if (strpos($row->getAttribute('class'), 'fila_agenda') !== false) {
                // Extract param_game attribute
                $paramGame = $row->getAttribute('param_game');
                $paramParts = explode('_', $paramGame);

                // Parse date
                $dateTd = $row->getElementsByTagName('td')->item(1);
                $dateStr = trim($dateTd->textContent);
                $date = Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d');

                // Parse time
                $timeTd = $row->getElementsByTagName('td')->item(2);
                $time = trim($timeTd->textContent);

                // Parse clubs and league
                $club1 = $row->getAttribute('club1');
                $club2 = $row->getAttribute('club2');
                $leagueId = end($paramParts);

                // Parse result (if exists)
                $resultTd = $row->getElementsByTagName('td')->item(7);
                $result = trim($resultTd->textContent);

                // Parse location
                $locationTd = $row->getElementsByTagName('td')->item(8);
                $location = trim($locationTd->textContent);

                /*  $games[] = [
                    'date' => $date,
                    'time' => $time,
                    'club1_id' => $club1,
                    'club2_id' => $club2,
                    'league_id' => $leagueId,
                    'result' => $result ?: null,
                    'location' => $location
                ]; */
                /*    if ($result && strpos($result, '-') !== false) {
                    // Split result into local and visitor scores
                    list($localResult, $visitorResult) = explode('-', trim($result));
        
                    // Find matching match in database
                    $match = Matches::where('club_local_id', $club1)
                        ->where('club_visitor_id', $club2)
                        ->where('group_id', $leagueId)
                        ->where('date', $date)
                        ->where('hour', $time)
                        ->first();
        
                    // Update match if found
                    if ($match) {
                        $match->update([
                            'localResult' => trim($localResult),
                            'visitorResult' => trim($visitorResult)
                        ]);
                    }
                } */
            }
        }
    }
}
