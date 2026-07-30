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
    <!-- TICKER STYLE (Top Horizontal Bar) -->
    <div class="inline-flex flex-col bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800/80 rounded-xl p-3 shadow-sm mr-3 flex-shrink-0 w-64 text-xs transition-transform hover:scale-[1.02]">
        <div class="text-[9px] font-bold text-stone-400 dark:text-neutral-500 uppercase tracking-wider mb-1 truncate">
            {{ $match->groupName }}:
        </div>
        <div class="flex items-center justify-between font-semibold text-stone-800 dark:text-neutral-200">
            <div class="flex items-center gap-1.5 truncate max-w-[75%]">
                <span class="truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}</span>
                <span class="font-extrabold text-[#f5c310]">{{ $match->localResult }} - {{ $match->visitorResult }}</span>
                <span class="truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}</span>
            </div>
            <!-- LIVE/FI Status Dot -->
            <div class="flex items-center gap-1 flex-shrink-0">
                @if(isset($match->localResult))
                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400 dark:bg-neutral-600"></span>
                    <span class="text-[9px] font-black text-stone-450 dark:text-neutral-500 uppercase">FI</span>
                @else
                    <span class="w-1.5 h-1.5 rounded-full bg-[#f5c310] animate-pulse"></span>
                    <span class="text-[9px] font-black text-[#f5c310] uppercase">LIVE</span>
                @endif
            </div>
        </div>
    </div>

