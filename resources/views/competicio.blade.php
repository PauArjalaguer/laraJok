@extends('layout.mainlayout')
@section('content')
<script>
    const leagueShow = (league) => {
        var leagueContainer = document.getElementsByClassName("leagueContainer");
        var leagueButton = document.getElementsByClassName("leagueButton");
        for (i = 0; i < leagueContainer.length; i++) {
            leagueContainer[i].style.display = 'none';
            leagueButton[i].style.backgroundColor = 'rgb(51 65 85 / 1)';
        }

        document.getElementById("league_" + league).style.display = "block";
        document.getElementById(league + "_button").style.backgroundColor = "rgb(08 23 43 / 1)";

    }
</script>

<div class="w-full text-slate-700 text-xl my-4 font-bold pb-2 border-b border-slate-400">
    <!--  <svg class="inline w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
    </svg> -->
    <a href="/desa/competicio/{{$matchesList[0]->idGroup}}"> <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 hover:text-slate-500 cursor-pointer">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
    </a>
    {{$matchesList[0]->leagueName}}
</div>

<div class="bg-slate-700 w-full my-2">
    <div class="bg-slate-500 w-[{{$totalPlayed['percentage_played']}}%] py-2 px-6 font-bold text-white">{{$totalPlayed['percentage_played']}} % jugat</div>

