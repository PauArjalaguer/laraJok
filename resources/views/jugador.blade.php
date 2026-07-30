@extends('layout.mainlayout')
@section('title', $playerInfo[0]->playerName." :: JOK.cat ")

@section('content')

@php
    $seasons = $playerMatchesList->pluck('seasonName')->unique()->sortByDesc(function($s) { return $s; })->values();
    $firstSeason = $seasons->first() ?? '';
@endphp

<!-- PLAYER HERO HEADER (Ultra-Clean Apple Sports) -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-5 md:p-6 mb-7 shadow-xs">
    <div class="flex items-center justify-between">
        <div>
            <!-- Badges enganxats -->
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <a href="/desa/jugador/{{$playerInfo[0]->idPlayer}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}" class="hallmark-stamp inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black transition-all text-[10px] font-black uppercase tracking-wider border border-stone-200/80 dark:border-stone-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $checkIfSaved==1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 {{ $checkIfSaved==1 ? 'text-red-600' : 'text-stone-400' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                    <span>{{ $checkIfSaved==1 ? 'Favorit' : 'Favorit' }}</span>
                </a>
            </div>

            <!-- Nom del Jugador -->
            <h1 class="font-['Comfortaa'] font-bold text-2xl md:text-3xl text-stone-900 dark:text-white leading-tight capitalize flex items-center gap-3">
                <span>{{ mb_strtolower($playerInfo[0]->playerName) }}</span>
                @if(isset($playerInfo[0]->number))
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-stone-900 text-white dark:bg-[#d4ff00] dark:text-black font-black text-xs md:text-sm shadow-xs">
                        {{ $playerInfo[0]->number }}
                    </span>
                @endif
            </h1>
        </div>
    </div>
</div>

<!-- MAIN GRID LAYOUT -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-7" x-data="{ activeSeason: '{{ $firstSeason }}' }">

    <!-- LEFT / MAIN CONTENT (Historial de Partits per Temporada) -->
    <div class="col-span-1 lg:col-span-8">

        <!-- Season Selector Pills (Apple Sports Style) -->
        @if($seasons->count() > 0)
            <div class="mb-4 flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                @foreach($seasons as $season)
                    <button @click="activeSeason = '{{ $season }}'"
                        :class="activeSeason === '{{ $season }}' ? 'bg-[#d4ff00] text-black font-black shadow-xs' : 'bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 font-bold hover:bg-stone-200 dark:hover:bg-stone-800 border border-stone-200/80 dark:border-stone-800'"
                        class="px-4 py-1.5 rounded-full text-xs font-display uppercase tracking-wider transition-all flex-shrink-0">
                        {{ $season }}
                    </button>
                @endforeach
            </div>
        @endif

        <!-- Matches per Season -->
        @if(count($playerMatchesList) > 0)
            @foreach($seasons as $season)
                <div x-show="activeSeason === '{{ $season }}'" class="space-y-1 transition-all">
                    <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                        <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                            PARTITS JUGATS ({{ $season }})
                        </h2>
                    </div>
                    @foreach($playerMatchesList as $match)
                        @if($match->seasonName === $season)
                            <x-matches-component :match="$match" />
                        @endif
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="font-display text-xs text-stone-500 dark:text-stone-400 text-center py-10 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl">
                No hi ha partits registrats per a aquest jugador.
            </div>
        @endif

    </div>

    <!-- RIGHT SIDEBAR (Estadístiques per Temporada) -->
    <div class="col-span-1 lg:col-span-4">

        @if(count($playerStats) > 0)
            <div class="w-full mb-6">
                <div class="flex items-center justify-between pb-1.5 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h3 class="font-display text-xs font-black uppercase tracking-wider text-stone-900 dark:text-white">
                        ESTADÍSTIQUES DEL JUGADOR
                    </h3>
                </div>

                <div class="space-y-3.5">
                    @foreach(collect($playerStats)->sortByDesc('seasonName') as $stats)
                        <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-4 shadow-xs">
                            <!-- Season Badge Inside Card -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-display text-xs font-black text-black bg-[#d4ff00] px-3 py-1 rounded-full uppercase shadow-xs">
                                    {{ $stats->seasonName }}
                                </span>
                            </div>

                            <!-- Grid of 4 Stats Boxes -->
                            <div class="grid grid-cols-2 gap-2.5 font-display">
                                <!-- Partits -->
                                <div class="bg-stone-100 dark:bg-stone-900/90 p-3 rounded-xl text-center border border-stone-200/50 dark:border-stone-800/50">
                                    <div class="text-[10px] text-stone-500 dark:text-stone-400 font-extrabold uppercase tracking-wider mb-0.5">Partits</div>
                                    <div class="text-base font-black text-stone-900 dark:text-white">{{ $stats->match_count }}</div>
                                </div>

                                <!-- Gols -->
                                <div class="bg-stone-100 dark:bg-stone-900/90 p-3 rounded-xl text-center border border-stone-200/50 dark:border-stone-800/50">
                                    <div class="text-[10px] text-stone-500 dark:text-stone-400 font-extrabold uppercase tracking-wider mb-0.5">Gols</div>
                                    <div class="text-base font-black text-stone-900 dark:text-white">{{ $stats->total_goals }} ⚽</div>
                                </div>

                                <!-- Targetes Blaves -->
                                <div class="bg-stone-100 dark:bg-stone-900/90 p-3 rounded-xl text-center border border-stone-200/50 dark:border-stone-800/50">
                                    <div class="text-[10px] text-blue-600 dark:text-blue-400 font-extrabold uppercase tracking-wider mb-0.5">Blaves</div>
                                    <div class="text-base font-black text-stone-900 dark:text-white">{{ $stats->total_blue }} 🟦</div>
                                </div>

                                <!-- Targetes Vermelles -->
                                <div class="bg-stone-100 dark:bg-stone-900/90 p-3 rounded-xl text-center border border-stone-200/50 dark:border-stone-800/50">
                                    <div class="text-[10px] text-red-600 dark:text-red-400 font-extrabold uppercase tracking-wider mb-0.5">Vermelles</div>
                                    <div class="text-base font-black text-stone-900 dark:text-white">{{ $stats->total_red }} 🟥</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</div>

@endsection
