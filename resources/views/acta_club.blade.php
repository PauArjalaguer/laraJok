@extends('layout.mainlayout')
@section('title', "Actas Club")
@section('content')

<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold my-8 text-center text-neutral-800">Actas de la Setmana</h1>

    @if(count($matches) == 0)
        <div class="text-center text-xl text-neutral-600 my-10">No hi ha partits amb acta disponible aquesta setmana.</div>
    @endif

    @foreach($matches as $matchData)
        @if(isset($matchData[0]))
            <div class="mb-12 border-b-2 border-neutral-300 pb-8 bg-white p-4 rounded-lg shadow-sm">
                {{-- Header part adapted from acta.blade.php --}}
                <div class="w-full text-neutral-700 text-xl my-4 font-bold pb-2 border-b border-neutral-400 flex">
                    <div class="lg:w-1/2 inline flex items-center">
                        <a class="inline active:text-neutral-300 hover:text-neutral-500 transition-colors" href="/competicio/{{$matchData[0]->idGroup}}/{{urlencode($matchData[0]->groupName)}}"> {{$matchData[0]->groupName}}</a>
                        <div class="w-6 h-6 flex items-center justify-center bg-neutral-700 text-white font-bold rounded-full inline text-xs p-1 mx-2">
                            {{$matchData[0]->idRound}}
                        </div>
                    </div>
                    <div class="w-1/2 inline text-right text-sm lg:text-base">
                        {{ \Carbon\Carbon::parse($matchData[0]->matchDate)->format('d-m-Y')}} {{ \Carbon\Carbon::parse($matchData[0]->matchHour)->format('H:i')}}
                    </div>
                </div>

                <div class="clear-both">&nbsp;</div>
                <div class="w-full">
                    <div class="w-full flex flex-col lg:flex-row items-center justify-center">
                        <div class="w-full lg:w-5/12 flex justify-center lg:justify-end text-right items-center mb-4 lg:mb-0">
                            <span class="text-lg lg:text-2xl mr-4 text-neutral-700 font-bold">
                                <a class="active:text-neutral-300 hover:text-neutral-500" href="/equip/{{$matchData[0]->idLocal}}/{{urlencode($matchData[0]->teamName)}}">{{$matchData[0]->teamName}}</a>
                            </span>
                            <a href="/equip/{{$matchData[0]->idLocal}}/{{urlencode($matchData[0]->teamName)}}">
                                <img class="w-[50px] lg:w-[80px] aspect-square object-contain" src={{$matchData[0]->clubImage1}} />
                            </a>
                        </div>
                        <div class="w-full lg:w-2/12 text-center flex justify-center items-center mb-4 lg:mb-0">
                            <div class="rounded-lg inline bg-neutral-600 text-xl lg:text-3xl px-4 py-2 mr-1 text-white font-bold">
                                {{$matchData[0]->localResult}}
                                <span class="block text-xs text-neutral-300 font-normal">{{$matchData[0]->localFaults}}</span>
                            </div>
                            <div class="text-xl font-bold mx-2">-</div>
                            <div class="inline rounded-lg bg-neutral-600 text-xl lg:text-3xl px-4 py-2 text-white font-bold">
                                {{$matchData[0]->visitorResult}}
                                <span class="block text-xs text-neutral-300 font-normal">{{$matchData[0]->visitorFaults}}</span>
                            </div>
                        </div>
                        <div class="w-full lg:w-5/12 text-left flex justify-center lg:justify-start items-center">
                            <a href="/equip/{{$matchData[0]->idVisitor}}/{{urlencode($matchData[0]->teamName2)}}">
                                <img class="w-[50px] lg:w-[80px] aspect-square object-contain" src={{$matchData[0]->clubImage2}} />
                            </a>
                            <span class="text-lg lg:text-2xl ml-4 text-neutral-700 font-bold">
                                <a class="active:text-neutral-300 hover:text-neutral-500" href="/equip/{{$matchData[0]->idVisitor}}/{{urlencode($matchData[0]->teamName2)}}">{{$matchData[0]->teamName2}}</a>
                            </span>
                        </div>
                    </div>
                    <div class="w-full text-center mt-4 text-sm text-neutral-500">
                        <span class="font-bold">Àrbitre:</span> {{App\Http\Controllers\TeamsController::teamFormat($matchData[0]->referee)}}
                    </div>
                    <div class="clear-both mb-6">&nbsp;</div>

                    @include('partials.acta_match_table', ['matchData' => $matchData])
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection
