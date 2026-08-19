@extends('layout.mainlayout')
@section('title',"Notícies :: JOK.cat ")
@section('content')

@php
    if (!function_exists('getNewsSourceBadge')) {
        function getNewsSourceBadge($externalLink) {
            if (empty($externalLink)) {
                return ['name' => 'JOK.cat', 'class' => 'bg-stone-900 text-white dark:bg-stone-200 dark:text-stone-900'];
            }
            $host = parse_url($externalLink, PHP_URL_HOST);
            if (str_contains($host, 'fcbarcelona')) return ['name' => 'FC Barcelona', 'class' => 'bg-blue-900 text-amber-300'];
            if (str_contains($host, 'reusdeportiu')) return ['name' => 'Reus Deportiu', 'class' => 'bg-red-700 text-white'];
            if (str_contains($host, 'hcpalau')) return ['name' => 'HC Palau', 'class' => 'bg-amber-600 text-white'];
            if (str_contains($host, 'cerdanyola')) return ['name' => 'Cerdanyola CH', 'class' => 'bg-emerald-700 text-white'];
            if (str_contains($host, 'regio7')) return ['name' => 'Regió 7', 'class' => 'bg-red-800 text-white'];
            if (str_contains($host, 'cenoia')) return ['name' => 'CE Noia', 'class' => 'bg-stone-900 text-amber-300'];
            if (str_contains($host, 'clubhoqueicaldes')) return ['name' => 'CH Caldes', 'class' => 'bg-red-600 text-white'];
            if (str_contains($host, 'shummassanet')) return ['name' => 'SHUM Maçanet', 'class' => 'bg-red-900 text-amber-300'];
            if (str_contains($host, 'amunthoquei')) return ['name' => 'Arenys de Munt', 'class' => 'bg-amber-500 text-black'];
            return ['name' => 'Notícia', 'class' => 'bg-stone-700 text-white'];
        }
    }
@endphp

