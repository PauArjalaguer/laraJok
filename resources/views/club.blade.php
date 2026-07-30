@extends('layout.mainlayout')
@section('title', $clubInfo[0]->clubName." :: JOK.cat ")
@section('content')

<!-- CLUB HEADER (Clean Unified Style) -->
<div class="w-full mt-2 mb-6">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-white dark:bg-transparent rounded-xl p-1 flex-shrink-0 flex items-center justify-center">
                <img class="max-w-full max-h-full object-contain" src="{{$clubInfo[0]->clubImage}}" alt="{{$clubInfo[0]->clubName}}" onerror="this.style.display='none'" />
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white font-display tracking-tight">
                {{$clubInfo[0]->clubName}}
            </h1>
        </div>
        <div class="text-right">
            <a href="/desa/club/{{$clubInfo[0]->idClub}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $checkIfSaved==1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 cursor-pointer transition-colors hover:text-red-700 {{ $checkIfSaved==1 ? 'text-red-700' : 'text-stone-400' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- MAIN GRID LAYOUT -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-7">

    <!-- LEFT / MAIN CONTENT (Classificacions, Propers Partits, Darrers Resultats) -->
    <div class="col-span-1 lg:col-span-8">

        <!-- CLASSIFICACIONS DEL CLUB (Amagat si no hi ha classificacions) -->
        @if(isset($classifications) && count($classifications) > 0)
            <div class="mb-7">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        CLASSIFICACIONS DEL CLUB
                    </h2>
                    <span class="hallmark-stamp bg-stone-900 text-[#d4ff00] dark:bg-[#d4ff00] dark:text-black">LLIGUES</span>
                </div>

                <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-display text-xs">
                            <thead class="bg-stone-900 text-[#d4ff00] dark:bg-black dark:text-[#d4ff00] text-[10px] uppercase font-black tracking-wider">
                                <tr>
                                    <th class="py-3 px-4">Competició</th>
                                    <th class="py-3 px-2 text-center">Pos</th>
                                    <th class="py-3 px-2 text-center">Pts</th>
                                    <th class="py-3 px-2 text-center">G</th>
                                    <th class="py-3 px-2 text-center">E</th>
                                    <th class="py-3 px-2 text-center">P</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 dark:divide-stone-800/80">
                                @foreach($classifications as $classification)
                                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-900/50 transition-colors">
                                        <td class="py-3 px-4 font-bold text-stone-900 dark:text-stone-100 capitalize">
                                            <a href="/competicio/{{$classification->idGroup}}/{{urlencode($classification->groupName)}}" class="hover:text-stone-600 dark:hover:text-[#d4ff00] transition-colors">
                                                {{$classification->groupName}}
                                            </a>
                                        </td>
                                        <td class="py-3 px-2 text-center">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#d4ff00] text-black font-black text-xs">
                                                {{$classification->position}}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 text-center font-black text-stone-900 dark:text-stone-100">
                                            {{$classification->points}}
                                        </td>
                                        <td class="py-3 px-2 text-center text-stone-600 dark:text-stone-400 font-bold">
                                            {{$classification->won}}
                                        </td>
                                        <td class="py-3 px-2 text-center text-stone-600 dark:text-stone-400 font-bold">
                                            {{$classification->draw}}
                                        </td>
                                        <td class="py-3 px-2 text-center text-stone-600 dark:text-stone-400 font-bold">
                                            {{$classification->lost}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- PROPERS PARTITS DEL CLUB -->
        @if(isset($matchesListNext) && count($matchesListNext) > 0)
            <div class="mb-7">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        PROPERS PARTITS
                    </h2>
                </div>
                <div class="flex flex-col gap-1">
                    @foreach($matchesListNext as $match)
                        <x-matches-component :match="$match" type="upcoming" />
                    @endforeach
                </div>
            </div>
        @endif

        <!-- DARRERS RESULTATS DEL CLUB -->
        @if(isset($matchesListLastWithResults) && count($matchesListLastWithResults) > 0)
            <div class="mb-7">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        DARRERS RESULTATS
                    </h2>
                    <span class="hallmark-stamp bg-stone-900 text-[#d4ff00] dark:bg-[#d4ff00] dark:text-black">RESULTATS</span>
                </div>
                <div class="flex flex-col gap-1">
                    @foreach($matchesListLastWithResults as $match)
                        <x-matches-component :match="$match" type="result" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- RIGHT SIDEBAR (Logo, Equips del Club Desplegable - Subtil Gris Suau) -->
    <div class="col-span-1 lg:col-span-4">
        
        <!-- Big Shield Container (Sense fons blanc en dark) -->
        <div class="hidden lg:flex justify-center mb-6">
            <div class="w-36 h-36 bg-white dark:bg-transparent rounded-3xl p-4 flex items-center justify-center">
                <img onerror="this.style.display='none'" class="max-w-full max-h-full object-contain" src="{{$clubInfo[0]->clubImage}}" alt="{{$clubInfo[0]->clubName}}" />
            </div>
        </div>

        <!-- Teams List (Desplegable per Temporada - Estil Suau & Subtil) -->
        <div class="w-full mb-6">
            <div class="flex items-center justify-between pb-1.5 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                <h3 class="font-display text-xs font-black uppercase tracking-wider text-stone-900 dark:text-white">
                    EQUIPS DEL CLUB
                </h3>
                <span class="hallmark-stamp bg-stone-100 text-stone-700 border border-stone-200/80 dark:bg-stone-900 dark:text-stone-300">TEMPORADES</span>
            </div>

            @php
                $groupedTeams = $teamsList->groupBy('seasonName');
            @endphp

            @foreach($groupedTeams as $seasonName => $teams)
                <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="mb-3">
                    <!-- Season Accordion Header (Negre sobre Verd-Lima Apple Sports) -->
                    <button @click="open = !open" class="group w-full flex items-center justify-between py-2.5 px-4 bg-[#d4ff00] text-black rounded-2xl font-display text-xs font-black uppercase tracking-wider hover:bg-[#c6f800] transition-all shadow-xs mb-2">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-black text-xs"></i>
                            <span class="text-black font-black">{{ $seasonName }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Badge de numero d'equips subtil en grisos -->
                            <span class="text-[10px] font-black text-stone-900 bg-stone-900/15 px-2.5 py-0.5 rounded-full">
                                {{ count($teams) }} {{ count($teams) == 1 ? 'equip' : 'equips' }}
                            </span>
                            <i class="fa-solid fa-chevron-down text-black transition-transform duration-200 text-[10px]" :class="{ 'rotate-180': open }"></i>
                        </div>
                    </button>

                    <!-- Collapsible Teams Content -->
                    <div x-show="open" class="space-y-1.5 transition-all">
                        @foreach($teams as $team)
                            <a href="/equip/{{$team->idTeam}}/{{urlencode($team->teamName)}}" class="group flex items-center justify-between bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3 hover:border-stone-400 dark:hover:border-[#d4ff00] transition-all shadow-xs">
                                <span class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-stone-100 group-hover:text-stone-600 dark:group-hover:text-[#d4ff00] transition-colors capitalize">
                                    {{App\Http\Controllers\TeamsController::teamFormat($team->teamName)}}
                                </span>
                                <span class="font-display text-[10px] font-bold text-stone-600 dark:text-stone-400 bg-stone-100 dark:bg-stone-900 px-2.5 py-0.5 rounded-full">
                                    {{$team->categoryName}}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-col gap-2.5 mb-6">
            <a href="/acta_club/{{$clubInfo[0]->idClub}}/actes-setmana" class="group flex items-center justify-center gap-2 py-2.5 px-4 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-xs font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                <i class="fa-solid fa-file-lines text-stone-500 group-hover:text-white dark:text-[#d4ff00] dark:group-hover:text-black transition-colors"></i> Actes de la setmana
            </a>
            <a href="/acta_header/{{$clubInfo[0]->idClub}}" target="_blank" class="group flex items-center justify-center gap-2 py-2.5 px-4 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-xs font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                <i class="fa-solid fa-chart-column text-xs"></i> Generar gràfic resultats
            </a>
        </div>

    </div>

</div>

@endsection
