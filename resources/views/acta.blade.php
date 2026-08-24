@if (!isset($matchGetInfoById[0]))
    @php abort(404); @endphp
@endif
@extends('layout.mainlayout')
@section('title', ($matchGetInfoById[0]->teamName ?? ($matchGetInfoById[0]->localTeam ?? 'Local')) . " - " . ($matchGetInfoById[0]->teamName2 ?? ($matchGetInfoById[0]->visitorTeam ?? 'Visitant')) . " :: JOK.cat ")
@section('content')

@php
    $match = $matchGetInfoById[0];
    $localName = $match->teamName ?? ($match->localTeam ?? 'Local');
    $visitorName = $match->teamName2 ?? ($match->visitorTeam ?? 'Visitant');
    $isPlayed = ($match->localResult !== null && $match->localResult !== '');
    $hasMap = (!empty($match->lat) && !empty($match->lon));
    $mapUrl = $hasMap ? "https://maps.google.com/?q={$match->lat},{$match->lon}" : ($match->placeAddress ? "https://maps.google.com/?q=".urlencode($match->placeAddress) : null);
    $wazeUrl = $hasMap ? "https://waze.com/ul?ll={$match->lat},{$match->lon}&navigate=yes" : null;
    $parkingSearchUrl = $hasMap ? "https://www.google.com/maps/search/parking/@{$match->lat},{$match->lon},16z" : ($match->placeAddress ? "https://www.google.com/maps/search/parking+".urlencode($match->placeAddress) : null);
    $barSearchUrl = $hasMap ? "https://www.google.com/maps/search/cafeteria/@{$match->lat},{$match->lon},16z" : ($match->placeAddress ? "https://www.google.com/maps/search/cafeteria+".urlencode($match->placeAddress) : null);
@endphp

