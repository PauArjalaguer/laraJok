@if (!isset($matchGetInfoById[0]))
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

    @include('partials.acta_match_table', ['matchData' => $matchGetInfoById])
</div>
@endsection
