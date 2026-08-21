<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * Genera la crònica d'un partit a partir de les dades de l'acta.
     */
    public function generateMatchChronicle($matchInfo): ?string
    {
        if (empty($matchInfo)) {
            return null;
        }

        // Comprovem que hi hagi dades de jugadors reals (més d'1 registre i amb idPlayer vàlid)
        $count = is_countable($matchInfo) ? count($matchInfo) : (method_exists($matchInfo, 'count') ? $matchInfo->count() : 0);
        $header = is_array($matchInfo) ? $matchInfo[0] : (method_exists($matchInfo, 'first') ? $matchInfo->first() : (isset($matchInfo[0]) ? $matchInfo[0] : null));

        if (!$header || $count <= 1 || empty($header->idPlayer)) {
            Log::info("AiService: No es genera crònica perquè no hi ha acta detallada de jugadors.");
            return null;
        }

        // Si no hi ha resultat disponible, no es pot fer la crònica
        if ($header->localResult === null || $header->visitorResult === null || $header->localResult === '') {
            return null;
        }

        $localTeam = $header->teamName ?? 'Local';
        $visitorTeam = $header->teamName2 ?? 'Visitant';
        $localScore = (int)$header->localResult;
        $visitorScore = (int)$header->visitorResult;
        $localFaults = (int)($header->localFaults ?? 0);
        $visitorFaults = (int)($header->visitorFaults ?? 0);
        $competition = $header->groupName ?? 'Competició';
        $round = $header->idRound ?? '';
        $matchDate = $header->matchDate ?? '';
        $referee = trim($header->referee ?? '');

        // Comprovem si és una categoria formativa (Aleví / Benjamí / Prebenjamí / Minibenjamí / Escola)
        $searchStrings = [
            $header->categoryName ?? '',
            $header->leagueName ?? '',
            $header->groupName ?? '',
            $header->teamName ?? '',
            $header->teamName2 ?? ''
        ];
        $combinedText = mb_strtolower(implode(' ', $searchStrings), 'UTF-8');
        $isFormativeCategory = (bool)preg_match('/(alevi|aleví|alevin|benjam|prebenjam|minibenjam|escola|iniciaci)/iu', $combinedText);

        // Detalls dels jugadors
        $localGoals = [];
        $visitorGoals = [];
        $localCards = [];
        $visitorCards = [];

        foreach ($matchInfo as $row) {
            $isLocal = ($row->idTeam == $row->idLocal);
            $pName = trim($row->playerName ?? '');
            if (empty($pName)) continue;

            $goals = (int)($row->goals ?? 0);
            $blue = (int)($row->blue ?? 0);
            $red = (int)($row->red ?? 0);
            $directes = (int)($row->directes ?? 0);
            $penalti = (int)($row->penalti ?? 0);

            if ($goals > 0 && !$isFormativeCategory) {
                $detail = "{$pName} ({$goals} " . ($goals > 1 ? 'gols' : 'gol');
                $extras = [];
                if ($penalti > 0) $extras[] = "{$penalti} de penal";
                if ($directes > 0) $extras[] = "{$directes} de falta directa";
                if (!empty($extras)) {
                    $detail .= " - " . implode(', ', $extras);
                }
                $detail .= ")";
                if ($isLocal) {
                    $localGoals[] = $detail;
                } else {
                    $visitorGoals[] = $detail;
                }
            }

            if ($blue > 0 || $red > 0) {
                $cDetail = "{$pName}";
                $cards = [];
                if ($blue > 0) $cards[] = "{$blue} blava" . ($blue > 1 ? 'es' : '');
                if ($red > 0) $cards[] = "{$red} vermella" . ($red > 1 ? 'es' : '');
                $cDetail .= " (" . implode(', ', $cards) . ")";
                if ($isLocal) {
                    $localCards[] = $cDetail;
                } else {
                    $visitorCards[] = $cDetail;
                }
            }
        }

        // Prompt estricte i objectiu
        $prompt = "Actua com un redactor de resultats d'hoquei patins. " .
            "Escriu un resum/crònica breu, objectiu i directe en CATALÀ, cenyint-te ÚNICAMENT i ESTRICTAMENT a les dades oficials de l'acta que et proporciono a continuació. " .
            "ESTÀ TOTALMENT PROHIBIT inventar fets, ambients, accions de joc no registrades o suposar com han jugat els equips.\n\n" .
            "Dades de l'acta:\n" .
            "- Categoria/Grup: {$competition} (Jornada {$round})\n" .
            "- Data del partit: {$matchDate}\n" .
            "- Resultat: {$localTeam} {$localScore} - {$visitorScore} {$visitorTeam}\n" .
            "- Faltes d'equip: {$localTeam} {$localFaults} faltes | {$visitorTeam} {$visitorFaults} faltes\n" .
            (!empty($referee) ? "- Àrbitre/s: {$referee}\n" : "");

        if ($isFormativeCategory) {
            $prompt .= "- Tipus de categoria: Categoria formativa (Aleví / Benjamí / Prebenjamí). NO s'han d'esmentar golejadors individuals, ja que a l'acta s'assignen tots a un únic jugador.\n";
        } else {
            $prompt .= "- Gols de {$localTeam}: " . (!empty($localGoals) ? implode(', ', $localGoals) : "Cap gol registrat a l'acta") . "\n" .
                "- Gols de {$visitorTeam}: " . (!empty($visitorGoals) ? implode(', ', $visitorGoals) : "Cap gol registrat a l'acta") . "\n";
        }

        $prompt .= "- Sancions de {$localTeam}: " . (!empty($localCards) ? implode(', ', $localCards) : "Cap targeta") . "\n" .
            "- Sancions de {$visitorTeam}: " . (!empty($visitorCards) ? implode(', ', $visitorCards) : "Cap targeta") . "\n\n" .
            "Normes obligatòries d'estil:\n" .
            "1. Comença amb un titular en negreta, informatiu i concís amb el resultat (Exemple: **Victòria del [Equip] davant el [Equip] per X-Y**).\n" .
            "2. Redacta exactament 2 paràgrafs curts:\n";

        if ($isFormativeCategory) {
            $prompt .= "   - Primer paràgraf: Indica la jornada i el marcador final ({$localTeam} {$localScore} - {$visitorScore} {$visitorTeam}) destacant el resultat global de l'enfrontament sense esmentar cap nom de golejador individual.\n";
        } else {
            $prompt .= "   - Primer paràgraf: Indica la jornada, el resultat final i la distribució dels gols detallant quins jugadors han marcat i quants gols (especifica si han estat de penal o falta directa si s'indica).\n";
        }

        $prompt .= "   - Segon paràgraf: Resumeix l'aspecte disciplinari (faltes d'equip totals de cada conjunt, targetes blaves o vermelles si n'hi ha hagut i àrbitre del partit).\n" .
            "3. Sigues 100% fidel a les dades. Si no hi ha targetes o penals, no els esmentis. No facis servir frases com 'ambient elèctric', 'grans aturades del porter' o 'ritme frenètic'.\n" .
            "4. Retorna directament el text en català net sense cap preàmbul ni comiat.";

        $generatedText = $this->generateText($prompt);
        if (empty($generatedText)) {
            return null;
        }

        return $this->linkifyMatchEntities($generatedText, $matchInfo);
    }

    /**
     * Converteix els noms d'equips, jugadors i àrbitres en enllaços Markdown clicables.
     */
    public function linkifyMatchEntities(string $text, $matchInfo): string
    {
        $header = is_array($matchInfo) ? $matchInfo[0] : (method_exists($matchInfo, 'first') ? $matchInfo->first() : (isset($matchInfo[0]) ? $matchInfo[0] : null));
        if (!$header) return $text;

        $replacements = [];

        // 1. Jugadors
        foreach ($matchInfo as $row) {
            $pName = trim($row->playerName ?? '');
            $idPlayer = $row->idPlayer ?? null;
            if (!empty($pName) && !empty($idPlayer)) {
                $formattedName = \App\Http\Controllers\TeamsController::teamFormat($pName);
                $url = "/jugador/{$idPlayer}/" . urlencode($pName);
                
                $replacements[$pName] = $url;
                if ($formattedName !== $pName) {
                    $replacements[$formattedName] = $url;
                }
            }
        }

        // 2. Equips
        if (!empty($header->teamName) && !empty($header->idLocal)) {
            $replacements[trim($header->teamName)] = "/equip/{$header->idLocal}/" . urlencode($header->teamName);
            $fLocal = \App\Http\Controllers\TeamsController::teamFormat($header->teamName);
            if ($fLocal !== $header->teamName) {
                $replacements[$fLocal] = "/equip/{$header->idLocal}/" . urlencode($header->teamName);
            }
        }
        if (!empty($header->teamName2) && !empty($header->idVisitor)) {
            $replacements[trim($header->teamName2)] = "/equip/{$header->idVisitor}/" . urlencode($header->teamName2);
            $fVisitor = \App\Http\Controllers\TeamsController::teamFormat($header->teamName2);
            if ($fVisitor !== $header->teamName2) {
                $replacements[$fVisitor] = "/equip/{$header->idVisitor}/" . urlencode($header->teamName2);
            }
        }

        // 3. Àrbitres
        if (!empty($header->referee)) {
            $rawRefs = preg_split('/[\n\/;]|(\s+-\s+|\s+i\s+|\s+y\s+)/iu', trim($header->referee));
            foreach ($rawRefs as $ref) {
                $ref = trim($ref);
                if (!empty($ref) && mb_strlen($ref) > 3) {
                    $replacements[$ref] = "/arbitre/" . urlencode($ref);
                    $fRef = \App\Http\Controllers\TeamsController::teamFormat($ref);
                    if ($fRef !== $ref) {
                        $replacements[$fRef] = "/arbitre/" . urlencode($ref);
                    }
                }
            }
        }

        // Ordenem per longitud descendent per substituir primer els noms més complets
        uksort($replacements, function($a, $b) {
            return mb_strlen($b) <=> mb_strlen($a);
        });

        foreach ($replacements as $name => $url) {
            if (mb_strlen($name) < 3) continue;

            // Substituïm el nom si no està ja enllaçat en Markdown o URL
            $pattern = '/(?<!\[)(?<!\/)\b(' . preg_quote($name, '/') . ')\b(?![^\[]*\])(?![^\(]*\))/iu';

            $text = preg_replace_callback($pattern, function($matches) use ($url) {
                return '[' . $matches[1] . '](' . $url . ')';
            }, $text, 1);
        }

        return $text;
    }

    /**
     * Crida al proveïdor d'IA configurat.
     */
    public function generateText(string $prompt, ?string $systemInstruction = null): ?string
    {
        $provider = config('services.ai.provider', 'gemini');

        try {
            switch ($provider) {
                case 'gemini':
                    return $this->callGemini($prompt, $systemInstruction);
                case 'groq':
                    return $this->callGroq($prompt, $systemInstruction);
                case 'deepseek':
                    return $this->callDeepSeek($prompt, $systemInstruction);
                case 'openrouter':
                    return $this->callOpenRouter($prompt, $systemInstruction);
                default:
                    return $this->callGemini($prompt, $systemInstruction);
            }
        } catch (\Exception $e) {
            Log::error("AiService Error [{$provider}]: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crida a Google Gemini API amb fallback automàtic de models
     */
    protected function callGemini(string $prompt, ?string $systemInstruction = null): ?string
    {
        $apiKey = config('services.ai.gemini.api_key');
        $primaryModel = config('services.ai.gemini.model', 'gemini-3.5-flash');

        if (empty($apiKey)) {
            Log::warning("AiService: No s'ha configurat la clau GEMINI_API_KEY.");
            return null;
        }

        $modelsToTry = array_unique([
            $primaryModel,
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
            'gemini-3.7-flash',
            'gemini-3.6-flash'
        ]);

        foreach ($modelsToTry as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 3000,
                ]
            ];

            $systemText = $systemInstruction ?? "Ets un redactor de resultats d'hoquei patins. Retorna únicament el text final de la crònica en català, mai notes internes.";
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemText]
                ]
            ];

            try {
                $response = Http::timeout(25)->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $parts = $data['candidates'][0]['content']['parts'] ?? [];
                    $text = null;

                    // Busquem la part que és el resultat final (no thought)
                    foreach ($parts as $part) {
                        if (empty($part['thought']) && !empty($part['text'])) {
                            $text = $part['text'];
                            break;
                        }
                    }

                    // Fallback a l'última part
                    if (!$text && !empty($parts)) {
                        $lastPart = end($parts);
                        $text = $lastPart['text'] ?? null;
                    }

                    if (!empty($text)) {
                        $cleaned = trim($text);
                        $cleaned = preg_replace('/^[^\w\*#\{\[]+/u', '', $cleaned);
                        return $cleaned;
                    }
                }

                Log::warning("Gemini model {$model} no disponible ({$response->status()}), provant següent...");
            } catch (\Exception $ex) {
                Log::warning("Gemini error amb {$model}: " . $ex->getMessage());
            }
        }

        return null;
    }

    /**
     * Crida a Groq Cloud
     */
    protected function callGroq(string $prompt, ?string $systemInstruction = null): ?string
    {
        $apiKey = config('services.ai.groq.api_key');
        $model = config('services.ai.groq.model', 'deepseek-r1-distill-llama-70b');

        if (empty($apiKey)) {
            return null;
        }

        $messages = [];
        if (!empty($systemInstruction)) {
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = Http::timeout(30)
            ->withToken($apiKey)
            ->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2,
                'max_tokens' => 800,
            ]);

        if ($response->successful()) {
            return trim($response->json()['choices'][0]['message']['content'] ?? '');
        }

        Log::error("Groq API Error: " . $response->status() . " - " . $response->body());
        return null;
    }

    /**
     * Crida a DeepSeek Directe
     */
    protected function callDeepSeek(string $prompt, ?string $systemInstruction = null): ?string
    {
        $apiKey = config('services.ai.deepseek.api_key');
        $model = config('services.ai.deepseek.model', 'deepseek-chat');

        if (empty($apiKey)) {
            return null;
        }

        $messages = [];
        if (!empty($systemInstruction)) {
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = Http::timeout(30)
            ->withToken($apiKey)
            ->post("https://api.deepseek.com/chat/completions", [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2,
                'max_tokens' => 800,
            ]);

        if ($response->successful()) {
            return trim($response->json()['choices'][0]['message']['content'] ?? '');
        }

        Log::error("DeepSeek API Error: " . $response->status() . " - " . $response->body());
        return null;
    }

    /**
     * Crida a OpenRouter
     */
    protected function callOpenRouter(string $prompt, ?string $systemInstruction = null): ?string
    {
        $apiKey = config('services.ai.openrouter.api_key');
        $model = config('services.ai.openrouter.model', 'deepseek/deepseek-r1:free');

        if (empty($apiKey)) {
            return null;
        }

        $messages = [];
        if (!empty($systemInstruction)) {
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = Http::timeout(30)
            ->withToken($apiKey)
            ->post("https://openrouter.ai/api/v1/chat/completions", [
                'model' => $model,
                'messages' => $messages,
            ]);

        if ($response->successful()) {
            return trim($response->json()['choices'][0]['message']['content'] ?? '');
        }

        Log::error("OpenRouter API Error: " . $response->status() . " - " . $response->body());
        return null;
    }

    /**
     * Identifica el partit (idMatch) d'un vídeo de YouTube a partir del títol, descripció i miniatura.
     */
    public function matchVideoToMatch($video): ?int
    {
        $title = $video->title ?? '';
        $description = $video->description ?? '';
        $publishedAt = $video->published_at ? \Carbon\Carbon::parse($video->published_at) : null;
        $thumbnailUrl = $video->thumbnail_url ?? '';

        $channelName = '';
        if (isset($video->channel) && $video->channel) {
            $channelName = $video->channel->name ?? '';
        } elseif (!empty($video->video_channel_id)) {
            $channelName = \App\Models\VideoChannel::where('id', $video->video_channel_id)->value('name') ?? '';
        }

        $channelInfo = !empty($channelName) ? "CANAL DE YOUTUBE: {$channelName}\n" : "";

        // 1. Extracció de dades del títol i descripció
        $prompt = "Analitza el següent títol i descripció d'un vídeo de YouTube d'un partit d'hoquei patins:\n\n" .
            $channelInfo .
            "TÍTOL: {$title}\n" .
            "DESCRIPCIÓ: {$description}\n" .
            ($publishedAt ? "DATA PUBLICACIÓ: {$publishedAt->format('Y-m-d')}\n" : "") .
            (!empty($channelName) ? "NOTA: Si al títol només s'esmenta una categoria i un sol equip rival (ex: 'FEM 13 2_5 CH CALDES' o 'Júnior vs Vic'), tingues en compte que el canal pertany al club del canal ('{$channelName}'), de manera que l'altre equip és aquest mateix club.\n\n" : "\n") .
            "Extreu les dades en JSON pur (sense markdown) amb els camps:\n" .
            "- local (nom equip local)\n" .
            "- visitor (nom equip visitant)\n" .
            "- localResult (int o null)\n" .
            "- visitorResult (int o null)\n" .
            "- round (int o null)\n" .
            "- group (string o null, ex: FEM 13, Benjamí, etc.)\n" .
            "- date (string format YYYY-MM-DD o MM-DD o null)";

        $json = $this->generateText($prompt, "Retorna ÚNICAMENT un objecte JSON sense comentaris ni markdown.");
        $extracted = $this->parseJsonSafe($json);

        // 2. Si no hi ha categoria/grup ni resultat i tenim thumbnail, fem servir visió multimodal
        if ((empty($extracted['group']) && empty($extracted['localResult'])) && !empty($thumbnailUrl)) {
            $visionData = $this->analyzeThumbnailWithVision($thumbnailUrl);
            if (!empty($visionData)) {
                $extracted = array_merge($extracted ?? [], array_filter($visionData));
            }
        }

        // 3. Fallback d'equip utilitzant el canal si només s'ha detectat un equip
        if (!empty($channelName)) {
            if (empty($extracted['local']) && !empty($extracted['visitor'])) {
                $extracted['local'] = $channelName;
            } elseif (!empty($extracted['local']) && empty($extracted['visitor'])) {
                $extracted['visitor'] = $channelName;
            }
        }

        if (empty($extracted['local']) || empty($extracted['visitor'])) {
            return null;
        }

        return $this->findMatchInDatabase($extracted, $publishedAt);
    }

    /**
     * Analitza una miniatura amb la visió multimodal de Gemini
     */
    public function analyzeThumbnailWithVision(string $thumbnailUrl): ?array
    {
        $apiKey = config('services.ai.gemini.api_key');
        if (empty($apiKey)) return null;

        try {
            $imgData = @file_get_contents($thumbnailUrl);
            if (!$imgData) return null;

            $base64 = base64_encode($imgData);
            $modelsToTry = ['gemini-3.6-flash', 'gemini-3.7-flash', 'gemini-3.5-flash'];

            foreach ($modelsToTry as $m) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$m}:generateContent?key={$apiKey}";
                $payload = [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Llegeix el text visible a la miniatura d'aquest partit d'hoquei patins. " .
                                              "Extreu en format JSON pur: local (equip local), visitor (equip visitant), group (categoria esportiva com Tercera Catalana, Juvenil, etc.), round (jornada si n'hi ha)."
                                ],
                                [
                                    'inline_data' => [
                                        'mime_type' => 'image/jpeg',
                                        'data' => $base64
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];

                $res = Http::timeout(25)->post($url, $payload);
                if ($res->successful()) {
                    $parts = $res->json()['candidates'][0]['content']['parts'] ?? [];
                    $text = '';
                    foreach ($parts as $p) {
                        if (empty($p['thought'])) {
                            $text .= $p['text'] ?? '';
                        }
                    }
                    return $this->parseJsonSafe($text);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error analitzant thumbnail: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Cerca el partit coincident a la base de dades
     */
    protected function findMatchInDatabase(array $extracted, ?\Carbon\Carbon $publishedAt): ?int
    {
        $local = trim($extracted['local'] ?? '');
        $visitor = trim($extracted['visitor'] ?? '');

        $extractMainClubWord = function($name) {
            $cleaned = preg_replace('/(club|patí|pati|ch|cp|ce|hc|esportiu|hoquei|de|del|d\')/iu', ' ', $name);
            $words = array_values(array_filter(explode(' ', trim($cleaned))));
            return !empty($words) ? $words[0] : $name;
        };

        $localKey = $extractMainClubWord($local);
        $visKey = $extractMainClubWord($visitor);

        $query = \Illuminate\Support\Facades\DB::table('matches')
            ->join('teams as t1', 'matches.idLocal', '=', 't1.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->leftJoin('phases', 'phases.idGroup', '=', 'matches.idGroup')
            ->leftJoin('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->leftJoin('categories', 'leagues.idCategory', '=', 'categories.idCategory')
            ->select('matches.idMatch', 't1.teamName as local', 't2.teamName as visitor', 'localResult', 'visitorResult', 'matchDate', 'matches.idRound', 'phases.groupName', 'categories.categoryName', 'leagues.leagueName');

        // Condició d'equips flexible
        $query->where(function($q) use ($localKey, $visKey) {
            $q->where(function($sub) use ($localKey, $visKey) {
                $sub->where('t1.teamName', 'like', "%{$localKey}%")
                    ->where('t2.teamName', 'like', "%{$visKey}%");
            })->orWhere(function($sub) use ($localKey, $visKey) {
                $sub->where('t1.teamName', 'like', "%{$visKey}%")
                    ->where('t2.teamName', 'like', "%{$localKey}%");
            });
        });

        // Condició de jornada si està disponible
        if (!empty($extracted['round'])) {
            $query->where('matches.idRound', (int)$extracted['round']);
        }

        // Condició de resultat si està disponible
        if (isset($extracted['localResult']) && isset($extracted['visitorResult']) && $extracted['localResult'] !== null && $extracted['visitorResult'] !== null) {
            $lr = (int)$extracted['localResult'];
            $vr = (int)$extracted['visitorResult'];
            $query->where(function($q) use ($lr, $vr) {
                $q->where(function($sub) use ($lr, $vr) {
                    $sub->where('localResult', $lr)->where('visitorResult', $vr);
                })->orWhere(function($sub) use ($lr, $vr) {
                    $sub->where('localResult', $vr)->where('visitorResult', $lr);
                });
            });
        }

        // Condició de categoria si s'ha extret
        if (!empty($extracted['group'])) {
            $groupKey = trim($extracted['group']);
            // Agafem la primera paraula clau de categoria (ex: Juvenil, Benjamí, Tercera...)
            if (preg_match('/(tercera|segona|primera|juvenil|infantil|alevi|aleví|benjami|benjamí|prebenjami|prebenjamí|ok|plata|or)/iu', $groupKey, $mCat)) {
                $cat = $mCat[1];
                $query->where(function($q) use ($cat) {
                    $q->where('phases.groupName', 'like', "%{$cat}%")
                      ->orWhere('categories.categoryName', 'like', "%{$cat}%")
                      ->orWhere('leagues.leagueName', 'like', "%{$cat}%");
                });
            }
        }

        // Condició de data exacta si s'ha extret de la descripció/títol
        if (!empty($extracted['date'])) {
            $parsedMatchDate = null;
            try {
                $rawDate = trim($extracted['date']);
                // Si només té DD/MM o DD-MM i tenim l'any de publicació
                if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})$/', $rawDate, $dm)) {
                    $year = $publishedAt ? $publishedAt->year : date('Y');
                    $parsedMatchDate = sprintf('%04d-%02d-%02d', $year, $dm[2], $dm[1]);
                } elseif (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $rawDate, $ymd)) {
                    $parsedMatchDate = sprintf('%04d-%02d-%02d', $ymd[1], $ymd[2], $ymd[3]);
                } elseif (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $rawDate, $dmy)) {
                    $parsedMatchDate = sprintf('%04d-%02d-%02d', $dmy[3], $dmy[2], $dmy[1]);
                }
            } catch (\Exception $e) {}

            if ($parsedMatchDate) {
                $query->where('matches.matchDate', $parsedMatchDate);
            }
        } elseif ($publishedAt) {
            // Si no hi ha data de partit especificada, busquem en un marge de +/- 15 dies respecte a la publicació
            $startDate = $publishedAt->copy()->subDays(15)->format('Y-m-d');
            $endDate = $publishedAt->copy()->addDays(5)->format('Y-m-d');
            $query->whereBetween('matches.matchDate', [$startDate, $endDate]);
        }

        $candidates = $query->orderBy('matches.matchDate', 'desc')->get();

        if ($candidates->count() === 1) {
            return $candidates->first()->idMatch;
        }

        // Si hi ha múltiples candidats i tenim data exacta
        if ($candidates->count() > 1 && $publishedAt) {
            // Busquem el més proper en data anterior a la publicació
            foreach ($candidates as $c) {
                if ($c->matchDate <= $publishedAt->format('Y-m-d')) {
                    return $c->idMatch;
                }
            }
            return $candidates->first()->idMatch;
        }

        return null;
    }

    /**
     * Neteja i parseja JSON de forma segura
     */
    protected function parseJsonSafe(?string $text): ?array
    {
        if (empty($text)) return null;

        $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $clean = preg_replace('/\s*```$/i', '', $clean);
        $clean = preg_replace('/^[^\w\*#\{\[]+/u', '', $clean);

        $jsonStart = strpos($clean, '{');
        $jsonEnd = strrpos($clean, '}');
        if ($jsonStart !== false && $jsonEnd !== false) {
            $clean = substr($clean, $jsonStart, $jsonEnd - $jsonStart + 1);
        }

        $data = json_decode($clean, true);
        return is_array($data) ? $data : null;
    }
}
