@extends('layout.mainlayout')
@section('title'," JOK.cat - L'hoquei a Catalunya ")
@section('content')
<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>

<script async src="https://tally.so/widgets/embed.js"></script>
<script>
    window.TallyConfig = {
    "formId": "EkPjzA",
    "popup": {
        "emoji": {
        "text": "👋",
        "animation": "flash"
        },
        "hideTitle": true,
        "autoClose": 0,
        "formEventsForwarding": true,
        "open": {
        "trigger": "scroll",
        "scrollPercent": 10
        }
    }
    };
</script>

<!-- TOP TICKER BAR (Modern Scoreboard Ticker) -->
<div class="w-full relative group mb-8">
    <div class="font-display text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2 px-1 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-[#f5c310] animate-pulse"></span>
        MARCADORS DIRECTES I DARRERS RESULTATS
    </div>
    <div class="w-full flex overflow-x-auto scroll-smooth gap-3 pb-2 snap-x snap-mandatory scrollbar-hide">
        @foreach($matchesListLastWithResults->take(8) as $match)
            <div class="snap-start flex-shrink-0">
                <x-matches-component :match="$match" type="ticker" />
            </div>
        @endforeach
    </div>
</div>

<!-- MAIN 3-COLUMN CONTENT GRID -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-7 my-4 items-start">
    
    <!-- COLUMN 1: LEFT (Propers partits - Agenda) -->
    <div class="col-span-1 lg:col-span-3">
        <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-4 px-0.5">
            <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                PROPERS PARTITS
            </h2>
            <span class="font-display text-[10px] font-bold text-[#f5c310] uppercase">AGENDA</span>
        </div>

        <div class="flex flex-col gap-1">
            @forelse($matchesListNext as $match)
                <x-matches-component :match="$match" type="upcoming" />
            @empty
                <div class="font-display text-xs text-stone-500 dark:text-stone-400 text-center py-8 bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-xl">
                    No hi ha propers partits programats.
                </div>
            @endforelse
        </div>
    </div>

    <!-- COLUMN 2: CENTER (Featured News Grid, Pavellons Banner, Merchandising) -->
    <div class="col-span-1 lg:col-span-6">
        <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-4 px-0.5">
            <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                NOTÍCIES DESTACADES
            </h2>
            <a href="/noticies" class="font-display text-[11px] font-semibold text-stone-500 dark:text-stone-400 hover:text-[#f5c310] uppercase">Més Notícies →</a>
        </div>
        
        <!-- News Layout Grid (1 large hero, 2 stacked) -->
        <div class="flex flex-col md:flex-row gap-4 mb-7">
            <!-- Large Featured News Card -->
            @if(isset($newsListTop[0]))
                @php $n = $newsListTop[0]; @endphp
                <a href="/noticies/detall/{{$n->idNew}}/{{urlencode($n->newsTitle)}}" class="group relative flex flex-col justify-end w-full md:w-7/12 h-84 rounded-2xl overflow-hidden shadow-sm hover:border-[#f5c310] border border-stone-200 dark:border-stone-800 transition-all duration-300">
                    <!-- Image -->
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $n->newsImage }}')"></div>
                    <!-- Dark overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                    <!-- Tag stamp -->
                    <div class="absolute top-3.5 left-3.5 hallmark-stamp bg-[#f5c310] text-black shadow-md">
                        CRÒNICA
                    </div>
                    <!-- Title info -->
                    <div class="relative p-5">
                        <h3 class="font-display text-base md:text-lg font-extrabold text-white leading-tight uppercase mb-1.5 drop-shadow-md group-hover:text-[#f5c310] transition-colors">
                            {{ $n->newsTitle }}
                        </h3>
                        <span class="font-display text-[10px] font-bold text-[#f5c310] uppercase tracking-wider">HOQUEI CATALUNYA</span>
                    </div>
                </a>
            @endif

            <!-- Stacked Small News Cards -->
            <div class="w-full md:w-5/12 flex flex-col gap-4">
                @for($i = 1; $i <= 2; $i++)
                    @if(isset($newsListTop[$i]))
                        @php $n = $newsListTop[$i]; @endphp
                        <a href="/noticies/detall/{{$n->idNew}}/{{urlencode($n->newsTitle)}}" class="group relative flex flex-col justify-end w-full h-[9.8rem] rounded-2xl overflow-hidden shadow-sm hover:border-[#f5c310] border border-stone-200 dark:border-stone-800 transition-all duration-300">
                            <!-- Image -->
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $n->newsImage }}')"></div>
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
                            <!-- Content -->
                            <div class="relative p-3.5">
                                <span class="hallmark-stamp bg-stone-900 text-[#f5c310] text-[9px] mb-1">ACTUALITAT</span>
                                <h4 class="font-display text-xs font-bold text-white leading-snug uppercase line-clamp-2 drop-shadow-sm group-hover:text-[#f5c310] transition-colors">
                                    {{ $n->newsTitle }}
                                </h4>
                            </div>
                        </a>
                    @endif
                @endfor
            </div>
        </div>

        <!-- Pavellons Banner -->
        <a href="/pavellons" class="relative block w-full rounded-2xl bg-stone-900 border border-stone-800 p-5 shadow-sm overflow-hidden group mb-7 transition-all hover:border-[#f5c310]">
            <!-- Map background dots effect -->
            <div class="absolute inset-0 opacity-20 hallmark-grid-bg"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <span class="hallmark-stamp bg-[#f5c310] text-black mb-1">GUIA PAVELLONS</span>
                    <h3 class="font-display text-sm md:text-base font-extrabold text-white uppercase tracking-wider mt-1">
                        Cerca el teu proper pavelló d'hoquei
                    </h3>
                    <p class="font-display text-xs font-medium text-stone-400 mt-1">
                        Localització, horaris i indicacions GPS
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-stone-800 border border-stone-700 flex items-center justify-center text-[#f5c310] group-hover:bg-[#f5c310] group-hover:text-black transition-colors duration-300 shadow">
                    <i class="fa-solid fa-map-location-dot text-base"></i>
                </div>
            </div>
        </a>

        <!-- Merchandising Slider -->
        <div class="mt-7">
            <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                <h2 class="font-display text-xs font-extrabold text-stone-900 dark:text-stone-100 uppercase tracking-wider">
                    MERCHANDISING DESTACAT
                </h2>
                <a href="/merchandising" class="font-display text-[11px] font-semibold text-stone-500 hover:text-[#f5c310] uppercase">Botiga →</a>
            </div>
            
            <div x-data="{ 
                scrollNext() { this.$refs.carousel.scrollBy({ left: 220, behavior: 'smooth' }) },
                scrollPrev() { this.$refs.carousel.scrollBy({ left: -220, behavior: 'smooth' }) }
            }" class="relative w-full group">
                <!-- Carousel -->
                <div x-ref="carousel" class="w-full flex gap-3.5 overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-hide py-1">
                    @foreach($merchandisingList as $merch)
                        <a href="{{ $merch->assetUrl }}" target="_blank" class="snap-start flex-shrink-0 w-[44%] sm:w-[30%] bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-2xl overflow-hidden shadow-sm hover:border-[#f5c310] transition-all flex flex-col">
                            <div class="w-full aspect-square bg-stone-50 dark:bg-stone-900 p-3 flex items-center justify-center relative overflow-hidden border-b border-stone-100 dark:border-stone-800">
                                <img class="max-w-full max-h-full object-contain rounded transition-transform duration-300 hover:scale-105" src="{{ $merch->assetThumbnail }}" alt="{{ $merch->assetName }}">
                            </div>
                            <div class="p-3 flex-grow flex flex-col justify-between">
                                <h4 class="font-display text-xs font-bold text-stone-800 dark:text-stone-200 truncate" title="{{ $merch->assetName }}">
                                    {{ $merch->assetName }}
                                </h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="font-display text-xs font-extrabold text-stone-900 dark:text-white">
                                        {{ $merch->assetPrice ? $merch->assetPrice.' €' : 'Cons.' }}
                                    </span>
                                    <span class="font-display text-[10px] font-bold text-[#f5c310] uppercase tracking-wider flex items-center gap-0.5">
                                        Comprar <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Controls -->
                <button @click="scrollPrev()" class="absolute left-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-black/90 border border-stone-200 dark:border-stone-800 flex items-center justify-center text-stone-800 dark:text-stone-200 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#f5c310] hover:text-black focus:outline-none">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button @click="scrollNext()" class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-black/90 border border-stone-200 dark:border-stone-800 flex items-center justify-center text-stone-800 dark:text-stone-200 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#f5c310] hover:text-black focus:outline-none">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- COLUMN 3: RIGHT (Darrers resultats i Segona mà Slider) -->
    <div class="col-span-1 lg:col-span-3">
        <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-2 px-0.5">
            <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                DARRERS RESULTATS
            </h2>
            <span class="font-display text-[10px] font-bold text-[#f5c310] uppercase">CERCA RÀPIDA</span>
        </div>

        <p class="font-display text-xs text-stone-500 dark:text-stone-400 font-medium mb-3 px-0.5 leading-snug">
            Cerca en temps real per equip o competició:
        </p>

        <!-- Dynamic inline filter input -->
        <div class="relative mb-3.5">
            <input type="text" id="resultsFilter" placeholder="Cerca equip o resultat..." class="w-full bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-xl px-3 py-2 text-xs text-stone-800 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:outline-none focus:border-[#f5c310] shadow-sm transition-all" />
        </div>

        <!-- Results list -->
        <div class="flex flex-col">
            @forelse($matchesListLastWithResults as $match)
                <div class="result-item transition-all duration-200">
                    <x-matches-component :match="$match" type="result" />
                </div>
            @empty
                <div class="font-mono text-xs text-stone-500 dark:text-stone-400 text-center py-6 bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-xl">
                    No hi ha resultats recents disponibles.
                </div>
            @endforelse
        </div>

        <!-- Segona Mà (Darrers Anuncis) Slider -->
        <div class="mt-7">
            <div class="flex items-center justify-between pb-1 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                <h2 class="font-syne text-xs font-bold text-stone-800 dark:text-stone-200 uppercase tracking-wider">
                    SEGONA MÀ (ANUNCIS)
                </h2>
                <a href="/anuncis" class="font-mono text-[9px] font-bold text-[#f5c310] uppercase">Veure Tots →</a>
            </div>
            
            <div x-data="{ 
                scrollNext() { this.$refs.carousel.scrollBy({ left: 200, behavior: 'smooth' }) },
                scrollPrev() { this.$refs.carousel.scrollBy({ left: -200, behavior: 'smooth' }) }
            }" class="relative w-full group">
                <!-- Carousel -->
                <div x-ref="carousel" class="w-full flex gap-3 overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-hide py-1">
                    @forelse($recentAds as $ad)
                        @php
                            $coverFoto = $ad->fotos->first()?->foto_ruta 
                                ? asset($ad->fotos->first()->foto_ruta) 
                                : 'https://picsum.photos/seed/'.$ad->id.'/200/150';
                        @endphp
                        <a href="{{ route('anuncis.show', ['id' => $ad->id, 'slug' => $ad->slug]) }}" class="snap-start flex-shrink-0 w-[47%] bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-xl overflow-hidden shadow-sm hover:border-[#f5c310] transition-all flex flex-col">
                            <div class="w-full aspect-[4/3] bg-stone-100 dark:bg-stone-900 flex items-center justify-center relative overflow-hidden border-b border-stone-200 dark:border-stone-800">
                                <img class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" src="{{ $coverFoto }}" alt="{{ $ad->titol }}">
                            </div>
                            <div class="p-2.5 flex-grow flex flex-col justify-between text-xs">
                                <div>
                                    <span class="font-mono text-[8.5px] font-extrabold text-[#f5c310] uppercase tracking-wide block">
                                        {{ $ad->marca->nom_marca ?? 'General' }}
                                    </span>
                                    <h4 class="font-mono font-bold text-stone-800 dark:text-stone-200 truncate mt-0.5" title="{{ $ad->titol }}">
                                        {{ $ad->titol }}
                                    </h4>
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-stone-200 dark:border-stone-800 font-mono">
                                    <span class="font-extrabold text-stone-900 dark:text-white">
                                        {{ $ad->preu ? $ad->preu.' €' : 'Cons.' }}
                                    </span>
                                    <span class="text-[9px] font-bold text-stone-400 flex items-center gap-0.5">
                                        Veure <i class="fa-solid fa-chevron-right text-[7px]"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="font-mono text-xs text-stone-500 dark:text-stone-400 text-center py-8 w-full bg-white dark:bg-[#131419] border border-stone-200 dark:border-stone-800 rounded-xl">
                            No hi ha anuncis recents.
                        </div>
                    @endforelse
                </div>
                
                <!-- Controls -->
                <button @click="scrollPrev()" class="absolute left-1 top-1/2 -translate-y-1/2 w-7 h-7 rounded bg-white/90 dark:bg-black/90 border border-stone-300 dark:border-stone-800 flex items-center justify-center text-stone-800 dark:text-stone-200 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#f5c310] hover:text-black focus:outline-none">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button @click="scrollNext()" class="absolute right-1 top-1/2 -translate-y-1/2 w-7 h-7 rounded bg-white/90 dark:bg-black/90 border border-stone-300 dark:border-stone-800 flex items-center justify-center text-stone-800 dark:text-stone-200 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#f5c310] hover:text-black focus:outline-none">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Live results local filter functionality
    document.getElementById('resultsFilter')?.addEventListener('keyup', function(e) {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.result-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            if (!query || text.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection

