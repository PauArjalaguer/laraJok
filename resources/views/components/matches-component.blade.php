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
    <!-- TICKER STYLE (Modern Scoreboard Bar) -->
    <div class="inline-flex flex-col bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800/90 rounded-xl p-2.5 shadow-sm mr-3 flex-shrink-0 w-64 text-xs transition-all hover:border-[#f5c310]">
        <div class="font-display text-[10px] font-extrabold text-stone-500 dark:text-stone-400 uppercase tracking-wider mb-1 truncate">
            {{ $match->groupName }}
        </div>
        <div class="flex items-center justify-between font-display">
            <div class="flex items-center gap-1.5 truncate max-w-[78%]">
                <span class="truncate font-semibold text-stone-800 dark:text-stone-200" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}</span>
                <span class="font-black text-[#f5c310] px-1.5 py-0.5 bg-stone-900 dark:bg-black rounded-md text-[11px]">{{ $match->localResult }} - {{ $match->visitorResult }}</span>
                <span class="truncate font-semibold text-stone-800 dark:text-stone-200" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}</span>
            </div>
            <!-- LIVE/FI Status Stamp -->
            <div class="flex items-center gap-1 flex-shrink-0">
                @if(isset($match->localResult))
                    <span class="font-display text-[9px] font-bold text-stone-500 dark:text-stone-400 bg-stone-100 dark:bg-stone-800 px-1.5 py-0.5 rounded-md">FI</span>
                @else
                    <span class="font-display text-[9px] font-extrabold text-black bg-[#f5c310] px-1.5 py-0.5 rounded-md animate-pulse">LIVE</span>
                @endif
            </div>
        </div>
    </div>

