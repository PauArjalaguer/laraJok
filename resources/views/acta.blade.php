@if (!isset($collection[0]))
    @php abort(404); @endphp
@endif
@extends('layout.mainlayout')
@section('title',$matchGetInfoById[0]->teamName."-".$matchGetInfoById[0]->teamName2." :: JOK.cat ")
@section('content')
<div class="w-full text-neutral-700 text-xl my-4 font-bold pb-2 border-b border-neutral-400 flex">
    <div class="lg:w-1/2 inline flex">
        <a class="inline active:text-neutral-300" href="/competicio/{{$matchGetInfoById[0]->idGroup}}/{{urlencode($matchGetInfoById[0]->groupName)}}"> {{$matchGetInfoById[0]->groupName}}</a>
        <div class="w-5 h-5 flex items-center justify-center bg-neutral-700 text-white font-bold rounded-full inline text-sm p-2 mx-1">
            {{$matchGetInfoById[0]->idRound}}
        </div>
    </div>
    <div class="w-1/2 inline text-right ">
        {{ \Carbon\Carbon::parse($matchGetInfoById[0]->matchDate)->format('d-m-Y')}} {{ \Carbon\Carbon::parse($matchGetInfoById[0]->matchHour)->format('H:i')}}
    </div>
</div>

<div class="clear-both">&nbsp;</div>
<div class="w-full">
    <div class="w-full flex">
        <div class="w-5/12 flex justify-end text-right items-center  mr-10 lg:m-0">
            <span class="text-lg lg:text-3xl mr-4  text-neutral-700 font-bold">
                <a class="active:text-neutral-300" href="/equip/{{$matchGetInfoById[0]->idLocal}}/{{urlencode($matchGetInfoById[0]->teamName)}}">{{$matchGetInfoById[0]->teamName}}</a>
            </span>
            <a href="/equip/{{$matchGetInfoById[0]->idLocal}}/{{urlencode($matchGetInfoById[0]->teamName)}}">
                <img class="md:w-[70px] lg:w-[120px] aspect-square hidden md:inline" src={{$matchGetInfoById[0]->clubImage1}} />
            </a>
        </div>
        <div class="w-2/12 text-center flex justify-center items-center">
            <div class="rounded-lg inline bg-neutral-600 text-lg lg:text-4xl px-6 py-4 mr-1 text-white">{{$matchGetInfoById[0]->localResult}} <br />-<br /><span class="text-lg text-neutral-400">{{$matchGetInfoById[0]->localFaults}}</span></div>
            <div class="inline rounded-lg bg-neutral-600 text-lg lg:text-4xl px-6 py-4 text-white">{{$matchGetInfoById[0]->visitorResult}} <br />-<br /><span class="text-lg text-neutral-400">{{$matchGetInfoById[0]->visitorFaults}}</span></div>
        </div>
        <div class="w-5/12 text-left flex  justify-start items-center ml-10 lg:m-0">
            <a href="/equip/{{$matchGetInfoById[0]->idVisitor}}/{{urlencode($matchGetInfoById[0]->teamName2)}}">
                <img class="md:w-[70px] lg:w-[120px] aspect-square hidden md:inline " src={{$matchGetInfoById[0]->clubImage2}} />
            </a>
            <span class="text-lg lg:text-3xl mr-4  text-neutral-700 font-bold">
                <a class="active:text-neutral-300" href="/equip/{{$matchGetInfoById[0]->idVisitor}}/{{urlencode($matchGetInfoById[0]->teamName2)}}">{{$matchGetInfoById[0]->teamName2}}</a>
            </span>
        </div>
    </div>
    <div class="w-full text-center mt-4"><span class="font-bold">Àrbitre</span><br /> {{App\Http\Controllers\TeamsController::teamFormat($matchGetInfoById[0]->referee)}}</div>
    <div class="clear-both">&nbsp;</div>

    <div class="w-full lg:flex">
        <div class="lg:w-1/2 text-right lg:p-2">
            <div class="bg-neutral-700 w-full  border-solid border-[1px]  border-b-[0px]  border-neutral-400 shadow-md transition-all shadow-neutral-100 flex text-white">
                <div class="p-4 w-8/12  text-left font-bold">Jugador</div>
                <div class='p-4 w-1/12  text-center bg-neutral-700 font-bold'>G</div>
                <div class='p-4 w-1/12  text-center font-bold'>B</div>
                <div class='p-4 w-1/12 text-center font-bold'>V</div>
                <div class='p-4 w-1/12  text-center font-bold'>FD</div>
                <div class='p-4 w-1/12  text-center font-bold'>Pe</div>
            </div>
            @foreach($matchGetInfoById as $m)
            @if($m->idLocal==$m->idTeam)
            <div class='bg-white w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-50 transition-all shadow-neutral-700 flex'>
                <div class='p-2 md:p-4 w-8/12 border-r-[1px] text-left  text-xs md:text-sm '>
                    <a class="active:text-neutral-300" href="/jugador/{{$m->idPlayer}}/{{urlencode($m->playerName)}}">{{App\Http\Controllers\TeamsController::teamFormat($m->playerName)}}</a>
                </div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->goals}}</div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->blue}}</div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->red}}</div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->directes}}</div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->penalti}}</div>
            </div>

            @endif
            @endforeach
        </div>

        <div class="lg:w-1/2 lg:p-2 border-solid border-neutral-300 border-l-[1px] mt-2 lg:mt-0">
            <div class="bg-neutral-700 w-full  border-solid border-[1px]  border-b-[0px]  border-neutral-400 shadow-md  transition-all shadow-neutral-100 flex text-white">
                <div class="p-4 w-8/12 text-left font-bold">Jugador</div>
                <div class="p-4 w-1/12 text-center bg-neutral-700 font-bold">G</div>
                <div class="p-4 w-1/12 text-center font-bold">B</div>
                <div class="p-4 w-1/12 text-center font-bold">V</div>
                <div class="p-4 w-1/12 text-center font-bold">FD</div>
                <div class="p-4 w-1/12 text-center font-bold">Pe</div>
            </div>
            @foreach($matchGetInfoById as $m)
            @if($m->idVisitor==$m->idTeam)
            <div class="bg-white w-full  border-solid border-t-[1px] border-neutral-400 shadow-md  hover:bg-neutral-50 transition-all shadow-neutral-700 flex">
                <div class="p-2 md:p-4 w-8/12 border-r-[1px] text-left  text-xs md:text-sm">
                    <a class="active:text-neutral-300" href="/jugador/{{$m->idPlayer}}/{{urlencode($m->playerName)}}">{{App\Http\Controllers\TeamsController::teamFormat($m->playerName)}}</a>
                </div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->goals}}</div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->blue}}</div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->red}}</div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->directes}}</div>
                <div class="p-2 md:p-4 w-1/12 border-r-[1px] text-center  text-xs md:text-sm">{{$m->penalti}}</div>
            </div>

            @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
