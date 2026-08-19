@extends('layout.mainlayout')
@section('title', "Agenda de Partits :: JOK.cat ")
@section('content')

<!-- UNIFIED HEADER (Ultra-Clean Apple Sports with Live Search) -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight">
                Agenda de Partits Programats
            </h1>
        </div>

        <!-- SEARCH INPUT -->
        <div class="relative w-full md:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-xs"></i>
            <input type="text" id="searchInput" placeholder="Cerca equip, competició o data..." class="w-full pl-9 pr-4 py-2 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-900 dark:text-white border border-stone-200 dark:border-stone-800 focus:outline-none focus:border-[#1c1917] dark:focus:border-[#1c1917] text-xs font-display font-medium shadow-xs transition-colors" oninput="filterAgenda()" />
        </div>
    </div>
</div>

<!-- AGENDA TABLE CONTAINER -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl overflow-hidden shadow-xs mb-8 font-display">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="agendaTable">
            <thead class="bg-stone-950 text-white dark:bg-black text-[10px] uppercase font-black tracking-wider">
                <tr>
                    <th class="py-3 px-4">Data i Hora</th>
                    <th class="py-3 px-3">Competició</th>
                    <th class="py-3 px-3 text-right">Local</th>
                    <th class="py-3 px-3 text-center">Resultat</th>
                    <th class="py-3 px-3 text-left">Visitant</th>
                    <th class="py-3 px-4 text-right">Acta</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 dark:divide-stone-800/80">
                @foreach($agenda as $match)
                @php
                    $localName = App\Http\Controllers\TeamsController::teamFormat($match->localTeamName);
                    $visitorName = App\Http\Controllers\TeamsController::teamFormat($match->visitorTeamName);
                    $matchLabel = $localName . '-' . $visitorName;
                    $hasResult = (isset($match->localResult) && isset($match->visitorResult) && $match->localResult !== '' && $match->visitorResult !== '');
                @endphp
                <tr class="agenda-row hover:bg-stone-50 dark:hover:bg-primary/50 transition-colors text-xs font-display">
                    <!-- Date, Time & Round Badge -->
                    <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 whitespace-nowrap">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 bg-stone-100 dark:bg-stone-900 px-2.5 py-1 rounded-full text-xs font-black border border-stone-200/60 dark:border-stone-800 text-stone-800 dark:text-stone-200">
                                <i class="fa-regular fa-calendar text-stone-900 dark:text-white"></i>
                                {{ \Carbon\Carbon::parse($match->matchDate)->format('d/m/Y') }}
                                <span class="text-stone-500 dark:text-stone-400 font-extrabold ml-1">{{ \Carbon\Carbon::parse($match->matchHour)->format('H:i') }}</span>
                            </span>
                            @if(isset($match->idRound) && $match->idRound !== '')
                                <span class="inline-block bg-stone-100 dark:bg-stone-900 px-2 py-0.5 rounded-full text-[10px] font-black border border-stone-200/60 dark:border-stone-800 text-stone-600 dark:text-stone-400" title="Jornada {{ $match->idRound }}">
                                    {{ $match->idRound }}
                                </span>
                            @endif
                        </div>
                    </td>

                    <!-- Competition -->
                    <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 font-extrabold text-stone-700 dark:text-stone-300">
                        <a href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}" class="hover:text-stone-900 dark:hover:text-white transition-colors truncate block max-w-[200px]" title="{{$match->groupName}}">
                            {{$match->groupName}}
                        </a>
                    </td>

                    <!-- Local Team with Shield -->
                    <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 text-right font-black">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/equip/{{$match->localTeamId}}/{{urlencode($match->localTeamName)}}" class="text-stone-900 dark:text-stone-100 hover:text-stone-900 dark:hover:text-white transition-colors truncate">
                                {{$localName}}
                            </a>
                            @if(!empty($match->clubImage1))
                                <img src="{{$match->clubImage1}}" alt="{{$localName}}" class="w-6 h-6 object-contain flex-shrink-0 bg-white dark:bg-transparent" />
                            @endif
                        </div>
                    </td>

                    <!-- Result -->
                    <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 text-center whitespace-nowrap">
                        @if($hasResult)
                            <span class="inline-flex items-center justify-center bg-stone-900 text-stone-900 dark:text-white dark:bg-black font-black text-xs px-2.5 py-1 rounded-full shadow-xs">
                                {{$match->localResult}} - {{$match->visitorResult}}
                            </span>
                        @else
                            <span class="text-stone-400 font-bold text-[11px]">vs</span>
                        @endif
                    </td>

                    <!-- Visitor Team with Shield -->
                    <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 text-left font-black">
                        <div class="flex items-center justify-start gap-2">
                            @if(!empty($match->clubImage2))
                                <img src="{{$match->clubImage2}}" alt="{{$visitorName}}" class="w-6 h-6 object-contain flex-shrink-0 bg-white dark:bg-transparent" />
                            @endif
                            <a href="/equip/{{$match->visitorTeamId}}/{{urlencode($match->visitorTeamName)}}" class="text-stone-900 dark:text-stone-100 hover:text-stone-900 dark:hover:text-white transition-colors truncate">
                                {{$visitorName}}
                            </a>
                        </div>
                    </td>

                    <!-- Match Report Link -->
                    <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 text-right whitespace-nowrap">
                        <a href="/acta/{{$match->idMatch}}/{{urlencode($matchLabel)}}" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-stone-100 text-stone-800 dark:bg-stone-900 dark:text-stone-200 hover:bg-primary text-black dark:bg-stone-800 dark:text-white hover:text-black dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black transition-all text-xs font-black border border-stone-200/80 dark:border-stone-800 shadow-xs">
                            Acta <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function filterAgenda() {
        const query = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#agendaTable tbody tr.agenda-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        let noResultsTr = document.getElementById('noResultsAgenda');
        if (visibleCount === 0 && rows.length > 0) {
            if (!noResultsTr) {
                noResultsTr = document.createElement('tr');
                noResultsTr.id = 'noResultsAgenda';
                noResultsTr.innerHTML = `
                    <td colspan="6" class="p-8 text-center text-xs font-bold text-stone-500 dark:text-stone-400">
                        <i class="fa-solid fa-magnifying-glass text-2xl text-stone-400 mb-2 block"></i>
                        No s'ha trobat cap partit que coincideixi amb la cerca.
                    </td>
                `;
                document.querySelector('#agendaTable tbody').appendChild(noResultsTr);
            }
            noResultsTr.style.display = '';
        } else if (noResultsTr) {
            noResultsTr.style.display = 'none';
        }
    }
</script>
@endsection