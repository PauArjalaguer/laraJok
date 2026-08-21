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

        // Comprovem si és una categoria formativa (Benjamí / Prebenjamí / Minibenjamí / Escola)
        $searchStrings = [
            $header->categoryName ?? '',
            $header->leagueName ?? '',
            $header->groupName ?? '',
            $header->teamName ?? '',
            $header->teamName2 ?? ''
        ];
        $combinedText = mb_strtolower(implode(' ', $searchStrings), 'UTF-8');
        $isFormativeCategory = (bool)preg_match('/(benjam|prebenjam|minibenjam|escola|iniciaci)/iu', $combinedText);

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
            $prompt .= "- Tipus de categoria: Categoria formativa (Benjamí / Prebenjamí). NO s'han d'esmentar golejadors individuals, ja que a l'acta s'assignen tots a un únic jugador.\n";
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
                        $cleaned = preg_replace('/^[^\w\*#]+/u', '', $cleaned);
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
}
