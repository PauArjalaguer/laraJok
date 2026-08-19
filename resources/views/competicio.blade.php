@extends('layout.mainlayout')
@section('title',$matchesList[0]->leagueName." :: JOK.cat ")
@section('content')

@php
    $targetRoundId = null;
    if (isset($round) && !empty($round)) {
        $rClean = str_replace(' ', '', urldecode($round));
        if (strlen($rClean) == 1 && is_numeric($rClean)) {
            $rClean = '0' . $rClean;
        }
        $targetRoundId = $rClean;
    }
@endphp

<script>
    let c = 0;
    const showActualRound = (date, counter) => {
        const currentDate = new Date().toISOString().split('T')[0];
        if(date > currentDate && c == 0){
            c++;
            if(counter < 10){
                counter = "0" + String(counter);
            }
            if(counter == "00"){
                counter = "01";
            }
            leagueShow(counter);
        }
    }

    const leagueShow = (league) => {
        const leagueContainers = document.getElementsByClassName("leagueContainer");
        const leagueButtons = document.getElementsByClassName("leagueButton");
        
        for (let i = 0; i < leagueContainers.length; i++) {
            leagueContainers[i].style.display = 'none';
        }
        for (let i = 0; i < leagueButtons.length; i++) {
            leagueButtons[i].style.backgroundColor = '';
            leagueButtons[i].style.color = '';
            leagueButtons[i].style.borderColor = '';
            leagueButtons[i].classList.remove('font-black');
        }
        
        const activeContainer = document.getElementById("league_" + league);
        const activeBtn = document.getElementById(league + "_button");
        const mobileSelect = document.getElementById("mobileRoundSelect");
        if (mobileSelect) {
            mobileSelect.value = league;
        }
        
        if (activeContainer) {
            activeContainer.style.display = "block";
            activeContainer.classList.remove('hidden');
        }
        if (activeBtn) {
            const isDark = document.documentElement.classList.contains('dark');
            activeBtn.style.backgroundColor = isDark ? '#27272a' : 'var(--color-primary)';
            activeBtn.style.color = isDark ? '#ffffff' : 'var(--color-primary-text)';
            activeBtn.style.borderColor = isDark ? '#3f3f46' : 'var(--color-primary)';
            activeBtn.classList.add('font-black');
        }
    }
</script>