<!-- Header Bar -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4 gap-2">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                @if(!empty($match->idRound))
                    <a href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}/{{urlencode($match->idRound)}}" class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 border border-stone-200 dark:border-stone-700 hover:bg-primary hover:text-primary-text dark:hover:bg-primary dark:hover:text-primary-text transition-all">
                        Jornada {{$match->idRound}}
                    </a>
                @endif
                @if(!$isPlayed)
                    <span class="hallmark-stamp bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30 text-[10px] uppercase font-black tracking-wider">
                        Partit Pendent
                    </span>
                @endif
            </div>
            <h1 class="text-xl md:text-2xl font-black text-stone-900 dark:text-white tracking-tight">
                <a class="hover:text-stone-900 dark:hover:text-white transition-colors" href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}">
                    {{$match->groupName}}
                </a>
            </h1>
        </div>
        <div class="text-xs md:text-sm font-extrabold text-stone-500 dark:text-stone-400 flex items-center gap-2">
            <i class="fa-regular fa-clock text-stone-900 dark:text-white"></i>
            <span>{{ \Carbon\Carbon::parse($match->matchDate)->format('d/m/Y')}} — {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i')}}</span>
        </div>
    </div>
</div>

<!-- Main Scoreboard Banner -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-4 md:p-8 shadow-xs font-display mb-6">
    <div class="flex items-center justify-between gap-2 md:gap-6">
        <!-- Local Team -->
        <div class="w-[40%] flex flex-col md:flex-row items-center justify-end text-right gap-3">
            <a href="/equip/{{$match->idLocal}}/{{urlencode($localName)}}" class="order-2 md:order-1 hover:text-stone-900 dark:hover:text-white transition-colors">
                <h2 class="text-sm md:text-2xl font-black text-stone-900 dark:text-white leading-tight tracking-tight">
                    {{$localName}}
                </h2>
            </a>
            <a href="/equip/{{$match->idLocal}}/{{urlencode($localName)}}" class="order-1 md:order-2 flex-shrink-0">
                <div class="w-14 h-14 md:w-24 md:h-24 bg-white dark:bg-transparent rounded-2xl p-1.5 flex items-center justify-center border border-stone-200 dark:border-stone-700/80 shadow-xs">
                    <img class="max-w-full max-h-full object-contain" src="{{$match->clubImage1}}" alt="{{$localName}}" />
                </div>
            </a>
        </div>

        <!-- Score Box & Status -->
        <div class="flex flex-col items-center justify-center flex-shrink-0">
            <div class="bg-stone-900 dark:bg-black border border-stone-800 rounded-full px-4 py-2.5 md:px-7 md:py-4 shadow-inner flex items-center gap-2 md:gap-4">
                @if($isPlayed)
                    <span class="text-2xl md:text-5xl font-black text-white">
                        {{$match->localResult}}
                    </span>
                    <span class="text-stone-500 text-lg md:text-3xl font-light">-</span>
                    <span class="text-2xl md:text-5xl font-black text-white">
                        {{$match->visitorResult}}
                    </span>
                @else
                    <span class="text-lg md:text-2xl font-black text-stone-300 uppercase tracking-widest px-2">
                        VS
                    </span>
                @endif
            </div>
            @if($isPlayed)
                <div class="mt-2 text-[10px] md:text-xs font-black text-stone-500 dark:text-stone-400 uppercase tracking-wider">
                    Faltes: <span class="text-stone-800 dark:text-stone-200">{{$match->localFaults}}</span> - <span class="text-stone-800 dark:text-stone-200">{{$match->visitorFaults}}</span>
                </div>
            @else
                <div class="mt-2 text-[11px] font-extrabold text-stone-500 dark:text-stone-400">
                    {{ \Carbon\Carbon::parse($match->matchDate)->format('d/m/Y') }} a les {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}
                </div>
            @endif
        </div>

        <!-- Visitor Team -->
        <div class="w-[40%] flex flex-col md:flex-row items-center justify-start text-left gap-3">
            <a href="/equip/{{$match->idVisitor}}/{{urlencode($visitorName)}}" class="flex-shrink-0">
                <div class="w-14 h-14 md:w-24 md:h-24 bg-white dark:bg-transparent rounded-2xl p-1.5 flex items-center justify-center border border-stone-200 dark:border-stone-700/80 shadow-xs">
                    <img class="max-w-full max-h-full object-contain" src="{{$match->clubImage2}}" alt="{{$visitorName}}" />
                </div>
            </a>
            <a href="/equip/{{$match->idVisitor}}/{{urlencode($visitorName)}}" class="hover:text-stone-900 dark:hover:text-white transition-colors">
                <h2 class="text-sm md:text-2xl font-black text-stone-900 dark:text-white leading-tight tracking-tight">
                    {{$visitorName}}
                </h2>
            </a>
        </div>
    </div>

    <!-- Referee Info Bar (només si està jugat i n'hi ha) -->
    @if($isPlayed && isset($match->referee) && !empty(trim($match->referee)))
        @php
            $refereeRaw = trim($match->referee);
            $refereesList = [];

            if (strpos($refereeRaw, "\n") !== false) {
                $lines = explode("\n", $refereeRaw);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) { $refereesList[] = $line; }
                }
            } elseif (strpos($refereeRaw, '/') !== false || strpos($refereeRaw, ';') !== false) {
                $parts = preg_split('/[\/;]/', $refereeRaw);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (!empty($p)) { $refereesList[] = $p; }
                }
            } elseif (substr_count($refereeRaw, ',') >= 2) {
                preg_match_all('/([A-Za-zÀ-ÿ\s\'-]+,\s*[A-Za-zÀ-ÿ\s\'-]+)/u', $refereeRaw, $matches);
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $matchName) {
                        $m = trim($matchName);
                        if (!empty($m)) { $refereesList[] = $m; }
                    }
                }
            }

            if (empty($refereesList)) {
                $parts = preg_split('/(\s+-\s+|\s+i\s+|\s+y\s+|\s+and\s+)/i', $refereeRaw);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (!empty($p)) { $refereesList[] = $p; }
                }
            }

            if (empty($refereesList)) {
                $refereesList = [$refereeRaw];
            }
        @endphp
        <div class="mt-6 pt-4 border-t border-stone-100 dark:border-stone-800 flex flex-wrap items-center justify-center gap-2 text-xs md:text-sm text-stone-500 dark:text-stone-400">
            <i class="fa-solid fa-user-shield text-stone-900 dark:text-white"></i>
            <span class="font-extrabold text-stone-700 dark:text-stone-300">
                {{ count($refereesList) > 1 ? 'Àrbitres:' : 'Àrbitre:' }}
            </span>
            <div class="flex items-center gap-1.5 flex-wrap">
                @foreach($refereesList as $index => $ref)
                    <a href="/arbitre/{{ urlencode($ref) }}" class="font-black text-stone-900 dark:text-stone-100 hover:text-stone-900 dark:hover:text-white transition-colors underline decoration-stone-300 dark:decoration-stone-700 underline-offset-4 capitalize">
                        {{ App\Http\Controllers\TeamsController::teamFormat($ref) }}
                    </a>
                    @if($index < count($refereesList) - 1)
                        <span class="text-stone-400 font-bold">i</span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- ========================================================================= --}}
{{-- 1. CAS: EL PARTIT ENCARA NO S'HA JUGAT -> FITXA D'INFORMACIÓ, METEO I GUIA --}}
{{-- ========================================================================= --}}
@if(!$isPlayed)
    <!-- Card Guia d'Aficionat, Ubicació i Temps -->
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-5 md:p-8 shadow-xs font-display mb-6">
        
        <!-- Header Secció -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-100 dark:border-stone-800 pb-5 mb-6">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-lg font-black shadow-xs">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <div>
                    <h3 class="text-lg md:text-xl font-black text-stone-900 dark:text-white tracking-tight">
                        Informació del Pavelló i Desplaçament
                    </h3>
                    <p class="text-xs font-semibold text-stone-400 dark:text-stone-500">
                        Guia pràctica per a jugadors, famílies i aficionats visitants
                    </p>
                </div>
            </div>

            <!-- Botons ràpids de navegació GPS i Serveis Reals -->
            <div class="flex items-center gap-1.5 flex-wrap">
                @if($mapUrl)
                    <a href="{{ $mapUrl }}" target="_blank" title="Obrir ubicació a Google Maps" class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-800 dark:text-stone-200 border border-stone-200 dark:border-stone-700 text-xs font-bold flex items-center gap-1.5 py-1.5 px-3 hover:bg-primary hover:text-primary-text transition-all shadow-xs">
                        <i class="fa-solid fa-location-arrow text-primary"></i> Com Arribar
                    </a>
                @endif
                @if($wazeUrl)
                    <a href="{{ $wazeUrl }}" target="_blank" title="Navegar amb Waze" class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-800 dark:text-stone-200 border border-stone-200 dark:border-stone-700 text-xs font-bold flex items-center gap-1.5 py-1.5 px-3 hover:bg-sky-500 hover:text-white transition-all shadow-xs">
                        <i class="fa-brands fa-waze text-sky-500 hover:text-white"></i> Waze
                    </a>
                @endif
                @if($parkingSearchUrl)
                    <a href="{{ $parkingSearchUrl }}" target="_blank" title="Veure aparcaments a Google Maps" class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-800 dark:text-stone-200 border border-stone-200 dark:border-stone-700 text-xs font-bold flex items-center gap-1.5 py-1.5 px-3 hover:bg-emerald-600 hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-square-parking text-emerald-500 hover:text-white"></i> Pàrquings
                    </a>
                @endif
                @if($barSearchUrl)
                    <a href="{{ $barSearchUrl }}" target="_blank" title="Veure cafeteries a Google Maps" class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-800 dark:text-stone-200 border border-stone-200 dark:border-stone-700 text-xs font-bold flex items-center gap-1.5 py-1.5 px-3 hover:bg-amber-600 hover:text-white transition-all shadow-xs">
                        <i class="fa-solid fa-mug-hot text-amber-500 hover:text-white"></i> Cafeteries
                    </a>
                @endif
            </div>
        </div>

        <!-- Dades Pavelló i Previsió Meteo (Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pavelló i Adreça -->
            <div class="md:col-span-2 bg-stone-50 dark:bg-stone-850/60 border border-stone-200/70 dark:border-stone-800 rounded-2xl p-4 md:p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-white dark:bg-stone-800 flex items-center justify-center text-stone-600 dark:text-stone-300 shadow-xs flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-black tracking-wider text-stone-400">Instal·lació Esportiva</div>
                        <h4 class="text-base md:text-lg font-black text-stone-900 dark:text-white mt-0.5">
                            {{ $match->placeName ?? 'Pavelló Municipal' }}
                        </h4>
                        @if(!empty($match->placeAddress))
                            <p class="text-xs md:text-sm font-semibold text-stone-600 dark:text-stone-300 mt-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-primary flex-shrink-0"></i>
                                <span>{{ $match->placeAddress }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Giny del Temps (Meteo) -->
            @if(isset($weatherForecast) && $weatherForecast)
                <div class="bg-stone-50 dark:bg-stone-850/60 border border-stone-200/70 dark:border-stone-800 rounded-2xl p-4 md:p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-[10px] uppercase font-black tracking-wider text-stone-500 dark:text-stone-400 mb-2">
                            <span>Previsió del Temps</span>
                            <span class="px-2 py-0.5 rounded-full bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-300 font-bold border border-stone-200/80 dark:border-stone-700/80 text-[9px] uppercase tracking-wider">2 dies vista</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <i class="{{ $weatherForecast['icon'] }} text-3xl"></i>
                                <div>
                                    <div class="text-2xl font-black text-stone-900 dark:text-white leading-none">
                                        {{ $weatherForecast['temperature'] }}°C
                                    </div>
                                    <div class="text-xs font-extrabold text-stone-600 dark:text-stone-300 mt-1">
                                        {{ $weatherForecast['condition'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-stone-200/80 dark:border-stone-700/80 flex items-center justify-between text-[11px] font-bold text-stone-500 dark:text-stone-400">
                        <span><i class="fa-solid fa-droplet text-blue-500 mr-1"></i> Prob. pluja:</span>
                        <span class="font-black {{ $weatherForecast['is_rainy'] ? 'text-red-500 dark:text-red-400 font-black' : 'text-stone-700 dark:text-stone-200' }}">
                            {{ $weatherForecast['rain_probability'] }}%
                        </span>
                    </div>
                </div>
            @else
                <div class="bg-stone-50 dark:bg-stone-850/60 border border-stone-200/70 dark:border-stone-800 rounded-2xl p-4 md:p-5 flex flex-col items-center justify-center text-center">
                    <i class="fa-solid fa-cloud-sun text-stone-300 dark:text-stone-600 text-2xl mb-1.5"></i>
                    <div class="text-[11px] font-extrabold text-stone-500 dark:text-stone-400">
                        Previsió Meteorològica
                    </div>
                    <div class="text-[10px] text-stone-400 dark:text-stone-500 mt-0.5">
                        Disponible 2 dies abans del partit
                    </div>
                </div>
            @endif
        </div>

        <!-- Guia Pràctica d'Aficionat Visitant (Gemini) -->
        <div class="border-t border-stone-100 dark:border-stone-800 pt-6 mt-6">
            <h4 class="text-sm font-black uppercase tracking-wider text-stone-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-compass text-primary"></i> Guia d'Aficionat Visitant
            </h4>

            @if(!empty($match->guide_info))
                <div class="prose prose-stone dark:prose-invert max-w-none text-stone-700 dark:text-stone-300 text-sm md:text-base leading-relaxed font-sans [&>p]:mb-3 [&>h3]:text-sm [&>h3]:font-black [&>h3]:text-stone-900 dark:[&>h3]:text-white [&>h3]:mt-5 [&>h3]:mb-2 [&_table]:w-full [&_table]:text-left [&_table]:border-collapse [&_table]:my-3 [&_table]:text-xs md:[&_table]:text-sm [&_th]:border-b-2 [&_th]:border-stone-200 dark:[&_th]:border-stone-700 [&_th]:p-2.5 [&_th]:font-bold [&_th]:text-stone-900 dark:[&_th]:text-white [&_td]:border-b [&_td]:border-stone-100 dark:[&_td]:border-stone-800/80 [&_td]:p-2.5 [&_ul]:list-disc [&_ul]:pl-5 [&_li]:mb-1">
                    {!! Illuminate\Support\Str::markdown($match->guide_info) !!}
                </div>
            @elseif(!empty($match->idPlace))
                <!-- Carregador asíncron de la guia de pavelló -->
                <div id="guia-loading" class="flex flex-col items-center justify-center py-6 text-center text-stone-500 dark:text-stone-400">
                    <div class="inline-flex items-center gap-3 bg-stone-50 dark:bg-stone-850 border border-stone-200 dark:border-stone-700/80 rounded-2xl px-5 py-3 shadow-xs">
                        <i class="fa-solid fa-circle-notch fa-spin text-primary text-base"></i>
                        <span class="text-xs md:text-sm font-bold text-stone-700 dark:text-stone-200">
                            Generant guia d'accessos, aparcament i llocs d'interès...
                        </span>
                    </div>
                </div>
                <div id="guia-content" class="hidden prose prose-stone dark:prose-invert max-w-none text-stone-700 dark:text-stone-300 text-sm md:text-base leading-relaxed font-sans [&>p]:mb-3 [&>h3]:text-sm [&>h3]:font-black [&>h3]:text-stone-900 dark:[&>h3]:text-white [&>h3]:mt-5 [&>h3]:mb-2 [&_table]:w-full [&_table]:text-left [&_table]:border-collapse [&_table]:my-3 [&_table]:text-xs md:[&_table]:text-sm [&_th]:border-b-2 [&_th]:border-stone-200 dark:[&_th]:border-stone-700 [&_th]:p-2.5 [&_th]:font-bold [&_th]:text-stone-900 dark:[&_th]:text-white [&_td]:border-b [&_td]:border-stone-100 dark:[&_td]:border-stone-800/80 [&_td]:p-2.5 [&_ul]:list-disc [&_ul]:pl-5 [&_li]:mb-1">
                </div>

                <script>
                    (function() {
                        const placeId = "{{ $match->idPlace }}";
                        const localTeam = "{{ addslashes($localName ?? '') }}";
                        if (!placeId) return;

                        fetch('/pavello/' + placeId + '/generar-guia?localTeam=' + encodeURIComponent(localTeam))
                            .then(res => {
                                if (!res.ok) throw new Error('Error al servidor: ' + res.status);
                                return res.json();
                            })
                            .then(data => {
                                const loadingEl = document.getElementById('guia-loading');
                                const contentEl = document.getElementById('guia-content');
                                if (data && data.success && data.html) {
                                    if (loadingEl) loadingEl.classList.add('hidden');
                                    if (contentEl) {
                                        contentEl.innerHTML = data.html;
                                        contentEl.classList.remove('hidden');
                                    }
                                } else {
                                    if (loadingEl) loadingEl.classList.add('hidden');
                                }
                            })
                            .catch(err => {
                                console.warn("Error carregant la guia:", err);
                                const loadingEl = document.getElementById('guia-loading');
                                if (loadingEl) loadingEl.classList.add('hidden');
                            });
                    })();
                </script>
            @endif
        </div>
    </div>

{{-- ========================================================================= --}}
{{-- 2. CAS: EL PARTIT JA S'HA JUGAT -> ACTA OFICIAL, RESULTATS, JUGADORS I CRÒNICA --}}
{{-- ========================================================================= --}}
@else
    <!-- Match Video (YouTube) -->
    @if(isset($matchVideo) && $matchVideo)
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-5 md:p-8 shadow-xs font-display mb-6">
        <div class="flex items-center justify-between gap-3 border-b border-stone-100 dark:border-stone-800 pb-4 mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-red-600/10 text-red-600 flex items-center justify-center text-sm font-black shadow-xs">
                    <i class="fa-brands fa-youtube"></i>
                </span>
                <div>
                    <h3 class="text-base md:text-lg font-black text-stone-900 dark:text-white tracking-tight">
                        Vídeo del Partit
                    </h3>
                    <p class="text-[11px] font-semibold text-stone-400 dark:text-stone-500">
                        {{ $matchVideo->title }}
                    </p>
                </div>
            </div>
            <a href="{{ $matchVideo->url }}" target="_blank" class="hallmark-stamp bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/60 text-[10px] uppercase font-black tracking-wider flex items-center gap-1.5 py-1 px-2.5 hover:bg-red-600 hover:text-white transition-all">
                <i class="fa-brands fa-youtube"></i> Veure a YouTube
            </a>
        </div>

        <div class="relative w-full aspect-video rounded-2xl overflow-hidden shadow-sm bg-black">
            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $matchVideo->youtube_id }}" title="{{ $matchVideo->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        </div>
    </div>
    @endif

    <!-- Match Table Partial -->
    <div class="w-full mb-6">
        @if($matchGetInfoById->count() > 1 && !empty($match->idPlayer))
            @include('partials.acta_match_table', ['matchData' => $matchGetInfoById])
        @else
            <div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-2xl p-8 text-center text-xs md:text-sm font-display text-stone-500 dark:text-stone-400">
                <i class="fa-regular fa-file-lines text-2xl text-stone-400 mb-2 block"></i>
                Acta de jugadors encara no disponible a Fecapa.
            </div>
        @endif
    </div>

    <!-- Match Chronicle (IA) sota la taula -->
    @php
        $hasActa = ($matchGetInfoById->count() > 1 && !empty($match->idPlayer));
        $hasCronica = !empty(trim($match->cronica ?? ''));
    @endphp

    @if($hasCronica)
        <div id="cronica-card" class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-5 md:p-8 shadow-xs font-display mb-6">
            <div class="flex items-center justify-between gap-3 border-b border-stone-100 dark:border-stone-800 pb-4 mb-5">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-black shadow-xs">
                        <i class="fa-solid fa-newspaper"></i>
                    </span>
                    <div>
                        <h3 class="text-base md:text-lg font-black text-stone-900 dark:text-white tracking-tight">
                            Crònica del Partit
                        </h3>
                        <p class="text-[11px] font-semibold text-stone-400 dark:text-stone-500">
                            Resum generat a partir de les dades oficials de l'acta
                        </p>
                    </div>
                </div>
            </div>

            <div class="prose prose-stone dark:prose-invert max-w-none text-stone-700 dark:text-stone-300 text-sm md:text-base leading-relaxed font-sans [&>p]:mb-4 [&>h2]:text-lg [&>h2]:font-black [&>h2]:mb-2 [&>h3]:text-base [&>h3]:font-black [&>h3]:mb-2 [&>strong]:text-stone-900 dark:[&>strong]:text-white [&_a]:text-primary [&_a]:font-bold [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:text-stone-900 dark:hover:[&_a]:text-white transition-colors">
                {!! Illuminate\Support\Str::markdown($match->cronica) !!}
            </div>
        </div>
    @elseif($hasActa)
        <div id="cronica-card" class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-5 md:p-8 shadow-xs font-display mb-6">
            <div class="flex items-center justify-between gap-3 border-b border-stone-100 dark:border-stone-800 pb-4 mb-5">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-black shadow-xs">
                        <i class="fa-solid fa-newspaper"></i>
                    </span>
                    <div>
                        <h3 class="text-base md:text-lg font-black text-stone-900 dark:text-white tracking-tight">
                            Crònica del Partit
                        </h3>
                        <p class="text-[11px] font-semibold text-stone-400 dark:text-stone-500">
                            Resum generat a partir de les dades oficials de l'acta
                        </p>
                    </div>
                </div>
            </div>

            <!-- Estat de càrrega asíncrona -->
            <div id="cronica-loading" class="flex flex-col items-center justify-center py-6 text-center text-stone-500 dark:text-stone-400">
                <div class="inline-flex items-center gap-3 bg-stone-50 dark:bg-stone-850 border border-stone-200 dark:border-stone-700/80 rounded-2xl px-5 py-3 shadow-xs">
                    <i class="fa-solid fa-circle-notch fa-spin text-primary text-base"></i>
                    <span class="text-xs md:text-sm font-bold text-stone-700 dark:text-stone-200">
                        S'està redactant la crònica del partit...
                    </span>
                </div>
            </div>

            <!-- Contingut injectat un cop generat -->
            <div id="cronica-content" class="hidden prose prose-stone dark:prose-invert max-w-none text-stone-700 dark:text-stone-300 text-sm md:text-base leading-relaxed font-sans [&>p]:mb-4 [&>h2]:text-lg [&>h2]:font-black [&>h2]:mb-2 [&>h3]:text-base [&>h3]:font-black [&>h3]:mb-2 [&>strong]:text-stone-900 dark:[&>strong]:text-white [&_a]:text-primary [&_a]:font-bold [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:text-stone-900 dark:hover:[&_a]:text-white transition-colors">
            </div>
        </div>

        <script>
            (function() {
                function loadCronica() {
                    const matchId = "{{ $match->idMatch }}";
                    if (!matchId) return;

                    fetch('/acta/' + matchId + '/generar-cronica')
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            const loadingEl = document.getElementById('cronica-loading');
                            const contentEl = document.getElementById('cronica-content');
                            const cardEl = document.getElementById('cronica-card');

                            if (data && data.success && data.html) {
                                if (loadingEl) loadingEl.classList.add('hidden');
                                if (contentEl) {
                                    contentEl.innerHTML = data.html;
                                    contentEl.classList.remove('hidden');
                                }
                            } else {
                                if (cardEl) cardEl.remove();
                            }
                        })
                        .catch(err => {
                            console.warn("No s'ha pogut carregar la crònica:", err);
                            const cardEl = document.getElementById('cronica-card');
                            if (cardEl) cardEl.remove();
                        });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', loadCronica);
                } else {
                    loadCronica();
                }
            })();
        </script>
    @endif
@endif
@endsection

