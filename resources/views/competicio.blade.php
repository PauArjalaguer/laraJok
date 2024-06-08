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
    <svg class="inline w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
    </svg>
    {{$matchesList[0]->leagueName}}
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
        </div>

    </div>

    <div class='{{ count($classification)>0  ?   "w-full lg:w-1/2  lg:pr-2 " : "w-full" }}'>
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
            <div class="mb-2 shadow-md  shadow-slate-700 mt-2">
                <div class="bg-slate-700 text-center w-full  border-[1px] border-b-[0px] border-slate-500  ">
                    <a class="text-white font-bold text-xs" href="/competicio/{{$match->idGroup}}/{{urlencode($match->leagueName)}}">{{$match->leagueName}} | {{$match->seasonName}}</a>
                </div>
                <div class="bg-white w-full h-full  border-solid   hover:bg-slate-50 transition-all   flex text-sm items-center">
                    <div class="p-4 w-5/12 text-left text-xs md:text-sm ">
                        <img class="hidden md:inline w-2/12  max-h-12 max-w-12 mx-2" src={{$match->clubImage1}}>
                        <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($match->localTeam)}}</a>
                    </div>
                    <div class="p-4 w-2/12 text-center bg-slate-400 text-gray-800   h-full">
                        <span class="text-[10px] lg:text-sm">{{ \Carbon\Carbon::parse($match->matchDate)->format('d-m')}} {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i')}}</span>
                        <br>
                        <span class="hidden md:block w-full text-[10px] lg:text-sm"> {{strlen($match->idRound)>2 ? '' : 'Jornada ' }} {{$match->idRound}}</span>
                        <span class="text-white font-bold md:text-lg">{{$match->localResult}} - {{$match->visitorResult}}</span>
                    </div>
                    <div class="p-4 w-5/12 text-right  text-xs md:text-sm">
                        <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam)}}</a>
                        <img class="hidden md:inline w-2/12  max-h-12 max-w-12  mx-2" src={{$match->clubImage2}}>
                    </div>
                </div>
            </div>
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