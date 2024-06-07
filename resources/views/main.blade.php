@extends('layout.mainlayout')
@section('content')
<div class="w-full md:flex">
    <div class="md:w-1/2 pr-1">
        <div class="block mb-2">
            <h1 class="font-bold text-xl text-slate-700">Propers partits</h1>
        </div>
        @foreach($matchesListNext as $match)
        <div class="mb-2 shadow-md  shadow-slate-700">
            <div class="bg-slate-700 text-center w-full  border-[1px] border-b-[0px] border-slate-500  ">
                <span class="text-white font-bold text-xs ">{{$match->leagueName}} | {{$match->seasonName}}</span>
            </div>
            <div class="bg-white w-full h-full  border-solid   hover:bg-slate-50 transition-all   flex text-sm items-center">
                <div class="p-4 w-5/12 text-left text-xs md:text-sm ">
                    <img class="hidden md:inline w-2/12  max-h-12 max-w-12 mx-2" src={{$match->clubImage1}}>
                    <a href="/equip/{{$match->idLocal}}/{{urlencode($match->visitorTeam)}}">
                        {{App\Http\Controllers\TeamsController::teamFormat($match->localTeam)}}
                    </a>
                </div>
                <div class="p-4 w-2/12 text-center bg-slate-400 text-gray-800   h-full">
                    <span class="text-[10px] lg:text-sm">{{ \Carbon\Carbon::parse($match->matchDate)->format('d-m')}} {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i')}}</span>
                    <br>
                    <span class="hidden md:block w-full text-[10px] lg:text-sm">Jornada {{$match->idRound}}</span>
                    <span class="hidden"> - </span>-
                </div>
                <div class="p-4 w-5/12 text-right  text-xs md:text-sm">
                    <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}">
                        {{App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam)}}
                    </a>
                    <img class="hidden md:inline w-2/12  max-h-12 max-w-12  mx-2" src={{$match->clubImage2}}>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="md:w-1/2 pl-1">
        <div class="block mb-2">
            <h1 class="font-bold text-xl text-slate-700">Propers partits</h1>
        </div>
        @foreach($matchesListLastWithResults as $match)
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
        @endforeach
    </div>

</div>


@endsection