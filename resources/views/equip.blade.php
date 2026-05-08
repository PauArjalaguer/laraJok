@extends('layout.mainlayout')
@section('title',$teamInfo[0]->teamName." :: JOK.cat ")
@section('content')

<div class="w-full mt-2 mb-4">
    <div class="flex items-center justify-between border-b border-neutral-200 pb-3">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 font-['Comfortaa']">
                <i class="fa-solid fa-shield-halved text-neutral-500 mr-2"></i>{{App\Http\Controllers\TeamsController::teamFormat($teamInfo[0]->teamName)}}
            </h1>
            <p class="text-sm text-neutral-500 mt-0.5">
                <a class="hover:underline" href="/club/{{$teamInfo[0]->idClub}}/{{urlencode($teamInfo[0]->clubName)}}">{{$teamInfo[0]->clubName}}</a>
            </p>
        </div>
        <div class="text-right">
            <a href="/desa/equip/{{$teamInfo[0]->idTeam}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill={{$checkIfSaved==1 ? 'currentColor':'none'}} viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 cursor-pointer transition-colors hover:text-red-700 {{$checkIfSaved==1 ? 'text-red-700':'text-neutral-400'}}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>
        </div>
    </div>
</div>
<div class='md:flex '>
    <div class="w-full md:w-2/3 ">
        <h1 class='{{ count($playersList)>0  ?  "font-bold text-neutral-700 pb-4 md:text-xl" : "hidden"}}'>Plantilla</h1>
        @foreach ($playersList as $player)
        <div class="bg-white w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-50 transition-all shadow-neutral-700 flex  items-center 
                                     p-4 capitalize flex ">
            <div class="w-full">
                
                <span class="inline-flex items-center justify-center w-6  h-6 rounded-full bg-neutral-700 text-white font-bold text-xs flex-shrink-0">
                    {{$player->number}}
                </span>
              
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
            <div class="group relative overflow-hidden bg-white w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-50 transition-all shadow-neutral-700 flex  items-center  p-4 capitalize flex ">
                <div class="absolute top-0 left-0 h-full bg-neutral-100 group-hover:bg-neutral-400 transition-colors" style="width: {{$goals->percentage}}%;"></div>
                <div class="w-10/12 relative z-10">
                    <a class="active:text-neutral-300" href="/jugador/{{$goals->idPlayer}}/{{urlencode($goals->playerName)}}">{{mb_strtolower($goals->playerName)}} </a> 
                </div>
                <div class="w-2/12 text-right relative z-10"><span class="text-xs text-neutral-500">{{sprintf('%04.1f', $goals->percentage)}}%</span>  &nbsp;   {{$goals->goals}} </div>
            </div>
            @endif
            @endforeach
        </div>

    <div class="clear-both"></div>

</div>
@endsection
