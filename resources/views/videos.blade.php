@extends('layout.mainlayout')

@section('title', "Vídeos d'Hoquei Patins - JOK.cat")

@section('content')
<div class="space-y-6">

<!-- UNIFIED HEADER (Ultra-Clean Apple Sports) -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight">
                Vídeos d'Hoquei Patins
            </h1>
        </div>
    </div>
</div>

    <!-- Filter Control Panel -->
    <form method="GET" action="{{ route('videos.index') }}" class="bg-stone-50 dark:bg-stone-900/70 border border-stone-200/80 dark:border-stone-800/90 rounded-2xl p-4 md:p-5 shadow-xs font-display">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-center">
            
            <!-- Text Search -->
            <div class="lg:col-span-5 relative">
                <label for="search" class="block text-[11px] font-black uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-1">
                    Cerca per text
                </label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-xs"></i>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Títol, equip o descripció..." class="w-full pl-9 pr-4 py-2 rounded-xl bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-700/80 text-xs font-medium text-stone-900 dark:text-white placeholder-stone-400 focus:ring-2 focus:ring-primary focus:outline-none transition-all" />
                </div>
            </div>

            <!-- Filter by Channel with Logo -->
            <div class="lg:col-span-3" x-data="{ open: false, selectedId: '{{ $channelId }}', selectedName: '{{ addslashes($channels->firstWhere('id', $channelId)->name ?? 'Tots els canals') }}', selectedAvatar: '{{ $channels->firstWhere('id', $channelId)->avatar_url ?? '' }}' }">
                <label class="block text-[11px] font-black uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-1">
                    Canal / Llista
                </label>
                <input type="hidden" name="channel_id" :value="selectedId" />
                <div class="relative">
                    <button type="button" @click="open = !open" @click.outside="open = false" class="w-full py-2 px-3 rounded-xl bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-700/80 text-xs font-medium text-stone-900 dark:text-white flex items-center justify-between shadow-xs hover:border-stone-400 dark:hover:border-stone-500 transition-all cursor-pointer">
                        <div class="flex items-center gap-2 truncate">
                            <template x-if="selectedAvatar">
                                <img :src="selectedAvatar" class="w-5 h-5 rounded-full object-cover flex-shrink-0 border border-stone-200 dark:border-stone-700" />
                            </template>
                            <template x-if="!selectedAvatar">
                                <span class="w-5 h-5 rounded-full bg-red-600/10 text-red-600 flex items-center justify-center text-[10px] flex-shrink-0 font-bold">
                                    <i class="fa-brands fa-youtube"></i>
                                </span>
                            </template>
                            <span class="truncate font-bold" x-text="selectedName"></span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-stone-400 ml-2 transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="open" x-transition.origin.top.duration.150ms class="absolute z-50 mt-1.5 w-full bg-white dark:bg-[#18181b] border border-stone-200 dark:border-stone-700/90 rounded-2xl shadow-2xl max-h-64 overflow-y-auto p-1.5 space-y-0.5 font-display text-xs" style="display: none;">
                        <div @click="selectedId = ''; selectedName = 'Tots els canals'; selectedAvatar = ''; open = false" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-stone-100 dark:hover:bg-stone-800/80 cursor-pointer transition-colors" :class="{ 'bg-stone-100 dark:bg-stone-800 font-extrabold': selectedId === '' }">
                            <span class="w-6 h-6 rounded-full bg-stone-200 dark:bg-stone-800 text-stone-600 dark:text-stone-300 flex items-center justify-center text-xs flex-shrink-0 font-bold">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                            <span class="truncate text-stone-800 dark:text-stone-200 font-bold">Tots els canals</span>
                        </div>

                        @foreach($channels as $channel)
                            <div @click="selectedId = '{{ $channel->id }}'; selectedName = '{{ addslashes($channel->name) }}'; selectedAvatar = '{{ $channel->avatar_url }}'; open = false" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-stone-100 dark:hover:bg-stone-800/80 cursor-pointer transition-colors" :class="{ 'bg-stone-100 dark:bg-stone-800 font-extrabold': selectedId == '{{ $channel->id }}' }">
                                @if($channel->avatar_url)
                                    <img src="{{ $channel->avatar_url }}" alt="{{ $channel->name }}" class="w-6 h-6 rounded-full object-cover flex-shrink-0 border border-stone-200 dark:border-stone-700" />
                                @else
                                    <span class="w-6 h-6 rounded-full bg-red-600/10 text-red-600 flex items-center justify-center text-xs flex-shrink-0 font-bold">
                                        <i class="fa-brands fa-youtube"></i>
                                    </span>
                                @endif
                                <span class="truncate text-stone-800 dark:text-stone-200 font-medium">{{ $channel->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Filter by Date -->
            <div class="lg:col-span-2">
                <label for="date" class="block text-[11px] font-black uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-1">
                    Data de publicació
                </label>
                <select id="date" name="date" class="w-full py-2 px-3 rounded-xl bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-700/80 text-xs font-medium text-stone-900 dark:text-white focus:ring-2 focus:ring-primary focus:outline-none transition-all">
                    <option value="">Qualsevol data</option>
                    <option value="week" {{ $dateFilter === 'week' ? 'selected' : '' }}>Últims 7 dies</option>
                    <option value="month" {{ $dateFilter === 'month' ? 'selected' : '' }}>Últim mes</option>
                    <option value="3months" {{ $dateFilter === '3months' ? 'selected' : '' }}>Últims 3 mesos</option>
                    <option value="year" {{ $dateFilter === 'year' ? 'selected' : '' }}>Últim any</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="lg:col-span-2 flex items-end gap-2 pt-2 lg:pt-0">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-stone-900 hover:bg-black dark:bg-stone-800 dark:hover:bg-stone-700 text-white font-black text-xs uppercase tracking-wider transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filtrar</span>
                </button>
                @if($search || $channelId || $dateFilter)
                    <a href="{{ route('videos.index') }}" class="py-2 px-3 rounded-xl bg-stone-200 hover:bg-stone-300 dark:bg-stone-800 dark:hover:bg-stone-700 text-stone-700 dark:text-stone-300 font-bold text-xs transition-all flex items-center justify-center" title="Netejar filtres">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>

        </div>
    </form>

    <!-- Active Filters Badge -->
    @if($search || $channelId || $dateFilter)
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="font-bold text-stone-500 dark:text-stone-400">Filtres actius:</span>
            @if($search)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-stone-200 dark:bg-stone-800 text-stone-800 dark:text-stone-200 font-bold">
                    Cerca: "{{ $search }}"
                </span>
            @endif
            @if($channelId)
                @php $activeChan = $channels->firstWhere('id', $channelId); @endphp
                @if($activeChan)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-stone-200 dark:bg-stone-800 text-stone-800 dark:text-stone-200 font-bold">
                        Canal: {{ $activeChan->name }}
                    </span>
                @endif
            @endif
            @if($dateFilter)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-stone-200 dark:bg-stone-800 text-stone-800 dark:text-stone-200 font-bold">
                    Data: {{ $dateFilter === 'week' ? 'Últims 7 dies' : ($dateFilter === 'month' ? 'Últim mes' : ($dateFilter === '3months' ? 'Últims 3 mesos' : 'Últim any')) }}
                </span>
            @endif
        </div>
    @endif

    <!-- Videos Grid -->
    @if($videos->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($videos as $video)
                <div class="group bg-white dark:bg-[#121215] border border-stone-200/80 dark:border-stone-800/90 rounded-2xl overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between cursor-pointer"
                     onclick="openVideoModal('{{ $video->youtube_id }}', '{{ addslashes($video->title) }}')">
                    
                    <!-- Thumbnail Container -->
                    <div class="relative aspect-video bg-stone-900 overflow-hidden">
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        
                        <!-- Dark Overlay on Hover -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-red-600 text-white flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-play text-lg ml-1"></i>
                            </div>
                        </div>

                        <!-- Channel Badge -->
                        @if($video->channel)
                            <div class="absolute top-2.5 left-2.5 z-10 bg-black/75 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-black text-white flex items-center gap-1.5 border border-white/10 shadow-md">
                                @if($video->channel->avatar_url)
                                    <img src="{{ $video->channel->avatar_url }}" alt="{{ $video->channel->name }}" class="w-3.5 h-3.5 rounded-full object-cover flex-shrink-0" />
                                @else
                                    <i class="fa-brands fa-youtube text-red-500"></i>
                                @endif
                                <span class="truncate max-w-[140px]">{{ $video->channel->name }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <h3 class="text-xs md:text-sm font-black font-display text-stone-900 dark:text-white line-clamp-2 leading-snug group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                            {{ $video->title }}
                        </h3>

                        <div class="flex items-center justify-between text-[11px] text-stone-400 dark:text-stone-500 font-bold pt-2 border-t border-stone-100 dark:border-stone-800/80">
                            <span class="flex items-center gap-1">
                                <i class="fa-regular fa-clock"></i>
                                {{ $video->published_at ? $video->published_at->format('d/m/Y') : '' }}
                            </span>
                            <span class="text-red-600 dark:text-red-400 font-extrabold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                                <span>Veure</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- PAGINACIÓ EN CATALÀ (Estil Càpsula Apple Sports idèntic a Notícies) -->
        @if($videos->hasPages())
            <div class="my-10 flex flex-wrap items-center justify-center gap-2 font-display text-xs">
                
                {{-- Botó Primera Pàgina (Només Icona) --}}
                @if (!$videos->onFirstPage())
                    <a href="{{ $videos->url(1) }}" class="w-9 h-9 rounded-full bg-stone-100 hover:bg-primary hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-black flex items-center justify-center transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs" title="Primera pàgina">
                        <i class="fa-solid fa-angles-left text-xs"></i>
                    </a>
                @endif

                {{-- Botó Anterior --}}
                @if ($videos->onFirstPage())
                    <span class="px-4 py-2 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-400 dark:text-stone-600 font-bold opacity-50 cursor-not-allowed border border-stone-200/50 dark:border-stone-800/50">
                        « Anterior
                    </span>
                @else
                    <a href="{{ $videos->previousPageUrl() }}" class="px-4 py-2 rounded-full bg-stone-100 hover:bg-primary hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-black transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                        « Anterior
                    </a>
                @endif

                {{-- Números de pàgina --}}
                @foreach ($videos->getUrlRange(max(1, $videos->currentPage() - 2), min($videos->lastPage(), $videos->currentPage() + 2)) as $page => $url)
                    @if ($page == $videos->currentPage())
                        <span class="w-9 h-9 rounded-full bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black flex items-center justify-center shadow-xs">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-9 h-9 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-800 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800 font-bold flex items-center justify-center transition-all border border-stone-200/80 dark:border-stone-800">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Botó Següent --}}
                @if ($videos->hasMorePages())
                    <a href="{{ $videos->nextPageUrl() }}" class="px-4 py-2 rounded-full bg-stone-100 hover:bg-primary hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-black transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                        Següent »
                    </a>
                @else
                    <span class="px-4 py-2 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-400 dark:text-stone-600 font-bold opacity-50 cursor-not-allowed border border-stone-200/50 dark:border-stone-800/50">
                        Següent »
                    </span>
                @endif

                {{-- Botó Última Pàgina (Només Icona) --}}
                @if ($videos->currentPage() < $videos->lastPage())
                    <a href="{{ $videos->url($videos->lastPage()) }}" class="w-9 h-9 rounded-full bg-stone-100 hover:bg-primary hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-black flex items-center justify-center transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs" title="Última pàgina">
                        <i class="fa-solid fa-angles-right text-xs"></i>
                    </a>
                @endif
            </div>
        @endif

    @else
        <!-- Empty State -->
        <div class="bg-stone-50 dark:bg-stone-900/50 border border-stone-200/80 dark:border-stone-800 rounded-3xl p-12 text-center space-y-4 max-w-lg mx-auto my-8">
            <div class="w-16 h-16 rounded-full bg-stone-200 dark:bg-stone-800 text-stone-400 dark:text-stone-500 flex items-center justify-center mx-auto text-2xl">
                <i class="fa-solid fa-video-slash"></i>
            </div>
            <h3 class="text-base font-black text-stone-800 dark:text-stone-200">No s'han trobat vídeos</h3>
            <p class="text-xs text-stone-500 dark:text-stone-400">
                No hi ha vídeos que coincideixin amb els filtres seleccionats. Prova de canviar els filtres o netejar la cerca.
            </p>
            <a href="{{ route('videos.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-stone-900 text-white dark:bg-stone-800 text-xs font-black uppercase tracking-wider hover:bg-black transition-all">
                Netejar filtres
            </a>
        </div>
    @endif

</div>

<!-- Video Modal Player -->
<div id="videoModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80 backdrop-blur-md font-display transition-opacity" onclick="closeVideoModalOnBackdrop(event)">
    <div class="relative w-full max-w-4xl bg-stone-900 border border-stone-800 rounded-3xl overflow-hidden shadow-2xl">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 px-6 border-b border-stone-800 bg-stone-950">
            <h3 id="videoModalTitle" class="text-xs md:text-sm font-black text-white truncate pr-4"></h3>
            <button onclick="closeVideoModal()" class="w-8 h-8 rounded-full bg-stone-800 text-stone-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer" aria-label="Tancar">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Video Player iFrame Container (16:9) -->
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
