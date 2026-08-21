@if (!isset($matchGetInfoById[0]))
    @php abort(404); @endphp
@endif
@extends('layout.mainlayout')
@section('title',$matchGetInfoById[0]->teamName." - ".$matchGetInfoById[0]->teamName2." :: JOK.cat ")
@section('content')

<!-- Header Bar -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4 gap-2">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="/competicio/{{$matchGetInfoById[0]->idGroup}}/{{urlencode($matchGetInfoById[0]->groupName)}}/{{urlencode($matchGetInfoById[0]->idRound)}}" class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 border border-stone-200 dark:border-stone-700 hover:bg-primary hover:text-primary-text dark:hover:bg-primary dark:hover:text-primary-text transition-all">
                    Jornada {{$matchGetInfoById[0]->idRound}}
                </a>
            </div>
            <h1 class="text-xl md:text-2xl font-black text-stone-900 dark:text-white tracking-tight">
                <a class="hover:text-stone-900 dark:hover:text-white transition-colors" href="/competicio/{{$matchGetInfoById[0]->idGroup}}/{{urlencode($matchGetInfoById[0]->groupName)}}">
                    {{$matchGetInfoById[0]->groupName}}
                </a>
            </h1>
        </div>
        <div class="text-xs md:text-sm font-extrabold text-stone-500 dark:text-stone-400 flex items-center gap-2">
            <i class="fa-regular fa-clock text-stone-900 dark:text-white"></i>
            <span>{{ \Carbon\Carbon::parse($matchGetInfoById[0]->matchDate)->format('d/m/Y')}} — {{ \Carbon\Carbon::parse($matchGetInfoById[0]->matchHour)->format('H:i')}}</span>
        </div>
    </div>
</div>

<!-- Main Scoreboard Banner -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-4 md:p-8 shadow-xs font-display mb-6">
    <div class="flex items-center justify-between gap-2 md:gap-6">
        <!-- Local Team -->
        <div class="w-[40%] flex flex-col md:flex-row items-center justify-end text-right gap-3">
            <a href="/equip/{{$matchGetInfoById[0]->idLocal}}/{{urlencode($matchGetInfoById[0]->teamName)}}" class="order-2 md:order-1 hover:text-stone-900 dark:hover:text-white transition-colors">
                <h2 class="text-sm md:text-2xl font-black text-stone-900 dark:text-white leading-tight tracking-tight">
                    {{$matchGetInfoById[0]->teamName}}
                </h2>
            </a>
            <a href="/equip/{{$matchGetInfoById[0]->idLocal}}/{{urlencode($matchGetInfoById[0]->teamName)}}" class="order-1 md:order-2 flex-shrink-0">
                <div class="w-14 h-14 md:w-24 md:h-24 bg-white dark:bg-transparent rounded-2xl p-1.5 flex items-center justify-center border border-stone-200 dark:border-stone-700/80 shadow-xs">
                    <img class="max-w-full max-h-full object-contain" src="{{$matchGetInfoById[0]->clubImage1}}" alt="{{$matchGetInfoById[0]->teamName}}" />
                </div>
            </a>
        </div>

        <!-- Score Box & Faults -->
        <div class="flex flex-col items-center justify-center flex-shrink-0">
            <div class="bg-stone-900 dark:bg-black border border-stone-800 rounded-full px-4 py-2.5 md:px-7 md:py-4 shadow-inner flex items-center gap-2 md:gap-4">
                <span class="text-2xl md:text-5xl font-black text-white">
                    {{$matchGetInfoById[0]->localResult}}
                </span>
                <span class="text-stone-500 text-lg md:text-3xl font-light">-</span>
                <span class="text-2xl md:text-5xl font-black text-white">
                    {{$matchGetInfoById[0]->visitorResult}}
                </span>
            </div>
            <div class="mt-2 text-[10px] md:text-xs font-black text-stone-500 dark:text-stone-400 uppercase tracking-wider">
                Faltes: <span class="text-stone-800 dark:text-stone-200">{{$matchGetInfoById[0]->localFaults}}</span> - <span class="text-stone-800 dark:text-stone-200">{{$matchGetInfoById[0]->visitorFaults}}</span>
            </div>
        </div>

        <!-- Visitor Team -->
        <div class="w-[40%] flex flex-col md:flex-row items-center justify-start text-left gap-3">
            <a href="/equip/{{$matchGetInfoById[0]->idVisitor}}/{{urlencode($matchGetInfoById[0]->teamName2)}}" class="flex-shrink-0">
                <div class="w-14 h-14 md:w-24 md:h-24 bg-white dark:bg-transparent rounded-2xl p-1.5 flex items-center justify-center border border-stone-200 dark:border-stone-700/80 shadow-xs">
                    <img class="max-w-full max-h-full object-contain" src="{{$matchGetInfoById[0]->clubImage2}}" alt="{{$matchGetInfoById[0]->teamName2}}" />
                </div>
            </a>
            <a href="/equip/{{$matchGetInfoById[0]->idVisitor}}/{{urlencode($matchGetInfoById[0]->teamName2)}}" class="hover:text-stone-900 dark:hover:text-white transition-colors">
                <h2 class="text-sm md:text-2xl font-black text-stone-900 dark:text-white leading-tight tracking-tight">
                    {{$matchGetInfoById[0]->teamName2}}
                </h2>
            </a>
        </div>
    </div>

    <!-- Referee Info Bar (Detecció avançada per a 1 o múltiples Àrbitres) -->
    @if(isset($matchGetInfoById[0]->referee) && !empty(trim($matchGetInfoById[0]->referee)))
        @php
            $refereeRaw = trim($matchGetInfoById[0]->referee);
            $refereesList = [];

            // 1. Si conté salts de línia \n
            if (strpos($refereeRaw, "\n") !== false) {
                $lines = explode("\n", $refereeRaw);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) { $refereesList[] = $line; }
                }
            }
            // 2. Si conté barres / o punts i coma ;
            elseif (strpos($refereeRaw, '/') !== false || strpos($refereeRaw, ';') !== false) {
                $parts = preg_split('/[\/;]/', $refereeRaw);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (!empty($p)) { $refereesList[] = $p; }
                }
            }
            // 3. Si té més d'una coma (format "Cognoms , Nom")
            elseif (substr_count($refereeRaw, ',') >= 2) {
                preg_match_all('/([A-Za-zÀ-ÿ\s\'-]+,\s*[A-Za-zÀ-ÿ\s\'-]+)/u', $refereeRaw, $matches);
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $matchName) {
                        $m = trim($matchName);
                        if (!empty($m)) { $refereesList[] = $m; }
                    }
                }
            }

            // 4. Fallback per a guiçons - o la paraula " i "
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
    @if($matchGetInfoById->count()>1)
        @include('partials.acta_match_table', ['matchData' => $matchGetInfoById])
    @else
        <div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-2xl p-8 text-center text-xs md:text-sm font-display text-stone-500 dark:text-stone-400">
            <i class="fa-regular fa-file-lines text-2xl text-stone-400 mb-2 block"></i>
            Acta encara no disponible o no existent a Fecapa.
        </div>
    @endif
</div>

<!-- Match Chronicle (IA) sota la taula -->
@php
    $hasActa = ($matchGetInfoById->count() > 1 && !empty($matchGetInfoById[0]->idPlayer));
    $hasCronica = !empty(trim($matchGetInfoById[0]->cronica ?? ''));
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
            {!! Illuminate\Support\Str::markdown($matchGetInfoById[0]->cronica) !!}
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
                const matchId = "{{ $matchGetInfoById[0]->idMatch }}";
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
@endsection

