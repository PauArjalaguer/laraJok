@if (!isset($matchGetInfoById[0]))
    @php abort(404); @endphp
@endif
@extends('layout.mainlayout')
@section('title',$matchGetInfoById[0]->teamName." - ".$matchGetInfoById[0]->teamName2." :: JOK.cat ")
@section('content')

<!-- Header Bar -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4 gap-2">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="hallmark-stamp bg-[#f5c310] text-black">ACTA OFICIAL</span>
                <span class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 border border-stone-200 dark:border-stone-700">Jornada {{$matchGetInfoById[0]->idRound}}</span>
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold text-stone-900 dark:text-white">
                <a class="hover:text-[#f5c310] transition-colors" href="/competicio/{{$matchGetInfoById[0]->idGroup}}/{{urlencode($matchGetInfoById[0]->groupName)}}">
                    {{$matchGetInfoById[0]->groupName}}
                </a>
            </h1>
        </div>
        <div class="text-xs md:text-sm font-bold text-stone-500 dark:text-stone-400 flex items-center gap-2">
            <i class="fa-regular fa-clock text-[#f5c310]"></i>
            <span>{{ \Carbon\Carbon::parse($matchGetInfoById[0]->matchDate)->format('d/m/Y')}} — {{ \Carbon\Carbon::parse($matchGetInfoById[0]->matchHour)->format('H:i')}}</span>
        </div>
    </div>
</div>

<!-- Main Scoreboard Banner -->
<div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-4 md:p-8 shadow-sm font-display mb-6">
    <div class="flex items-center justify-between gap-2 md:gap-6">
        <!-- Local Team -->
        <div class="w-[40%] flex flex-col md:flex-row items-center justify-end text-right gap-3">
            <a href="/equip/{{$matchGetInfoById[0]->idLocal}}/{{urlencode($matchGetInfoById[0]->teamName)}}" class="order-2 md:order-1 hover:text-[#f5c310] transition-colors">
                <h2 class="text-sm md:text-2xl font-extrabold text-stone-900 dark:text-white leading-tight">
                    {{$matchGetInfoById[0]->teamName}}
                </h2>
            </a>
            <a href="/equip/{{$matchGetInfoById[0]->idLocal}}/{{urlencode($matchGetInfoById[0]->teamName)}}" class="order-1 md:order-2 flex-shrink-0">
                <div class="w-14 h-14 md:w-24 md:h-24 bg-white rounded-xl p-1 flex items-center justify-center border border-stone-200 dark:border-stone-700 shadow-sm">
                    <img class="max-w-full max-h-full object-contain" src="{{$matchGetInfoById[0]->clubImage1}}" alt="{{$matchGetInfoById[0]->teamName}}" />
                </div>
            </a>
        </div>

        <!-- Score Box & Faults -->
        <div class="flex flex-col items-center justify-center flex-shrink-0">
            <div class="bg-stone-900 dark:bg-black border border-stone-800 rounded-2xl px-4 py-2.5 md:px-7 md:py-4 shadow-inner flex items-center gap-2 md:gap-4">
                <span class="text-2xl md:text-5xl font-black text-[#f5c310]">
                    {{$matchGetInfoById[0]->localResult}}
                </span>
                <span class="text-stone-500 text-lg md:text-3xl font-light">-</span>
                <span class="text-2xl md:text-5xl font-black text-[#f5c310]">
                    {{$matchGetInfoById[0]->visitorResult}}
                </span>
            </div>
            <div class="mt-2 text-[10px] md:text-xs font-bold text-stone-500 dark:text-stone-400 uppercase tracking-wider">
                Faltes: <span class="text-stone-800 dark:text-stone-200">{{$matchGetInfoById[0]->localFaults}}</span> - <span class="text-stone-800 dark:text-stone-200">{{$matchGetInfoById[0]->visitorFaults}}</span>
            </div>
        </div>

        <!-- Visitor Team -->
        <div class="w-[40%] flex flex-col md:flex-row items-center justify-start text-left gap-3">
            <a href="/equip/{{$matchGetInfoById[0]->idVisitor}}/{{urlencode($matchGetInfoById[0]->teamName2)}}" class="flex-shrink-0">
                <div class="w-14 h-14 md:w-24 md:h-24 bg-white rounded-xl p-1 flex items-center justify-center border border-stone-200 dark:border-stone-700 shadow-sm">
                    <img class="max-w-full max-h-full object-contain" src="{{$matchGetInfoById[0]->clubImage2}}" alt="{{$matchGetInfoById[0]->teamName2}}" />
                </div>
            </a>
            <a href="/equip/{{$matchGetInfoById[0]->idVisitor}}/{{urlencode($matchGetInfoById[0]->teamName2)}}" class="hover:text-[#f5c310] transition-colors">
                <h2 class="text-sm md:text-2xl font-extrabold text-stone-900 dark:text-white leading-tight">
                    {{$matchGetInfoById[0]->teamName2}}
                </h2>
            </a>
        </div>
    </div>

    <!-- Referee Info Bar -->
    @if(isset($matchGetInfoById[0]->referee) && !empty($matchGetInfoById[0]->referee))
    <div class="mt-6 pt-4 border-t border-stone-100 dark:border-stone-800 flex items-center justify-center gap-2 text-xs md:text-sm text-stone-500 dark:text-stone-400">
        <i class="fa-solid fa-user-shield text-[#f5c310]"></i>
        <span class="font-bold text-stone-700 dark:text-stone-300">Àrbitre:</span>
        <a href="/arbitre/{{ urlencode($matchGetInfoById[0]->referee) }}" class="font-bold text-stone-900 dark:text-stone-100 hover:text-[#f5c310] transition-colors underline decoration-stone-300 dark:decoration-stone-700 underline-offset-4">
            {{App\Http\Controllers\TeamsController::teamFormat($matchGetInfoById[0]->referee)}}
        </a>
    </div>
    @endif
</div>

<!-- Match Table Partial -->
<div class="w-full">
    @if($matchGetInfoById->count()>1)
        @include('partials.acta_match_table', ['matchData' => $matchGetInfoById])
    @else
        <div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-2xl p-8 text-center text-xs md:text-sm font-display text-stone-500 dark:text-stone-400">
            <i class="fa-regular fa-file-lines text-2xl text-stone-400 mb-2 block"></i>
            Acta encara no disponible o no existent a Fecapa.
        </div>
    @endif
</div>
@endsection

