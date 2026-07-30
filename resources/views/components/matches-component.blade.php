@props(['match', 'type'])

@php
    // Auto-detect type if not passed
    if (!isset($type)) {
        $type = isset($match->localResult) ? 'result' : 'upcoming';
    }

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $mapUrl = (isset($userAgent) && $userAgent == 'iOSWebView' && isset($match->lat))
        ? "https://maps.apple.com/?q=" . $match->lat . "," . $match->lon
        : (isset($match->lat) ? "https://maps.google.com/?q=" . $match->lat . "," . $match->lon : "#");
@endphp

@if ($type === 'ticker')
    <!-- TICKER STYLE (Apple Sports Bar) -->
    <div class="inline-flex flex-col bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3 shadow-xs mr-3 flex-shrink-0 w-64 text-xs transition-all hover:border-[#d4ff00]">
        <div class="font-display text-[10px] font-extrabold text-stone-500 dark:text-stone-400 uppercase tracking-wider mb-1 truncate" title="{{ $match->groupName }}">
            <a href="/competicio/{{ $match->idGroup }}/{{ urlencode($match->groupName) }}" class="hover:text-[#d4ff00] transition-colors">
                {{ $match->groupName }}
            </a>
        </div>
        <div class="flex items-center justify-between font-display">
            <div class="flex items-center gap-1.5 truncate max-w-[78%]">
                <span class="truncate font-bold text-stone-800 dark:text-stone-200" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}</span>
                <span class="font-black text-[#d4ff00] px-2 py-0.5 bg-stone-900 dark:bg-black rounded-full text-[11px]">{{ $match->localResult }} - {{ $match->visitorResult }}</span>
                <span class="truncate font-bold text-stone-800 dark:text-stone-200" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}</span>
            </div>
            <!-- LIVE/FI Status Stamp -->
            <div class="flex items-center gap-1 flex-shrink-0">
                @if(isset($match->localResult))
                    <span class="font-display text-[9px] font-bold text-stone-500 dark:text-stone-400 bg-stone-100 dark:bg-stone-800 px-2 py-0.5 rounded-full">FI</span>
                @else
                    <span class="font-display text-[9px] font-black text-black bg-[#d4ff00] px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
                @endif
            </div>
        </div>
    </div>

