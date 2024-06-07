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
        document.getElementById(league).style.display = "block";
        document.getElementById(league + "_button").style.backgroundColor = "rgb(08 23 43 / 1)";

    }
</script>
<div class="w-full text-slate-700 text-xl my-4 font-bold pb-2 border-b border-slate-400">
    <svg class="inline w-6 text-red-500 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
    </svg>
    {{$playerInfo[0]->playerName}}
</div>

<div class='md:flex '>
    <div class="w-full md:w-2/3 md:pr-2  ">
        <div class="mb-3">
            @php
            $currentSeason=1;
            $counter=0;
            @endphp
            @foreach($playerMatchesList as $match)
            @if ($currentSeason!=$match->seasonName)
            <div id="{{$match->seasonName}}_button" class="block rounded-xl p-2 my-2 bg-slate-700 font-bold inline mr-2 text-white cursor-pointer leagueButton text-sm md:text-base" onClick="leagueShow('{{$match->seasonName}}')">{{$match->seasonName}}</div>
            @endif
            @php
            $currentSeason=$match->seasonName;
            $counter++;
            @endphp
            @endforeach
           
        </div>
        <div id="season0">
            @php
            $currentSeason=1;
            $counter=0;
            @endphp
            @foreach($playerMatchesList as $match)
            @if ($currentSeason!=$match->seasonName)
        </div>
        <div id={{$match->seasonName}} class='leagueContainer @if ($counter!=0) hidden @endif'>
            <div class='block rounded-t-xl my-2 text-slate-700 font-bold hidden'>{{$match->seasonName}}</div>
            @endif
            <div class="mb-2 shadow-md  shadow-slate-700">
                <div class="bg-slate-700 text-center w-full  border-[1px] border-b-[0px] border-slate-500  ">
                    <span class="text-white font-bold text-xs ">{{$match->leagueName}} | {{$match->seasonName}}</span>
                </div>
                <div class="bg-white w-full h-full  border-solid   hover:bg-slate-50 transition-all   flex text-sm items-center">
                    <div class="p-4 w-5/12 text-left text-xs md:text-sm ">
                        <img class="hidden md:inline w-2/12  max-h-12 max-w-12 mx-2" src={{$match->clubImage1}}>
                        <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($match->localTeam)}}</a>
                    </div>
                    <div class="p-4 w-2/12 text-center bg-slate-400 text-gray-800   h-full">
                        <span class="text-[10px] lg:text-sm">{{ \Carbon\Carbon::parse($match->matchDate)->format('d-m')}} {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i')}}</span>
                        <br>
                        <span class="hidden md:block w-full text-[10px] lg:text-sm">Jornada {{$match->idRound}}</span>
                        <span class="text-white font-bold md:text-lg">{{$match->localResult}} - {{$match->visitorResult}}</span>
                    </div>
                    <div class="p-4 w-5/12 text-right  text-xs md:text-sm">
                        <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam)}}</a>
                        <img class="hidden md:inline w-2/12  max-h-12 max-w-12  mx-2" src={{$match->clubImage2}}>
                    </div>
                </div>
            </div>
            @php
            $currentSeason=$match->seasonName;
            $counter++;
            @endphp


            @endforeach
        </div>
    </div>
    <div class="w-full md:block md:w-1/3  justify-center items-start md:pl-2   ">
        <div class="w-full flex justify-center  ">
            <a href="/jugador/{{$playerInfo[0]->idPlayer}}/{{urlencode($playerInfo[0]->playerName)}}">
                <!-- <img class='p-10 ' src='http://ronaldmottram.co.nz/wp-content/uploads/2019/01/default-user-icon-8-300x300.jpg' /> -->
                 <img class='p-10' src="http://clubolesapati.cat/images/dynamic/playersImages/{{$playerInfo[0]->idPlayer}}.jpg" onerror="this.onerror=null;this.src='http://ronaldmottram.co.nz/wp-content/uploads/2019/01/default-user-icon-8-300x300.jpg';" /> 
            </a>
        </div>
        <div>
            <h1 class="text-slate-700 font-bold  text-xl pb-2 ">Estadístiques</h1>
            @foreach($playerStats as $stats)
            <div class='mb-1'>
                <div class='bg-slate-700 text-center w-full border-[1px] border-b-[0px] border-slate-500'>
                    <span class='text-white font-bold text-xs'>{{$stats->seasonName}}</span>
                </div>
                <div class='bg-white w-full h-full  border-solid  shadow-md  hover:bg-slate-50 transition-all shadow-slate-700   items-center p-4'>
                    <div><span class='font-bold'>Partits jugats:</span> {{$stats->match_count}}</div>
                    <div><span class='font-bold'>Gols:</span> {{$stats->total_goals}}</div>
                    <div><span class='font-bold'>Tarjetes blaves:</span> {{$stats->total_blue}}</div>
                    <div><span class='font-bold'>Tarjetes blaves:</span> {{$stats->total_red}}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection