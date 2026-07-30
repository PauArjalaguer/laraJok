@extends('layout.mainlayout')
@section('title',$matchesList[0]->leagueName." :: JOK.cat ")
@section('content')
<script>
    let c=0;
    const showActualRound = (date,counter)=>{
        const currentDate = new Date().toISOString().split('T')[0];
        if(date>currentDate && c==0){
            c++;
            if(counter<10){
                counter="0"+String(counter);
            }
            if(counter=="00"){
                counter="01";
            }
        leagueShow(counter);
        }
    }

    const leagueShow = (league) => {
        const leagueContainer = document.getElementsByClassName("leagueContainer");
        const leagueButton = document.getElementsByClassName("leagueButton");
        for (let i = 0; i < leagueContainer.length; i++) {
            leagueContainer[i].style.display = 'none';
            leagueButton[i].style.backgroundColor = '';
            leagueButton[i].style.color = '';
            leagueButton[i].style.borderColor = '';
            leagueButton[i].classList.remove('font-black');
        }
        const activeContainer = document.getElementById("league_" + league);
        const activeBtn = document.getElementById(league + "_button");
        if (activeContainer) activeContainer.style.display = "block";
        if (activeBtn) {
            activeBtn.style.backgroundColor = '#f5c310';
            activeBtn.style.color = '#000000';
            activeBtn.style.borderColor = '#f5c310';
            activeBtn.classList.add('font-black');
        }
    }
</script>

<style>
    .classCol-team { width: 58.333%; min-width: 0; /* w-7/12 */ }
    .classCol-form { width: 16.666%; /* w-2/12 */ }
    .classification-expanded .classCol-team { width: 25%; min-width: 0; /* w-3/12 */ }
    .classification-expanded .classCol-form { width: 16.666%; }
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
            <div class="flex items-center gap-2">
                <span class="hallmark-stamp bg-[#f5c310] text-black">CLASSIFICACIÓ I JORNADES</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-stone-900 dark:text-white font-display mt-2">
                {{$matchesList[0]->groupName}}
            </h1>
            <p class="text-xs md:text-sm text-stone-500 dark:text-stone-400 mt-1 font-display flex items-center gap-1.5">
                <i class="fa-solid fa-trophy text-[#f5c310]"></i> {{ $matchesList[0]->leagueName ?? 'Competició' }}
            </p>
        </div>
        <div class="text-right">
            <a href="/desa/competicio/{{$matchesList[0]->idGroup}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}" class="inline-flex p-2 rounded-xl bg-stone-100 dark:bg-stone-850 hover:bg-stone-200 dark:hover:bg-stone-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill={{$checkIfSaved==1 ? 'currentColor':'none'}} viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cursor-pointer transition-colors {{$checkIfSaved==1 ? 'text-red-600':'text-stone-400'}}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>
        </div>
    </div>
</div>

@if ($totalPlayed['percentage_played']>0)
<div class="bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 w-full my-4 rounded-xl overflow-hidden p-1 shadow-sm">
    <div id="percent" class="bg-stone-900 dark:bg-[#f5c310] text-white dark:text-black py-1.5 font-display text-xs font-extrabold rounded-lg transition-all ease-in text-center uppercase tracking-wider" style='width:0%'>
      <span id="percentPlace" class='px-4'> &nbsp;</span>
      <span id="percentText" class='hidden px-4'>{{$totalPlayed['percentage_played']}}% COMPLETAT</span>
    </div>
</div>
@endif