@elseif ($type === 'result')
    <!-- RESULT STYLE (Apple Sports Card) -->
    @php
        $userSavedData = \App\Models\User::userSavedData();
        $selectedTeams = \App\Models\User::userTeamsSelected($userSavedData) ?? [];
        $isLocalSelected = in_array($match->idLocal, $selectedTeams);
        $isVisitorSelected = in_array($match->idVisitor, $selectedTeams);
    @endphp
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl mb-3.5 overflow-hidden shadow-xs hover:border-stone-400 dark:hover:border-[#d4ff00] transition-all p-4">
        <!-- Top header info -->
        <div class="font-display text-[10px] font-extrabold text-stone-500 dark:text-stone-400 uppercase tracking-wider mb-3 truncate flex items-center justify-between" title="{{ $match->groupName }}">
            <a href="/competicio/{{ $match->idGroup }}/{{ urlencode($match->groupName) }}" class="hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors truncate">
                <span class="text-stone-400 dark:text-[#d4ff00] mr-1">●</span> {{ $match->groupName }}
            </a>
        </div>
        
        <!-- Teams Content -->
        <div class="flex items-center justify-between mb-3.5 gap-2">
            <!-- Local Team Name -->
            <div class="w-[38%] text-right font-extrabold text-xs md:text-sm truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
                <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}" class="{{ $isLocalSelected ? 'text-stone-900 font-black dark:text-[#d4ff00]' : 'text-stone-900 dark:text-stone-100 hover:text-stone-600 dark:hover:text-[#d4ff00]' }} transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}
                </a>
            </div>

            <!-- Local Logo -->
            <div class="w-8 h-8 md:w-10 md:h-10 bg-white rounded-xl p-0.5 flex-shrink-0 flex items-center justify-center border border-stone-200 dark:border-stone-700/80 shadow-xs">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage1 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
            </div>
            
            <!-- Score Box -->
            <div class="flex items-center justify-center flex-shrink-0 bg-stone-900 dark:bg-black px-3 py-1 rounded-full border border-stone-800 shadow-inner">
                <span class="font-display text-xs md:text-sm font-black text-[#d4ff00]">
                    {{ $match->localResult }} - {{ $match->visitorResult }}
                </span>
            </div>
            
            <!-- Visitor Logo -->
            <div class="w-8 h-8 md:w-10 md:h-10 bg-white rounded-xl p-0.5 flex-shrink-0 flex items-center justify-center border border-stone-200 dark:border-stone-700/80 shadow-xs">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage2 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
            </div>

            <!-- Visitor Team Name -->
            <div class="w-[38%] text-left font-extrabold text-xs md:text-sm truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
                <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}" class="{{ $isVisitorSelected ? 'text-stone-900 font-black dark:text-[#d4ff00]' : 'text-stone-900 dark:text-stone-100 hover:text-stone-600 dark:hover:text-[#d4ff00]' }} transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
                </a>
            </div>
        </div>

        <!-- Info area: date, time, location -->
        <div class="mt-3 pt-2.5 border-t border-stone-100 dark:border-stone-800/80 font-display text-[11px] font-bold text-stone-500 dark:text-stone-400 space-y-0.5">
            <div class="flex items-center gap-1.5">
                <i class="fa-regular fa-calendar text-stone-400 dark:text-[#d4ff00]"></i>
                <span>{{ \Carbon\Carbon::parse($match->matchDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}</span>
            </div>
            @if(isset($match->placeAddress))
                <div class="flex items-center gap-1.5 truncate" title="{{ $match->placeAddress }}">
                    <i class="fa-solid fa-location-pin text-stone-400"></i>
                    <span class="truncate">{{ $match->placeAddress }}</span>
                </div>
            @endif
        </div>

        <!-- Action Buttons (Pavelló & Acta) -->
        @if(isset($match->lat) || isset($match->localResult))
            <div class="flex items-center gap-2 mt-3 pt-1">
                @if(isset($match->lat))
                    <a href="{{ $mapUrl }}" target="_blank" class="group flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-[11px] font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800">
                        <i class="fa-solid fa-location-dot text-stone-500 group-hover:text-white dark:text-[#d4ff00] dark:group-hover:text-black transition-colors"></i> Pavelló
                    </a>
                @endif
                @isset($match->localResult)
                    <a href="/acta/{{$match->idMatch}}/{{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}" class="group flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-[11px] font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800">
                        <i class="fa-solid fa-file-lines text-stone-500 group-hover:text-white dark:text-[#d4ff00] dark:group-hover:text-black transition-colors"></i> Acta
                    </a>
                @endisset
            </div>
        @endif
    </div>

@else
    <!-- UPCOMING STYLE (Apple Sports Agenda Fixture Card) -->
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl mb-3.5 overflow-hidden shadow-xs hover:border-stone-400 dark:hover:border-[#d4ff00] transition-all p-4">
        <!-- Top header info -->
        <div class="font-display text-[10px] font-extrabold text-stone-500 dark:text-stone-400 uppercase tracking-wider mb-3 truncate" title="{{ $match->groupName }}">
            <a href="/competicio/{{ $match->idGroup }}/{{ urlencode($match->groupName) }}" class="hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors">
                <span class="text-stone-400 dark:text-[#d4ff00] mr-1">●</span> {{ $match->groupName }}
            </a>
        </div>
        
        <!-- Teams Content -->
        <div class="flex items-center justify-between mb-3.5 gap-2">
            <!-- Local Team Name -->
            <div class="w-[38%] text-right font-extrabold text-xs md:text-sm text-stone-900 dark:text-stone-100 truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
                <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}" class="hover:text-stone-600 dark:hover:text-[#d4ff00] transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}
                </a>
            </div>

            <!-- Local Logo -->
            <div class="w-8 h-8 md:w-10 md:h-10 bg-white rounded-xl p-0.5 flex-shrink-0 flex items-center justify-center border border-stone-200 dark:border-stone-700/80 shadow-xs">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage1 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
            </div>
            
            <!-- Mid Time & Date Stamp (Light Grey Pill in Light Mode, Pitch Black in Dark Mode) -->
            <div class="flex flex-col items-center justify-center flex-shrink-0 bg-stone-100 dark:bg-black px-3 py-1.5 rounded-full border border-stone-200/80 dark:border-stone-800 shadow-xs min-w-[4rem] text-center font-display">
                <span class="text-xs md:text-sm font-black text-stone-900 dark:text-[#d4ff00] leading-none">
                    {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}
                </span>
                <span class="text-[9px] font-bold text-stone-500 dark:text-stone-400 mt-0.5 uppercase tracking-wider">
                    {{ \Carbon\Carbon::parse($match->matchDate)->format('d/m') }}
                </span>
            </div>
            
            <!-- Visitor Logo -->
            <div class="w-8 h-8 md:w-10 md:h-10 bg-white rounded-xl p-0.5 flex-shrink-0 flex items-center justify-center border border-stone-200 dark:border-stone-700/80 shadow-xs">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage2 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
            </div>

            <!-- Visitor Team Name -->
            <div class="w-[38%] text-left font-extrabold text-xs md:text-sm text-stone-900 dark:text-stone-100 truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
                <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}" class="hover:text-stone-600 dark:hover:text-[#d4ff00] transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
                </a>
            </div>
        </div>

        <!-- Pavelló Map Button -->
        @if(isset($match->lat))
            <div class="w-full mt-2">
                <a href="{{ $mapUrl }}" target="_blank" class="group w-full flex items-center justify-center gap-1.5 py-1.5 px-3 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-[11px] font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800">
                    <i class="fa-solid fa-location-dot text-stone-500 group-hover:text-white dark:text-[#d4ff00] dark:group-hover:text-black transition-colors"></i> Pavelló
                </a>
            </div>
        @endif

        <!-- Prediction Bar -->
        @if($match->idMatch > 1000000)
            <div class="mt-2.5 pt-2 border-t border-stone-100 dark:border-stone-800/80">
                <div id="predict_{{ $match->idMatch }}" class="font-display text-[10px] text-stone-400 text-center">
                    Calculant predicció...
                </div>
            </div>
        @endif
    </div>
@endif


