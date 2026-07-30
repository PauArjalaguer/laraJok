@extends('layout.mainlayout')
@section('title', $teamInfo[0]->teamName." :: JOK.cat ")
@section('content')

<!-- TEAM HERO HEADER (Ultra-Clean Apple Sports) -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-5 md:p-6 mb-7 shadow-xs">
    <div class="flex items-center gap-4">
        <!-- Escut de l'Equip (Sense fons blanc en dark) -->
        <a href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}" class="w-14 h-14 md:w-18 md:h-18 bg-white dark:bg-transparent rounded-2xl p-2 flex-shrink-0 flex items-center justify-center hover:scale-105 transition-transform">
            <img class="max-w-full max-h-full object-contain" src="{{$teamInfo[0]->clubImage}}" alt="{{$teamInfo[0]->clubName}}" onerror="this.style.display='none'" />
        </a>
        
        <!-- Detalls & Badges Enganxats -->
        <div>
            <!-- Fila de Badges enganxats -->
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <a href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}" class="hallmark-stamp bg-stone-100 text-stone-700 border border-stone-200/80 dark:bg-stone-900 dark:text-stone-300 hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors">
                    {{$teamInfo[0]->clubName}}
                </a>

                <a href="/desa/equip/{{$teamInfo[0]->idTeam}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}" class="hallmark-stamp inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black transition-all text-[10px] font-black uppercase tracking-wider border border-stone-200/80 dark:border-stone-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $checkIfSaved==1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 {{ $checkIfSaved==1 ? 'text-red-600' : 'text-stone-400' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                    <span>{{ $checkIfSaved==1 ? 'Favorit' : 'Favorit' }}</span>
                </a>
            </div>

            <!-- Nom de l'Equip -->
            <h1 class="font-['Comfortaa'] font-bold text-xl md:text-3xl text-stone-900 dark:text-white leading-tight">
                {{App\Http\Controllers\TeamsController::teamFormat($teamInfo[0]->teamName)}}
            </h1>
        </div>
    </div>
</div>

<!-- MAIN GRID LAYOUT -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-7">

    <!-- LEFT / MAIN CONTENT (Plantilla & Competicions) -->
    <div class="col-span-1 lg:col-span-8">

        <!-- PLANTILLA DE JUGADORS -->
        @if(count($playersList) > 0)
            <div class="mb-7">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        PLANTILLA D'EQUIP
                    </h2>
                    <span class="hallmark-stamp bg-stone-100 text-stone-700 border border-stone-200/80 dark:bg-stone-900 dark:text-stone-300">
                        {{ count($playersList) }} {{ count($playersList) == 1 ? 'jugador' : 'jugadors' }}
                    </span>
                </div>

                <div class="space-y-1.5">
                    @foreach ($playersList as $player)
                        <div class="group flex items-center justify-between bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3 md:p-3.5 hover:border-stone-400 dark:hover:border-[#d4ff00] transition-all shadow-xs">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-stone-900 text-white dark:bg-[#d4ff00] dark:text-black font-black text-xs mr-3 flex-shrink-0">
                                    {{ $player->number }}
                                </span>
                                <a href="/jugador/{{$player->idPlayer}}/{{urlencode($player->playerName)}}" class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-stone-100 group-hover:text-stone-600 dark:group-hover:text-[#d4ff00] transition-colors capitalize">
                                    {{ mb_strtolower($player->playerName) }}
                                </a>
                            </div>
                            <span class="text-stone-400 dark:text-stone-500 group-hover:text-stone-900 dark:group-hover:text-[#d4ff00] transition-colors">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- COMPETICIONS DE L'EQUIP -->
        @if(count($teamLeaguesList) > 0)
            <div class="mb-7">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        COMPETICIONS EN CURS
                    </h2>
                </div>

                <div class="space-y-1.5">
                    @foreach($teamLeaguesList as $league)
                        <a href="/competicio/{{$league->idGroup}}/{{urlencode($league->groupName)}}" class="group flex items-center justify-between bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3.5 hover:border-stone-400 dark:hover:border-[#d4ff00] transition-all shadow-xs">
                            <span class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-stone-100 group-hover:text-stone-600 dark:group-hover:text-[#d4ff00] transition-colors capitalize">
                                {{ mb_strtolower($league->groupName) }}
                            </span>
                            <span class="font-display text-[10px] font-bold text-stone-500 dark:text-stone-400 bg-stone-100 dark:bg-stone-900 px-3 py-1 rounded-full">
                                {{ \Carbon\Carbon::parse($league->startDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($league->endDate)->format('d/m/Y') }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- RIGHT SIDEBAR (Logo del Club & Golejadors) -->
    <div class="col-span-1 lg:col-span-4">
        
        <!-- Big Shield Container (Sense fons blanc en dark) -->
        <div class="hidden lg:flex justify-center mb-6">
            <a href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}" class="w-36 h-36 bg-white dark:bg-transparent rounded-3xl p-4 flex items-center justify-center hover:scale-105 transition-transform">
                <img onerror="this.style.display='none'" class="max-w-full max-h-full object-contain" src="{{$teamInfo[0]->clubImage}}" alt="{{$teamInfo[0]->clubName}}" />
            </a>
        </div>

        <!-- GOLEJADORS DE L'EQUIP -->
        @if(count($teamGoals) > 0)
            <div class="w-full mb-6">
                <div class="flex items-center justify-between pb-1.5 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h3 class="font-display text-xs font-black uppercase tracking-wider text-stone-900 dark:text-white">
                        MÀXIMS GOLEJADORS
                    </h3>
                </div>

                <div class="space-y-1.5">
                    @foreach ($teamGoals as $goals)
                        @if($goals->goals != 0)
                            <div class="group relative overflow-hidden bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3 md:p-3.5 shadow-xs hover:border-stone-400 dark:hover:border-[#d4ff00] transition-all flex items-center justify-between">
                                <!-- Progress bar overlay background -->
                                <div class="absolute top-0 left-0 h-full bg-stone-100/80 dark:bg-stone-800/40 transition-colors" style="width: {{$goals->percentage}}%;"></div>
                                
                                <div class="relative z-10">
                                    <a href="/jugador/{{$goals->idPlayer}}/{{urlencode($goals->playerName)}}" class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-stone-100 group-hover:text-stone-600 dark:group-hover:text-[#d4ff00] transition-colors capitalize">
                                        {{ mb_strtolower($goals->playerName) }}
                                    </a>
                                </div>
                                <div class="relative z-10 flex items-center gap-1.5">
                                    <span class="font-display text-[10px] font-bold text-stone-500 dark:text-stone-400">
                                        {{ sprintf('%04.1f', $goals->percentage) }}%
                                    </span>
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-stone-900 text-white dark:bg-[#d4ff00] dark:text-black font-black text-xs shadow-xs">
                                        {{ $goals->goals }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</div>

@endsection
