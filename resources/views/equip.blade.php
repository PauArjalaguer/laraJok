@extends('layout.mainlayout')
@section('title',$teamInfo[0]->teamName." :: JOK.cat ")
@section('content')

<div class="w-full text-neutral-700 text-xl mb-4 font-bold pb-2 border-b border-neutral-400">
    <a href="/desa/equip/{{$teamInfo[0]->idTeam}}">
        <svg xmlns="http://www.w3.org/2000/svg" fill={{$checkIfSaved==1 ? 'currentColor':'none'}} viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 cursor-pointer {{$checkIfSaved==1 ? 'text-red-800':''}}">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
    </a>
    {{App\Http\Controllers\TeamsController::teamFormat($teamInfo[0]->teamName)}}
  <span class="text-xs md:hidden "> - 
    <a class="active:text-neutral-300" class='' href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}">{{$teamInfo[0]->clubName}}</a>
  </span>
</div>
<div class='md:flex '>
    <div class="w-full md:w-2/3 ">
        <h1 class='{{ count($playersList)>0  ?  "font-bold text-neutral-700 pb-4 md:text-xl" : "hidden"}}'>Plantilla</h1>
        @foreach ($playersList as $player)
        <div class="bg-white w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-50 transition-all shadow-neutral-700 flex  items-center 
                                     p-4 capitalize flex ">
            <div class="w-full">
                <a class="active:text-neutral-300" href="/jugador/{{$player->idPlayer}}/{{urlencode($player->playerName)}}">
                    {{mb_strtolower($player->playerName)}} </a>
            </div>

        </div>
        @endforeach
        <h1 class="text-neutral-700 font-bold py-4 md:text-xl">Competicions</h1>
        @foreach($teamLeaguesList as $league)
        <div class="bg-white w-full border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-50 transition-all shadow-neutral-700 flex  items-center 
                                     p-5  ">
            <div class="w-1/2 capitalize">
                <a class="active:text-neutral-300" href="/competicio/{{$league->idGroup}}/{{urlencode($league->groupName)}}">
                    {{mb_strtolower($league->groupName)}}</a>
            </div>
            <div class="w-1/2  text-right text-sm text-gray-500">{{ \Carbon\Carbon::parse($league->startDate)->format('d-m-Y')}} a {{ \Carbon\Carbon::parse($league->endDate)->format('d-m-Y')}}</div>
        </div>
        @endforeach
    </div>
   {{--   --}}
   
   <div class="w-full px-0 md:w-1/3  md:pl-4  mt-4 md:mt-0   ">
        <div class="hidden md:w-full md:flex justify-center items-start pt-12   ">
            <a class="active:text-neutral-300" href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}">
                <img class=' w-full p-8 pt-0  ' src={{$teamInfo[0]->clubImage}} />
            </a>
        </div>
            <h1 class="text-neutral-700 font-bold pb-4 md:text-xl">Golejadors</h1>
            @foreach ($teamGoals as $goals)
            @if($goals->goals!=0)
            <div class="bg-white w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-50 transition-all shadow-neutral-700 flex  items-center  p-4 capitalize flex ">
                <div class="w-11/12">
                    <a class="active:text-neutral-300" href="/jugador/{{$goals->idPlayer}}/{{urlencode($goals->playerName)}}">{{mb_strtolower($goals->playerName)}} </a> 
                </div>
                <div class="w-1/12">{{$goals->goals}}</div>
            </div>
            @endif
            @endforeach
        </div>

    <div class="clear-both"></div>

</div>
@endsection