<!-- UNIFIED HEADER & SEARCH FILTER BAR -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight">
                Notícies i Novetats
            </h1>
            <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5 font-medium">
                Tota l'actualitat de l'hoquei patins reunida en un sol lloc
            </p>
        </div>

        <!-- SEARCH FORM (TEXT + SOURCE FILTER) -->
        <form method="GET" action="/noticies" class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full md:w-auto">
            <!-- Text Search Input -->
            <div class="relative flex-1 sm:w-60">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-xs pointer-events-none"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Cercar notícies..." 
                    class="w-full pl-9 pr-8 py-2 text-xs font-semibold bg-stone-100 dark:bg-stone-900 text-stone-900 dark:text-white rounded-full border border-stone-200/80 dark:border-stone-800 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                />
                @if(!empty($search))
                    <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 dark:hover:text-stone-200 text-xs" title="Esborrar cerca">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>

            <!-- Source Filter Dropdown -->
            <div class="relative flex-1 sm:flex-none">
                <select 
                    name="source" 
                    onchange="this.form.submit()" 
                    class="w-full appearance-none pl-4 pr-9 py-2 text-xs font-bold bg-stone-100 dark:bg-stone-900 text-stone-900 dark:text-white rounded-full border border-stone-200/80 dark:border-stone-800 focus:outline-none focus:ring-2 focus:ring-primary transition-all cursor-pointer"
                >
                    @foreach($sourcesMap as $key => $label)
                        <option value="{{ $key }}" {{ ($source ?? '') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-[10px] pointer-events-none"></i>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="px-4 py-2 text-xs font-black bg-primary text-primary-text hover:bg-primary-hover dark:bg-stone-800 dark:text-white rounded-full transition-all shadow-xs shrink-0">
                Cercar
            </button>

            <!-- Reset Filters -->
            @if(!empty($search) || !empty($source))
                <a href="/noticies" class="px-3 py-2 text-xs font-bold text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-white transition-colors shrink-0" title="Restablir filtres">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>
</div>

<!-- NEWS FEED CONTAINER -->
@if(count($newsListTop) > 0)
    @php
        $isFirstPage = ($newsListTop->currentPage() == 1);
        $featuredNews = $isFirstPage ? $newsListTop->first() : null;
        $otherNews = $isFirstPage ? $newsListTop->slice(1) : $newsListTop;
    @endphp

    <!-- FEATURED ARTICLE (Top Big Card) -->
    @if($featuredNews)
        @php $featBadge = getNewsSourceBadge($featuredNews->externalLink); @endphp
        <a href="/noticies/detall/{{$featuredNews->idNew}}/{{urlencode(str_replace('/', '-', $featuredNews->newsTitle))}}" class="group block bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl overflow-hidden shadow-xs hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all mb-7">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                <!-- Featured Image -->
                <div class="lg:col-span-6 relative aspect-video lg:aspect-auto overflow-hidden bg-stone-100 dark:bg-stone-900 min-h-[220px]">
                    @if(!empty($featuredNews->newsImage))
                        <img src="{{ $featuredNews->newsImage }}" alt="{{ $featuredNews->newsTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-stone-200 dark:bg-stone-800 text-stone-400">
                            <i class="fa-regular fa-newspaper text-4xl"></i>
                        </div>
                    @endif
                    
                    <!-- Source Badge -->
                    <span class="absolute top-4 left-4 font-black text-[10px] uppercase px-3 py-1 rounded-full shadow-md tracking-wider {{ $featBadge['class'] }}">
                        {{ $featBadge['name'] }}
                    </span>
                </div>

                <!-- Featured Text Content -->
                <div class="lg:col-span-6 p-6 md:p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-extrabold text-stone-500 dark:text-stone-400 mb-3">
                            <i class="fa-regular fa-calendar text-stone-900 dark:text-white"></i>
                            <span>{{ \Carbon\Carbon::parse($featuredNews->newsDatetime)->format('d/m/Y') }}</span>
                        </div>
                        <h2 class="text-xl md:text-3xl font-black text-stone-900 dark:text-white font-display group-hover:text-stone-600 dark:group-hover:text-stone-300 transition-colors leading-tight mb-3">
                            {{ $featuredNews->newsTitle }}
                        </h2>
                        @if(!empty($featuredNews->newsSubtitle))
                            <p class="text-xs md:text-sm text-stone-600 dark:text-stone-300 font-medium line-clamp-3 leading-relaxed mb-4">
                                {{ $featuredNews->newsSubtitle }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-black bg-primary text-primary-text hover:bg-primary-hover dark:bg-stone-800 dark:text-white transition-all shadow-xs">
                            Llegir crònica completa <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </div>
                </div>
            </div>
        </a>
    @endif

    <!-- OTHER ARTICLES GRID -->
    @if(count($otherNews) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($otherNews as $news)
                @php $cardBadge = getNewsSourceBadge($news->externalLink); @endphp
                <a href="/noticies/detall/{{$news->idNew}}/{{urlencode(str_replace('/', '-', $news->newsTitle))}}" class="group bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-4 md:p-5 shadow-xs hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all flex flex-col justify-between">
                    <div>
                        <!-- Image Container -->
                        <div class="relative aspect-video w-full rounded-2xl overflow-hidden bg-stone-100 dark:bg-stone-900 mb-4">
                            @if(!empty($news->newsImage))
                                <img src="{{ $news->newsImage }}" alt="{{ $news->newsTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-stone-200 dark:bg-stone-800 text-stone-400">
                                    <i class="fa-regular fa-newspaper text-3xl"></i>
                                </div>
                            @endif
                            
                            <!-- Source Badge -->
                            <span class="absolute top-3 left-3 font-black text-[9px] uppercase px-2.5 py-0.5 rounded-full shadow-xs {{ $cardBadge['class'] }}">
                                {{ $cardBadge['name'] }}
                            </span>
                        </div>

                        <!-- Date & Title -->
                        <div class="flex items-center gap-1.5 text-[11px] font-extrabold text-stone-500 dark:text-stone-400 mb-2">
                            <i class="fa-regular fa-calendar text-stone-900 dark:text-white"></i>
                            <span>{{ \Carbon\Carbon::parse($news->newsDatetime)->format('d/m/Y') }}</span>
                        </div>
                        <h3 class="text-base font-black text-stone-900 dark:text-white font-display group-hover:text-stone-600 dark:group-hover:text-stone-300 transition-colors leading-snug mb-2 line-clamp-2">
                            {{ $news->newsTitle }}
                        </h3>
                        @if(!empty($news->newsSubtitle))
                            <p class="text-xs text-stone-600 dark:text-stone-400 font-medium line-clamp-2 leading-relaxed mb-4">
                                {{ $news->newsSubtitle }}
                            </p>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <div class="pt-3 border-t border-stone-100 dark:border-stone-800/80">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-stone-100 text-stone-800 dark:bg-stone-900 dark:text-stone-200 group-hover:bg-primary group-hover:text-primary-text dark:group-hover:bg-primary dark:group-hover:text-primary-text transition-all border border-stone-200/80 dark:border-stone-800">
                            Llegir notícia <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- PAGINACIÓ EN CATALÀ -->
    @if($newsListTop->hasPages())
        <div class="my-10 flex flex-wrap items-center justify-center gap-2 font-display text-xs">
            
            {{-- Botó Primera Pàgina --}}
            @if (!$newsListTop->onFirstPage())
                <a href="{{ $newsListTop->url(1) }}" class="w-9 h-9 rounded-full bg-stone-100 hover:bg-primary hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-black flex items-center justify-center transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs" title="Primera pàgina">
                    <i class="fa-solid fa-angles-left text-xs"></i>
                </a>
            @endif

            {{-- Botó Anterior --}}
            @if ($newsListTop->onFirstPage())
                <span class="px-4 py-2 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-400 dark:text-stone-600 font-bold opacity-50 cursor-not-allowed border border-stone-200/50 dark:border-stone-800/50">
                    « Anterior
                </span>
            @else
                <a href="{{ $newsListTop->previousPageUrl() }}" class="px-4 py-2 rounded-full bg-stone-100 hover:bg-primary hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-black transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                    « Anterior
                </a>
            @endif

            {{-- Números de pàgina --}}
            @foreach ($newsListTop->getUrlRange(max(1, $newsListTop->currentPage() - 2), min($newsListTop->lastPage(), $newsListTop->currentPage() + 2)) as $page => $url)
                @if ($page == $newsListTop->currentPage())
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
            @if ($newsListTop->hasMorePages())
                <a href="{{ $newsListTop->nextPageUrl() }}" class="px-4 py-2 rounded-full bg-stone-100 hover:bg-primary hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-black transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                    Següent »
                </a>
            @else
                <span class="px-4 py-2 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-400 dark:text-stone-600 font-bold opacity-50 cursor-not-allowed border border-stone-200/50 dark:border-stone-800/50">
                    Següent »
                </span>
            @endif

            {{-- Botó Última Pàgina --}}
            @if ($newsListTop->currentPage() < $newsListTop->lastPage())
                <a href="{{ $newsListTop->url($newsListTop->lastPage()) }}" class="w-9 h-9 rounded-full bg-stone-100 hover:bg-primary hover:text-primary-text dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary dark:hover:text-primary-text font-black flex items-center justify-center transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs" title="Última pàgina">
                    <i class="fa-solid fa-angles-right text-xs"></i>
                </a>
            @endif
        </div>
    @endif

@else
    <div class="font-display text-xs md:text-sm text-stone-500 dark:text-stone-400 text-center py-16 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl">
        <i class="fa-regular fa-newspaper text-4xl text-stone-400 mb-3 block"></i>
        No s'ha trobat cap notícia que coincideixi amb la cerca o filtre seleccionat.
        <div class="mt-4">
            <a href="/noticies" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-black bg-stone-100 text-stone-800 dark:bg-stone-900 dark:text-stone-200 hover:bg-primary hover:text-primary-text transition-all">
                <i class="fa-solid fa-rotate-left"></i> Veure totes les notícies
            </a>
        </div>
    </div>
@endif

@endsection
