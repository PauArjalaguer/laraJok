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
    (function() {
        const TALLY_KEY = 'jok_tally_last_popup';
        const DAYS_INTERVAL = 14; // Només mostrar com a molt un cop cada 14 dies
        const lastPopup = localStorage.getItem(TALLY_KEY);
        const now = Date.now();

        if (!lastPopup || (now - parseInt(lastPopup)) > DAYS_INTERVAL * 24 * 60 * 60 * 1000) {
            window.TallyConfig = {
                "formId": "EkPjzA",
                "popup": {
                    "emoji": {
                        "text": "👋",
                        "animation": "flash"
                    },
                    "hideTitle": true,
                    "autoClose": 0,
                    "doNotShowAfterClose": true,
                    "formEventsForwarding": true,
                    "open": {
                        "trigger": "scroll",
                        "scrollPercent": 60
                    },
                    "onClose": function() {
                        localStorage.setItem(TALLY_KEY, Date.now());
                    },
                    "onSubmit": function() {
                        localStorage.setItem(TALLY_KEY, Date.now() + 365 * 24 * 60 * 60 * 1000);
                    }
                }
            };
            localStorage.setItem(TALLY_KEY, Date.now());
        }
    })();
</script>

<!-- TOP TICKER BAR (Apple Sports Scoreboard Ticker) -->
<div class="w-full relative group mb-8 hidden">
    <div class="font-display text-[11px] font-extrabold uppercase tracking-wider text-stone-500 dark:text-stone-400 mb-2 px-1 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-[#d4ff00] animate-pulse"></span>
        MARCADORS DIRECTES
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
        </div>

        <div class="flex flex-col gap-1">
            @forelse($matchesListNext as $match)
                <x-matches-component :match="$match" type="upcoming" />
            @empty
                <div class="font-display text-xs text-stone-500 dark:text-stone-400 text-center py-8 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-2xl">
                    No hi ha propers partits programats.
                </div>
            @endforelse
        </div>
    </div>

    <!-- COLUMN 2: CENTER (Segona Mà Banner, Featured News Grid, Pavellons Banner, Merchandising) -->
    <div class="col-span-1 lg:col-span-6">
        <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-4 px-0.5">
            <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                MERCAT SEGONA MÀ
            </h2>
            <a href="/anuncis" class="inline-flex items-center gap-1.5 px-3 py-1 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-[10px] font-black uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs group">
                <span>Anuncis</span>
                <i class="fa-solid fa-arrow-right text-[9px] transition-transform group-hover:translate-x-0.5"></i>
            </a>
        </div>

        <!-- Segona Mà Promo Banner (Over News) -->
        <a href="/anuncis" class="relative block w-full rounded-3xl bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 shadow-xs overflow-hidden group mb-6 transition-all hover:border-[#d4ff00] p-5 md:p-6">
            <!-- Background Image with gradient mask fade -->
            <div class="absolute inset-y-0 right-0 w-full md:w-8/12 bg-cover bg-right bg-no-repeat opacity-40 dark:opacity-30 transition-transform duration-700 group-hover:scale-105 pointer-events-none" style="background-image: url('/images/segona_ma_promo_banner.jpg'); mask-image: linear-gradient(to right, transparent 0%, black 40%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 40%);"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-display text-base md:text-lg font-black text-stone-900 dark:text-white uppercase tracking-tight leading-tight group-hover:text-[#d4ff00] transition-colors">
                        SEGONA MÀ
                    </h3>
                    <p class="font-display text-[11px] font-black text-stone-500 dark:text-stone-400 uppercase tracking-wider mt-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-tags text-[#d4ff00]"></i>
                        <span>COMPRA I VEN EQUIPAMENT D'HOQUEI PATINS</span>
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#d4ff00] text-black font-black text-xs uppercase tracking-wider group-hover:bg-lime-400 transition-colors shadow-xs self-start md:self-auto flex-shrink-0">
                    <span>Explora el Mercat</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-4 px-0.5">
            <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                NOTÍCIES DESTACADES
            </h2>
            <a href="/noticies" class="inline-flex items-center gap-1.5 px-3 py-1 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-[10px] font-black uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs group">
                <span>Més Notícies</span>
                <i class="fa-solid fa-arrow-right text-[9px] transition-transform group-hover:translate-x-0.5"></i>
            </a>
        </div>
        
        <!-- News Layout Grid (1 large hero, 2 stacked) -->
        <div class="flex flex-col md:flex-row gap-4 mb-7">
            <!-- Large Featured News Card -->
            @if(isset($newsListTop[0]))
                @php $n = $newsListTop[0]; @endphp
                <a href="/noticies/detall/{{$n->idNew}}/{{urlencode(str_replace('/', '-', $n->newsTitle))}}" class="group relative flex flex-col justify-end w-full md:w-7/12 h-84 rounded-3xl overflow-hidden shadow-xs hover:border-[#d4ff00] border border-stone-200 dark:border-stone-800 transition-all duration-300">
                    <!-- Image -->
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $n->newsImage }}')"></div>
                    <!-- Dark overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                    <!-- Tag stamp sobre la foto (amagat provisionalment a petició de l'usuari, es conserva l'estructura) -->
                    <div class="hidden absolute top-3.5 left-3.5 hallmark-stamp bg-[#d4ff00] text-black shadow-md">
                        CRÒNICA
                    </div>
                    <!-- Title info -->
                    <div class="relative p-5">
                        <h3 class="font-display text-base md:text-lg font-black text-white leading-tight uppercase mb-1.5 drop-shadow-md group-hover:text-[#d4ff00] transition-colors">
                            {{ $n->newsTitle }}
                        </h3>
                    </div>
                </a>
            @endif

            <!-- Stacked Small News Cards -->
            <div class="w-full md:w-5/12 flex flex-col gap-4">
                @for($i = 1; $i <= 2; $i++)
                    @if(isset($newsListTop[$i]))
                        @php $n = $newsListTop[$i]; @endphp
                        <a href="/noticies/detall/{{$n->idNew}}/{{urlencode(str_replace('/', '-', $n->newsTitle))}}" class="group relative flex flex-col justify-end w-full h-[9.8rem] rounded-2xl overflow-hidden shadow-xs hover:border-[#d4ff00] border border-stone-200 dark:border-stone-800 transition-all duration-300">
                            <!-- Image -->
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $n->newsImage }}')"></div>
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
                            <!-- Content -->
                            <div class="relative p-3.5">
                                <!-- Badge sobre la imatge (amagat provisionalment a petició de l'usuari) -->
                                <span class="hidden hallmark-stamp bg-stone-900 text-[#d4ff00] text-[9px] mb-1">ACTUALITAT</span>
                                <h4 class="font-display text-xs font-bold text-white leading-snug uppercase line-clamp-2 drop-shadow-sm group-hover:text-[#d4ff00] transition-colors">
                                    {{ $n->newsTitle }}
                                </h4>
                            </div>
                        </a>
                    @endif
                @endfor
            </div>
        </div>

        <!-- Pavellons Banner with Map Background -->
        <a href="/pavellons" class="relative block w-full rounded-3xl bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 shadow-xs overflow-hidden group mb-7 transition-all hover:border-[#d4ff00] p-6 md:p-7">
            <!-- Background Map Image positioned on the right with smooth mask fade -->
            <div class="absolute inset-y-0 right-0 w-full md:w-8/12 bg-cover bg-right bg-no-repeat opacity-40 dark:opacity-25 transition-transform duration-700 group-hover:scale-105 pointer-events-none" style="background-image: url('/images/catalonia_map_pavellons.jpg'); mask-image: linear-gradient(to right, transparent 0%, black 40%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 40%);"></div>

            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h3 class="font-display text-lg md:text-xl font-black text-stone-900 dark:text-white uppercase tracking-tight leading-tight group-hover:text-[#d4ff00] transition-colors">
                        PAVELLONS D'HOQUEI A CATALUNYA
                    </h3>
                    <p class="font-display text-xs font-black text-stone-500 dark:text-stone-400 uppercase tracking-wider mt-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot text-[#d4ff00]"></i>
                        <span>CERCA EL TEU PROPER PAVELLÓ</span>
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 flex items-center justify-center text-stone-900 dark:text-[#d4ff00] group-hover:bg-[#d4ff00] group-hover:text-black group-hover:border-[#d4ff00] transition-all duration-300 shadow-xs flex-shrink-0">
                    <i class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-0.5"></i>
                </div>
            </div>
        </a>

        <!-- Merchandising Slider -->
        <div class="mb-7">
            <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                <h2 class="font-display text-xs font-extrabold text-stone-900 dark:text-stone-100 uppercase tracking-wider">
                    MERCHANDISING DESTACAT
                </h2>
                <a href="/merchandising" class="inline-flex items-center gap-1.5 px-3 py-1 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-[10px] font-black uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs group">
                    <span>Botiga</span>
                    <i class="fa-solid fa-arrow-right text-[9px] transition-transform group-hover:translate-x-0.5"></i>
                </a>
            </div>
            
            <div x-data="{ 
                scrollNext() { this.$refs.carousel.scrollBy({ left: 220, behavior: 'smooth' }) },
                scrollPrev() { this.$refs.carousel.scrollBy({ left: -220, behavior: 'smooth' }) }
            }" class="relative w-full group">
                <!-- Carousel -->
                <div x-ref="carousel" class="w-full flex gap-3.5 overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-hide py-1">
                    @foreach($merchandisingList as $merch)
                        <a href="{{ $merch->assetUrl }}" target="_blank" class="snap-start flex-shrink-0 w-[44%] sm:w-[30%] bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-2xl overflow-hidden shadow-xs hover:border-[#d4ff00] transition-all flex flex-col">
                            <div class="w-full aspect-square bg-stone-50 dark:bg-stone-900 p-3 flex items-center justify-center relative overflow-hidden border-b border-stone-100 dark:border-stone-800">
                                <img class="max-w-full max-h-full object-contain rounded-xl transition-transform duration-300 hover:scale-105" src="{{ $merch->assetThumbnail }}" alt="{{ $merch->assetName }}">
                            </div>
                            <div class="p-3 flex-grow flex flex-col justify-between">
                                <h4 class="font-display text-xs font-extrabold text-stone-800 dark:text-stone-200 truncate" title="{{ $merch->assetName }}">
                                    {{ $merch->assetName }}
                                </h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="font-display text-xs font-black text-stone-900 dark:text-white">
                                        {{ $merch->assetPrice ? $merch->assetPrice.' €' : 'Cons.' }}
                                    </span>
                                    <span class="font-display text-[10px] font-black text-stone-700 dark:text-[#d4ff00] uppercase tracking-wider flex items-center gap-0.5">
                                        Comprar <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Controls -->
                <button @click="scrollPrev()" class="absolute left-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-black/90 border border-stone-200 dark:border-stone-800 flex items-center justify-center text-stone-800 dark:text-stone-200 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#d4ff00] hover:text-black focus:outline-none">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button @click="scrollNext()" class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-black/90 border border-stone-200 dark:border-stone-800 flex items-center justify-center text-stone-800 dark:text-stone-200 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#d4ff00] hover:text-black focus:outline-none">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Segona Mà (Darrers Anuncis) Slider -->
        <div class="mb-7">
            <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-3 px-0.5">
                <h2 class="font-display text-xs font-extrabold text-stone-900 dark:text-stone-100 uppercase tracking-wider">
                    SEGONA MÀ (ANUNCIS)
                </h2>
                <a href="/anuncis" class="inline-flex items-center gap-1.5 px-3 py-1 bg-stone-100 hover:bg-stone-900 text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-[#d4ff00] dark:hover:text-black font-display text-[10px] font-black uppercase tracking-wider rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs group">
                    <span>Veure Tots</span>
                    <i class="fa-solid fa-arrow-right text-[9px] transition-transform group-hover:translate-x-0.5"></i>
                </a>
            </div>
            
            <div x-data="{ 
                scrollNext() { this.$refs.carousel.scrollBy({ left: 220, behavior: 'smooth' }) },
                scrollPrev() { this.$refs.carousel.scrollBy({ left: -220, behavior: 'smooth' }) }
            }" class="relative w-full group">
                <!-- Carousel -->
                <div x-ref="carousel" class="w-full flex gap-3.5 overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-hide py-1">
                    @forelse($recentAds as $ad)
                        @php
                            $coverFoto = $ad->fotos->first()?->foto_ruta 
                                ? asset($ad->fotos->first()->foto_ruta) 
                                : 'https://picsum.photos/seed/'.$ad->id.'/200/150';
                        @endphp
                        <a href="{{ route('anuncis.show', ['id' => $ad->id, 'slug' => $ad->slug]) }}" class="snap-start flex-shrink-0 w-[44%] sm:w-[30%] bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-2xl overflow-hidden shadow-xs hover:border-[#d4ff00] transition-all flex flex-col">
                            <div class="w-full aspect-[4/3] bg-stone-100 dark:bg-stone-900 flex items-center justify-center relative overflow-hidden border-b border-stone-200 dark:border-stone-800">
                                <img class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" src="{{ $coverFoto }}" alt="{{ $ad->titol }}">
                            </div>
                            <div class="p-3 flex-grow flex flex-col justify-between text-xs">
                                <div>
                                    <span class="font-display text-[9px] font-black text-[#d4ff00] uppercase tracking-wide block">
                                        {{ $ad->marca->nom_marca ?? 'General' }}
                                    </span>
                                    <h4 class="font-display font-extrabold text-stone-800 dark:text-stone-200 truncate mt-0.5" title="{{ $ad->titol }}">
                                        {{ $ad->titol }}
                                    </h4>
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-stone-200 dark:border-stone-800 font-display">
                                    <span class="font-black text-stone-900 dark:text-white">
                                        {{ $ad->preu ? $ad->preu.' €' : 'Cons.' }}
                                    </span>
                                    <span class="text-[9px] font-bold text-stone-400 flex items-center gap-0.5">
                                        Veure <i class="fa-solid fa-chevron-right text-[7px]"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="font-display text-xs text-stone-500 dark:text-stone-400 text-center py-8 w-full bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-2xl">
                            No hi ha anuncis recents.
                        </div>
                    @endforelse
                </div>
                
                <!-- Controls -->
                <button @click="scrollPrev()" class="absolute left-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-black/90 border border-stone-200 dark:border-stone-800 flex items-center justify-center text-stone-800 dark:text-stone-200 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#d4ff00] hover:text-black focus:outline-none">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button @click="scrollNext()" class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 dark:bg-black/90 border border-stone-200 dark:border-stone-800 flex items-center justify-center text-stone-800 dark:text-stone-200 shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#d4ff00] hover:text-black focus:outline-none">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

    </div>

    <!-- COLUMN 3: RIGHT (Darrers resultats) -->
    <div class="col-span-1 lg:col-span-3">
        <div class="flex items-center justify-between pb-2 border-b border-stone-200 dark:border-stone-800 mb-2 px-0.5">
            <h2 class="font-display text-xs md:text-sm font-extrabold text-stone-900 dark:text-white uppercase tracking-wider">
                DARRERS RESULTATS
            </h2>
        </div>

        <p class="font-display text-xs text-stone-500 dark:text-stone-400 font-medium mb-3 px-0.5 leading-snug">
            Cerca en temps real per equip o competició:
        </p>

        <!-- Dynamic inline filter input -->
        <div class="relative mb-3.5">
            <input type="text" id="resultsFilter" placeholder="Cerca equip o resultat..." class="w-full bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-full px-3.5 py-2 text-xs text-stone-800 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:outline-none focus:border-[#d4ff00] shadow-xs transition-all" />
        </div>

        <!-- Results list -->
        <div class="flex flex-col">
            @forelse($matchesListLastWithResults as $match)
                <div class="result-item transition-all duration-200">
                    <x-matches-component :match="$match" type="result" />
                </div>
            @empty
                <div class="font-mono text-xs text-stone-500 dark:text-stone-400 text-center py-6 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-2xl">
                    No hi ha resultats recents disponibles.
                </div>
            @endforelse
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