@elseif ($type === 'result')
    <!-- RESULT STYLE (Modern Scoreboard Card) -->
    @php
        $userSavedData = \App\Models\User::userSavedData();
        $selectedTeams = \App\Models\User::userTeamsSelected($userSavedData) ?? [];
        $isLocalSelected = in_array($match->idLocal, $selectedTeams);
        $isVisitorSelected = in_array($match->idVisitor, $selectedTeams);
    @endphp
    <div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800/90 rounded-2xl mb-3.5 overflow-hidden shadow-sm hover:border-[#f5c310]/80 transition-all p-3.5">
        <!-- Top header info -->
        <div class="font-display text-[10px] font-bold text-stone-500 dark:text-stone-400 uppercase tracking-wider mb-2.5 truncate" title="{{ $match->groupName }}">
            <span class="text-[#f5c310] mr-1">●</span> {{ $match->groupName }}
        </div>
        
        <!-- Teams Content -->
        <div class="flex items-center justify-between mb-3 gap-2">
            <!-- Local Team Name -->
            <div class="w-[38%] text-right font-bold text-xs md:text-sm truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
                <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}" class="{{ $isLocalSelected ? 'text-[#f5c310]' : 'text-stone-900 dark:text-stone-100 hover:text-[#f5c310]' }} transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}
                </a>
            </div>

            <!-- Local Logo -->
            <div class="w-7 h-7 bg-white rounded-lg p-0.5 flex-shrink-0 flex items-center justify-center border border-stone-200 dark:border-stone-700 shadow-sm">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage1 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
            </div>
            
            <!-- Score Box -->
            <div class="flex items-center justify-center flex-shrink-0 bg-stone-900 dark:bg-black px-2.5 py-1 rounded-lg border border-stone-800 shadow-inner">
                <span class="font-display text-xs md:text-sm font-black text-[#f5c310]">
                    {{ $match->localResult }} - {{ $match->visitorResult }}
                </span>
            </div>
            
            <!-- Visitor Logo -->
            <div class="w-7 h-7 bg-white rounded-lg p-0.5 flex-shrink-0 flex items-center justify-center border border-stone-200 dark:border-stone-700 shadow-sm">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage2 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
            </div>

            <!-- Visitor Team Name -->
            <div class="w-[38%] text-left font-bold text-xs md:text-sm truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
                <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}" class="{{ $isVisitorSelected ? 'text-[#f5c310]' : 'text-stone-900 dark:text-stone-100 hover:text-[#f5c310]' }} transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
                </a>
            </div>
        </div>

        <!-- Info area: date, time, location & Acta link -->
        <div class="mt-2.5 pt-2 border-t border-stone-100 dark:border-stone-800/80 font-display text-[11px] font-medium text-stone-500 dark:text-stone-400 flex justify-between items-center gap-2">
            <div class="space-y-0.5 truncate max-w-[75%]">
                <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar text-[#f5c310]"></i>
                    <span>{{ \Carbon\Carbon::parse($match->matchDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}</span>
                </div>
                @if(isset($match->placeAddress))
                    <div class="flex items-center gap-1.5 truncate" title="{{ $match->placeAddress }}">
                        <i class="fa-solid fa-location-pin text-stone-400"></i>
                        <span class="truncate">{{ $match->placeAddress }}</span>
                    </div>
                @endif
            </div>
            @isset($match->localResult)
                <a class="hallmark-stamp bg-stone-100 dark:bg-stone-800 text-stone-800 dark:text-stone-200 hover:bg-[#f5c310] hover:text-black transition-colors" href="/acta/{{$match->idMatch}}/{{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}">
                    ACTA <i class="fa-solid fa-[#f5c310] fa-arrow-right ml-1 text-[8px]"></i>
                </a>
            @endisset
        </div>

        <!-- Pavelló Map Button -->
        @if(isset($match->lat))
            <div class="w-full mt-2.5">
                <a href="{{ $mapUrl }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-1.5 bg-stone-100 hover:bg-[#f5c310] dark:bg-stone-850 dark:hover:bg-[#f5c310] text-stone-800 dark:text-stone-200 hover:text-black font-display text-xs font-bold uppercase tracking-wider rounded-xl transition-all border border-stone-200 dark:border-stone-800">
                    <i class="fa-solid fa-location-dot text-[#f5c310]"></i> Pavelló on Maps
                </a>
            </div>
        @endif
    </div>

@else
    <!-- UPCOMING STYLE (Modern Agenda Fixture Card) -->
    <div class="bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800/90 rounded-2xl mb-3.5 overflow-hidden shadow-sm hover:border-[#f5c310]/80 transition-all p-3.5">
        <!-- Top header info -->
        <div class="font-display text-[10px] font-bold text-stone-500 dark:text-stone-400 uppercase tracking-wider mb-2.5 truncate" title="{{ $match->groupName }}">
            {{ $match->groupName }}
        </div>
        
        <!-- Teams Content -->
        <div class="flex items-center justify-between mb-3 gap-2">
            <!-- Local Team Name -->
            <div class="w-[38%] text-right font-bold text-xs md:text-sm text-stone-900 dark:text-stone-100 truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
                <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}" class="hover:text-[#f5c310] transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}
                </a>
            </div>

            <!-- Local Logo -->
            <div class="w-7 h-7 bg-white rounded-lg p-0.5 flex-shrink-0 flex items-center justify-center border border-stone-200 dark:border-stone-700 shadow-sm">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage1 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
            </div>
            
            <!-- Mid Time & Date Stamp -->
            <div class="flex flex-col items-center justify-center flex-shrink-0 bg-stone-100 dark:bg-stone-850 px-2 py-1 rounded-lg border border-stone-200 dark:border-stone-800 min-w-[3.5rem] text-center font-display">
                <span class="text-xs md:text-sm font-extrabold text-[#f5c310] leading-none">
                    {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}
                </span>
                <span class="text-[9px] font-bold text-stone-500 dark:text-stone-400 mt-0.5 uppercase tracking-wider">
                    {{ \Carbon\Carbon::parse($match->matchDate)->format('d/m') }}
                </span>
            </div>
            
            <!-- Visitor Logo -->
            <div class="w-7 h-7 bg-white rounded-lg p-0.5 flex-shrink-0 flex items-center justify-center border border-stone-200 dark:border-stone-700 shadow-sm">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage2 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
            </div>

            <!-- Visitor Team Name -->
            <div class="w-[38%] text-left font-bold text-xs md:text-sm text-stone-900 dark:text-stone-100 truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
                <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}" class="hover:text-[#f5c310] transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
                </a>
            </div>
        </div>

        <!-- Pavelló Map Button -->
        @if(isset($match->lat))
            <div class="w-full mt-2">
                <a href="{{ $mapUrl }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-1.5 bg-stone-100 hover:bg-[#f5c310] dark:bg-stone-850 dark:hover:bg-[#f5c310] text-stone-800 dark:text-stone-200 hover:text-black font-display text-xs font-bold uppercase tracking-wider rounded-xl transition-all border border-stone-200 dark:border-stone-800">
                    <i class="fa-solid fa-location-dot text-[#f5c310]"></i> Pavelló on Maps
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