</div>
<div class='w-full lg:flex  '>

    <div class='{{ count($classification)>0  ?  "w-full lg:w-1/2  lg:pr-2 mb-2" : "hidden"}}'>
        <div class='bg-slate-700 w-full  border-solid border-[1px]  border-b-[0px]  border-slate-400 shadow-md    transition-all shadow-slate-100 flex text-white'>
            <div class='p-4 w-1/12 text-center '>&nbsp;</div>
            <div class='p-4 w-7/12  text-left font-bold'>Equips</div>
            <div class='p-4 w-1/12  text-center bg-slate-700 font-bold'>P</div>
            <div class='p-4 w-1/12  text-center font-bold'>G</div>
            <div class='p-4 w-1/12 text-center font-bold'>E</div>
            <div class='p-4 w-1/12  text-center font-bold'>Pe</div>
        </div>
        @foreach($classification as $classificationRow)
        <div class='bg-white w-full  border-solid border-t-[1px] border-slate-400 shadow-md  hover:bg-slate-50 transition-all shadow-slate-700 flex'>
            <div class='p-2 md:p-4 w-1/12 text-center border-r-[1px] text-xs md:text-sm '>{{$classificationRow->position}}</div>
            <div class='p-2 md:p-4 w-7/12 border-r-[1px] text-left  text-xs md:text-sm '>
                <a href="/equip/{{$classificationRow->idTeam}}/{{urlencode($classificationRow->teamName)}}">{{App\Http\Controllers\TeamsController::teamFormat($classificationRow->teamName)}}</a>
            </div>
            <div class='p-2 md:p-4 w-1/12 border-r-[1px] text-center bg-slate-700  text-xs md:text-sm text-white'>{{$classificationRow->points}}</div>
            <div class='p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm'>{{$classificationRow->won}}</div>
            <div class='p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm'>{{$classificationRow->draw}}</div>
            <div class='p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm'>{{$classificationRow->lost}}</div>

        </div>
        @endforeach
        <div class=" mt-2 ">
            <div class="font-bold bg-slate-700 p-2 text-white border-[1px] border-b-[0px] border-slate-500  shadow-md shadow-slate-700 ">Equip més golejador</div>
            <div class="flex bg-white items-center  border-[1px]  border-slate-500 shadow-md shadow-slate-700">
                <div class="p-2 w-1/12 ">
                    <img src={{ count($bestGoalsMade)>0 ? $bestGoalsMade[0]->clubImage:'' }} class="max-h-8 max-w-8 md:max-h-12 md:max-w-12  " />
                </div>
                <div class="p-2 w-11/12 ml-2">
                    <a href="/equip/{{count($bestGoalsMade)>0 ? $bestGoalsMade[0]->idTeam : ''}}/{{count($bestGoalsMade)>0 ? urlencode($bestGoalsMade[0]->teamName ) :''}}">
                        <span class="font-bold">{{count($bestGoalsMade)>0 ? App\Http\Controllers\TeamsController::teamFormat($bestGoalsMade[0]->teamName) : ''}}</span>
                    </a>
                    <br />{{count($bestGoalsMade)>0 ? $bestGoalsMade[0]->goalsMade : ''}} gols
                </div>
            </div>
            <div class="font-bold bg-slate-700 p-2 text-white border-[1px] border-b-[0px] border-slate-500 mt-2 shadow-md shadow-slate-700">Equip menys golejat </div>
            <div class="flex bg-white items-center border-[1px]  border-slate-500 shadow-md shadow-slate-700">
                <div class="p-2 w-1/12 ">
                    <a href="/equip/{{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->idTeam  : ''}}/{{count($leastGoalsReceived) ? urlencode($leastGoalsReceived[0]->teamName) : ''}}">
                        <img src={{count($leastGoalsReceived)>0 ?  $leastGoalsReceived[0]->clubImage : ''}} class="max-h-8 max-w-8 md:max-h-12 md:max-w-12  " />
                </div>
                <div class="p-2 w-11/12 ml-2">
                    <a href="/equip/{{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->idTeam : ''}}/{{count($leastGoalsReceived)>0 ? urlencode($leastGoalsReceived[0]->teamName) :''}}">
                        <span class="font-bold">{{count($leastGoalsReceived)>0 ? App\Http\Controllers\TeamsController::teamFormat($leastGoalsReceived[0]->teamName) : ''}}</span>
                    </a>
                    <br />{{count($leastGoalsReceived)>0 ? $leastGoalsReceived[0]->goalsReceived : ''}} gols
                </div>
            </div>
            <div class="font-bold bg-slate-700 p-2 text-white border-[1px] border-b-[0px] border-slate-500 mt-2 shadow-md shadow-slate-700">Golejadors </div>
            @foreach($maxGoalsPerLeague as $player)
            <div class='bg-white w-full  border-solid border-t-[1px] border-slate-400 shadow-md  hover:bg-slate-50 transition-all shadow-slate-700 flex'>

                <div class='p-2 md:p-4 w-11/12 border-r-[1px] text-left  text-xs md:text-sm '>
                    <a href="/jugador/{{$player->idPlayer}}/{{urlencode($player->playerName)}}">{{App\Http\Controllers\TeamsController::teamFormat($player->playerName)}}</a>
                </div>
                <div class='p-2 md:p-4 w-1/12 text-center border-r-[1px] text-xs md:text-sm '>{{$player->goals}}</div>

            </div>
            @endforeach
        </div>

    </div>

    <div class='{{ count($classification)>0  ?   "w-full lg:w-1/2  lg:pl-2 " : "w-full" }}'>
        <div class='flex justify-stretch flex-wrap'>
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
            <div id="{{str_replace(" ","",$match->idRound)}}_button" class="min-w-10 {{ strlen($match->idRound)> 2 ? 'rounded-3xl  px-3' : 'rounded-[50%]'; }} mr-1 mt-1 bg-slate-700 text-center leading-10  font-bold inline space-between  text-white cursor-pointer leagueButton " onClick="leagueShow('{{str_replace(" ","",$match->idRound)}}')">{{$match->idRound}}</div>

            @endif

            @php
            $currentRound=$match->idRound;
            $counter++;
            @endphp

            @endforeach
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
            <div class='block rounded-t-xl my-2 text-slate-700 font-bold hidden'>{{$match->seasonName}}</div>
            @endif
            <x-matches-component :matchArray="$match" />
            @php
            $currentRound=$match->idRound;
            $counter++;
            @endphp


            @endforeach
        </div>


        <div class="clear-both"></div>
    </div>
</div>
@endsection