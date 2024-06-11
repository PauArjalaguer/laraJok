 <div class="mb-2 shadow-md  shadow-slate-700 mt-2">
     <div class="bg-slate-700 text-center w-full  border-[1px] border-b-[0px] border-slate-500  ">
         <a class="text-white font-bold text-xs" href="/competicio/{{$matchArray->idGroup}}/{{urlencode($matchArray->leagueName)}}">{{$matchArray->leagueName}} | {{$matchArray->seasonName}} </a>
                     <span class="hidden md:inline w-full text-white font-bold text-xs">| {{strlen($matchArray->idRound)>2 ? '' : 'Jornada ' }} {{$matchArray->idRound}}</span>
     </div>
     <div class="bg-white w-full h-full  border-solid   hover:bg-slate-50 transition-all   flex text-sm items-center">
         <div class="p-4 w-5/12 text-left text-xs md:text-sm ">
             <img class="hidden md:inline w-2/12  max-h-12 max-w-12 mx-2" src={{$matchArray->clubImage1}}>
             <a href="/equip/{{$matchArray->idLocal}}/{{urlencode($matchArray->localTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($matchArray->localTeam)}}</a>
         </div>
         <div class="py-4 px-2 w-2/12 text-center bg-slate-400 text-gray-800   h-full">
             <span class="text-[10px] lg:text-sm">{{ \Carbon\Carbon::parse($matchArray->matchDate)->format('d-m')}} {{ \Carbon\Carbon::parse($matchArray->matchHour)->format('H:i')}}</span>
             <br>
   
            
             <span class="text-white font-bold md:text-lg">{{$matchArray->localResult}} - {{$matchArray->visitorResult}} </span><br />
 @isset($matchArray->localResult)
             <span class=""><a href="/acta/{{$matchArray->idMatch}}/{{urlencode($matchArray->localTeam)}}-{{urlencode($matchArray->visitorTeam)}}">Veure acta</a></span>
             @endisset

         </div>
         <div class="p-4 w-5/12 text-right  text-xs md:text-sm">
             <a href="/equip/{{$matchArray->idVisitor}}/{{urlencode($matchArray->visitorTeam)}}">{{App\Http\Controllers\TeamsController::teamFormat($matchArray->visitorTeam)}}</a>
             <img class="hidden md:inline w-2/12  max-h-12 max-w-12  mx-2" src={{$matchArray->clubImage2}}>
         </div>
     </div>
 </div>
