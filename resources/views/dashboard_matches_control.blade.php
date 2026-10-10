<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Control de Partits Obsolets
        </h2>
        <p class="text-xs text-gray-500 mt-1">
            Partits que el cron de la FECAPA no ha actualitzat (columna <code>updated</code>) en els darrers {{ $days }} dies. Probablement ja no existeixen a la FECAPA.
        </p>
    </x-slot>

    @php
        $keep = ['season' => $idSeason, 'days' => $days, 'stale' => $onlyStale ? '1' : '0'];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="bg-green-500 rounded-md p-4 text-white font-bold shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Filtres -->
            <form method="GET" action="{{ route('dashboard.matches.control') }}" class="bg-white shadow-sm sm:rounded-lg p-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Temporada</label>
                    <select name="season" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                        @foreach($seasons as $season)
                            <option value="{{ $season->idSeason }}" @selected($season->idSeason == $idSeason)>{{ $season->seasonName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dies sense actualitzar</label>
                    <input type="number" min="1" name="days" value="{{ $days }}" class="w-full text-xs rounded-md border-gray-300 shadow-sm" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mostrar</label>
                    <select name="stale" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                        <option value="1" @selected($onlyStale)>Només obsolets</option>
                        <option value="0" @selected(!$onlyStale)>Tots</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Grup</label>
                    <select name="group" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                        <option value="">Tots els grups</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->idGroup }}" @selected($g->idGroup == $idGroup)>{{ $g->leagueName }} · {{ $g->groupName ?? $g->idGroup }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-2 px-4 bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase rounded-md">Filtrar</button>
            </form>

            <!-- Resum per grup -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Resum per grup ({{ $groups->count() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Lliga</th>
                                <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Grup</th>
                                <th class="px-3 py-2 text-right font-bold text-gray-500 uppercase">Partits</th>
                                <th class="px-3 py-2 text-right font-bold text-gray-500 uppercase">Obsolets</th>
                                <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Darrera actualització</th>
                                <th class="px-3 py-2 text-right font-bold text-gray-500 uppercase">Accions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($groups as $g)
                                @php $allStale = $g->stale == $g->total; @endphp
                                <tr class="{{ $allStale ? 'bg-red-100 text-red-900' : ($g->stale > 0 ? 'bg-red-50' : '') }}">
                                    <td class="px-3 py-2">
                                        <span class="font-bold">{{ $g->leagueName }}</span>
                                        <span class="text-gray-500">{{ $g->categoryName }}</span>
                                        <span class="text-gray-400">#{{ $g->idLeague }}</span>
                                    </td>
                                    <td class="px-3 py-2">{{ $g->groupName ?? '(sense fase)' }} <span class="text-gray-400">#{{ $g->idGroup }}</span></td>
                                    <td class="px-3 py-2 text-right">{{ $g->total }}</td>
                                    <td class="px-3 py-2 text-right font-bold {{ $g->stale > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $g->stale }}</td>
                                    <td class="px-3 py-2">{{ $g->lastUpdated ? \Illuminate\Support\Carbon::parse($g->lastUpdated)->format('d/m/Y H:i') : 'mai' }}</td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        <a href="{{ route('dashboard.matches.control', $keep + ['group' => $g->idGroup]) }}" class="text-indigo-600 hover:underline font-bold mr-3">Veure partits</a>
                                        <form method="POST" action="{{ route('dashboard.matches.control.group.delete', $g->idGroup) }}" class="inline"
                                              onsubmit="return confirm('Eliminar el grup {{ addslashes($g->groupName ?? $g->idGroup) }} i els seus {{ $g->total }} partits?')">
                                            @csrf @method('DELETE')
                                            @foreach($keep as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                                            <button type="submit" class="text-red-700 hover:text-red-900 font-bold">Eliminar grup</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Cap grup amb partits obsolets 🎉</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Partits -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Partits ({{ $matches->total() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Partit</th>
                                <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Data</th>
                                <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Partit</th>
                                <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Lliga / Grup</th>
                                <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Actualitzat</th>
                                <th class="px-3 py-2 text-right font-bold text-gray-500 uppercase">Accions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($matches as $m)
                                <tr class="{{ $m->isStale ? 'bg-red-100 text-red-900' : '' }}">
                                    <td class="px-3 py-2 font-mono">
                                        <a href="/acta/{{ $m->idMatch }}/partit" target="_blank" class="hover:underline">{{ $m->idMatch }}</a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        {{ \Illuminate\Support\Carbon::parse($m->matchDate)->format('d/m/Y') }} {{ substr($m->matchHour, 0, 5) }}
                                        <span class="text-gray-500">J{{ $m->idRound }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $m->localTeam }}
                                        <span class="font-bold">{{ $m->localResult ?? '-' }} - {{ $m->visitorResult ?? '-' }}</span>
                                        {{ $m->visitorTeam }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="font-bold">{{ $m->leagueName }}</span><br>
                                        <span class="text-gray-500">{{ $m->groupName ?? '(sense fase)' }} #{{ $m->idGroup }}</span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap {{ $m->isStale ? 'font-bold text-red-700' : 'text-green-700' }}">
                                        @if($m->updated)
                                            {{ \Illuminate\Support\Carbon::parse($m->updated)->format('d/m/Y H:i') }}
                                            <br><span class="text-[10px]">{{ \Illuminate\Support\Carbon::parse($m->updated)->locale('ca')->diffForHumans() }}</span>
                                        @else
                                            mai
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap space-x-2">
                                        <form method="POST" action="{{ route('dashboard.matches.control.match.delete', $m->idMatch) }}" class="inline"
                                              onsubmit="return confirm('Eliminar el partit {{ $m->idMatch }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2 py-1 rounded bg-white border border-red-300 text-red-700 hover:bg-red-50 font-bold">Eliminar partit</button>
                                        </form>
                                        <form method="POST" action="{{ route('dashboard.matches.control.match-group.delete', $m->idMatch) }}" class="inline"
                                              onsubmit="return confirm('Eliminar el partit {{ $m->idMatch }} i TOT el grup {{ addslashes($m->groupName ?? $m->idGroup) }} (tots els seus partits, actes i classificació)?')">
                                            @csrf @method('DELETE')
                                            @foreach($keep as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                                            <button type="submit" class="px-2 py-1 rounded bg-red-600 text-white hover:bg-red-700 font-bold">Eliminar partit i grup</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Cap partit.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $matches->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
