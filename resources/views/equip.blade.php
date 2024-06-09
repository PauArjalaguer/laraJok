@extends('layout.mainlayout')
@section('content')

<div class="w-full text-slate-700 text-xl my-4 font-bold pb-2 border-b border-slate-400">
   <a href="/desa/equip/{{$teamInfo[0]->idTeam}}"> <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 hover:text-slate-500 cursor-pointer">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
    </svg></a>
    {{App\Http\Controllers\TeamsController::teamFormat($teamInfo[0]->teamName)}}
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
            <div class="w-1/2  text-right text-sm text-gray-500">{{ \Carbon\Carbon::parse($league->startDate)->format('d-m-Y')}} a {{ \Carbon\Carbon::parse($league->endDate)->format('d-m-Y')}}</div>
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