<style>
    .classCol-team { width: 58.333%; min-width: 0; /* w-7/12 */ }
    .classCol-form { width: 16.666%; /* w-2/12 */ }
    .classification-expanded .classCol-team { width: 25%; min-width: 0; /* w-3/12 */ }
    .classification-expanded .classCol-team a {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<script>
    let classificationExpanded = false;
    const toggleClassification = () => {
        classificationExpanded = !classificationExpanded;
        const container = document.getElementById('classificationContainer');
        const matchesSection = document.getElementById('matchesSection');
        const extras = document.getElementsByClassName('classCol-extra');
        const arrow = document.getElementById('expandClassArrow');
        const text = document.getElementById('expandClassText');

        if (classificationExpanded) {
            container.classList.remove('lg:col-span-1');
            container.classList.add('lg:col-span-2', 'classification-expanded');
            if (matchesSection) {
                matchesSection.classList.remove('lg:col-span-1');
                matchesSection.classList.add('lg:col-span-2');
            }
            for (let i = 0; i < extras.length; i++) {
                extras[i].classList.remove('hidden');
            }
            arrow.style.transform = 'rotate(180deg)';
            text.textContent = 'Reduir';
        } else {
            container.classList.remove('lg:col-span-2', 'classification-expanded');
            container.classList.add('lg:col-span-1');
            if (matchesSection) {
                matchesSection.classList.remove('lg:col-span-2');
                matchesSection.classList.add('lg:col-span-1');
            }
            for (let i = 0; i < extras.length; i++) {
                extras[i].classList.add('hidden');
            }
            arrow.style.transform = 'rotate(0deg)';
            text.textContent = 'Ampliar';
        }
    }
</script>

<div class="w-full mt-2 mb-6">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white font-display tracking-tight">
                {{$matchesList[0]->groupName}}
            </h1>
        </div>
        <div class="text-right">
            <a href="/desa/competicio/{{$matchesList[0]->idGroup}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill={{$checkIfSaved==1 ? 'currentColor':'none'}} viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 cursor-pointer transition-colors hover:text-red-700 {{$checkIfSaved==1 ? 'text-red-700':'text-stone-400'}}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- COLUMN 1: CLASSIFICACIÓ -->
    <div id="classificationContainer" class='{{ count($classification)>0  ? "col-span-1 lg:col-span-1 transition-all duration-300" : "hidden" }}'>
        
        <div class="flex items-center justify-between pb-2 mb-3">
            <h3 class="font-display font-black text-sm uppercase tracking-wider text-stone-900 dark:text-white">
                Classificació
            </h3>
            
            <button onclick="toggleClassification()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-stone-100 hover:bg-stone-200 dark:bg-stone-900 dark:hover:bg-stone-800 text-stone-700 dark:text-stone-300 font-display text-xs font-bold rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs cursor-pointer">
                <span id="expandClassText">Ampliar</span>
                <svg id="expandClassArrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 transition-transform duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        <div class="bg-stone-950 dark:bg-[#121215] border border-stone-800 rounded-t-2xl overflow-hidden text-white text-[11px] font-black uppercase tracking-wider flex items-center p-3 font-display shadow-xs">
            <div class="w-1/12 text-center">Pos</div>
            <div class="classCol-team text-left pl-2">Equip</div>
            <div class="w-1/12 text-center">Pts</div>
            <div class="w-1/12 text-center classCol-extra hidden text-stone-400">PJ</div>
            <div class="w-1/12 text-center">PG</div>
            <div class="w-1/12 text-center">PE</div>
            <div class="w-1/12 text-center">PP</div>
            <div class="w-1/12 text-center classCol-extra hidden text-stone-400">GF</div>
            <div class="w-1/12 text-center classCol-extra hidden text-stone-400">GC</div>
            <div class="classCol-form text-center classCol-extra hidden text-stone-400">Forma</div>
        </div>

        @php $p=0; @endphp
        @foreach ($classification as $classificationRow)
        @php $p++; @endphp
        
        <div class="bg-white dark:bg-[#121215] border-x border-b border-stone-200 dark:border-stone-800 hover:bg-stone-50 dark:hover:bg-stone-850 transition-all text-xs font-display flex items-center text-stone-900 dark:text-stone-100 {{ $p == count($classification) ? 'rounded-b-2xl' : '' }}">
            
            <div class="p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-extrabold flex items-center justify-center">
                @if ($p==1)
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black text-xs">1</span>
                @else
                    <span class="text-stone-500 dark:text-stone-400 font-bold">{{$p}}</span>
                @endif
            </div>

            <div class="p-3 classCol-team border-r border-stone-100 dark:border-stone-850 text-left font-black capitalize flex items-center gap-2 truncate">
                @if(!empty($classificationRow->clubImage) && !str_contains($classificationRow->clubImage, 'no_logo'))
                    <img alt="{{App\Http\Controllers\TeamsController::teamFormat($classificationRow->teamName)}}" class="w-6 h-6 object-contain flex-shrink-0" src="{{ str_replace('images//', 'images/', $classificationRow->clubImage) }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" />
                    <div class="w-6 h-6 rounded-full bg-stone-100 dark:bg-stone-800 hidden items-center justify-center flex-shrink-0 text-stone-400 text-[10px]">
                        <i class="fa-solid fa-shield"></i>
                    </div>
                @else
                    <div class="w-6 h-6 rounded-full bg-stone-100 dark:bg-stone-800 flex items-center justify-center flex-shrink-0 text-stone-400 text-[10px]">
                        <i class="fa-solid fa-shield"></i>
                    </div>
                @endif
                <a class="hover:text-stone-900 dark:hover:text-white transition-colors truncate" title="{{App\Http\Controllers\TeamsController::teamFormat($classificationRow->teamName)}}" href="/equip/{{$classificationRow->idTeam}}/{{urlencode($classificationRow->teamName)}}">
                    {{App\Http\Controllers\TeamsController::teamFormat($classificationRow->teamName)}}
                </a>
            </div>

            <div class="p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-black text-stone-900 dark:text-white bg-stone-50/50 dark:bg-stone-900/30">
                {{$classificationRow->points}}
            </div>

            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center classCol-extra hidden text-stone-500'>{{$classificationRow->played}}</div>
          
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center'>{{$classificationRow->won}}</div>
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center'>{{$classificationRow->draw}}</div>
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center'>{{$classificationRow->lost}}</div>

            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center classCol-extra hidden text-stone-500'>{{$classificationRow->goalsMade}}</div>
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center classCol-extra hidden text-stone-500'>{{$classificationRow->goalsReceived}}</div>
            <div class='p-3 classCol-form text-center classCol-extra hidden'>
                <div class="flex items-center justify-center gap-1">
                    @if(isset($teamForm[$classificationRow->idTeam]))
                        @foreach(array_reverse($teamForm[$classificationRow->idTeam]) as $matchData)
                            @if($matchData['result'] === 'W')
                                <span class="inline-block w-3 h-3 rounded-full bg-emerald-500" title="{{ $matchData['tooltip'] }}"></span>
                            @elseif($matchData['result'] === 'L')
                                <span class="inline-block w-3 h-3 rounded-full bg-rose-500" title="{{ $matchData['tooltip'] }}"></span>
                            @else
                                <span class="inline-block w-3 h-3 rounded-full bg-amber-400" title="{{ $matchData['tooltip'] }}"></span>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <!-- Highlight Stats Section -->
        <div class="mt-6 space-y-3 font-display">
            <!-- Equip més golejador -->
            <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-2xl p-4 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img alt="{{count($bestGoalsMade)>0 ? App\Http\Controllers\TeamsController::teamFormat($bestGoalsMade[0]->teamName) : ''}}" src={{ count($bestGoalsMade)>0 ? $bestGoalsMade[0]->clubImage:'' }} class="w-9 h-9 object-contain" />
                    <div>
                        <span class="text-[10px] font-black text-stone-900 dark:text-white uppercase tracking-wider block">MÉS GOLEJADOR</span>
                        <a class="font-extrabold text-xs md:text-sm text-stone-900 dark:text-stone-100 hover:text-stone-900 dark:hover:text-white" href="/equip/{{count($bestGoalsMade)>0 ? $bestGoalsMade[0]->idTeam : ''}}/{{count($bestGoalsMade)>0 ? urlencode($bestGoalsMade[0]->teamName ) :''}}">
                            {{count($bestGoalsMade)>0 ? App\Http\Controllers\TeamsController::teamFormat($bestGoalsMade[0]->teamName) : ''}}
                        </a>
                    </div>
                </div>
                <div class="text-right font-black text-sm md:text-base text-stone-900 dark:text-white bg-stone-100 dark:bg-stone-900 px-3 py-1 rounded-full">
                    {{count($bestGoalsMade)>0 ? $bestGoalsMade[0]->goalsMade : '0'}} <span class="text-[10px] text-stone-500 font-normal">gols</span>
                </div>
            </div>

            <!-- Equip menys golejat -->
            <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-2xl p-4 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img alt="{{count($leastGoalsReceived)>0 ? App\Http\Controllers\TeamsController::teamFormat($leastGoalsReceived[0]->teamName) : ''}}" src={{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->clubImage : ''}} class="w-9 h-9 object-contain" />
                    <div>
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-wider block">MENYS GOLEJAT</span>
                        <a class="font-extrabold text-xs md:text-sm text-stone-900 dark:text-stone-100 hover:text-stone-900 dark:hover:text-white" href="/equip/{{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->idTeam : ''}}/{{count($leastGoalsReceived)>0 ? urlencode($leastGoalsReceived[0]->teamName) :''}}">
                            {{count($leastGoalsReceived)>0 ? App\Http\Controllers\TeamsController::teamFormat($leastGoalsReceived[0]->teamName) : ''}}
                        </a>
                    </div>
                </div>
                <div class="text-right font-black text-sm md:text-base text-stone-900 dark:text-white bg-stone-100 dark:bg-stone-900 px-3 py-1 rounded-full">
                    {{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->goalsReceived : '0'}} <span class="text-[10px] text-stone-500 font-normal">gols</span>
                </div>
            </div>

            <!-- Golejadors -->
            @if(count($maxGoalsPerLeague) > 0)
            <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-stone-100 dark:border-stone-800">
                    <svg class="w-4 h-4 inline-block drop-shadow-xs flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" title="Bola d'Hoquei">
                        <defs>
                            <radialGradient id="hockeyBallGradHeader" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#a1a1aa" />
                                <stop offset="35%" stop-color="#3f3f46" />
                                <stop offset="85%" stop-color="#09090b" />
                                <stop offset="100%" stop-color="#000000" />
                            </radialGradient>
                        </defs>
                        <circle cx="12" cy="12" r="9.5" fill="url(#hockeyBallGradHeader)" stroke="#ffffff" stroke-width="1.5"/>
                    </svg>
                    <span class="text-xs font-black text-stone-900 dark:text-white uppercase tracking-wider">MÀXIMS GOLEJADORS DE LA LLIGA</span>
                </div>
                <div class="space-y-2">
                    @foreach($maxGoalsPerLeague as $index => $player)
                    <div class="flex items-center justify-between text-xs font-display">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 font-extrabold text-[10px] flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <a class="font-extrabold text-stone-800 dark:text-stone-200 hover:text-stone-900 dark:hover:text-white transition-colors" href="/jugador/{{$player->idPlayer}}/{{urlencode($player->playerName)}}">
                                {{App\Http\Controllers\TeamsController::teamFormat($player->playerName)}}
                            </a>
                        </div>
                        <span class="font-black bg-primary text-primary-text dark:bg-stone-800 dark:text-white px-2.5 py-0.5 rounded-full text-[11px]">
                            {{$player->goals}} Gols
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    </div>

    <!-- COLUMN 2: JORNADES I PARTITS -->
    <div id="matchesSection" class='{{ count($classification)>0  ? "col-span-1 lg:col-span-1 transition-all duration-300" : "col-span-1 lg:col-span-2" }}'>
        <div class="mb-4">
            <h3 class="hidden md:block font-display font-black text-sm uppercase tracking-wider text-stone-900 dark:text-white mb-3">
                Jornades
            </h3>
            @php
            $currentRound = 0;
            $counter = 0;
            $hasActiveRoundMatch = false;
            if ($targetRoundId) {
                foreach ($matchesList as $mCheck) {
                    $cleanCheck = str_replace(" ", "", $mCheck->idRound);
                    if (strlen($cleanCheck) == 1) { $cleanCheck = "0" . $cleanCheck; }
                    if ($cleanCheck === $targetRoundId) {
                        $hasActiveRoundMatch = true;
                        break;
                    }
                }
            }
            @endphp

            <!-- Mobile Round Selector (md:hidden) -->
            <div class="block md:hidden mb-3 font-display">
                <div class="relative">
                    <select id="mobileRoundSelect" onchange="leagueShow(this.value)" class="w-full appearance-none bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 text-stone-900 dark:text-white text-xs font-black py-2.5 pl-4 pr-10 rounded-full focus:outline-none focus:border-[#1c1917] transition-all shadow-xs cursor-pointer">
                        @php
                        $uniqueRounds = [];
                        foreach($matchesList as $mItem) {
                            $rVal = $mItem->idRound;
                            if (!in_array($rVal, $uniqueRounds)) {
                                $uniqueRounds[] = $rVal;
                            }
                        }
                        @endphp
                        @foreach($uniqueRounds as $rVal)
                            @php
                            $rClean = strlen($rVal) == 1 ? '0'.$rVal : str_replace(' ', '', $rVal);
                            $isSelected = $hasActiveRoundMatch ? ($rClean === $targetRoundId) : false;
                            @endphp
                            <option value="{{ $rClean }}" {{ $isSelected ? 'selected' : '' }}>
                                Jornada {{ $rVal }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-stone-500 dark:text-stone-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Desktop Round Buttons (hidden md:flex) -->
            <div class='hidden md:flex justify-start flex-wrap gap-1.5'>
                @foreach($matchesList as $match)
                    @php
                    $btnRound = $match->idRound;
                    if (strlen($btnRound) == 1) { $btnRound = "0" . $btnRound; }
                    $btnClean = str_replace(" ", "", $btnRound);
                    $isBtnActive = $hasActiveRoundMatch ? ($btnClean === $targetRoundId) : false;
                    @endphp
                    @if ($currentRound != $match->idRound)
                    <button id="{{$btnClean}}_button" class="inline-flex items-center justify-center text-center {{ strlen($match->idRound) > 2 ? 'px-3 min-w-9 w-auto' : 'w-9' }} h-9 rounded-full font-display text-xs font-black leading-none {{ $isBtnActive ? 'bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 border-[#1c1917]' : 'bg-stone-100 dark:bg-stone-900 text-stone-800 dark:text-stone-200 border border-stone-200 dark:border-stone-800' }} hover:bg-primary hover:text-primary-text dark:hover:bg-primary dark:hover:text-primary-text transition-all cursor-pointer leagueButton" onClick="leagueShow('{{$btnClean}}')">
                        {{$match->idRound}}
                    </button>
                    @endif

                    @php
                    $currentRound = $match->idRound;
                    $counter++;
                    @endphp
                @endforeach
            </div>
        </div>

        <div id="season0">
            @php
            $currentRound = 0;
            $counter = 1;
            @endphp
            @foreach($matchesList as $match)
                @php
                $contRound = $match->idRound;
                if (strlen($contRound) == 1) { $contRound = "0" . $contRound; }
                $contClean = str_replace(" ", "", $contRound);
                $isContActive = $hasActiveRoundMatch ? ($contClean === $targetRoundId) : ($counter == 1);
                @endphp
                @if ($currentRound != $match->idRound)
        </div>
        <div id="league_{{$contClean}}" class='leagueContainer @if(!$isContActive) hidden @endif' style="{{ $isContActive ? 'display: block;' : 'display: none;' }}">
                @endif
                @if(!$round)
                    <script>showActualRound('{{$match->matchDate}}',{{ $currentRound}})</script>
                @endif
                <x-matches-component :match="$match" />
                @php
                $currentRound = $match->idRound;
                $counter++;
                @endphp
            @endforeach
        </div>
       
        <div class="clear-both"></div>
    </div>
</div>

@if($targetRoundId)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        leagueShow(@json($targetRoundId));
    });
</script>
@endif

<script>
    setTimeout(() => {
        document.getElementById("percent").style.width = {{$totalPlayed['percentage_played']}} + "%";
        setTimeout(() => {
            document.getElementById("percentPlace").style.display="none";
            document.getElementById("percentText").style.display="block";
        },500);
    }, 1500);
</script>
@endsection
