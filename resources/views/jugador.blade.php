@extends('layout.mainlayout')
@section('title',$playerInfo[0]->playerName." :: JOK.cat ")

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
<div class="w-full text-slate-700 text-xl mb-4 font-bold pb-2 border-b border-slate-400">
    <a href="/desa/jugador/{{$playerInfo[0]->idPlayer}}">
        <svg xmlns="http://www.w3.org/2000/svg" fill={{$checkIfSaved==1 ? 'currentColor':'none'}} viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 cursor-pointer {{$checkIfSaved==1 ? 'text-red-800':''}}">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
        </a>
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
            <x-matches-component :match="$match" />

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
                    <div><span class='font-bold'>Tarjetes vermelles:</span> {{$stats->total_red}}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
