@extends('layout.mainlayout')
@section('title',$clubInfo[0]->clubName." :: JOK.cat ")
@section('content')

<div class="w-full text-neutral-700 text-xl mb-4 font-bold pb-2 border-b border-neutral-400">
    <a href="/desa/club/{{$clubInfo[0]->idClub}}">
        <svg xmlns="http://www.w3.org/2000/svg" fill={{$checkIfSaved==1 ? 'currentColor':'none'}} viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 hover:text-neutral-500 cursor-pointer {{$checkIfSaved==1 ? 'text-red-800':''}}">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
    </a>
    {{$clubInfo[0]->clubName}}
</div>
<div class='md:flex '>
    <div class="w-full md:w-2/3">
        @php
        $currentSeason=1;
        @endphp
        @foreach($teamsList as $team)
        @if ($currentSeason!=$team->seasonName)
        <div class="block rounded-t-xl my-2 text-neutral-700 font-bold">{{$team->seasonName}}</div>
        @endif
        <!--  <div class='block rounded-t-xl my-2 text-neutral-700 font-bold'>{{$team->seasonName}}</div> -->
        <div class="bg-white cursor-pointer w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-300 transition-all  duration-500 shadow-neutral-700 flex  items-center 
                                     p-4 capitalize flex  ">
            <div class="w-full">
                <a href="/equip/{{$team->idTeam}}/{{urlencode($team->teamName)}}">
                    <img onerror="this.style.display='none'" class='w-1/12  inline md:hidden  mix-blend-multiply' src={{$clubInfo[0]->clubImage}} /> {{App\Http\Controllers\TeamsController::teamFormat($team->teamName)}}
                    {{$team->categoryName}}
                </a>
            </div>
        </div>
        @php
        $currentSeason=$team->seasonName;
        @endphp

        @endforeach

    </div>
    <div class="hidden md:w-1/3 md:flex justify-center items-start pt-12   ">
        <a href="/club/{{$clubInfo[0]->idClub}}/{{urlencode($clubInfo[0]->clubName)}}">
            <img onerror="this.style.display='none'" className=' w-full p-8 pt-0  ' src={{$clubInfo[0]->clubImage}} />
        </a>
    </div>

    <div className="clear-both"></div>

</div>
@endsection
