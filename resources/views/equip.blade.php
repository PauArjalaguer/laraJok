@extends('layout.mainlayout')
@section('title', $teamInfo[0]->teamName." :: JOK.cat ")
@section('content')

<!-- TEAM HEADER (Clean Unified Style) -->
<div class="w-full mt-2 mb-6">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div class="flex items-center gap-3.5">
            <a href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}" class="w-10 h-10 md:w-12 md:h-12 bg-white dark:bg-transparent rounded-xl p-1 flex-shrink-0 flex items-center justify-center hover:scale-105 transition-transform">
                <img class="max-w-full max-h-full object-contain" src="{{$teamInfo[0]->clubImage}}" alt="{{$teamInfo[0]->clubName}}" onerror="this.style.display='none'" />
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white font-display tracking-tight">
                    {{App\Http\Controllers\TeamsController::teamFormat($teamInfo[0]->teamName)}}
                </h1>
                <a href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}" class="text-xs font-bold text-stone-500 dark:text-stone-400 hover:text-stone-900 dark:hover:text-white transition-colors">
                    {{$teamInfo[0]->clubName}}
                </a>
            </div>
        </div>
        <div class="text-right">
            <a href="/desa/equip/{{$teamInfo[0]->idTeam}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $checkIfSaved==1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 cursor-pointer transition-colors hover:text-red-700 {{ $checkIfSaved==1 ? 'text-red-700' : 'text-stone-400' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>
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
                        <div class="group flex items-center justify-between bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3 md:p-3.5 hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all shadow-xs">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary text-primary-text font-black text-xs mr-3 flex-shrink-0">
                                    {{ $player->number }}
                                </span>
                                <a href="/jugador/{{$player->idPlayer}}/{{urlencode($player->playerName)}}" class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-stone-100 group-hover:text-stone-600 dark:group-hover:text-stone-900 dark:hover:text-white transition-colors capitalize">
                                    {{ mb_strtolower($player->playerName) }}
                                </a>
                            </div>
                            <span class="text-stone-400 dark:text-stone-500 group-hover:text-stone-900 dark:group-hover:text-stone-900 dark:hover:text-white transition-colors">
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
                        <a href="/competicio/{{$league->idGroup}}/{{urlencode($league->groupName)}}" class="group flex items-center justify-between bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3.5 hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all shadow-xs">
                            <span class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-stone-100 group-hover:text-stone-600 dark:group-hover:text-stone-900 dark:hover:text-white transition-colors capitalize">
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

    <!-- RIGHT SIDEBAR (Golejadors) -->
    <div class="col-span-1 lg:col-span-4">

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
                            <div class="group relative overflow-hidden bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3 md:p-3.5 shadow-xs hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all flex items-center justify-between">
                                <!-- Progress bar overlay background -->
                                <div class="absolute top-0 left-0 h-full bg-stone-100/80 dark:bg-stone-800/40 transition-colors" style="width: {{$goals->percentage}}%;"></div>
                                
                                <div class="relative z-10">
                                    <a href="/jugador/{{$goals->idPlayer}}/{{urlencode($goals->playerName)}}" class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-stone-100 group-hover:text-stone-600 dark:group-hover:text-stone-900 dark:hover:text-white transition-colors capitalize">
                                        {{ mb_strtolower($goals->playerName) }}
                                    </a>
                                </div>
                                <div class="relative z-10 flex items-center gap-1.5">
                                    <span class="font-display text-[10px] font-bold text-stone-500 dark:text-stone-400">
                                        {{ sprintf('%04.1f', $goals->percentage) }}%
                                    </span>
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-primary text-primary-text dark:bg-stone-800 dark:text-white font-black text-xs shadow-xs">
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
