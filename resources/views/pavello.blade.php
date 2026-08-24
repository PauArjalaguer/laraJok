@extends('layout.mainlayout')
@section('title', ($pavello->placeName ?? 'Pavelló') . " :: JOK.cat ")
@section('content')

@php
    $hasMap = (!empty($pavello->lat) && !empty($pavello->lon));
    $mapUrl = $hasMap ? "https://maps.google.com/?q={$pavello->lat},{$pavello->lon}" : ($pavello->placeAddress ? "https://maps.google.com/?q=".urlencode($pavello->placeAddress) : null);
    $wazeUrl = $hasMap ? "https://waze.com/ul?ll={$pavello->lat},{$pavello->lon}&navigate=yes" : null;
    $parkingSearchUrl = $hasMap ? "https://www.google.com/maps/search/parking/@{$pavello->lat},{$pavello->lon},16z" : ($pavello->placeAddress ? "https://www.google.com/maps/search/parking+".urlencode($pavello->placeAddress) : null);
    $barSearchUrl = $hasMap ? "https://www.google.com/maps/search/cafeteria/@{$pavello->lat},{$pavello->lon},16z" : ($pavello->placeAddress ? "https://www.google.com/maps/search/cafeteria+".urlencode($pavello->placeAddress) : null);
@endphp

<!-- BACK BUTTON -->
<div class="mb-5">
    <a href="/pavellons" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black bg-stone-100 dark:bg-stone-900 text-stone-800 dark:text-stone-200 hover:bg-primary hover:text-primary-text dark:hover:bg-primary dark:hover:text-primary-text transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs font-display">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Torna a Pavellons
    </a>
</div>

<!-- CARD INFORMACIÓ PAVELLÓ I DESPLAÇAMENT -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-5 md:p-8 shadow-xs font-display mb-8">
    <!-- Header Secció -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-100 dark:border-stone-800 pb-5 mb-6">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-lg font-black shadow-xs">
                <i class="fa-solid fa-map-location-dot"></i>
            </span>
            <div>
                <h1 class="text-lg md:text-2xl font-black text-stone-900 dark:text-white tracking-tight">
                    {{ $pavello->placeName ?? 'Pavelló' }}
                </h1>
                <p class="text-xs font-semibold text-stone-400 dark:text-stone-500">
                    Guia pràctica de la instal·lació i serveis per a visitants
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
                    <h2 class="text-base md:text-lg font-black text-stone-900 dark:text-white mt-0.5">
                        {{ $pavello->placeName ?? 'Pavelló Municipal' }}
                    </h2>
                    @if(!empty($pavello->placeAddress))
                        <p class="text-xs md:text-sm font-semibold text-stone-600 dark:text-stone-300 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-primary flex-shrink-0"></i>
                            <span>{{ $pavello->placeAddress }}</span>
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
                        <span class="px-2 py-0.5 rounded-full bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-300 font-bold border border-stone-200/80 dark:border-stone-700/80 text-[9px] uppercase tracking-wider">Avui</span>
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
                    No disponible per a aquesta ubicació
                </div>
            </div>
        @endif
    </div>

    <!-- Guia Pràctica d'Aficionat Visitant (Gemini) -->
    <div class="border-t border-stone-100 dark:border-stone-800 pt-6 mt-6">
        <h4 class="text-sm font-black uppercase tracking-wider text-stone-900 dark:text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-compass text-primary"></i> Guia d'Aficionat Visitant
        </h4>

        @if(!empty($pavello->guide_info))
            <div class="prose prose-stone dark:prose-invert max-w-none text-stone-700 dark:text-stone-300 text-sm md:text-base leading-relaxed font-sans [&>p]:mb-3 [&>h3]:text-sm [&>h3]:font-black [&>h3]:text-stone-900 dark:[&>h3]:text-white [&>h3]:mt-5 [&>h3]:mb-2 [&_table]:w-full [&_table]:text-left [&_table]:border-collapse [&_table]:my-3 [&_table]:text-xs md:[&_table]:text-sm [&_th]:border-b-2 [&_th]:border-stone-200 dark:[&_th]:border-stone-700 [&_th]:p-2.5 [&_th]:font-bold [&_th]:text-stone-900 dark:[&_th]:text-white [&_td]:border-b [&_td]:border-stone-100 dark:[&_td]:border-stone-800/80 [&_td]:p-2.5 [&_ul]:list-disc [&_ul]:pl-5 [&_li]:mb-1">
                {!! Illuminate\Support\Str::markdown($pavello->guide_info) !!}
            </div>
        @elseif(!empty($pavello->idPlace))
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
                    const placeId = "{{ $pavello->idPlace }}";
                    if (!placeId) return;

                    fetch('/pavello/' + placeId + '/generar-guia')
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

<!-- MATCHES SECTION HEADER -->
<div class="flex items-center justify-between mb-4 font-display">
    <div class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-850 text-stone-700 dark:text-stone-300 flex items-center justify-center text-sm font-bold border border-stone-200/70 dark:border-stone-800 shadow-xs">
            <i class="fa-regular fa-calendar-days"></i>
        </span>
        <h2 class="text-base md:text-lg font-black text-stone-900 dark:text-white tracking-tight">
            Propers Partits Programats
        </h2>
    </div>
    @if(count($partits_pavello) > 0)
        <span class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-400 border border-stone-200 dark:border-stone-700 text-xs font-bold">
            {{ count($partits_pavello) }} {{ count($partits_pavello) == 1 ? 'partit' : 'partits' }}
        </span>
    @endif
</div>

<!-- MATCHES BY DATE LIST -->
@if(count($partits_pavello) > 0)
    @php $dia = ''; @endphp
    <div class="space-y-4">
        @foreach($partits_pavello as $key => $match)
            @if($dia != $match->matchDate)
                <div class="flex items-center gap-2 pt-3 border-b border-stone-200 dark:border-stone-800 pb-2 mb-3">
                    <i class="fa-regular fa-calendar text-stone-900 dark:text-white"></i>
                    <h3 class="font-display font-black text-xs md:text-sm uppercase tracking-wider text-stone-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($match->matchDate)->locale('ca')->isoFormat('LL') }}
                    </h3>
                </div>
            @endif
            <x-matches-component :match="$match" />
            @php $dia = $match->matchDate; @endphp
        @endforeach
    </div>
@else
    <div class="font-display text-xs md:text-sm text-stone-500 dark:text-stone-400 text-center py-12 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl shadow-xs">
        <i class="fa-solid fa-calendar-xmark text-3xl text-stone-300 dark:text-stone-700 mb-2 block"></i>
        No hi ha partits programats en aquest pavelló pròximament.
    </div>
@endif

@endsection
