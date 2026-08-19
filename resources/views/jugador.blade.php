@extends('layout.mainlayout')
@section('title', $playerInfo[0]->playerName." :: JOK.cat ")

@section('content')

@php
    $seasons = $playerMatchesList->pluck('seasonName')->unique()->sortByDesc(function($s) { return $s; })->values();
    $firstSeason = $seasons->first() ?? '';
@endphp

<!-- PLAYER HEADER (Clean Unified Style) -->
<div class="w-full mt-2 mb-6">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white font-display tracking-tight capitalize">
                {{ mb_strtolower($playerInfo[0]->playerName) }}
            </h1>
            @if(isset($playerInfo[0]->number))
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary text-primary-text dark:bg-primary text-black dark:bg-stone-800 dark:text-white dark:text-black font-black text-xs shadow-xs">
                    {{ $playerInfo[0]->number }}
                </span>
            @endif
        </div>
        <div class="text-right">
            <a href="/desa/jugador/{{$playerInfo[0]->idPlayer}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $checkIfSaved==1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 cursor-pointer transition-colors hover:text-red-700 {{ $checkIfSaved==1 ? 'text-red-700' : 'text-stone-400' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>
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
                        :class="activeSeason === '{{ $season }}' ? 'bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black shadow-xs' : 'bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 font-bold hover:bg-stone-200 dark:hover:bg-stone-800 border border-stone-200/80 dark:border-stone-800'"
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

    <!-- RIGHT SIDEBAR (Estadístiques del Jugador per Temporada - Apple Sports Table) -->
    <div class="col-span-1 lg:col-span-4">

        @if(count($playerStats) > 0)
            <div class="w-full mb-6 font-display">
                <div class="flex items-center justify-between pb-1.5 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h3 class="text-xs font-black uppercase tracking-wider text-stone-900 dark:text-white">
                        ESTADÍSTIQUES PER TEMPORADA
                    </h3>
                </div>

                <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-display text-xs">
                            <thead class="bg-primary text-primary-text dark:bg-black text-[10px] uppercase font-black tracking-wider">
                                <tr>
                                    <th class="py-2.5 px-3">Temporada</th>
                                    <th class="py-2.5 px-2 text-center" title="Partits Jugats">PJ</th>
                                    <th class="py-2.5 px-2 text-center text-stone-900 dark:text-white" title="Gols Marcats">Gols</th>
                                    <th class="py-2.5 px-2 text-center text-blue-400" title="Targetes Blaves">TB</th>
                                    <th class="py-2.5 px-2 text-center text-red-400" title="Targetes Vermelles">TV</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 dark:divide-stone-800/80">
                                @foreach(collect($playerStats)->sortByDesc('seasonName') as $stats)
                                    <tr class="hover:bg-stone-50 dark:hover:bg-primary/50 transition-colors">
                                        <td class="py-3 px-3 font-bold text-stone-900 dark:text-stone-100">
                                            <span class="inline-block bg-stone-100 dark:bg-stone-900 px-2 py-0.5 rounded-full text-[10px] font-black border border-stone-200/80 dark:border-stone-800">
                                                {{ $stats->seasonName }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 text-center font-bold text-stone-700 dark:text-stone-300">
                                            {{ $stats->match_count }}
                                        </td>
                                        <td class="py-3 px-2 text-center font-black">
                                            @if($stats->total_goals > 0)
                                                <span class="inline-flex items-center gap-1.5 bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 px-2.5 py-0.5 rounded-full text-[11px] font-black shadow-xs">
                                                    {{ $stats->total_goals }}
                                                    <svg class="w-3.5 h-3.5 inline-block drop-shadow-xs flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" title="Bola d'Hoquei">
                                                        <defs>
                                                            <radialGradient id="hockeyBallGrad_{{$stats->seasonName}}" cx="35%" cy="35%" r="65%">
                                                                <stop offset="0%" stop-color="#a1a1aa" />
                                                                <stop offset="35%" stop-color="#3f3f46" />
                                                                <stop offset="85%" stop-color="#09090b" />
                                                                <stop offset="100%" stop-color="#000000" />
                                                            </radialGradient>
                                                        </defs>
                                                        <circle cx="12" cy="12" r="10" fill="url(#hockeyBallGrad_{{$stats->seasonName}})" stroke="#18181b" stroke-width="1"/>
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="text-stone-400 font-bold">0</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-center font-bold text-stone-600 dark:text-stone-400">
                                            {{ $stats->total_blue > 0 ? $stats->total_blue : '0' }}
                                        </td>
                                        <td class="py-3 px-2 text-center font-bold text-stone-600 dark:text-stone-400">
                                            {{ $stats->total_red > 0 ? $stats->total_red : '0' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>

</div>

@endsection
