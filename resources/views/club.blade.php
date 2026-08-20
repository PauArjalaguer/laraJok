@extends('layout.mainlayout')
@section('title', $clubInfo[0]->clubName." :: JOK.cat ")
@section('content')

<!-- CLUB HEADER (Clean Unified Style) -->
<div class="w-full mt-2 mb-4">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-white dark:bg-transparent rounded-xl p-1 flex-shrink-0 flex items-center justify-center">
                <img class="max-w-full max-h-full object-contain" src="{{$clubInfo[0]->clubImage}}" alt="{{$clubInfo[0]->clubName}}" onerror="this.style.display='none'" />
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white font-display tracking-tight">
                {{$clubInfo[0]->clubName}}
            </h1>
        </div>
        <div class="text-right">
            <a href="/desa/club/{{$clubInfo[0]->idClub}}" title="{{ $checkIfSaved==1 ? 'Treu de favorits' : 'Desa als favorits' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $checkIfSaved==1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 cursor-pointer transition-colors hover:text-red-700 {{ $checkIfSaved==1 ? 'text-red-700' : 'text-stone-400' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- MINIMALIST QUICK SECTION NAVIGATION BAR -->
<div class="sticky top-14 z-20 mb-5 py-1 font-display">
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5">
        @if(isset($classifications) && count($classifications) > 0)
            <a href="#sec-classificacions" onclick="smoothScrollToTarget('sec-classificacions'); return false;" class="px-3.5 py-1.5 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 hover:bg-stone-900 hover:text-white dark:hover:bg-stone-800 dark:hover:text-white font-extrabold text-xs transition-all border border-stone-200/80 dark:border-stone-800 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                <i class="fa-solid fa-list-ol text-[10px] text-stone-400"></i> Classificacions
            </a>
        @endif

        @if(isset($matchesListNext) && count($matchesListNext) > 0)
            <a href="#sec-propers-partits" onclick="smoothScrollToTarget('sec-propers-partits'); return false;" class="px-3.5 py-1.5 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 hover:bg-stone-900 hover:text-white dark:hover:bg-stone-800 dark:hover:text-white font-extrabold text-xs transition-all border border-stone-200/80 dark:border-stone-800 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                <i class="fa-regular fa-calendar-days text-[10px] text-stone-400"></i> Propers Partits
            </a>
        @endif

        @if(isset($matchesListLastWithResults) && count($matchesListLastWithResults) > 0)
            <a href="#sec-darrers-resultats" onclick="smoothScrollToTarget('sec-darrers-resultats'); return false;" class="px-3.5 py-1.5 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 hover:bg-stone-900 hover:text-white dark:hover:bg-stone-800 dark:hover:text-white font-extrabold text-xs transition-all border border-stone-200/80 dark:border-stone-800 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                <i class="fa-solid fa-trophy text-[10px] text-stone-400"></i> Darrers Resultats
            </a>
        @endif

        @if(isset($clubVideos) && count($clubVideos) > 0)
            <a href="#sec-videos" onclick="smoothScrollToTarget('sec-videos'); return false;" class="px-3.5 py-1.5 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 hover:bg-stone-900 hover:text-white dark:hover:bg-stone-800 dark:hover:text-white font-extrabold text-xs transition-all border border-stone-200/80 dark:border-stone-800 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                <i class="fa-solid fa-video text-[10px] text-stone-400"></i> Vídeos
            </a>
        @endif

        <a href="#sec-equips" onclick="smoothScrollToTarget('sec-equips'); return false;" class="px-3.5 py-1.5 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 hover:bg-stone-900 hover:text-white dark:hover:bg-stone-800 dark:hover:text-white font-extrabold text-xs transition-all border border-stone-200/80 dark:border-stone-800 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
            <i class="fa-solid fa-users text-[10px] text-stone-400"></i> Equips
        </a>
    </div>
</div>

<script>
function smoothScrollToTarget(id) {
    const target = document.getElementById(id);
    if (!target) return;
    const navOffset = 90;
    const elementPosition = target.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - navOffset;

    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
    });
}
</script>