@elseif ($type === 'result')
    <!-- RESULT STYLE (Right Column Results) -->
    @php
        $userSavedData = \App\Models\User::userSavedData();
        $selectedTeams = \App\Models\User::userTeamsSelected($userSavedData) ?? [];
        $isLocalSelected = in_array($match->idLocal, $selectedTeams);
        $isVisitorSelected = in_array($match->idVisitor, $selectedTeams);
    @endphp
    <div class="bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800 rounded-2xl mb-4 overflow-hidden shadow-sm hover:shadow-md transition-all p-4">
        <!-- Top header info (Same as left) -->
        <div class="text-[9px] font-bold text-stone-400 dark:text-neutral-500 uppercase tracking-wider mb-3 truncate" title="{{ $match->groupName }}: {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }} vs. {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
            {{ $match->groupName }}: {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }} vs. {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
        </div>
        
        <!-- Teams Content (Horizontal row, same as left) -->
        <div class="flex items-center justify-between mb-4 gap-2">
            <!-- Local Team Name -->
            <div class="w-[38%] text-right font-bold text-xs md:text-sm truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
                <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}" class="{{ $isLocalSelected ? 'text-[#f5c310] hover:text-[#e5b50e]' : 'text-stone-850 dark:text-neutral-200 hover:text-[#f5c310]' }} transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}
                </a>
            </div>

            <!-- Local Logo -->
            <div class="w-7 h-7 bg-white rounded-full p-0.5 flex-shrink-0 flex items-center justify-center overflow-hidden border border-stone-200 dark:border-neutral-700 shadow-sm">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage1 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
            </div>
            
            <!-- Mid Result (instead of time) -->
            <div class="flex flex-col items-center justify-center flex-shrink-0 min-w-[4rem] text-center">
                <span class="text-sm md:text-base font-extrabold text-[#f5c310] leading-none whitespace-nowrap">
                    {{ $match->localResult }} - {{ $match->visitorResult }}
                </span>
            </div>
            
            <!-- Visitor Logo -->
            <div class="w-7 h-7 bg-white rounded-full p-0.5 flex-shrink-0 flex items-center justify-center overflow-hidden border border-stone-200 dark:border-neutral-700 shadow-sm">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage2 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
            </div>

            <!-- Visitor Team Name -->
            <div class="w-[38%] text-left font-bold text-xs md:text-sm truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
                <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}" class="{{ $isVisitorSelected ? 'text-[#f5c310] hover:text-[#e5b50e]' : 'text-stone-850 dark:text-neutral-200 hover:text-[#f5c310]' }} transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
                </a>
            </div>
        </div>

        <!-- Info area: date, time, location on the left, and Acta link on the right -->
        <div class="mt-3 pt-2 border-t border-stone-100 dark:border-neutral-800 text-[10px] text-stone-500 dark:text-neutral-400 flex justify-between items-center gap-2">
            <div class="space-y-1 truncate max-w-[75%]">
                <div class="flex items-center gap-1">
                    <i class="fa-regular fa-calendar text-stone-400 dark:text-neutral-500"></i>
                    <span>{{ \Carbon\Carbon::parse($match->matchDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}</span>
                </div>
                @if(isset($match->placeAddress))
                    <div class="flex items-center gap-1 truncate" title="{{ $match->placeAddress }}">
                        <i class="fa-solid fa-location-pin text-stone-400 dark:text-neutral-500"></i>
                        <span class="truncate">{{ $match->placeAddress }}</span>
                    </div>
                @endif
            </div>
            @isset($match->localResult)
                <a class="hover:text-[#f5c310] text-[10px] text-stone-450 dark:text-neutral-400 flex items-center gap-1 transition-colors flex-shrink-0 font-bold" href="/acta/{{$match->idMatch}}/{{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}">
                    Acta <i class="fa-solid fa-chart-line text-[9px]"></i>
                </a>
            @endisset
        </div>

        <!-- Pavelló Map Button -->
        @if(isset($match->lat))
            <div class="w-full mt-3">
                <a href="{{ $mapUrl }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-2 bg-[#f5c310] hover:bg-[#e5b50e] text-stone-950 text-xs font-bold rounded-xl transition-all shadow-sm">
                    <i class="fa-solid fa-location-dot"></i> Pavelló on Maps
                </a>
            </div>
        @endif
    </div>

@else
    <!-- UPCOMING STYLE (Left Column Agenda) -->
    <div class="bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800 rounded-2xl mb-4 overflow-hidden shadow-sm hover:shadow-md transition-all p-4">
        <!-- Top header info (No background, plain text, uppercase, small) -->
        <div class="text-[9px] font-bold text-stone-400 dark:text-neutral-500 uppercase tracking-wider mb-3 truncate" title="{{ $match->groupName }}: {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }} vs. {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
            {{ $match->groupName }}: {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }} vs. {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
        </div>
        
        <!-- Teams Content (Horizontal row) -->
        <div class="flex items-center justify-between mb-4 gap-2">
            <!-- Local Team Name -->
            <div class="w-[38%] text-right font-bold text-xs md:text-sm text-stone-850 dark:text-neutral-200 truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
                <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}" class="hover:text-[#f5c310] transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}
                </a>
            </div>

            <!-- Local Logo -->
            <div class="w-7 h-7 bg-white rounded-full p-0.5 flex-shrink-0 flex items-center justify-center overflow-hidden border border-stone-200 dark:border-neutral-700 shadow-sm">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage1 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
            </div>
            
            <!-- Mid Time & Date info -->
            <div class="flex flex-col items-center justify-center flex-shrink-0 min-w-[3.5rem] text-center">
                <span class="text-base font-black text-stone-900 dark:text-white leading-none">
                    {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}
                </span>
                <span class="text-[9px] font-semibold text-stone-400 dark:text-neutral-500 mt-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::parse($match->matchDate)->format('d/m') }}
                </span>
            </div>
            
            <!-- Visitor Logo -->
            <div class="w-7 h-7 bg-white rounded-full p-0.5 flex-shrink-0 flex items-center justify-center overflow-hidden border border-stone-200 dark:border-neutral-700 shadow-sm">
                <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage2 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
            </div>

            <!-- Visitor Team Name -->
            <div class="w-[38%] text-left font-bold text-xs md:text-sm text-stone-850 dark:text-neutral-200 truncate" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
                <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}" class="hover:text-[#f5c310] transition-colors">
                    {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
                </a>
            </div>
        </div>

        <!-- Pavelló Map Button -->
        @if(isset($match->lat))
            <div class="w-full mt-2">
                <a href="{{ $mapUrl }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-2 bg-[#f5c310] hover:bg-[#e5b50e] text-stone-950 text-xs font-bold rounded-xl transition-all shadow-sm">
                    <i class="fa-solid fa-location-dot"></i>
                    Pavelló on Maps
                </a>
            </div>
        @endif

        <!-- Prediction Bar (if applicable) -->
        @if($match->idMatch > 1000000)
            <div class="mt-3 pt-2 border-t border-stone-100 dark:border-neutral-850">
                <div id="predict_{{ $match->idMatch }}" class="text-[11px] text-stone-400 dark:text-neutral-500 text-center">
                    Calculant predicció...
                </div>
            </div>
        @endif
    </div>
@endif
