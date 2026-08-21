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
    <div class="inline-flex flex-col bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3 shadow-xs mr-3 flex-shrink-0 w-64 text-xs transition-all hover:border-primary dark:hover:border-stone-600">
        <div class="font-display text-[10px] font-extrabold text-stone-500 dark:text-stone-400 uppercase tracking-wider mb-1 truncate" title="{{ $match->groupName }}">
            <a href="/competicio/{{ $match->idGroup }}/{{ urlencode($match->groupName) }}" class="hover:text-stone-900 dark:hover:text-white transition-colors">
                {{ $match->groupName }}
            </a>
        </div>
        <div class="flex items-center justify-between font-display">
            <div class="flex items-center gap-1.5 truncate max-w-[78%]">
                <span class="truncate font-bold text-stone-800 dark:text-stone-200" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}</span>
                <span class="font-black px-2 py-0.5 bg-primary text-primary-text dark:bg-stone-800 dark:text-white rounded-full text-[11px]">{{ $match->localResult }} - {{ $match->visitorResult }}</span>
                <span class="truncate font-bold text-stone-800 dark:text-stone-200" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}</span>
            </div>
            <!-- LIVE/FI Status Stamp -->
            <div class="flex items-center gap-1 flex-shrink-0">
                @if(isset($match->localResult))
                    <span class="font-display text-[9px] font-bold text-stone-500 dark:text-stone-400 bg-stone-100 dark:bg-stone-800 px-2 py-0.5 rounded-full">FI</span>
                @else
                    <span class="font-display text-[9px] font-black bg-primary text-primary-text dark:bg-stone-800 dark:text-white px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
                @endif
            </div>
        </div>
    </div>

@else
    <!-- UNIFIED APPLE SPORTS MATCH CARD (Played & Upcoming) -->
    @php
        $userSavedData = \App\Models\User::userSavedData();
        $selectedTeams = \App\Models\User::userTeamsSelected($userSavedData) ?? [];
        $isLocalSelected = in_array($match->idLocal, $selectedTeams);
        $isVisitorSelected = in_array($match->idVisitor, $selectedTeams);
        $isPlayed = isset($match->localResult);
    @endphp
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl mb-3.5 overflow-hidden shadow-xs hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all p-4">
        <!-- Top header info -->
        <div class="font-display text-[10px] font-extrabold text-stone-500 dark:text-stone-400 uppercase tracking-wider mb-3 truncate flex items-center justify-between gap-2" title="{{ $match->groupName }}">
            <a href="/competicio/{{ $match->idGroup }}/{{ urlencode($match->groupName) }}" class="hover:text-stone-900 dark:hover:text-white transition-colors truncate flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-primary text-primary-text dark:bg-stone-800 dark:text-white inline-block flex-shrink-0 shadow-xs"></span>
                <span class="truncate">{{ $match->groupName }}</span>
            </a>
            @if(!empty($match->hasVideo))
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-600/10 text-red-600 dark:bg-red-950/50 dark:text-red-400 text-[9px] font-black tracking-wider uppercase border border-red-200 dark:border-red-900/60 shadow-2xs flex-shrink-0">
                    <i class="fa-brands fa-youtube"></i> Amb Vídeo
                </span>
            @endif
        </div>
        
        <!-- Teams Content (Shield top, Name below, Score center) -->
        <div class="flex items-center justify-between mb-3 gap-2 font-display">
            <!-- Local Team Side (Shield + Name Below) -->
            <div class="w-[40%] min-w-0 flex flex-col items-center text-center">
                <!-- Local Logo -->
                <div class="w-7 h-7 md:w-8 md:h-8 bg-white dark:bg-transparent rounded-lg p-0.5 flex-shrink-0 flex items-center justify-center mb-1">
                    <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage1 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
                </div>
                <!-- Local Team Name -->
                <div class="w-full font-extrabold text-[11px] md:text-xs leading-tight line-clamp-2" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}">
                    <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}" class="{{ $isLocalSelected ? 'text-stone-900 font-black dark:text-white' : 'text-stone-900 dark:text-stone-100 hover:text-stone-600 dark:hover:text-white' }} transition-colors">
                        {{ App\Http\Controllers\TeamsController::teamFormat($match->localTeam) }}
                    </a>
                </div>
            </div>
            
            <!-- Mid Center Box (Groc Daurat en Light / Monocrom en Dark) -->
            <div class="flex items-center justify-center flex-shrink-0 bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 px-3.5 py-1.5 rounded-full shadow-xs min-w-[3.6rem] text-center font-display">
                <span class="text-xs md:text-sm font-black text-primary-text dark:text-white leading-none">
                    @if($isPlayed)
                        {{ $match->localResult }} - {{ $match->visitorResult }}
                    @else
                        - 
                    @endif
                </span>
            </div>
            
            <!-- Visitor Team Side (Shield + Name Below) -->
            <div class="w-[40%] min-w-0 flex flex-col items-center text-center">
                <!-- Visitor Logo -->
                <div class="w-7 h-7 md:w-8 md:h-8 bg-white dark:bg-transparent rounded-lg p-0.5 flex-shrink-0 flex items-center justify-center mb-1">
                    <img class="max-w-full max-h-full object-contain" src="{{ $match->clubImage2 }}" alt="Escut de {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
                </div>
                <!-- Visitor Team Name -->
                <div class="w-full font-extrabold text-[11px] md:text-xs leading-tight line-clamp-2" title="{{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}">
                    <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}" class="{{ $isVisitorSelected ? 'text-stone-900 font-black dark:text-white' : 'text-stone-900 dark:text-stone-100 hover:text-stone-600 dark:hover:text-white' }} transition-colors">
                        {{ App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam) }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Info area: date, time, location (Identical for ALL matches!) -->
        <div class="mt-3 pt-2.5 border-t border-stone-100 dark:border-stone-800/80 font-display text-[11px] font-bold text-stone-500 dark:text-stone-400 space-y-0.5">
            <div class="flex items-center gap-1.5">
                <i class="fa-regular fa-calendar text-stone-400 dark:text-white"></i>
                <span>{{ \Carbon\Carbon::parse($match->matchDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}</span>
            </div>
            @php
                $locationText = !empty($match->placeAddress) ? $match->placeAddress : (!empty($match->placeName) ? $match->placeName : null);
            @endphp
            @if(!empty($locationText))
                <div class="flex items-center gap-1.5 truncate" title="{{ $locationText }}">
                    <i class="fa-solid fa-location-pin text-stone-400 dark:text-stone-500"></i>
                    <span class="truncate">{{ $locationText }}</span>
                </div>
            @endif
        </div>

        <!-- Action Buttons (Pavelló & Acta) -->
        @if(isset($match->lat) || $isPlayed)
            <div class="flex items-center gap-2 mt-3 pt-1">
                @if(isset($match->lat))
                    <a href="{{ $mapUrl }}" target="_blank" class="group flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 bg-stone-100 hover:bg-primary text-stone-800 hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-display text-[11px] font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                        <i class="fa-solid fa-location-dot text-stone-500 group-hover:text-primary-text dark:text-white dark:group-hover:text-primary-text transition-colors"></i> Pavelló
                    </a>
                @endif
                @if($isPlayed)
                    <a href="/acta/{{$match->idMatch}}/{{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}" class="group flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 bg-stone-100 hover:bg-primary text-stone-800 hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-display text-[11px] font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                        <i class="fa-solid fa-file-lines text-stone-500 group-hover:text-primary-text dark:text-white dark:group-hover:text-primary-text transition-colors"></i> Acta
                    </a>
                @endif
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
