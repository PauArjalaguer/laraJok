@extends('layout.mainlayout')
@section('content')

<div class="w-full text-slate-700 text-xl my-4 font-bold pb-2 border-b border-slate-400">
    <svg class="inline w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
    </svg>
    {{$clubInfo[0]->clubName}}
</div>
<div class='md:flex '>
    <div class="w-full md:w-2/3">
        @php
        $currentSeason=1;
        @endphp
        @foreach($teamsList as $team)
        @if ($currentSeason!=$team->seasonName)
        <div class='block rounded-t-xl my-2 text-slate-700 font-bold'>{{$team->seasonName}}</div>
        @endif
        <!--  <div class='block rounded-t-xl my-2 text-slate-700 font-bold'>{{$team->seasonName}}</div> -->
        <div class="bg-white cursor-pointer w-full  border-solid border-t-[1px] border-slate-400 shadow-md  hover:bg-slate-300 transition-all  duration-500 shadow-slate-700 flex  items-center 
                                     p-5 capitalize flex  ">
            <div class="w-full">
                <!--  <Link href={`/equip/${team.idTeam}/${encodeURIComponent(team.teamName)}`}> {team.teamName.toLowerCase()} {team.categoryName}</Link> -->
                <a href="/equip/{{$team->idTeam}}/{{urlencode($team->teamName)}}">{{App\Http\Controllers\TeamsController::teamFormat($team->teamName)}} {{$team->categoryName}} </a>
            </div>

        </div>
        @php
        $currentSeason=$team->seasonName;
        @endphp

        @endforeach

    </div>
    <div class="hidden md:w-1/3 md:flex justify-center items-start pt-12   ">
        <a href="/club/{{$clubInfo[0]->idClub}}/{{urlencode($clubInfo[0]->clubName)}}">
            <img className=' w-full p-8 pt-0  ' src={{$clubInfo[0]->clubImage}} />
            </Link>
    </div>

    <div className="clear-both"></div>

</div>
@endsection