<!-- MAIN GRID LAYOUT -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-7">

    <!-- LEFT / MAIN CONTENT (Classificacions, Propers Partits, Darrers Resultats) -->
    <div class="col-span-1 lg:col-span-8">

        <!-- CLASSIFICACIONS DEL CLUB (Amagat si no hi ha classificacions) -->
        @if(isset($classifications) && count($classifications) > 0)
            <div id="sec-classificacions" class="scroll-mt-28 mb-7">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        CLASSIFICACIONS DEL CLUB
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="hallmark-stamp bg-stone-900 text-white dark:bg-stone-800 dark:text-stone-300">LLIGUES</span>
                        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="group text-[11px] font-bold text-stone-400 hover:text-stone-900 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer" title="Tornar a dalt">
    <span>Amunt</span>
    <i class="fa-solid fa-arrow-up text-[9px] group-hover:-translate-y-0.5 transition-transform"></i>
</button>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-display text-xs">
                            <thead class="bg-stone-900 text-white dark:bg-black text-[10px] uppercase font-black tracking-wider">
                                <tr>
                                    <th class="py-3 px-4">Competició</th>
                                    <th class="py-3 px-2 text-center">Pos</th>
                                    <th class="py-3 px-2 text-center">Pts</th>
                                    <th class="py-3 px-2 text-center">G</th>
                                    <th class="py-3 px-2 text-center">E</th>
                                    <th class="py-3 px-2 text-center">P</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 dark:divide-stone-800/80">
                                @foreach($classifications as $classification)
                                    <tr class="hover:bg-stone-50 dark:hover:bg-primary/50 transition-colors">
                                        <td class="py-3 px-4 font-bold text-stone-900 dark:text-stone-100 capitalize">
                                            <a href="/competicio/{{$classification->idGroup}}/{{urlencode($classification->groupName)}}" class="hover:text-stone-600 dark:hover:text-white transition-colors">
                                                {{$classification->groupName}}
                                            </a>
                                        </td>
                                        <td class="py-3 px-2 text-center">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black text-xs">
                                                {{$classification->position}}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 text-center font-black text-stone-900 dark:text-stone-100">
                                            {{$classification->points}}
                                        </td>
                                        <td class="py-3 px-2 text-center text-stone-600 dark:text-stone-400 font-bold">
                                            {{$classification->won}}
                                        </td>
                                        <td class="py-3 px-2 text-center text-stone-600 dark:text-stone-400 font-bold">
                                            {{$classification->draw}}
                                        </td>
                                        <td class="py-3 px-2 text-center text-stone-600 dark:text-stone-400 font-bold">
                                            {{$classification->lost}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- PROPERS PARTITS DEL CLUB -->
        @if(isset($matchesListNext) && count($matchesListNext) > 0)
            <div id="sec-propers-partits" class="scroll-mt-28 mb-7">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        PROPERS PARTITS
                    </h2>
                    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="group text-[11px] font-bold text-stone-400 hover:text-stone-900 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer" title="Tornar a dalt">
    <span>Amunt</span>
    <i class="fa-solid fa-arrow-up text-[9px] group-hover:-translate-y-0.5 transition-transform"></i>
</button>
                </div>
                <div class="flex flex-col gap-1">
                    @foreach($matchesListNext as $match)
                        <x-matches-component :match="$match" type="upcoming" />
                    @endforeach
                </div>
            </div>
        @endif

        <!-- DARRERS RESULTATS DEL CLUB -->
        @if(isset($matchesListLastWithResults) && count($matchesListLastWithResults) > 0)
            <div id="sec-darrers-resultats" class="scroll-mt-28 mb-7">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        DARRERS RESULTATS
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="hallmark-stamp bg-stone-900 text-white dark:bg-stone-800 dark:text-stone-300">RESULTATS</span>
                        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="group text-[11px] font-bold text-stone-400 hover:text-stone-900 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer" title="Tornar a dalt">
    <span>Amunt</span>
    <i class="fa-solid fa-arrow-up text-[9px] group-hover:-translate-y-0.5 transition-transform"></i>
</button>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    @foreach($matchesListLastWithResults as $match)
                        <x-matches-component :match="$match" type="result" />
                    @endforeach
                </div>
            </div>
        @endif

        <!-- VÍDEOS DEL CLUB -->
        @if(isset($clubVideos) && count($clubVideos) > 0)
            <div id="sec-videos" class="scroll-mt-28 mb-7" x-data="{ showCount: 6, totalCount: {{ count($clubVideos) }} }">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-4 px-0.5">
                    <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                        Vídeos i Directes del Club (<span x-text="Math.min(showCount, totalCount)"></span>/<span x-text="totalCount"></span>)
                    </h2>
                    <div class="flex items-center gap-3 font-display">
                        <a href="/videos?search={{ urlencode($clubInfo[0]->clubName) }}" class="text-[11px] font-bold text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-white transition-colors flex items-center gap-1">
                            <span>Cercador de vídeos</span>
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="group text-[11px] font-bold text-stone-400 hover:text-stone-900 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer" title="Tornar a dalt">
                            <span>Amunt</span>
                            <i class="fa-solid fa-arrow-up text-[9px] group-hover:-translate-y-0.5 transition-transform"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($clubVideos as $video)
                        <div x-show="{{ $loop->index }} < showCount"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="group bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-xl overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col cursor-pointer" 
                             onclick="openVideoModal('{{ $video->youtube_id }}', '{{ addslashes($video->title) }}')">
                            <!-- Thumbnail Container -->
                            <div class="relative aspect-video bg-stone-900 overflow-hidden">
                                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-play text-xs ml-0.5"></i>
                                    </div>
                                </div>

                                @if($video->channel)
                                    <div class="absolute top-1.5 left-1.5 z-10 bg-black/75 backdrop-blur-md px-1.5 py-0.5 rounded-md text-[8px] font-black text-white flex items-center gap-1 border border-white/10">
                                        @if($video->channel->avatar_url)
                                            <img src="{{ $video->channel->avatar_url }}" class="w-3 h-3 rounded-full object-cover" />
                                        @else
                                            <i class="fa-brands fa-youtube text-red-500"></i>
                                        @endif
                                        <span class="truncate max-w-[90px]">{{ $video->channel->name }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Content -->
                            <div class="p-2.5 flex-1 flex flex-col justify-between space-y-1.5 font-display">
                                <h3 class="text-[11px] font-black font-display text-stone-900 dark:text-white line-clamp-2 leading-tight group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                    {{ $video->title }}
                                </h3>
                                <div class="flex items-center justify-between text-[9px] text-stone-400 font-bold pt-1 border-t border-stone-100 dark:border-stone-800/80">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $video->published_at ? $video->published_at->format('d/m/Y') : '' }}
                                    </span>
                                    <span class="text-red-600 dark:text-red-400 font-black flex items-center gap-0.5">
                                        Veure <i class="fa-solid fa-chevron-right text-[7px]"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Load More / Load All Action Buttons -->
                <div x-show="showCount < totalCount" class="mt-4 flex flex-wrap items-center justify-center gap-2 font-display">
                    <button @click="showCount += 6" class="px-4 py-2 rounded-full bg-stone-100 hover:bg-stone-900 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800 font-black text-xs transition-all border border-stone-200/80 dark:border-stone-800 shadow-2xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-plus text-[10px] text-stone-400"></i>
                        <span>Carregar més vídeos</span>
                    </button>
                    <button @click="showCount = totalCount" class="px-4 py-2 rounded-full bg-stone-900 text-white hover:bg-black dark:bg-stone-800 dark:hover:bg-stone-700 font-black text-xs transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-layer-group text-[10px] text-stone-300"></i>
                        <span>Carregar tots (<span x-text="totalCount"></span>)</span>
                    </button>
                </div>
            </div>
        @endif

    </div>

    <!-- RIGHT SIDEBAR (Logo, Equips del Club Desplegable - Subtil Gris Suau) -->
    <div class="col-span-1 lg:col-span-4">
        
        <!-- Big Shield Container (Sense fons blanc en dark) -->
        <div class="hidden lg:flex justify-center mb-6">
            <div class="w-36 h-36 bg-white dark:bg-transparent rounded-3xl p-4 flex items-center justify-center">
                <img onerror="this.style.display='none'" class="max-w-full max-h-full object-contain" src="{{$clubInfo[0]->clubImage}}" alt="{{$clubInfo[0]->clubName}}" />
            </div>
        </div>

        <!-- Teams List (Desplegable per Temporada - Estil Suau & Subtil) -->
        <div id="sec-equips" class="scroll-mt-28 w-full mb-6">
            <div class="flex items-center justify-between pb-1.5 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                <h3 class="font-display text-xs font-black uppercase tracking-wider text-stone-900 dark:text-white">
                    EQUIPS DEL CLUB
                </h3>
                <div class="flex items-center gap-2 hidden">
                    <span class="hallmark-stamp bg-stone-100 text-stone-700 border border-stone-200/80 dark:bg-stone-900 dark:text-stone-300">TEMPORADES</span>
                    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="group text-[11px] font-bold text-stone-400 hover:text-stone-900 dark:hover:text-white transition-colors flex items-center gap-1 cursor-pointer" title="Tornar a dalt">
    <span>Amunt</span>
    <i class="fa-solid fa-arrow-up text-[9px] group-hover:-translate-y-0.5 transition-transform"></i>
