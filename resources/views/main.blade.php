@extends('layout.mainlayout')
@section('title'," JOK.cat ")
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

<!-- TOP TICKER BAR (Live / Recently played matches) -->
<div class="w-full relative group mb-6">
    <div class="text-[10px] font-black text-stone-400 dark:text-neutral-500 uppercase tracking-widest mb-2 px-1">
        Partits Recents i Directes
    </div>
    <div class="w-full flex overflow-x-auto scroll-smooth gap-3 pb-3 snap-x snap-mandatory scrollbar-hide">
        @foreach($matchesListLastWithResults->take(8) as $match)
            <div class="snap-start flex-shrink-0">
                <x-matches-component :match="$match" type="ticker" />
            </div>
        @endforeach
    </div>
</div>

<!-- MAIN 3-COLUMN CONTENT GRID -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 my-4 items-start">
    
    <!-- COLUMN 1: LEFT (Propers partits - Agenda) -->
    <div class="col-span-1 lg:col-span-3">
        <h2 class="text-xs font-black text-stone-850 dark:text-white uppercase tracking-wider mb-4 px-1">
            PROPERS PARTITS (AGENDA)
        </h2>
        <div class="flex flex-col gap-1">
            @forelse($matchesListNext as $match)
                <x-matches-component :match="$match" type="upcoming" />
            @empty
                <div class="text-xs text-stone-400 dark:text-neutral-500 text-center py-8 bg-white dark:bg-neutral-900 border border-stone-250 dark:border-neutral-800 rounded-2xl">
                    No hi ha propers partits programats.
                </div>
            @endforelse
        </div>
    </div>

    <!-- COLUMN 2: CENTER (News Grid, Pavellons Banner, Merchandising Slider) -->
    <div class="col-span-1 lg:col-span-6">
        <h2 class="text-xs font-black text-stone-850 dark:text-white uppercase tracking-wider mb-4 px-1">
            NOTÍCIES DESTACADES
        </h2>
        
        <!-- News Layout Grid (1 large, 2 small) -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <!-- Large Featured News Card -->
            @if(isset($newsListTop[0]))
                @php $n = $newsListTop[0]; @endphp
                <a href="/noticies/detall/{{$n->idNew}}/{{urlencode($n->newsTitle)}}" class="group relative flex flex-col justify-end w-full md:w-7/12 h-80 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:border-[#f5c310] border border-stone-200/40 dark:border-neutral-800 transition-all duration-300">
                    <!-- Image -->
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('{{ $n->newsImage }}')"></div>
                    <!-- Gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/45 to-transparent"></div>
                    <!-- Tag -->
                    <div class="absolute top-4 left-4 bg-[#f5c310] text-stone-950 text-[10px] font-black uppercase px-2 py-1 rounded-md shadow">
                        Crònica
                    </div>
                    <!-- Title info -->
                    <div class="relative p-5">
                        <h3 class="text-base md:text-lg font-black text-white leading-tight uppercase mb-1 drop-shadow-md">
                            {{ $n->newsTitle }}
                        </h3>
                        <span class="text-[10px] font-extrabold text-[#f5c310] uppercase tracking-wider">Crònica</span>
                    </div>
                </a>
            @endif

            <!-- Stacked Small News Cards -->
            <div class="w-full md:w-5/12 flex flex-col gap-4">
                @for($i = 1; $i <= 2; $i++)
                    @if(isset($newsListTop[$i]))
                        @php $n = $newsListTop[$i]; @endphp
                        <a href="/noticies/detall/{{$n->idNew}}/{{urlencode($n->newsTitle)}}" class="group relative flex flex-col justify-end w-full h-[9.5rem] rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:border-[#f5c310] border border-stone-200/40 dark:border-neutral-800 transition-all duration-300">
                            <!-- Image -->
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('{{ $n->newsImage }}')"></div>
                            <!-- Gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/45 to-transparent"></div>
                            <!-- Content -->
                            <div class="relative p-4">
                                <h4 class="text-xs font-black text-white leading-snug uppercase mb-1 line-clamp-2 drop-shadow-sm">
                                    {{ $n->newsTitle }}
                                </h4>
                                <span class="text-[9px] font-extrabold text-[#f5c310] uppercase tracking-wider">Crònica</span>
                            </div>
                        </a>
                    @endif
                @endfor
            </div>
        </div>

        <!-- Pavellons Banner -->
        <a href="/pavellons" class="relative block w-full rounded-2xl bg-neutral-900 border border-neutral-800 p-5 shadow-sm overflow-hidden group mb-6 transition-all hover:border-[#f5c310]">
            <!-- Map background dots effect -->
            <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#f5c310_1.5px,transparent_1.5px)] [background-size:16px_16px]"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <h3 class="text-xs md:text-sm font-black text-white uppercase tracking-wider">
                        Pavellons d'hoquei a Catalunya
                    </h3>
                    <p class="text-[10px] md:text-[11px] font-bold text-[#f5c310] uppercase tracking-wide mt-1">
                        Cerca el teu proper pavelló
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full bg-neutral-800 border border-neutral-700/60 flex items-center justify-center text-[#f5c310] group-hover:bg-[#f5c310] group-hover:text-stone-950 transition-colors duration-300 shadow">
                    <i class="fa-solid fa-map-location-dot text-base"></i>
                </div>
            </div>
        </a>

        <!-- Merchandising Slider -->
        <div class="mt-6">
            <h2 class="text-xs font-black text-stone-500 dark:text-neutral-400 uppercase tracking-wider mb-3 px-1">
                Merchandising destacat
            </h2>
            
            <div x-data="{ 
                scrollNext() { this.$refs.carousel.scrollBy({ left: 220, behavior: 'smooth' }) },
                scrollPrev() { this.$refs.carousel.scrollBy({ left: -220, behavior: 'smooth' }) }
            }" class="relative w-full group">
                <!-- Carousel -->
                <div x-ref="carousel" class="w-full flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-hide py-1">
                    @foreach($merchandisingList as $merch)
                        <a href="{{ $merch->assetUrl }}" target="_blank" class="snap-start flex-shrink-0 w-[44%] sm:w-[30%] bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all hover:border-[#f5c310]/50 flex flex-col">
                            <div class="w-full aspect-square bg-stone-50 dark:bg-neutral-850 p-3 flex items-center justify-center relative overflow-hidden border-b border-stone-100 dark:border-neutral-800">
                                <img class="max-w-full max-h-full object-contain rounded-lg transition-transform duration-300 hover:scale-105" src="{{ $merch->assetThumbnail }}" alt="{{ $merch->assetName }}">
                            </div>
                            <div class="p-3 flex-grow flex flex-col justify-between">
                                <h4 class="text-[11px] font-bold text-stone-700 dark:text-neutral-300 truncate" title="{{ $merch->assetName }}">
                                    {{ $merch->assetName }}
                                </h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs font-black text-stone-900 dark:text-white">
                                        {{ $merch->assetPrice ? $merch->assetPrice.' €' : 'Cons.' }}
                                    </span>
                                    <span class="text-[9px] font-black text-[#f5c310] dark:text-[#f5c310] uppercase tracking-wider flex items-center gap-0.5">
                                        Comprar <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Controls -->
                <button @click="scrollPrev()" class="absolute left-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-neutral-950/90 border border-stone-200 dark:border-neutral-800 flex items-center justify-center text-stone-700 dark:text-neutral-300 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-stone-50 dark:hover:bg-neutral-850 focus:outline-none">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button @click="scrollNext()" class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-neutral-950/90 border border-stone-200 dark:border-neutral-800 flex items-center justify-center text-stone-700 dark:text-neutral-300 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-stone-50 dark:hover:bg-neutral-850 focus:outline-none">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- COLUMN 3: RIGHT (Darrers resultats i Segona mà Slider) -->
    <div class="col-span-1 lg:col-span-3">
        <h2 class="text-xs font-black text-stone-850 dark:text-white uppercase tracking-wider mb-1 px-1">
            DARRERS RESULTATS I FILTRE RÀPID
        </h2>
        <p class="text-[10px] text-stone-400 dark:text-neutral-500 font-medium mb-3 px-1 leading-snug">
            Powerful live-search par equipes i resultats
        </p>

        <!-- Dynamic inline filter input -->
        <div class="relative mb-3 px-0.5">
            <input type="text" id="resultsFilter" placeholder="Cerca equips i resultats..." class="w-full bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800 rounded-xl px-3 py-2 text-xs text-stone-800 dark:text-neutral-100 placeholder-stone-400 dark:placeholder-neutral-500 focus:outline-none focus:ring-1 focus:ring-[#f5c310] focus:border-[#f5c310] shadow-sm transition-all" />
        </div>

        <!-- Results list -->
        <div class="flex flex-col">
            @forelse($matchesListLastWithResults as $match)
                <div class="result-item transition-all duration-200">
                    <x-matches-component :match="$match" type="result" />
                </div>
            @empty
                <div class="text-xs text-stone-400 dark:text-neutral-500 text-center py-6 bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800 rounded-xl">
                    No hi ha resultats recents disponibles.
                </div>
            @endforelse
        </div>

        <!-- Segona Mà (Darrers Anuncis) Slider -->
        <div class="mt-8">
            <h2 class="text-xs font-black text-stone-500 dark:text-neutral-400 uppercase tracking-wider mb-3 px-1">
                Segona mà (Anuncis)
            </h2>
            
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
                        <a href="{{ route('anuncis.show', ['id' => $ad->id, 'slug' => $ad->slug]) }}" class="snap-start flex-shrink-0 w-[47%] bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all hover:border-[#f5c310]/50 flex flex-col">
                            <div class="w-full aspect-[4/3] bg-stone-100 dark:bg-neutral-850 flex items-center justify-center relative overflow-hidden border-b border-stone-100 dark:border-neutral-800">
                                <img class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" src="{{ $coverFoto }}" alt="{{ $ad->titol }}">
                            </div>
                            <div class="p-2.5 flex-grow flex flex-col justify-between text-xs">
                                <div>
                                    <span class="text-[9px] font-extrabold text-[#f5c310] uppercase tracking-wide block">
                                        {{ $ad->marca->nom_marca ?? 'General' }}
                                    </span>
                                    <h4 class="font-bold text-stone-700 dark:text-neutral-300 truncate mt-0.5" title="{{ $ad->titol }}">
                                        {{ $ad->titol }}
                                    </h4>
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-stone-100 dark:border-neutral-850">
                                    <span class="font-black text-stone-900 dark:text-white">
                                        {{ $ad->preu ? $ad->preu.' €' : 'Cons.' }}
                                    </span>
                                    <span class="text-[9px] font-black text-stone-400 dark:text-neutral-500 flex items-center gap-0.5">
                                        Veure <i class="fa-solid fa-chevron-right text-[7px]"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-xs text-stone-400 dark:text-neutral-500 text-center py-8 w-full bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800 rounded-2xl">
                            No hi ha anuncis recents.
                        </div>
                    @endforelse
                </div>
                
                <!-- Controls -->
                <button @click="scrollPrev()" class="absolute left-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-neutral-950/90 border border-stone-200 dark:border-neutral-800 flex items-center justify-center text-stone-700 dark:text-neutral-300 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-stone-50 dark:hover:bg-neutral-850 focus:outline-none">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button @click="scrollNext()" class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-neutral-950/90 border border-stone-200 dark:border-neutral-800 flex items-center justify-center text-stone-700 dark:text-neutral-300 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-stone-50 dark:hover:bg-neutral-850 focus:outline-none">
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
