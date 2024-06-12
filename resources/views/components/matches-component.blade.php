 <div class="mb-2 shadow-md  shadow-slate-700 mt-2">
     <div class="bg-slate-700 w-full  border-[1px] border-b-[0px] border-slate-500 flex ">
         <div class="inline w-2/3  pl-2">
             <a class="text-white font-bold text-xs " href="/competicio/{{$matchArray->idGroup}}/{{urlencode($matchArray->groupName)}}">{{$matchArray->groupName}} {{-- | {{$matchArray->seasonName}} --}}</a>
         </div>
         <div class="w-1/3 text-right pr-2">
             <span class="hidden md:inline w-full text-white font-bold text-xs">{{strlen($matchArray->idRound)>2 ? '' : 'Jornada ' }} {{$matchArray->idRound}}</span>
             @isset($matchArray->localResult)
             <span class=" text-white font-bold text-xs"> | <a href="/acta/{{$matchArray->idMatch}}/{{urlencode($matchArray->localTeam)}}-{{urlencode($matchArray->visitorTeam)}}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 inline pb-1">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
</svg>

                 </a></span>
             @endisset
         </div>
     </div>
     <div class="bg-white w-full h-full  border-solid   hover:bg-slate-50 transition-all   flex text-sm items-center">
         <div class="p-4 w-5/12 text-left text-xs md:text-sm ">
             <img class="hidden md:inline w-2/12  max-h-12 max-w-12 mx-2" src={{$matchArray->clubImage1}} alt="Escut de {{App\Http\Controllers\TeamsController::teamFormat($matchArray->localTeam)}}">
             <a href="/equip/{{$matchArray->idLocal}}/{{urlencode($matchArray->localTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($matchArray->localTeam)}}</a>
         </div>
         <div class="py-4 px-2 w-2/12 text-center bg-slate-400 text-gray-800   h-full">
             <span class="text-[10px] lg:text-sm">{{ \Carbon\Carbon::parse($matchArray->matchDate)->format('d-m')}} {{ \Carbon\Carbon::parse($matchArray->matchHour)->format('H:i')}}</span>
             <br> <span class="text-white font-bold md:text-lg">{{$matchArray->localResult}} - {{$matchArray->visitorResult}} </span><br />

         </div>
         <div class="p-4 w-5/12 text-right  text-xs md:text-sm">
             <a href="/equip/{{$matchArray->idVisitor}}/{{urlencode($matchArray->visitorTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($matchArray->visitorTeam)}}</a>
             <img class="hidden md:inline w-2/12  max-h-12 max-w-12  mx-2" src={{$matchArray->clubImage2}} alt="Escut de {{App\Http\Controllers\TeamsController::teamFormat($matchArray->visitorTeam)}}">
         </div>
     </div>
 </div>