</button>
                </div>
            </div>

            @php
                $groupedTeams = $teamsList->groupBy('seasonName');
            @endphp

            @foreach($groupedTeams as $seasonName => $teams)
                <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="mb-3">
                    <!-- Season Accordion Header -->
                    <button @click="open = !open" class="group w-full flex items-center justify-between py-2.5 px-4 bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 rounded-2xl font-display text-xs font-black uppercase tracking-wider hover:bg-primary-hover transition-all shadow-xs mb-2">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-primary-text dark:text-white text-xs"></i>
                            <span class="text-primary-text dark:text-white font-black">{{ $seasonName }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Badge de numero d'equips subtil en grisos -->
                            <span class="text-[10px] font-black text-primary-text dark:text-white bg-black/15 dark:bg-stone-700/50 px-2.5 py-0.5 rounded-full">
                                {{ count($teams) }} {{ count($teams) == 1 ? 'equip' : 'equips' }}
                            </span>
                            <i class="fa-solid fa-chevron-down text-primary-text dark:text-white transition-transform duration-200 text-[10px]" :class="{ 'rotate-180': open }"></i>
                        </div>
                    </button>

                    <!-- Collapsible Teams Content -->
                    <div x-show="open" class="space-y-1.5 transition-all">
                        @foreach($teams as $team)
                            <a href="/equip/{{$team->idTeam}}/{{urlencode($team->teamName)}}" class="group flex items-center justify-between bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-2xl p-3 hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all shadow-xs">
                                <span class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-stone-100 group-hover:text-stone-600 dark:group-hover:text-stone-900 dark:hover:text-white transition-colors capitalize">
                                    {{App\Http\Controllers\TeamsController::teamFormat($team->teamName)}}
                                </span>
                                <span class="font-display text-[10px] font-bold text-stone-600 dark:text-stone-400 bg-stone-100 dark:bg-stone-900 px-2.5 py-0.5 rounded-full">
                                    {{$team->categoryName}}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-col gap-2.5 mb-6">
            <a href="/acta_club/{{$clubInfo[0]->idClub}}/actes-setmana" class="group flex items-center justify-center gap-2 py-2.5 px-4 bg-stone-100 hover:bg-primary text-stone-800 hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-display text-xs font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                <i class="fa-solid fa-file-lines text-stone-500 group-hover:text-primary-text dark:text-white dark:group-hover:text-primary-text transition-colors"></i> Actes de la setmana
            </a>
            <a href="/acta_header/{{$clubInfo[0]->idClub}}" target="_blank" class="group flex items-center justify-center gap-2 py-2.5 px-4 bg-stone-100 hover:bg-primary text-stone-800 hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-display text-xs font-extrabold uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                <i class="fa-solid fa-chart-column text-xs text-stone-500 group-hover:text-primary-text dark:text-white dark:group-hover:text-primary-text transition-colors"></i> Generar gràfic resultats
            </a>
        </div>

    </div>

</div>

<!-- Video Modal Player -->
<div id="videoModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80 backdrop-blur-md font-display transition-opacity" onclick="closeVideoModalOnBackdrop(event)">
    <div class="relative w-full max-w-4xl bg-stone-900 border border-stone-800 rounded-3xl overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between p-4 px-6 border-b border-stone-800 bg-stone-950">
            <h3 id="videoModalTitle" class="text-xs md:text-sm font-black text-white truncate pr-4"></h3>
            <button onclick="closeVideoModal()" class="w-8 h-8 rounded-full bg-stone-800 text-stone-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer" aria-label="Tancar">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="relative aspect-video w-full bg-black">
            <iframe id="videoModalIframe" class="w-full h-full" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<script>
    function openVideoModal(youtubeId, title) {
        const modal = document.getElementById('videoModal');
        const iframe = document.getElementById('videoModalIframe');
        const titleEl = document.getElementById('videoModalTitle');

        if (modal && iframe) {
            titleEl.textContent = title || 'Vídeo';
            iframe.src = 'https://www.youtube.com/embed/' + youtubeId + '?autoplay=1';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const iframe = document.getElementById('videoModalIframe');

        if (modal && iframe) {
            iframe.src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    function closeVideoModalOnBackdrop(event) {
        if (event.target.id === 'videoModal') {
            closeVideoModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVideoModal();
        }
    });
</script>

@endsection
