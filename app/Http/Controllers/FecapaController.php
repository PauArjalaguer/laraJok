<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class FecapaController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // HELPERS PRIVATS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Curl genèric per cridar les URLs de FECAPA (sidgad).
     */
    private function getCurl(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'idc'        => '3063',
            'tipo_stats' => 'plantillas',
            'site_lang'  => 'ca',
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: server2.sidgad.es',
            'Origin: http://server2.sidgad.es',
            'Referer: http://server2.sidgad.es',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Content-Type: text/html; charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('[FecapaController] cURL Error: ' . curl_error($ch) . ' URL: ' . $url);
            curl_close($ch);
            return null;
        }
        curl_close($ch);
        return $response;
    }

    /**
     * Curl específic per a l'acta del partit (POST amb paràmetres de partit).
     */
    private function getCurlMatchReport(int $idMatch, int $idLeague): ?string
    {
        $url = "https://www.server2.sidgad.es/fecapa/fecapa_gr_{$idMatch}_1.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'idm' => 1,
            'idc' => $idLeague,
            'idp' => $idMatch,
            'tab' => 'tab_ficha_resumen',
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: server2.sidgad.es',
            'Origin: http://server2.sidgad.es',
            'Referer: http://server2.sidgad.es',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Content-Type: text/html; charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('[FecapaController] cURL matchReport Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }
        curl_close($ch);
        return $response;
    }

    /**
     * Descarrega la fitxa de temporada d'un jugador a FECAPA.
     */
    private function getPlayerProfile(int $idPlayer, int $idSeason): ?string
    {
        $url = "https://www.server2.sidgad.es/fecapa/profiles/fecapa_profileseason_{$idPlayer}_1_{$idSeason}.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['idm' => 1, 'idc' => $idSeason]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Origin: http://server2.sidgad.es',
            'Referer: http://server2.sidgad.es',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error("[FecapaController] getPlayerProfile cURL Error: " . curl_error($ch) . " idPlayer=$idPlayer");
            curl_close($ch);
            return null;
        }
        curl_close($ch);
        return $response ?: null;
    }

    /**
     * Acta buida (problema de FECAPA): recorre els jugadors del partit i
     * recupera gols i targetes de la seva fitxa de temporada, a mes de la data de naixement.
     */
    private function fixMatchFromPlayerProfiles(int $idMatch): array
    {
        $row = DB::selectOne("
            SELECT l.idSeason
            FROM matches m
            JOIN leagues l ON l.idLeague = m.idLeague
            WHERE m.idMatch = ?
        ", [$idMatch]);

        if (!$row || !$row->idSeason) {
            return ['idMatch' => $idMatch, 'error' => 'Temporada no trobada'];
        }

        $idSeason = (int)$row->idSeason;
        DB::statement("UPDATE matches SET cronica = NULL WHERE idMatch = ?", [$idMatch]);

        $players = DB::select("SELECT idPlayer FROM player_match WHERE idMatch = ?", [$idMatch]);
        $updatedCount = 0;

        foreach ($players as $p) {
            $idPlayer = (int)$p->idPlayer;
            if (!$idPlayer) continue;

            $response = $this->getPlayerProfile($idPlayer, $idSeason);
            if (!$response) continue;

            // Data de naixement
            if (preg_match('/Data de Naixement:?.*?(\d{2})\/(\d{2})\/(\d{4})/s', $response, $m)) {
                $birthDate = $m[3] . '-' . $m[2] . '-' . $m[1];
                try {
                    DB::statement("UPDATE players SET birthDate = ? WHERE idPlayer = ?", [$birthDate, $idPlayer]);
                } catch (\Exception $ex) {
                    Log::warning("[FecapaController] Error birthDate idPlayer=$idPlayer: " . $ex->getMessage());
                }
            }

            $dom = new DOMDocument('1.0');
            if (!@$dom->loadHTML('<?xml encoding="UTF-8">' . $response)) {
                continue;
            }
            $xpath = new DOMXPath($dom);
            $links = $xpath->query("//a[contains(@class,'game_report') and @idp='$idMatch']");
            if ($links->length === 0) continue;

            $tr = $links->item(0);
            while ($tr && $tr->nodeName !== 'tr') {
                $tr = $tr->parentNode;
            }
            if (!$tr) continue;

            $tds = $tr->getElementsByTagName('td');
            if ($tds->length < 11) continue;

            $gols     = (int)trim($tds->item(5)->nodeValue ?? 0);
            $directes = (int)trim($tds->item(7)->nodeValue ?? 0);
            $penalti  = (int)trim($tds->item(8)->nodeValue ?? 0);
            $blue     = (int)trim($tds->item(9)->nodeValue ?? 0);
            $red      = (int)trim($tds->item(10)->nodeValue ?? 0);

            try {
                DB::statement("
                    UPDATE player_match
                    SET goals = ?, blue = ?, red = ?, directes = ?, penalti = ?, updated = NOW()
                    WHERE idPlayer = ? AND idMatch = ?
                ", [$gols, $blue, $red, $directes, $penalti, $idPlayer, $idMatch]);
                $updatedCount++;
            } catch (\Exception $ex) {
                Log::warning("[FecapaController] Error update player_match idMatch=$idMatch idPlayer=$idPlayer: " . $ex->getMessage());
            }
        }

        return ['idMatch' => $idMatch, 'fixed_players' => $updatedCount];
    }

    private function get_categories(): \Illuminate\Support\Collection
    {
        return Categories::orderBy('idCategory', 'asc')->get();
    }

    private function normalize_string(string $string): string
    {
        $string = mb_strtolower($string, 'UTF-8');
        $string = str_replace(
            ['a', 'a', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'i', 'u', 'c', ' '],
            ['a', 'a', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'i', 'u', 'c', ' '],
            $string
        );
        return preg_replace('/[^a-z0-9]/', '', $string);
    }

    private function match_category(string $nomLliga, $categories): int
    {
        $normalitzat = $this->normalize_string($nomLliga);
        foreach ($categories as $category) {
            if (strpos($normalitzat, $this->normalize_string($category->categoryName)) !== false) {
                return $category->idCategory;
            }
        }
        return 0;
    }

    /**
     * Extreu l'idClub a partir de la URL de la imatge del club.
     * També actualitza clubImage a la taula clubs.
     */
    private function extractIdClub(string $clubImage): int
    {
        if (empty($clubImage)) return 0;
        if ($clubImage === 'https://sidgad.cloud/shared/portales_files/images/no_logo.png') {
            return 0;
        }

        $idClub = str_replace(
            [
                'https://sidgad.cloud/fecapa/images//logos_clubes/',
                'https://ns3104249.ip-54-37-85.eu/fecapa/images//logos_clubes/',
                'http://server2.sidgad.es/fecapa/images//logos_clubes/',
                'https://server2.sidgad.es/fecapa/images//logos_clubes/',
            ],
            '',
            strtolower($clubImage)
        );
        $idClub = str_replace(['.png', '.gif'], '', $idClub);
        $parts  = explode('_', $idClub);
        $idClub = (int) ($parts[0] ?? 0);

        // Guard: si el valor es fora del rang normal d'un idClub, ignora
        if ($idClub <= 0 || $idClub > 99999) {
            return 0;
        }

        try {
            DB::statement("UPDATE clubs SET clubImage = ? WHERE idClub = ?", [$clubImage, $idClub]);
        } catch (\Exception $e) {
            // No es crític si no es pot actualitzar la imatge
        }

        return $idClub;
    }

    /**
     * Converteix data de DD/MM/YYYY a YYYY-MM-DD.
     */
    private function prepareDate(string $date): string
    {
        $parts = explode('/', $date);
        if (count($parts) === 3) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $date;
    }

    /**
     * Neteja espais i caracters estranys dels noms.
     */
    private function cleanName(string $s): string
    {
        $s = trim($s);
        $s = str_replace("\xC2\xA0", ' ', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return $s;
    }

    // ═══════════════════════════════════════════════════════════════
    // 02 - GESTIO LLIGUES (insereix/actualitza lligues de FECAPA)
    // ═══════════════════════════════════════════════════════════════

    /**
     * GET /api/fecapa/gestio_lligues
     * Origen: 02_lligues_insertaLLiguesaDDBB.php
     *
     * Parseja la llista de lligues de FECAPA i les insereix/actualitza a la BD.
     * POST-cron: recalcula idCategory per lligues que han quedat a 0.
     */
    public function gestio_lligues(): \Illuminate\Http\JsonResponse
    {
        $categories = $this->get_categories();

        $html = $this->getCurl("https://www.server2.sidgad.es/fecapa/fecapa_ls_1.php");
        if (!$html) {
            return response()->json(['error' => 'Error fetching leagues HTML'], 500);
        }

        $dom = new DOMDocument('1.0');
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = true;
        $xpath = new DOMXPath($dom);
        $links = $xpath->query('//a');

        $inserted = 0;
        foreach ($links as $league) {
            $llistaClubs = $league->attributes[1]->nodeValue ?? '';
            $t = explode('temp_', $llistaClubs);
            if (!isset($t[1])) continue;

            $t1       = explode(' ', $t[1]);
            $idSeason = $t1[0];
            $nomLliga = trim(mb_convert_encoding($league->childNodes[3]->nodeValue ?? '', 'ISO-8859-1', 'UTF-8'));
            $idLliga  = $league->attributes[2]->nodeValue ?? null;

            if (!$idLliga) continue;

            $idCategory = $this->match_category($nomLliga, $categories);

            try {
                DB::statement("
                    INSERT INTO leagues (idLeague, leagueName, idSeason, idCategory)
                    VALUES (?, SUBSTRING(?, 1, 100), ?, ?)
                    ON DUPLICATE KEY UPDATE
                        leagueName  = VALUES(leagueName),
                        idSeason    = VALUES(idSeason),
                        idCategory  = IF(VALUES(idCategory) > 0, VALUES(idCategory), idCategory)
                ", [$idLliga, $nomLliga, $idSeason, $idCategory]);
            } catch (\Exception $e) {
                Log::warning('[FecapaController] gestio_lligues skip: ' . $e->getMessage() . ' | lliga: ' . $idLliga . ' ' . substr($nomLliga, 0, 30));
                continue;
            }

            $inserted++;
        }

        // POST-CRON: Recalcula idCategory per lligues que han quedat a 0
        DB::statement("
            UPDATE leagues l
            JOIN categories c ON LOWER(l.leagueName) LIKE CONCAT('%', LOWER(c.categoryName), '%')
            SET l.idCategory = c.idCategory
            WHERE l.idCategory = 0
        ");

        return response()->json([
            'message'        => "Processades $inserted lligues",
            'total'          => $inserted,
            'post_fix_zeros' => 'idCategory recalculat per lligues amb 0',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // 04 - PARSEJA PARTITS (calendari, grups, equips i partits)
    // ═══════════════════════════════════════════════════════════════

    /**
     * GET /api/fecapa/parseja_partits?idLeague=XXX&force=1&idSeason=41
     * Origen: 04_lligues_parsejaICopiaPartits.php
     *
     * Per cada lliga activa, descarrega el calendari i insereix/actualitza:
     * - phases (grups)
     * - teams (equips locals i visitants)
     * - matches (partits amb data, hora, resultat)
     * - clubs (imatge)
     *
     * POST-cron: actualitza numberofmatches, startdate, enddate a phases.
     */
    public function parseja_partits(Request $request): \Illuminate\Http\JsonResponse
    {
        @set_time_limit(0);
        $force    = $request->boolean('force', false);
        $idLeague = $request->integer('idLeague', 0);
        $idSeason = $request->integer('idSeason', 41);
        $limit    = $request->integer('limit', 10);

        $subquery = $idLeague ? "AND idLeague = $idLeague" : '';

        $leagues = DB::select("
            SELECT idLeague FROM leagues
            WHERE idSeason = $idSeason $subquery
            ORDER BY lastUpdated ASC, idLeague DESC
            LIMIT $limit
        ");

        $log       = [];
        $processed = 0;

        foreach ($leagues as $row) {
            $result = $this->parseCalendariLliga((int)$row->idLeague, $idSeason, $force);
            $log[]  = $result;
            $processed++;
        }

        // POST-CRON: Actualitza estadistiques de phases
        DB::statement("
            UPDATE phases SET
                numberofmatches = (SELECT COUNT(*) FROM matches WHERE idGroup = phases.idGroup),
                startdate = (SELECT MIN(matchDate) FROM matches WHERE idGroup = phases.idGroup),
                enddate   = (SELECT MAX(matchDate) FROM matches WHERE idGroup = phases.idGroup)
            WHERE numberofmatches IS NULL OR startdate IS NULL OR enddate IS NULL
        ");

        return response()->json([
            'message'   => "Processades $processed lligues",
            'processed' => $processed,
            'log'       => $log,
        ]);
    }

    /**
     * Parseja el calendari d'una lliga concreta i insereix grups, equips i partits.
     */
    private function parseCalendariLliga(int $idLliga, int $idSeason, bool $force): array
    {
        $log = ["idLeague" => $idLliga];

        $html = $this->getCurl("https://www.server2.sidgad.es/fecapa/fecapa_cal_idc_{$idLliga}_1.php");
        if (!$html) {
            return array_merge($log, ['error' => "No s'ha pogut descarregar el calendari"]);
        }

        $dom = new DOMDocument('1.0');
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = true;
        $xpath = new DOMXPath($dom);

        // Comprova si la pagina es recent (actualitzada avui o forcada)
        $divs      = $xpath->query('//div');
        $lastDiv   = $divs->item($divs->length - 2);
        $dateText  = $lastDiv ? trim(str_replace(['GMT', 'Report Created on '], '', $lastDiv->nodeValue)) : '';
        $dateNode  = strtotime($dateText);
        $todayMidnight = strtotime(date('Y-m-d'));

        if (!$force && $dateNode < $todayMidnight) {
            $log['skipped'] = 'Pagina no actualitzada avui';
            return $log;
        }

        // Parseja grups/fases (select -> options)
        $selects = $xpath->query('//select');

        if ($selects->length > 0 && $selects->item(0)->childNodes->length > 0) {
            foreach ($selects->item(0)->childNodes as $option) {
                if ($option->nodeName !== 'option') continue;
                $idPhase   = $option->attributes->getNamedItem('value')->nodeValue ?? $idLliga;
                $phaseName = trim(mb_convert_encoding($option->nodeValue, 'ISO-8859-1', 'UTF-8'));
                DB::statement("
                    INSERT INTO phases (idGroup, groupName, idLeague)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE groupName = VALUES(groupName)
                ", [$idPhase, $phaseName, $idLliga]);
            }
        } else {
            DB::statement("
                INSERT INTO phases (idGroup, groupName, idLeague)
                VALUES (?, 'Grup unic', ?)
                ON DUPLICATE KEY UPDATE groupName = 'Grup unic'
            ", [$idLliga, $idLliga]);
        }

        // Parseja taules de calendari
        $tables     = $xpath->query("//table[@id='my_calendar_table']");
        $matchCount = 0;

        foreach ($tables as $calendar) {
            $idGroupRaw   = $calendar->attributes->item(0)->nodeValue ?? '';
            $idGroupParts = explode(' ', $idGroupRaw);
            $idGrup       = str_replace('content_fase_', '', $idGroupParts[2] ?? '');
            if (!$idGrup) $idGrup = $idLliga;

            foreach ($calendar->childNodes as $tbody) {
                if ($tbody->nodeName !== 'tbody') continue;
                foreach ($tbody->childNodes as $tr) {
                    if ($tr->nodeName !== 'tr') continue;

                    $matchDateRaw = $tr->childNodes->item(3) ? $tr->childNodes->item(3)->nodeValue : '';
                    if (!$matchDateRaw || $matchDateRaw === '00/00/0000') continue;

                    $matchDate = $this->prepareDate($matchDateRaw);
                    $matchHour = trim($tr->childNodes->item(5) ? $tr->childNodes->item(5)->nodeValue : '') ?: '00:00:00';

                    $attrVal   = $tr->attributes->item(0) ? $tr->attributes->item(0)->nodeValue : '';
                    $atParts   = explode(' ', $attrVal);
                    $idLocal   = (int) str_replace('team_', '', $atParts[0] ?? 0);
                    $idVisitor = (int) str_replace('team_', '', $atParts[1] ?? 0);

                    if (!$idLocal || !$idVisitor) continue;

                    $localImageNode   = $tr->childNodes->item(11);
                    $visitorImageNode = $tr->childNodes->item(15);
                    $localImage   = ($localImageNode && $localImageNode->childNodes->item(0))
                        ? trim($localImageNode->childNodes->item(0)->attributes->item(0)->nodeValue ?? '')
                        : '';
                    $visitorImage = ($visitorImageNode && $visitorImageNode->childNodes->item(0))
                        ? trim($visitorImageNode->childNodes->item(0)->attributes->item(0)->nodeValue ?? '')
                        : '';

                    $localTeamNode   = $tr->childNodes->item(13);
                    $visitorTeamNode = $tr->childNodes->item(17);
                    $localTeam   = $localTeamNode && $localTeamNode->childNodes->item(1)
                        ? trim(mb_convert_encoding($localTeamNode->childNodes->item(1)->nodeValue, 'ISO-8859-1', 'UTF-8'))
                        : '';
                    $visitorTeam = $visitorTeamNode && $visitorTeamNode->childNodes->item(1)
                        ? trim(mb_convert_encoding($visitorTeamNode->childNodes->item(1)->nodeValue, 'ISO-8859-1', 'UTF-8'))
                        : '';

                    $localClub   = $localImage   ? $this->extractIdClub($localImage)   : 0;
                    $visitorClub = $visitorImage ? $this->extractIdClub($visitorImage) : 0;

                    $idMatchNode = $tr->childNodes->item(27);
                    $idMatchRaw  = ($idMatchNode && $idMatchNode->childNodes->item(1))
                        ? ($idMatchNode->childNodes->item(1)->attributes->getNamedItem('id')->nodeValue ?? '')
                        : '';
                    $idMatch     = str_replace('fichapartido_', '', $idMatchRaw);

                    // Resultat
                    $resultatNode  = $tr->childNodes->item(23);
                    $resultatRaw   = $resultatNode ? trim(mb_convert_encoding($resultatNode->nodeValue, 'ISO-8859-1', 'UTF-8')) : '';
                    $rParts        = explode('-', $resultatRaw);
                    $localResult   = trim($rParts[0] ?? '');
                    $visitorResult = trim($rParts[1] ?? '');
                    if (strlen($localResult) < 1 || in_array($localResult, ['NO JUGAT', 'SUSPES', 'SUSPÈS'])) {
                        $localResult = null;
                    }
                    if (strlen($visitorResult) < 1 || in_array($visitorResult, ['NO JUGAT', 'SUSPES', 'SUSPÈS'])) {
                        $visitorResult = null;
                    }

                    $roundNode = $tr->childNodes->item(9);
                    $round     = $roundNode
                        ? str_replace('JORNADA ', '', trim(mb_convert_encoding($roundNode->nodeValue, 'ISO-8859-1', 'UTF-8')))
                        : '';

                    if (!$localTeam || !$visitorTeam) continue;

                    // Insereix/actualitza equips
                    DB::statement("
                        INSERT INTO teams (idTeam, teamName, idClub, idSeason)
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE teamName = VALUES(teamName), idSeason = VALUES(idSeason)
                    ", [$idLocal, $localTeam, $localClub, $idSeason]);

                    DB::statement("
                        INSERT INTO teams (idTeam, teamName, idClub, idSeason)
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE teamName = VALUES(teamName), idSeason = VALUES(idSeason)
                    ", [$idVisitor, $visitorTeam, $visitorClub, $idSeason]);

                    // Si no hi ha idMatch de FECAPA, en construim un propi
                    if (!$idMatch) {
                        $idMatch = $idLliga . $idGrup . $idLocal . $idVisitor;
                    } else {
                        DB::statement("DELETE FROM matches WHERE idMatch = ?", [$idLliga . $idGrup . $idLocal . $idVisitor]);
                    }

                    DB::statement("
                        INSERT INTO matches
                            (idMatch, idLocal, idVisitor, matchDate, matchHour, idPlace, idRound, localResult, visitorResult, idLeague, idGroup, idMatchShort)
                        VALUES
                            (?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                            matchDate     = VALUES(matchDate),
                            matchHour     = VALUES(matchHour),
                            localResult   = VALUES(localResult),
                            visitorResult = VALUES(visitorResult),
                            idMatchShort  = VALUES(idMatchShort),
                            updated       = NOW()
                    ", [
                        $idMatch, $idLocal, $idVisitor, $matchDate, $matchHour,
                        $round, $localResult, $visitorResult, $idLliga, $idGrup, $idMatch,
                    ]);

                    $matchCount++;
                }
            }
        }

        // Actualitza lastUpdated de la lliga
        DB::statement("UPDATE leagues SET lastUpdated = NOW() WHERE idLeague = ?", [$idLliga]);

        // Actualitza enddate de les fases d'aquesta lliga
        DB::statement("
            UPDATE phases p
            JOIN (
                SELECT idGroup, MAX(matchDate) AS last_match_date
                FROM matches
                GROUP BY idGroup
            ) m ON p.idGroup = m.idGroup
            SET p.enddate = m.last_match_date
            WHERE p.idLeague = ?
        ", [$idLliga]);

        $log['matches_processed'] = $matchCount;
        return $log;
    }

    // ═══════════════════════════════════════════════════════════════
    // 07 - ACTUALITZA PARTITS (resultats en temps real)
    // ═══════════════════════════════════════════════════════════════

    /**
     * GET /api/fecapa/actualitza_partits?force=1
     * Origen: 07_actualitzaPartits.php
     *
     * Agafa els partits d'avui que haurien d'haver acabat i actualitza el resultat.
     */
    public function actualitza_partits(Request $request): \Illuminate\Http\JsonResponse
    {
        @set_time_limit(0);
        $force = $request->boolean('force', false);

        if ($force) {
            $partits = DB::select("
                SELECT DISTINCT idLeague, idLocal, idVisitor
                FROM matches
                WHERE idMatch NOT IN (SELECT idMatch FROM player_match)
                  AND LENGTH(idMatch) >= 7
                  AND matchDate <= NOW()
                  AND matchDate > '2024-01-01'
                LIMIT 50
            ");
        } else {
            $partits = DB::select("
                SELECT idLocal, idVisitor, idLeague
                FROM matches
                WHERE matchDate = CURDATE()
                  AND matchHour <= SUBTIME(CURTIME(), '01:00:00')
                  AND localResult IS NULL
                ORDER BY RAND()
                LIMIT 3
            ");
        }

        $log       = [];
        $processed = 0;

        foreach ($partits as $row) {
            $result = $this->actualitzaResultatPartit((int)$row->idLeague, (int)$row->idLocal, (int)$row->idVisitor, $force);
            if ($result) {
                $log[]  = $result;
                $processed++;
            }
        }

        return response()->json([
            'message'   => "Processats $processed partits",
            'processed' => $processed,
            'log'       => $log,
        ]);
    }

    private function actualitzaResultatPartit(int $idLeague, int $idLocal, int $idVisitor, bool $force): ?array
    {
        $html = $this->getCurl("https://www.server2.sidgad.es/fecapa/fecapa_cal_idc_{$idLeague}_1.php");
        if (!$html) return null;

        $dom = new DOMDocument('1.0');
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = true;
        $xpath = new DOMXPath($dom);

        $divs       = $xpath->query('//div');
        $lastDiv    = $divs->item($divs->length - 2);
        $dateText   = $lastDiv ? trim(str_replace(['GMT', 'Report Created on '], '', $lastDiv->nodeValue)) : '';
        $dateNode   = strtotime($dateText);
        $quarterAgo = time() - 900;

        if (!$force && $dateNode < $quarterAgo) {
            return ['skipped' => "Pagina no actualitzada $idLeague $idLocal-$idVisitor"];
        }

        $tbodies = $xpath->query('//tbody');
        foreach ($tbodies as $tbody) {
            foreach ($tbody->childNodes as $tr) {
                if ($tr->nodeName !== 'tr') continue;
                $attrVal = $tr->attributes->item(0) ? $tr->attributes->item(0)->nodeValue : '';
                if ($attrVal !== "team_{$idLocal} team_{$idVisitor} team_class") continue;

                $resultatNode  = $tr->childNodes->item(23);
                $resultatRaw   = $resultatNode ? trim($resultatNode->nodeValue) : '';
                $result        = explode('-', $resultatRaw);
                $idMatchNode   = $tr->childNodes->item(27);
                $idMatchRaw    = ($idMatchNode && $idMatchNode->childNodes->item(1))
                    ? ($idMatchNode->childNodes->item(1)->attributes->getNamedItem('id')->nodeValue ?? '')
                    : '';
                $idMatch       = str_replace('fichapartido_', '', $idMatchRaw);

                $dateNode3     = $tr->childNodes->item(3);
                $matchDateParts = $dateNode3 ? explode('/', $dateNode3->nodeValue) : [];
                $matchDate      = count($matchDateParts) === 3
                    ? "{$matchDateParts[2]}-{$matchDateParts[1]}-{$matchDateParts[0]}"
                    : null;

                $localResult   = trim($result[0] ?? '');
                $visitorResult = trim($result[1] ?? '');

                if (!empty($localResult) && !empty($idMatch) && $matchDate) {
                    DB::statement("
                        UPDATE matches
                        SET localResult = ?, visitorResult = ?, updated = NOW(), idMatch = ?
                        WHERE matchDate = ? AND idLocal = ? AND idVisitor = ? AND idLeague = ?
                    ", [(int)$localResult, (int)$visitorResult, $idMatch, $matchDate, $idLocal, $idVisitor, $idLeague]);

                    @mail('pau.arjalaguer@gmail.com', 'Partit Enviat', "idlocal: $idLocal \nVisitor: $idVisitor\nLeague: $idLeague");

                    return [
                        'updated'  => true,
                        'idLeague' => $idLeague,
                        'idMatch'  => $idMatch,
                        'result'   => "{$localResult}-{$visitorResult}",
                    ];
                }
            }
        }

        return ['no_result_found' => "$idLeague $idLocal-$idVisitor"];
    }

    // ═══════════════════════════════════════════════════════════════
    // 06 - PARSEJA CLASSIFICACIONS
    // ═══════════════════════════════════════════════════════════════

    /**
     * GET /api/fecapa/parseja_classificacions?idLeague=XXX
     * Origen: 06_classificacions_parseja.php
     *
     * Per cada lliga activa, descarrega la classificacio i insereix/actualitza
     * la taula classifications.
     */
    public function parseja_classificacions(Request $request): \Illuminate\Http\JsonResponse
    {
        @set_time_limit(0);
        $idLeague = $request->integer('idLeague', 0);
        $idSeason = $request->integer('idSeason', 41);
        $subquery = $idLeague ? "AND idLeague = $idLeague" : '';

        $leagues = DB::select("
            SELECT idLeague FROM leagues
            WHERE idSeason = $idSeason $subquery
            GROUP BY idLeague
            ORDER BY MAX(lastUpdated) DESC, idLeague DESC
            LIMIT 100
        ");

        $log       = [];
        $processed = 0;

        foreach ($leagues as $row) {
            $result = $this->parseClassificacioLliga((int)$row->idLeague);
            $log[]  = $result;
            $processed++;
        }

        return response()->json([
            'message'   => "Processades classificacions de $processed lligues",
            'processed' => $processed,
            'log'       => $log,
        ]);
    }

    private function parseClassificacioLliga(int $idLliga): array
    {
        $log  = ['idLeague' => $idLliga];
        $html = $this->getCurl("https://www.server2.sidgad.es/fecapa/fecapa_clasif_idc_{$idLliga}_1.php");
        if (!$html) {
            return array_merge($log, ['error' => "No s'ha pogut descarregar la classificacio"]);
        }

        $dom = new DOMDocument('1.0');
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = true;
        $xpath = new DOMXPath($dom);

        // Fallback: si la lliga no te fases, usem el primer idGroup de phases
        $defaultPhase = DB::selectOne("SELECT idGroup FROM phases WHERE idLeague = ? LIMIT 1", [$idLliga]);
        $idGroup      = $defaultPhase->idGroup ?? null;

        $tables  = $xpath->query('//table');
        $count   = 0;

        foreach ($tables as $table) {
            // Traversal manual per trobar l'element germà anterior
            $prev = $table->previousSibling;
            while ($prev && $prev->nodeType !== XML_ELEMENT_NODE) {
                $prev = $prev->previousSibling;
            }
            if ($prev && ($prev->attributes->getNamedItem('id')->nodeValue ?? '') === 'div_titulo_fase_idc') {
                $phaseName = trim(mb_convert_encoding($prev->nodeValue, 'ISO-8859-1', 'UTF-8'));
                $phaseRow  = DB::selectOne("
                    SELECT idGroup FROM phases
                    WHERE TRIM(groupName) = ? AND idLeague = ?
                ", [$phaseName, $idLliga]);
                // Si trobem la fase pel nom, actualitzem idGroup; si no, conservem el default
                $idGroup = $phaseRow->idGroup ?? $idGroup;
            }

            $tbody = $table->childNodes->item(3);
            if (!$tbody) continue;

            foreach ($tbody->childNodes as $tr) {
                if ($tr->nodeName !== 'tr') continue;

                $position = trim($tr->childNodes->item(1) ? $tr->childNodes->item(1)->nodeValue : '');
                $teamNameNode = $tr->childNodes->item(5);
                $teamName = $teamNameNode && $teamNameNode->childNodes->item(0)
                    ? trim(mb_convert_encoding($teamNameNode->childNodes->item(0)->nodeValue, 'ISO-8859-1', 'UTF-8'))
                    : '';
                $points = trim($tr->childNodes->item(7) ? $tr->childNodes->item(7)->nodeValue : 0);
                $played = trim($tr->childNodes->item(9) ? $tr->childNodes->item(9)->nodeValue : 0);
                $won    = trim($tr->childNodes->item(11) ? $tr->childNodes->item(11)->nodeValue : 0);
                $draw   = trim($tr->childNodes->item(13) ? $tr->childNodes->item(13)->nodeValue : 0);
                $lost   = trim($tr->childNodes->item(15) ? $tr->childNodes->item(15)->nodeValue : 0);
                $gA     = trim($tr->childNodes->item(17) ? $tr->childNodes->item(17)->nodeValue : 0);
                $gC     = trim($tr->childNodes->item(19) ? $tr->childNodes->item(19)->nodeValue : 0);

                if (!$teamName || !$position) continue;

                // Cerca equip: primer amb grup, si no troba prova sense filtre de grup
                $teamRow = DB::selectOne("
                    SELECT t.idTeam FROM teams t
                    JOIN matches m ON m.idLocal = t.idTeam
                    WHERE m.idLeague = ? AND m.idGroup = ?
                      AND t.teamName = ?
                    LIMIT 1
                ", [$idLliga, $idGroup, $teamName]);

                $idTeam = $teamRow->idTeam ?? null;
                if (!$idTeam || !$played) continue;

                $idClassification = (int)$idGroup . (int)$idTeam;

                $prevRow      = DB::selectOne("SELECT position FROM classifications WHERE idClassification = ?", [$idClassification]);
                $prevPosition = ($prevRow && $prevRow->position != $position) ? $prevRow->position : $position;

                DB::statement("
                    INSERT INTO classifications
                        (idClassification, idTeam, points, position, played, won, draw, lost, goalsMade, goalsReceived, idLeague, idGroup, prevPosition)
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        position      = VALUES(position),
                        prevPosition  = VALUES(prevPosition),
                        won           = VALUES(won),
                        draw          = VALUES(draw),
                        played        = VALUES(played),
                        lost          = VALUES(lost),
                        goalsMade     = VALUES(goalsMade),
                        goalsReceived = VALUES(goalsReceived),
                        points        = VALUES(points)
                ", [
                    $idClassification, $idTeam, $points, $position, $played,
                    $won, $draw, $lost, $gA, $gC, $idLliga, $idGroup, $prevPosition,
                ]);
                $count++;
            }
        }

        $log['rows_processed'] = $count;
        return $log;
    }

    // ═══════════════════════════════════════════════════════════════
    // parseMatch - PARSEJA ACTA DEL PARTIT (jugadors, arbitre, faltes)
    // ═══════════════════════════════════════════════════════════════

    /**
     * GET /api/fecapa/parseja_acta_partits
     * Origen: parseMatch.php
     *
     * Per cada partit jugat sense dades d'acta, descarrega i parseja:
     * - Arbitre i faltes (local/visitant)
     * - Jugadors del local i visitant (gols, targetes blaves/vermelles)
     */
    public function parseja_acta_partits(Request $request): \Illuminate\Http\JsonResponse
    {
        @set_time_limit(0);

        // Mode reparacio: ?fixEmpty=1 per partits guardats amb acta buida
        if ($request->boolean('fixEmpty', false)) {
            $partitsBuits = DB::select("
                SELECT m.idMatch, m.idLeague
                FROM matches m
                WHERE m.matchDate < NOW()
                  AND m.localFaults = 0 AND m.visitorFaults = 0
                  AND (m.localResult + m.visitorResult) > 0
                  AND EXISTS (SELECT 1 FROM player_match pm WHERE pm.idMatch = m.idMatch)
                  AND (SELECT COALESCE(SUM(goals), 0) FROM player_match pm WHERE pm.idMatch = m.idMatch) = 0
                  AND NOT EXISTS (SELECT 1 FROM player_match pm WHERE pm.idMatch = m.idMatch AND pm.updated IS NOT NULL)
                ORDER BY m.matchDate DESC
                LIMIT 15
            ");

            $log = [];
            foreach ($partitsBuits as $p) {
                $log[] = $this->fixMatchFromPlayerProfiles((int)$p->idMatch);
            }

            return response()->json([
                'message'   => 'Reparats ' . count($log) . ' partits amb actes buides',
                'processed' => count($log),
                'log'       => $log,
            ]);
        }

        $partits = DB::select("
            SELECT idMatch, idLeague
            FROM matches m
            WHERE matchDate < NOW()
              AND (SELECT COUNT(*) FROM player_match WHERE idMatch = m.idMatch) < 5
              AND LENGTH(m.idMatch) < 7
              AND (m.error != 1 OR m.error IS NULL)
              AND m.updatedTries < 10
            ORDER BY m.updated ASC, m.matchDate DESC
            LIMIT 50
        ");

        $log       = [];
        $processed = 0;

        foreach ($partits as $row) {
            $result = $this->parseActaPartit((int)$row->idMatch, (int)$row->idLeague);
            $log[]  = $result;
            $processed++;
        }

        return response()->json([
            'message'   => "Processades actes de $processed partits",
            'processed' => $processed,
            'log'       => $log,
        ]);
    }

    private function parseActaPartit(int $idMatch, int $idLeague): array
    {
        $log = ['idMatch' => $idMatch];

        $match = DB::selectOne("SELECT idLocal, idVisitor, localResult, visitorResult FROM matches WHERE idMatch = ?", [$idMatch]);
        if (!$match) {
            return array_merge($log, ['error' => 'Partit no trobat a la BD']);
        }
        $idLocal   = (int)$match->idLocal;
        $idVisitor = (int)$match->idVisitor;

        DB::statement("DELETE FROM player_match WHERE idMatch = ?", [$idMatch]);
        // Reiniciem la cronica IA per regenerar-la amb les dades noves de l'acta
        DB::statement("UPDATE matches SET cronica = NULL WHERE idMatch = ?", [$idMatch]);

        $html = $this->getCurlMatchReport($idMatch, $idLeague);
        if (!$html) {
            DB::statement("UPDATE matches SET error = 1 WHERE idMatch = ?", [$idMatch]);
            return array_merge($log, ['error' => "No s'ha pogut descarregar l'acta"]);
        }

        $dom = new DOMDocument('1.0');
        if (!@$dom->loadHTML($html)) {
            DB::statement("UPDATE matches SET error = 1 WHERE idMatch = ?", [$idMatch]);
            return array_merge($log, ['error' => 'HTML invalid']);
        }

        DB::statement("UPDATE matches SET error = 0 WHERE idMatch = ?", [$idMatch]);
        $dom->preserveWhiteSpace = true;
        $xpath = new DOMXPath($dom);

        // Arbitre i faltes
        $tables = $xpath->query('//table');
        $faltesLocal = 0;
        $faltesVisitant = 0;
        try {
            $table1 = $tables->item(1);
            if ($table1) {
                $faltesLocal    = (int)($table1->childNodes->item(1)->childNodes->item(5)->nodeValue ?? 0);
                $faltesVisitant = (int)($table1->childNodes->item(1)->childNodes->item(9)->nodeValue ?? 0);
            }
            $table2 = $tables->item(2);
            if ($table2) {
                $arbitreRaw  = mb_convert_encoding(
                    trim($table2->childNodes->item(1)->childNodes->item(1)->nodeValue ?? ''),
                    'ISO-8859-1', 'UTF-8'
                );
                $arbitreParts = explode("ARBITRATGE\t", $arbitreRaw);
                $arbitre      = trim($arbitreParts[1] ?? '');

                DB::statement("
                    UPDATE matches
                    SET referee = ?, localFaults = ?, visitorFaults = ?
                    WHERE idMatch = ?
                ", [$arbitre, $faltesLocal, $faltesVisitant, $idMatch]);
            }
        } catch (\Exception $e) {
            Log::warning("[FecapaController] Error parsing arbitre/faltes idMatch=$idMatch: " . $e->getMessage());
        }

        // Jugadors amb idPlayer oficial de FECAPA (player_season_stats)
        $pStats = $xpath->query("//div[contains(@class, 'player_season_stats')]");
        foreach ($pStats as $pNode) {
            $id = $pNode->attributes->item(0)->nodeValue ?? null;
            if (!$id) continue;
            $cognomPre = $pNode->nodeValue;
            $c = explode(" ", $cognomPre);
            $cognom = "";
            for ($a = 1; $a <= count($c); $a++) {
                $cognom .= ($c[$a] ?? '') . " ";
            }
            $nomNode = $pNode->childNodes->item(3);
            $nom = $nomNode ? $nomNode->nodeValue : '';
            $cognomNet = str_replace($nom, "", $cognom);
            $playerName = $this->cleanName($nom . " " . $cognomNet);
            if (strlen($playerName) > 2) {
                try {
                    DB::statement("
                        INSERT INTO players (idPlayer, playerName)
                        VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE playerName = VALUES(playerName)
                    ", [(int)$id, $playerName]);
                } catch (\Exception $ex) {
                    Log::warning("[FecapaController] Error insert player id=$id: " . $ex->getMessage());
                }
            }
        }

        // Jugadors de l'acta: taula[3] = local, taula[6] = visitant
        $actes = $xpath->query("//table[contains(@class, 'tabla_acta_print')]");
        $totalGols = 0;

        foreach ([3 => $idLocal, 6 => $idVisitor] as $tableIndex => $idEquip) {
            $tActa = $actes->item($tableIndex);
            if (!$tActa) continue;

            foreach ($tActa->childNodes as $playerRow) {
                if ($playerRow->nodeName !== 'tr' || $playerRow->childElementCount <= 11) continue;

                $dorsal = trim($playerRow->childNodes->item(1) ? $playerRow->childNodes->item(1)->nodeValue : '');
                $nomRaw = mb_convert_encoding(
                    trim($playerRow->childNodes->item(9) ? $playerRow->childNodes->item(9)->nodeValue : ''),
                    'ISO-8859-1', 'UTF-8'
                );

                $gols = (int)trim($playerRow->childNodes->item(11) ? $playerRow->childNodes->item(11)->nodeValue : 0);
                $totalGols += $gols;

                $blue = 0;
                if ($playerRow->childNodes->item(15) && $playerRow->childNodes->item(15)->nodeValue) $blue = 1;
                if ($playerRow->childNodes->item(17) && $playerRow->childNodes->item(17)->nodeValue) $blue = 2;
                if ($playerRow->childNodes->item(19) && $playerRow->childNodes->item(19)->nodeValue) $blue = 3;
                $red  = (int)(bool)($playerRow->childNodes->item(21) ? $playerRow->childNodes->item(21)->nodeValue : 0);

                // Nom en format "Cognom, Nom" -> "Nom Cognom"
                $nomParts = explode(',', $nomRaw);
                $nom      = $this->cleanName(trim(($nomParts[1] ?? '') . ' ' . ($nomParts[0] ?? '')));

                if (strlen($nom) <= 3) continue;

                // Insereix jugador si no s'havia trobat a player_season_stats
                DB::statement("INSERT IGNORE INTO players (playerName) VALUES (?)", [$nom]);

                // Actualitza dorsal
                if ($dorsal) {
                    DB::statement("UPDATE players SET number = ? WHERE playerName = ?", [(int)$dorsal, $nom]);
                }

                // Insereix player_match
                DB::statement("
                    INSERT INTO player_match (idPlayer, idMatch, goals, blue, red, idTeam)
                    SELECT idPlayer, ?, ?, ?, ?, ?
                    FROM players WHERE playerName = ? LIMIT 1
                    ON DUPLICATE KEY UPDATE goals = VALUES(goals), blue = VALUES(blue), red = VALUES(red)
                ", [$idMatch, $gols, $blue, $red, $idEquip, $nom]);
            }
        }

        // Si l'acta es buida pero hi ha hagut gols al partit, recupera dades de les fitxes individuals
        $totalResult = (int)($match->localResult ?? 0) + (int)($match->visitorResult ?? 0);
        if ($totalGols === 0 && $faltesLocal === 0 && $faltesVisitant === 0 && $totalResult > 0) {
            $fixLog = $this->fixMatchFromPlayerProfiles($idMatch);
            $log['fixed_from_profiles'] = $fixLog;
        }

        DB::statement("
            UPDATE matches SET updatedTries = updatedTries + 1, updated = NOW()
            WHERE idMatch = ?
        ", [$idMatch]);

        $log['processed'] = true;
        return $log;
    }

    // ═══════════════════════════════════════════════════════════════
    // parseTeams - ASSIGNA idCategory I idSeason ALS EQUIPS
    // ═══════════════════════════════════════════════════════════════

    /**
     * GET /api/fecapa/parseja_equips
     * Origen: parseTeams.php
     *
     * Actualitza idCategory i idSeason de tots els equips a partir
     * dels partits que han jugat i les lligues corresponents.
     */
    public function parseja_equips(): \Illuminate\Http\JsonResponse
    {
        // Equips locals — MAX() per compatibilitat amb only_full_group_by
        DB::statement("
            UPDATE teams t
            JOIN (
                SELECT m.idLocal AS idTeam, MAX(l.idSeason) AS idSeason, MAX(l.idCategory) AS idCategory
                FROM matches m
                JOIN leagues l ON l.idLeague = m.idLeague
                WHERE l.idCategory > 0
                GROUP BY m.idLocal
            ) sub ON t.idTeam = sub.idTeam
            SET t.idSeason = sub.idSeason, t.idCategory = sub.idCategory
            WHERE t.idCategory IS NULL OR t.idCategory = 0
        ");

        // Equips visitants — MAX() per compatibilitat amb only_full_group_by
        DB::statement("
            UPDATE teams t
            JOIN (
                SELECT m.idVisitor AS idTeam, MAX(l.idSeason) AS idSeason, MAX(l.idCategory) AS idCategory
                FROM matches m
                JOIN leagues l ON l.idLeague = m.idLeague
                WHERE l.idCategory > 0
                GROUP BY m.idVisitor
            ) sub ON t.idTeam = sub.idTeam
            SET t.idSeason = sub.idSeason, t.idCategory = sub.idCategory
            WHERE t.idCategory IS NULL OR t.idCategory = 0
        ");

        return response()->json([
            'message' => 'idCategory i idSeason actualitzats per tots els equips sense categoria',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // parseClubsAndPlaces - CLUBS I PAVELLONS
    // ═══════════════════════════════════════════════════════════════

    /**
     * GET /api/fecapa/parseja_clubs_i_pavellons
     * Origen: parseClubsAndPlaces.php
     *
     * Parseja l'agenda de FECAPA per extreure la llista de clubs i pavellons,
     * i actualitza idPlace als partits corresponents.
     */
    public function parseja_clubs_i_pavellons(): \Illuminate\Http\JsonResponse
    {
        @set_time_limit(0);
        $html = $this->getCurl("https://server2.sidgad.es/fecapa/00_fecapa_agenda_1.php");
        if (!$html) {
            return response()->json(['error' => "No s'ha pogut descarregar l'agenda"], 500);
        }

        $dom = new DOMDocument('1.0');
        @$dom->loadHTML($html);
        $dom->preserveWhiteSpace = true;
        $xpath = new DOMXPath($dom);

        $selects     = $xpath->query('//select');
        $clubsCount  = 0;
        $placesCount = 0;

        // Clubs (select[1])
        $sel1 = $selects->item(1);
        if ($sel1) {
            foreach ($sel1->childNodes as $option) {
                if ($option->nodeName !== 'option') continue;
                $idClub   = $option->attributes->item(0)->nodeValue ?? null;
                $clubName = trim(mb_convert_encoding($option->nodeValue, 'ISO-8859-1', 'UTF-8'));
                if (!$idClub) continue;
                DB::statement("
                    INSERT INTO clubs (idClub, clubName)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE clubName = VALUES(clubName)
                ", [$idClub, $clubName]);
                $clubsCount++;
            }
        }

        // Pavellons (select[4])
        $sel4 = $selects->item(4);
        if ($sel4) {
            foreach ($sel4->childNodes as $option) {
                if ($option->nodeName !== 'option') continue;
                $idPlace   = $option->attributes->item(0)->nodeValue ?? null;
                $placeName = trim(mb_convert_encoding($option->nodeValue, 'ISO-8859-1', 'UTF-8'));
                if (!$idPlace) continue;
                DB::statement("
                    INSERT INTO places (idPlace, placeName)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE placeName = VALUES(placeName)
                ", [$idPlace, $placeName]);
                $placesCount++;
            }
        }

        // Pavellons de partits a partir de l'agenda (tabla_standard)
        $taula = $xpath->query("//table[contains(@class, 'tabla_standard')]");
        $matchesPlacesCount = 0;
        if ($taula->length > 0 && $taula->item(0)->childNodes->length > 3) {
            $partits = $taula->item(0)->childNodes->item(3)->childNodes;
            foreach ($partits as $partit) {
                if ($partit->nodeName !== 'tr') continue;
                $idLocal = $partit->attributes->item(2)->nodeValue ?? '';
                $idPista = $partit->attributes->item(4)->nodeValue ?? '';
                $param   = $partit->attributes->item(5)->nodeValue ?? '';
                $idGroup = null;
                if ($param) {
                    $p = explode('_', $param);
                    $idGroup = $p[4] ?? null;
                }

                if (!empty($idLocal) && !empty($idPista) && !empty($idGroup)) {
                    DB::statement("
                        UPDATE matches m
                        JOIN teams t ON m.idLocal = t.idTeam
                        SET m.idPlace = ?
                        WHERE t.idClub = ? AND m.idGroup = ?
                    ", [(int)$idPista, (int)$idLocal, (int)$idGroup]);
                    $matchesPlacesCount++;
                }
            }
        }

        return response()->json([
            'message'              => 'Clubs, pavellons i pistes de partits actualitzats',
            'clubs_count'          => $clubsCount,
            'places_count'         => $placesCount,
            'matches_places_count' => $matchesPlacesCount,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // POST-CRON MANUAL - Accions de neteja i recalcul
    // ═══════════════════════════════════════════════════════════════

    /**
     * GET /api/fecapa/post_cron_cleanup
     *
     * Executa totes les accions post-cron de manteniment:
     * - Recalcula idCategory de lligues a 0
     * - Actualitza phases (numberofmatches, startdate, enddate)
     * - Actualitza idCategory/idSeason dels equips
     * - Reinicia error flag de partits recents
     */
    public function post_cron_cleanup(): \Illuminate\Http\JsonResponse
    {
        @set_time_limit(0);
        // 1. Recalcula idCategory per lligues a 0
        DB::statement("
            UPDATE leagues l
            JOIN categories c ON LOWER(l.leagueName) LIKE CONCAT('%', LOWER(c.categoryName), '%')
            SET l.idCategory = c.idCategory
            WHERE l.idCategory = 0
        ");

        // 2. Actualitza phases
        DB::statement("
            UPDATE phases SET
                numberofmatches = (SELECT COUNT(*) FROM matches WHERE idGroup = phases.idGroup),
                startdate = (SELECT MIN(matchDate) FROM matches WHERE idGroup = phases.idGroup),
                enddate   = (SELECT MAX(matchDate) FROM matches WHERE idGroup = phases.idGroup)
            WHERE numberofmatches IS NULL OR startdate IS NULL OR enddate IS NULL
        ");

        // 3. Actualitza idCategory/idSeason dels equips locals i visitants (MAX per only_full_group_by)
        DB::statement("
            UPDATE teams t
            JOIN (
                SELECT m.idLocal AS idTeam, MAX(l.idSeason) AS idSeason, MAX(l.idCategory) AS idCategory
                FROM matches m JOIN leagues l ON l.idLeague = m.idLeague
                WHERE l.idCategory > 0 GROUP BY m.idLocal
            ) sub ON t.idTeam = sub.idTeam
            SET t.idSeason = sub.idSeason, t.idCategory = sub.idCategory
            WHERE t.idCategory IS NULL OR t.idCategory = 0
        ");
        DB::statement("
            UPDATE teams t
            JOIN (
                SELECT m.idVisitor AS idTeam, MAX(l.idSeason) AS idSeason, MAX(l.idCategory) AS idCategory
                FROM matches m JOIN leagues l ON l.idLeague = m.idLeague
                WHERE l.idCategory > 0 GROUP BY m.idVisitor
            ) sub ON t.idTeam = sub.idTeam
            SET t.idSeason = sub.idSeason, t.idCategory = sub.idCategory
            WHERE t.idCategory IS NULL OR t.idCategory = 0
        ");

        // 4. Reinicia error flag de partits recents (30 dies)
        DB::statement("
            UPDATE matches SET error = NULL, updatedTries = 0
            WHERE error = 1 AND matchDate > DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");

        return response()->json(['message' => 'Post-cron cleanup executat correctament']);
    }
}
