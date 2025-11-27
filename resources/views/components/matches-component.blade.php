<div class="mb-4 rounded-2xl overflow-hidden shadow-md bg-white border border-neutral-200">

    <!-- Header -->
    <div class="bg-neutral-700 text-white px-4 py-2 flex items-center justify-between">
        <a href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}" 
           class="font-semibold text-sm hover:text-neutral-200 transition">
            {{$match->groupName}}
        </a>

        <div class="flex items-center gap-2 text-sm">
            <a href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}/{{$match->idRound}}"
               class="hover:text-neutral-300 transition">
                {{ strlen($match->idRound)>2 ? '' : 'Jornada ' }} {{$match->idRound}}
            </a> |

            @isset($match->localResult)
                <a href="/acta/{{$match->idMatch}}/{{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}" 
                   class="hover:text-neutral-300 transition"
                   aria-label="Acta del partit">
                    <i class="fa-solid fa-chart-line"></i>
                </a>
            @endisset

            @php $userAgent = $_SERVER['HTTP_USER_AGENT']; @endphp

            @if(isset($match->lat) && !isset($match->localResult))
                @if(isset($userAgent) && $userAgent == 'iOSWebView')
                    <a target="_blank" 
                       href="https://maps.apple.com/?q={{$match->lat}},{{$match->lon}}" 
                       class="hover:text-neutral-300 transition"
                       aria-label="Ubicació del partit">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </a>
                @else
                    <a target="_blank" 
                       href="https://maps.google.com/?q={{$match->lat}},{{$match->lon}}" 
                       class="hover:text-neutral-300 transition"
                       aria-label="Ubicació del partit">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- Match content -->
    <div class="grid grid-cols-3 items-center text-center py-4 bg-white hover:bg-neutral-50 transition">

        <!-- Local -->
        <div class="flex flex-col items-center gap-2 px-2">
            <img src="{{$match->clubImage1}}"
                 class="h-14 object-contain mix-blend-multiply"
                 alt="Escut {{App\Http\Controllers\TeamsController::teamFormat($match->localTeam)}}">
            
            <a href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}"
               class="font-medium text-neutral-800 hover:text-neutral-500 transition">
               {{App\Http\Controllers\TeamsController::teamFormat($match->localTeam)}}
            </a>
        </div>

        <!-- Center date/result block -->
        <div class="flex flex-col items-center gap-1">
            <div class="text-neutral-600 text-sm">
                {{ \Carbon\Carbon::parse($match->matchDate)->format('d-m') }}
                {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}
            </div>

            <div class="text-2xl font-bold text-neutral-700">
                {{$match->localResult}} - {{$match->visitorResult}}
            </div>
        </div>

        <!-- Visitor -->
        <div class="flex flex-col items-center gap-2 px-2">
            <img src="{{$match->clubImage2}}"
                 class="h-14 object-contain mix-blend-multiply"
                 alt="Escut {{App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam)}}">
            
            <a href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}"
               class="font-medium text-neutral-800 hover:text-neutral-500 transition">
               {{App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam)}}
            </a>
        </div>

    </div>
    @if($match->idMatch > 1000000)
    <div id="predict_{{ $match->idMatch }}" class="text-center text-sm py-2">Calculant predicció</div>
    @else
    <div class="h-5"></div>
    @endif
</div>


