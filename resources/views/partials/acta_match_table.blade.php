<div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-6 font-display">
    
    <!-- Local Team Players Table -->
    <div class="w-full">
        <div class="bg-stone-900 dark:bg-[#181920] w-full border border-stone-800 rounded-t-xl overflow-hidden text-white text-xs font-bold uppercase tracking-wider flex items-center p-3 shadow-sm">
            <div class="w-7/12 text-left">Jugador (Local)</div>
            <div class='w-1/12 text-center text-[#f5c310] font-black'>G</div>
            <div class='w-1/12 text-center text-stone-300'>B</div>
            <div class='w-1/12 text-center text-stone-300'>V</div>
            <div class='w-1/12 text-center text-stone-300'>FD</div>
            <div class='w-1/12 text-center text-stone-300'>Pe</div>
        </div>
        @foreach($matchData as $m)
        @if($m->idLocal==$m->idTeam)
        <div class='bg-white dark:bg-[#131419] w-full border-x border-b border-stone-200 dark:border-stone-800 hover:bg-stone-50 dark:hover:bg-stone-850 transition-all text-xs md:text-sm flex items-center text-stone-900 dark:text-stone-100'>
            <div class='p-2.5 md:p-3 w-7/12 border-r border-stone-100 dark:border-stone-850 text-left font-bold truncate flex items-center gap-1.5'>
                @if($m->captain==1)
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-[#f5c310] text-black font-extrabold text-[10px] flex-shrink-0" title="Capità">
                    C
                </span>
                @endif
                @if($m->gk==1)
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-stone-100 dark:bg-stone-800 text-stone-800 dark:text-stone-200 font-extrabold text-[10px] flex-shrink-0" title="Porter">
                    P
                </span>
                @endif
                <a class="hover:text-[#f5c310] transition-colors truncate" href="/jugador/{{$m->idPlayer}}/{{urlencode($m->playerName)}}">
                    {{App\Http\Controllers\TeamsController::teamFormat($m->playerName)}}
                </a>
            </div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-extrabold {{ $m->goals > 0 ? 'text-[#f5c310] bg-stone-900 dark:bg-black' : 'text-stone-700 dark:text-stone-300' }}">{{$m->goals}}</div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-semibold text-stone-600 dark:text-stone-400">{{$m->blue}}</div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-semibold text-stone-600 dark:text-stone-400">{{$m->red}}</div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center text-stone-500 dark:text-stone-400">{{$m->directes}}</div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center text-stone-500 dark:text-stone-400">{{$m->penalti}}</div>
        </div>
        @endif
        @endforeach
    </div>

    <!-- Visitor Team Players Table -->
    <div class="w-full">
        <div class="bg-stone-900 dark:bg-[#181920] w-full border border-stone-800 rounded-t-xl overflow-hidden text-white text-xs font-bold uppercase tracking-wider flex items-center p-3 shadow-sm">
            <div class="w-7/12 text-left">Jugador (Visitant)</div>
            <div class="w-1/12 text-center text-[#f5c310] font-black">G</div>
            <div class="w-1/12 text-center text-stone-300">B</div>
            <div class="w-1/12 text-center text-stone-300">V</div>
            <div class="w-1/12 text-center text-stone-300">FD</div>
            <div class="w-1/12 text-center text-stone-300">Pe</div>
        </div>
        @foreach($matchData as $m)
        @if($m->idVisitor==$m->idTeam)
        <div class="bg-white dark:bg-[#131419] w-full border-x border-b border-stone-200 dark:border-stone-800 hover:bg-stone-50 dark:hover:bg-stone-850 transition-all text-xs md:text-sm flex items-center text-stone-900 dark:text-stone-100">
            <div class="p-2.5 md:p-3 w-7/12 border-r border-stone-100 dark:border-stone-850 text-left font-bold truncate flex items-center gap-1.5">
                @if($m->captain==1)
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-[#f5c310] text-black font-extrabold text-[10px] flex-shrink-0" title="Capità">
                    C
                </span>
                @endif
                @if($m->gk==1)
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-stone-100 dark:bg-stone-880 text-stone-800 dark:text-stone-200 font-extrabold text-[10px] flex-shrink-0" title="Porter">
                    P
                </span>
                @endif
                <a class="hover:text-[#f5c310] transition-colors truncate" href="/jugador/{{$m->idPlayer}}/{{urlencode($m->playerName)}}">
                    {{App\Http\Controllers\TeamsController::teamFormat($m->playerName)}}
                </a>
            </div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-extrabold {{ $m->goals > 0 ? 'text-[#f5c310] bg-stone-900 dark:bg-black' : 'text-stone-700 dark:text-stone-300' }}">{{$m->goals}}</div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-semibold text-stone-600 dark:text-stone-400">{{$m->blue}}</div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center font-semibold text-stone-600 dark:text-stone-400">{{$m->red}}</div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center text-stone-500 dark:text-stone-400">{{$m->directes}}</div>
            <div class="p-2.5 md:p-3 w-1/12 border-r border-stone-100 dark:border-stone-850 text-center text-stone-500 dark:text-stone-400">{{$m->penalti}}</div>
        </div>
        @endif
        @endforeach
    </div>
</div>