<div class='w-full grid grid-cols-1 lg:grid-cols-2 gap-6'>

    <div id="classificationContainer" class='{{ count($classification)>0  ?  "col-span-1 lg:col-span-1 mb-4 transition-all duration-300" : "hidden"}}'>
        {{-- Expand/Collapse button --}}
        <div class="hidden md:flex justify-between items-center mb-3">
            <h3 class="font-display font-extrabold text-sm uppercase tracking-wider text-stone-900 dark:text-white">
                Classificació
            </h3>
            <button id="expandClassBtn" onclick="toggleClassification()" class="flex items-center gap-1.5 text-xs font-display font-bold text-stone-500 hover:text-[#f5c310] transition-colors px-2.5 py-1 rounded-lg bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800">
                <span id="expandClassText">Ampliar</span>
                <svg id="expandClassArrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 transition-transform duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        {{-- Classification Header --}}
        <div class='bg-stone-900 dark:bg-[#181920] w-full border border-stone-800 rounded-t-xl overflow-hidden text-white font-display text-xs font-bold uppercase tracking-wider flex items-center shadow-sm'>
            <div class='p-3 w-1/12 text-center text-stone-400'>#</div>
            <div class='p-3 classCol-team text-left'>Equips</div>
            <div class='p-3 w-1/12 text-center bg-black text-[#f5c310] font-black'>P</div>
            <div class='p-3 w-1/12 text-center classCol-extra hidden'>PJ</div>
            <div class='p-3 w-1/12 text-center'>G</div>
            <div class='p-3 w-1/12 text-center'>E</div>
            <div class='p-3 w-1/12 text-center'>Pe</div>
            <div class='p-3 w-1/12 text-center classCol-extra hidden'>GF</div>
            <div class='p-3 w-1/12 text-center classCol-extra hidden'>GC</div>
            <div class='p-3 classCol-form text-center classCol-extra hidden'>Darrers partits</div>
        </div>

        {{-- Prepare last matches data per team --}}
        @php
            $teamForm = [];
            $lastPlayedMatches = $lastPlayedMatches->reverse();
            foreach ($lastPlayedMatches as $match) {
                $localId = $match->idLocal;
                $visitorId = $match->idVisitor;
                $localGoals = (int)$match->localResult;
                $visitorGoals = (int)$match->visitorResult;

                $localTeamName = App\Http\Controllers\TeamsController::teamFormat($match->localTeamName);
                $visitorTeamName = App\Http\Controllers\TeamsController::teamFormat($match->visitorTeamName);
                $tooltip = "{$localTeamName} {$localGoals} - {$visitorGoals} {$visitorTeamName} ".date('d/m/Y', strtotime($match->matchDate));

                if (!isset($teamForm[$localId])) $teamForm[$localId] = [];
                if (count($teamForm[$localId]) < 5) {
                    if ($localGoals > $visitorGoals) {
                        $teamForm[$localId][] = ['result' => 'W', 'tooltip' => $tooltip];
                    } elseif ($localGoals < $visitorGoals) {
                        $teamForm[$localId][] = ['result' => 'L', 'tooltip' => $tooltip];
                    } else {
                        $teamForm[$localId][] = ['result' => 'D', 'tooltip' => $tooltip];
                    }
                }

                if (!isset($teamForm[$visitorId])) $teamForm[$visitorId] = [];
                if (count($teamForm[$visitorId]) < 5) {
                    if ($visitorGoals > $localGoals) {
                        $teamForm[$visitorId][] = ['result' => 'W', 'tooltip' => $tooltip];
                    } elseif ($visitorGoals < $localGoals) {
                        $teamForm[$visitorId][] = ['result' => 'L', 'tooltip' => $tooltip];
                    } else {
                        $teamForm[$visitorId][] = ['result' => 'D', 'tooltip' => $tooltip];
                    }
                }
            }
        @endphp

        @foreach($classification as $classificationRow)
        <div class='bg-white dark:bg-[#131419] w-full border-x border-b border-stone-200 dark:border-stone-800 hover:bg-stone-50 dark:hover:bg-stone-850 transition-all font-display text-xs md:text-sm flex items-center text-stone-900 dark:text-stone-100'>
            <div class='p-3 w-1/12 text-center font-bold text-stone-500 border-r border-stone-100 dark:border-stone-850'>{{$classificationRow->position}}</div>
            <div class='p-3 classCol-team border-r border-stone-100 dark:border-stone-850 text-left font-bold truncate'>
                <a class="hover:text-[#f5c310] transition-colors" href="/equip/{{$classificationRow->idTeam}}/{{urlencode($classificationRow->teamName)}}">{{App\Http\Controllers\TeamsController::teamFormat($classificationRow->teamName)}}</a>
            </div>
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-black bg-stone-100 dark:bg-black text-stone-900 dark:text-[#f5c310]'>{{$classificationRow->points}}</div>
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center classCol-extra hidden text-stone-500'>{{$classificationRow->played}}</div>
          
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center'>{{$classificationRow->won}}</div>
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center'>{{$classificationRow->draw}}</div>
            <div class='p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center'>{{$classificationRow->lost}}</div>
            {{-- Expanded columns --}}
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
            <div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-xl p-3.5 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img alt="{{count($bestGoalsMade)>0 ? App\Http\Controllers\TeamsController::teamFormat($bestGoalsMade[0]->teamName) : ''}}" src={{ count($bestGoalsMade)>0 ? $bestGoalsMade[0]->clubImage:'' }} class="w-9 h-9 object-contain" />
                    <div>
                        <span class="text-[10px] font-bold text-[#f5c310] uppercase tracking-wider block">MÉS GOLEJADOR</span>
                        <a class="font-bold text-xs md:text-sm text-stone-900 dark:text-stone-100 hover:text-[#f5c310]" href="/equip/{{count($bestGoalsMade)>0 ? $bestGoalsMade[0]->idTeam : ''}}/{{count($bestGoalsMade)>0 ? urlencode($bestGoalsMade[0]->teamName ) :''}}">
                            {{count($bestGoalsMade)>0 ? App\Http\Controllers\TeamsController::teamFormat($bestGoalsMade[0]->teamName) : ''}}
                        </a>
                    </div>
                </div>
                <div class="text-right font-extrabold text-sm md:text-base text-stone-900 dark:text-white bg-stone-100 dark:bg-stone-850 px-2.5 py-1 rounded-lg">
                    {{count($bestGoalsMade)>0 ? $bestGoalsMade[0]->goalsMade : '0'}} <span class="text-[10px] text-stone-500 font-normal">gols</span>
                </div>
            </div>

            <!-- Equip menys golejat -->
            <div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-xl p-3.5 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img alt="{{count($leastGoalsReceived)>0 ? App\Http\Controllers\TeamsController::teamFormat($leastGoalsReceived[0]->teamName) : ''}}" src={{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->clubImage : ''}} class="w-9 h-9 object-contain" />
                    <div>
                        <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider block">MENYS GOLEJAT</span>
                        <a class="font-bold text-xs md:text-sm text-stone-900 dark:text-stone-100 hover:text-[#f5c310]" href="/equip/{{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->idTeam : ''}}/{{count($leastGoalsReceived)>0 ? urlencode($leastGoalsReceived[0]->teamName) :''}}">
                            {{count($leastGoalsReceived)>0 ? App\Http\Controllers\TeamsController::teamFormat($leastGoalsReceived[0]->teamName) : ''}}
                        </a>
                    </div>
                </div>
                <div class="text-right font-extrabold text-sm md:text-base text-stone-900 dark:text-white bg-stone-100 dark:bg-stone-850 px-2.5 py-1 rounded-lg">
                    {{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->goalsReceived : '0'}} <span class="text-[10px] text-stone-500 font-normal">gols</span>
                </div>
            </div>

            <!-- Golejadors -->
            @if(count($maxGoalsPerLeague)>0)
            <div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-xl p-3.5 shadow-sm mt-4">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-stone-900 dark:text-white mb-3 flex items-center justify-between">
                    <span>MÀXIMS GOLEJADORS</span>
                    <i class="fa-solid fa-futbol text-[#f5c310]"></i>
                </h4>
                <div class="space-y-2">
                    @foreach($maxGoalsPerLeague->take(5) as $player)
                    <div class="flex items-center justify-between text-xs py-1 border-b border-stone-100 dark:border-stone-850 last:border-0">
                        <a class="font-semibold text-stone-800 dark:text-stone-200 hover:text-[#f5c310] transition-colors" href="/jugador/{{$player->idPlayer}}/{{urlencode($player->playerName)}}">
                            {{App\Http\Controllers\TeamsController::teamFormat($player->playerName)}}
                        </a>
                        <span class="font-extrabold text-[#f5c310] bg-stone-900 dark:bg-black px-2 py-0.5 rounded text-[11px]">
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
            <h3 class="font-display font-extrabold text-sm uppercase tracking-wider text-stone-900 dark:text-white mb-3">
                Jornades
            </h3>
            <div class='flex justify-start flex-wrap gap-1.5'>
                @php
                $currentRound=0;
                $counter=0;
                @endphp
                @foreach($matchesList as $match)
                @if ($currentRound!=$match->idRound)
                @if(strlen($match->idRound)==1)
                @php
                $match->idRound="0".$match->idRound
                @endphp
                @endif
                <button id="{{str_replace(" ","",$match->idRound)}}_button" class="inline-flex items-center justify-center text-center {{ strlen($match->idRound) > 2 ? 'px-3 min-w-9 w-auto' : 'w-9' }} h-9 rounded-xl font-display text-xs font-extrabold leading-none bg-stone-100 dark:bg-stone-850 text-stone-800 dark:text-stone-200 border border-stone-200 dark:border-stone-800 hover:bg-[#f5c310] hover:text-black transition-all cursor-pointer leagueButton" onClick="leagueShow('{{str_replace(" ","",$match->idRound)}}')">
                    {{$match->idRound}}
                </button>
                @endif

                @php
                $currentRound=$match->idRound;
                $counter++;
                @endphp

                @endforeach
            </div>
        </div>

        <div id="season0">
            @php
            $currentRound=0;
            $counter=1;
            @endphp
            @foreach($matchesList as $match)
            @if ($currentRound!=$match->idRound)
        </div>
        <div id=league_{{str_replace(" ","",$match->idRound)}} class='leagueContainer @if ($counter!=1) hidden @endif'>
            @endif
            @if(!$round)
            <script>showActualRound('{{$match->matchDate}}',{{ $currentRound}})</script>
            @endif
            <x-matches-component :match="$match" />
            @php
            $currentRound=$match->idRound;
            $counter++;
            @endphp
            @endforeach
        </div>
       
        <div class="clear-both"></div>
    </div>
</div>

@if($round)
<script>showActualRound('{{$match->matchDate}}',{{ $round}})</script>
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

