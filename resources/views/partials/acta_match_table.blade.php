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
        @foreach($matchData as $m)
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
        @foreach($matchData as $m)
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
