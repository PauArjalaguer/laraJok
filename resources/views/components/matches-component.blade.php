 <div class="mb-2 shadow-md shadow-neutral-700 mt-2">
     <div class="bg-neutral-700 w-full  border-[2px] border-b-[0px] border-neutral-700 flex ">
         <div class="inline w-2/3  pl-2">
             <a class="text-white font-bold text-xs active:text-neutral-300 " href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}">{{$match->groupName}} {{-- | {{$match->seasonName}} --}}</a>
         </div>
         <div class="w-1/3 text-right pr-2">
             <span class="inline w-full text-white font-bold text-xs">
                 <a class="text-white font-bold text-xs active:text-neutral-300 " href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}/{{$match->idRound}}">{{strlen($match->idRound)>2 ? '' : 'Jornada ' }} {{$match->idRound}}</a></span>
             @isset($match->localResult)          
                <a class="text-white" href="/acta/{{$match->idMatch}}/{{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}" aria-label="Acta del partit {{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}">  | <i class="fa-solid fa-chart-line"></i></a>
            </span>
             @endisset

             @php
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
            @endphp
            @isset($match->lat)
                @if(isset($userAgent) && $userAgent == 'iOSWebView')
                   <a class="text-white" target="_blank" href="https://maps.apple.com/?q={{$match->lat}},{{$match->lon}}" aria-label="Ubicació del partit {{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}"> | <i class="fa-solid fa-location-crosshairs"></i></a>
                @else
                   <a class="text-white" target="_blank" href="https://maps.google.com/?q={{$match->lat}},{{$match->lon}}" aria-label="Ubicació del partit {{urlencode($match->localTeam)}}-{{urlencode($match->visitorTeam)}}"> | <i class="fa-solid fa-location-crosshairs"></i></a>
                @endif
            @endisset
         </div>
     </div>
     <div class="bg-white w-full h-full hover:bg-neutral-50 transition-all   flex text-sm items-center">
         <div class="p-2 w-5/12 text-left text-sm md:text-md  ">
             <img class="hidden md:inline w-2/12  max-h-12 max-w-12 mx-2 mix-blend-multiply " src={{$match->clubImage1}} alt="Escut de {{App\Http\Controllers\TeamsController::teamFormat($match->localTeam)}}">
             <a class="active:text-neutral-300" href="/equip/{{$match->idLocal}}/{{urlencode($match->localTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($match->localTeam)}}</a>
         </div>
         <div class="py-4 px-2 w-2/12 text-center bg-neutral-600 text-white  h-full">
             <span class="text-[10px] lg:text-sm">{{ \Carbon\Carbon::parse($match->matchDate)->format('d-m')}} {{ \Carbon\Carbon::parse($match->matchHour)->format('H:i')}}</span>
             <br> <span class="text-white font-bold md:text-lg">{{$match->localResult}} - {{$match->visitorResult}} </span><br />

         </div>
         <div class="p-2 w-5/12 text-right  text-sm md:text-md ">
             <a class="active:text-neutral-300" href="/equip/{{$match->idVisitor}}/{{urlencode($match->visitorTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam)}}</a>
             <img class="hidden md:inline w-2/12  max-h-12 max-w-12  mx-2 mix-blend-multiply" src={{$match->clubImage2}} alt="Escut de {{App\Http\Controllers\TeamsController::teamFormat($match->visitorTeam)}}">
         </div>
     </div>
 </div>
 @if($match->idMatch>1000000)
 <div id="predict_<?= $match->idMatch ?>">Calculant predicció</div>
 <script>
  
</script>
@else
    <div class="h-3">&nbsp;</div>
@endif

{{--  {{ \App\Http\Controllers\MatchesController::predict($match->idMatch)}} --}}
