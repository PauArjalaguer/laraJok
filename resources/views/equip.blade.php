@extends('layout.mainlayout')
@section('content')

<div class="w-full text-slate-700 text-xl my-4 font-bold pb-2 border-b border-slate-400">
    <svg class="inline w-6 text-red-500 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
    </svg>
    {{$teamInfo[0]->teamName}}
</div>
<div class='md:flex '>
    <div class="w-full md:w-2/3 ">
        <h1 class='{{ count($playersList)>0  ?  "font-bold text-slate-700 pb-4 md:text-xl" : "hidden"}}'>Plantilla</h1>
        @foreach ($playersList as $player)
        <div class="bg-white w-full  border-solid border-t-[1px] border-slate-400 shadow-md  hover:bg-slate-50 transition-all shadow-slate-700 flex  items-center 
                                     p-5 capitalize flex ">
            <div class="w-full">
                <a href="/jugador/{{$player->idPlayer}}/{{urlencode($player->playerName)}}">
                    {{mb_strtolower($player->playerName)}} </Link>
            </div>

        </div>
        @endforeach
        <h1 class="text-slate-700 font-bold py-4 md:text-xl">Competicions</h1>
        @foreach($teamLeaguesList as $league)
        <div class="bg-white w-full border-solid border-t-[1px] border-slate-400 shadow-md  hover:bg-slate-50 transition-all shadow-slate-700 flex  items-center 
                                     p-5  ">
            <div class="w-1/2 capitalize">
                <a href="/competicio/{{$league->idGroup}}/{{urlencode($league->groupName)}}">
                    {{mb_strtolower($league->groupName)}}</a>
            </div>
            <div class="w-1/2  text-right">{{ \Carbon\Carbon::parse($league->startDate)->format('d-m-Y')}} a {{ \Carbon\Carbon::parse($league->endDate)->format('d-m-Y')}}</div>
        </div>
        @endforeach
    </div>
    <div class="hidden md:w-1/3 md:flex justify-center items-start pt-12   ">
        <a href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}">
            <img class=' w-full p-8 pt-0  ' src={{$teamInfo[0]->clubImage}} />
        </a>
    </div>

    <div class="clear-both"></div>

</div>
@endsection