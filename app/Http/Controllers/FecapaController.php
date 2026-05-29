<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class FecapaController extends Controller
{

    private function get_categories()
    {
        return Categories::orderBy('idCategory', 'asc')->get();
    }
    private function get_fecapa_leagues_HTML()
    {
        $url = "https://www.server2.sidgad.es/fecapa/fecapa_ls_1.php";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'idc' => '3063',
            'tipo_stats' => 'plantillas',
            'site_lang' => 'ca',
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: server2.sidgad.es',
            'Origin: http://server2.sidgad.es',
            'Referer: http://server2.sidgad.es',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Content-Type: text/html; charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return $response;
    }
    protected function parse_league_node($node, $categories)
    {
        $llistaClubs = $node->attributes[1]->nodeValue;
        $t = explode("temp_", $llistaClubs);

        if (!isset($t[1])) {
            return null;
        }

        $t1 = explode(" ", $t[1]);
        $idSeason = $t1[0];
        $nomLliga = trim(mb_convert_encoding($node->childNodes[3]->nodeValue, 'ISO-8859-1', 'UTF-8'));
        $idLliga = $node->attributes[2]->nodeValue;

        $idCategory = $this->match_category($nomLliga, $categories);

        return [
            'idLeague' => $idLliga,
            'leagueName' => $nomLliga,
            'idSeason' => $idSeason,
            'idCategory' => $idCategory,
        ];
    }
    private function normalize_string($string)
    {
        $string = mb_strtolower($string, 'UTF-8');
        $string = str_replace(
            ['à', 'á', 'è', 'é', 'ì', 'í', 'ò', 'ó', 'ù', 'ú', 'ï', 'ü', 'ç', '·'],
            ['a', 'a', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'i', 'u', 'c', ' '],
            $string
        );
        Log::info($string);
        return preg_replace('/[^a-z0-9]/', '', $string);
    }

    protected function match_category($nomLliga, $categories)
    {
        $nomLligaNormalitzat = $this->normalize_string($nomLliga);

        foreach ($categories as $category) {
            $catNameNormalitzat = $this->normalize_string($category->categoryName);

            if (strpos($nomLligaNormalitzat, $catNameNormalitzat) !== false) {
                return $category->idCategory;
            }
        }

        return 0;
    }

    protected function saveLeague($data)
    {
        DB::statement("
            INSERT INTO leagues (idLeague, leagueName, idSeason, idCategory)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                leagueName = VALUES(leagueName),
                idSeason = VALUES(idSeason),
                idCategory = VALUES(idCategory)
        ", [
            $data['idLeague'],
            $data['leagueName'],
            $data['idSeason'],
            $data['idCategory'],
        ]);
    }
    public function gestio_lligues()
    {
        //1 Obtenim les categories
        $categories = $this->get_categories();

        //2 Carrega la web amb les lligues
        $html = $this->get_fecapa_leagues_HTML();
        if (!$html) {
            return response()->json(['error' => 'Error fetching content'], 500);
        }

        //3 parsejo els links
        $dom = new DOMDocument('1.0');
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = true;
        $xpath = new DOMXPath($dom);

        $links = $xpath->query("//a");
        $inserted = 0;

        foreach ($links as $league) {
            $data = $this->parse_league_node($league, $categories);
            if ($data) {
                $this->saveLeague($data);
                $inserted++;
            }
        }
        return response()->json([
            'message' => "Processades $inserted lligues",
            'total' => $inserted,
        ]);
    }
}
