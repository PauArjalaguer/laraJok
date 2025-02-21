@extends('layout.mainlayout')
@section('title',$clubInfo[0]->clubName." :: JOK.cat ")
@section('content')

<div class="w-full text-neutral-700 text-xl mb-4 font-bold pb-2 border-b border-neutral-400">

    <img onerror="this.style.display='none'" class='  inline md:hidden  mix-blend-multiply' width="20" src={{$clubInfo[0]->clubImage}} /> {{$clubInfo[0]->clubName}}
    <a class="active:text-neutral-300" href="/desa/club/{{$clubInfo[0]->idClub}}">
        <svg xmlns="http://www.w3.org/2000/svg" fill={{$checkIfSaved==1 ? 'currentColor':'none'}} viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 hover:text-neutral-500 cursor-pointer {{$checkIfSaved==1 ? 'text-red-800':''}}">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
    </a>
</div>
<div class='md:flex '>
    <div class="w-full md:w-2/3 md:mr-5">
        <h1 class="text-xl font-bold hidden">Classificacions</h1>
        <div class="bg-whitew-full  border-solid border-t-[1px]  shadow-md  hover:bg-neutral-300 transition-all  duration-500 shadow-neutral-700 flex  items-center">
            <div class="w-full flex justify-between bg-neutral-700 border-t-[1px]  border-neutral-400  text-white">
                <div class="w-7/12 border-r-[1px] border-neutral-400 py-2 px-2 font-bold">
                    Classificacions del club
                </div>
                <div class="w-1/12  text-center  border-r-[1px] border-neutral-400 py-2 font-bold">
                    <span class="hidden lg:inline">Pos</span>
                    <span class="w-full lg:hidden">Po</span>
                </div>
                <div class="w-1/12 text-center border-r-[1px] border-neutral-400 py-2 font-bold">
                    <span class="hidden lg:inline">Punts</span>
                    <span class="w-full lg:hidden">P</span>
                </div>
                <div class="w-1/12 text-center border-r-[1px] border-neutral-400 py-2 font-bold">
                    <span class="hidden lg:inline">Guanyats</span>
                    <span class="w-full lg:hidden">G</span>
                </div>
                <div class="w-1/12 text-center border-r-[1px] border-neutral-400 py-2 font-bold">
                    <span class="hidden lg:inline">Empats</span>
                    <span class="w-full lg:hidden">E</span>
                </div>
                <div class="w-1/12 text-center py-2 font-bold">
                    <span class="hidden lg:inline">Perduts</span>
                    <span class="w-full lg:hidden">Pe</span>
                </div>
            </div>
        </div>
        @foreach($classifications as $classification)
        <div class="bg-white w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-300 transition-all  duration-500 shadow-neutral-700 flex  items-center 
                                    capitalize flex  ">
            <div class="w-full flex justify-between">
                <div class="w-7/12 py-2 px-2 text-sm md:text-base">
                    <a class="active:text-neutral-300" href="/competicio/{{$classification->idGroup}}/{{urlencode($classification->groupName)}}"> {{$classification->groupName}}</a>
                </div>
                <div class="w-1/12 py-2 items-center justify-center flex bg-neutral-700 text-white font-bold">
                    {{$classification->position}}
                </div>
                <div class="w-1/12 py-2 border-r-[1px] border-neutral-400 items-center justify-center flex">
                    {{$classification->points}}
                </div>
                <div class="w-1/12 py-2 border-r-[1px] border-neutral-400 items-center justify-center flex">
                    {{$classification->won}}
                </div>
                <div class="w-1/12 py-2 border-r-[1px] border-neutral-400 items-center justify-center flex">
                    {{$classification->draw}}
                </div>
                <div class="w-1/12 py-2 border-r-[1px] border-neutral-400 items-center justify-center flex">
                    {{$classification->lost}}
                </div>
            </div>
        </div>
        @endforeach
        <div class="pr-1 mt-3">
            <div class="block mb-2">
                <h1 class="font-bold text-xl text-neutral-700">Propers partits</h1>
            </div>
            @foreach($matchesListNext as $match)
            <x-matches-component :match="$match" />
            @endforeach
        </div>
        <div class="s pl-1">
            <div class="block mb-2">
                <h1 class="font-bold text-xl text-neutral-700">Darrers resultats</h1>
            </div>
            @foreach($matchesListLastWithResults as $match)
            <x-matches-component :match="$match" />
            @endforeach
        </div>
        <div id="propersPartits" class="hidden">
            <h1 class="text-xl font-bold">Propers partits</h1>
        </div>
        <div id="darrersResultats" class="hidden">
            <h1 class="text-xl font-bold">Darrers resultats</h1>
        </div>
    </div>
    <div class="md:w-1/3 justify-center items-start pt-12   ">
        <div class="hidden md:flex justify-center">
            <a href="/club/{{$clubInfo[0]->idClub}}/{{urlencode($clubInfo[0]->clubName)}}">
                <img onerror="this.style.display='none'" className=' w-full p-8 pt-0  ' src={{$clubInfo[0]->clubImage}} />
            </a>
        </div>
        <div class="w-full">

            @php
            $currentSeason=1;
            @endphp
            @foreach($teamsList as $team)
            @if ($currentSeason!=$team->seasonName)
            <div class="block rounded-t-xl my-2 text-neutral-700 font-bold text-sm flex justify-between">
                <h1 class="text-xl inline font-bold ">{{$currentSeason==1? "Equips":""}}</h1><span class="inline"> {{$team->seasonName}}</span>
            </div>
            @endif
            <!--  <div class='block rounded-t-xl my-2 text-neutral-700 font-bold'>{{$team->seasonName}}</div> -->
            <div class="bg-white cursor-pointer w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-300 transition-all  duration-500 shadow-neutral-700 flex  items-center 
                                     p-4 capitalize flex  ">
                <div class="w-full">
                    <a class="active:text-neutral-300" href="/equip/{{$team->idTeam}}/{{urlencode($team->teamName)}}">
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
    </div>

    <div className="clear-both"></div>

</div>
@endsection
