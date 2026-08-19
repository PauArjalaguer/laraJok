@extends('layout.mainlayout')
@section('title',"Notícies :: JOK.cat ")
@section('content')

<!-- UNIFIED HEADER (Ultra-Clean Apple Sports) -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight">
                Notícies i Novetats
            </h1>
        </div>
    </div>
</div>

<!-- NEWS FEED CONTAINER -->
@if(count($newsListTop) > 0)
    @php
        $featuredNews = $newsListTop->first();
        $otherNews = $newsListTop->slice(1);
    @endphp

    <!-- FEATURED ARTICLE (Top Big Card) -->
    @if($featuredNews)
        <a href="/noticies/detall/{{$featuredNews->idNew}}/{{urlencode(str_replace('/', '-', $featuredNews->newsTitle))}}" class="group block bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl overflow-hidden shadow-xs hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all mb-7">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                <!-- Featured Image -->
                <div class="lg:col-span-6 relative aspect-video lg:aspect-auto overflow-hidden bg-stone-100 dark:bg-stone-900">
                    @if(!empty($featuredNews->newsImage))
                        <img src="{{ $featuredNews->newsImage }}" alt="{{ $featuredNews->newsTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-stone-200 dark:bg-stone-800 text-stone-400">
                            <i class="fa-regular fa-newspaper text-4xl"></i>
                        </div>
                    @endif
                    <!-- Badge sobre la imatge (amagat provisionalment a petició de l'usuari, es conserva l'estructura) -->
                    <span class="hidden absolute top-4 left-4 bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black text-[10px] uppercase px-3 py-1 rounded-full shadow-md tracking-wider">
                        DESTACAT
                    </span>
                </div>

                <!-- Featured Text Content -->
                <div class="lg:col-span-6 p-6 md:p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-extrabold text-stone-500 dark:text-stone-400 mb-3">
                            <i class="fa-regular fa-calendar text-stone-900 dark:text-white"></i>
                            <span>{{ \Carbon\Carbon::parse($featuredNews->newsDatetime)->format('d/m/Y') }}</span>
                        </div>
                        <h2 class="text-xl md:text-3xl font-black text-stone-900 dark:text-white font-display group-hover:text-stone-600 dark:group-hover:text-stone-900 dark:hover:text-white transition-colors leading-tight mb-3">
                            {{ $featuredNews->newsTitle }}
                        </h2>
                        @if(!empty($featuredNews->newsSubtitle))
                            <p class="text-xs md:text-sm text-stone-600 dark:text-stone-300 font-medium line-clamp-3 leading-relaxed mb-4">
                                {{ $featuredNews->newsSubtitle }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-black bg-primary text-primary-text dark:bg-black dark:text-white group-hover:bg-primary text-black dark:bg-stone-800 dark:text-white group-hover:text-black dark:group-hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:group-hover:text-black transition-all shadow-xs">
                            Llegir crònica completa <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </div>
                </div>
            </div>
        </a>
    @endif

    <!-- OTHER ARTICLES GRID -->
    @if(count($otherNews) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($otherNews as $news)
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
                            <!-- Badge sobre la foto de la notícia (amagat provisionalment a petició de l'usuari) -->
                            <span class="hidden absolute top-3 left-3 bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black text-[9px] uppercase px-2.5 py-0.5 rounded-full shadow-xs">
                                NOTÍCIA
                            </span>
                        </div>

                        <!-- Date & Title -->
                        <div class="flex items-center gap-1.5 text-[11px] font-extrabold text-stone-500 dark:text-stone-400 mb-2">
                            <i class="fa-regular fa-calendar text-stone-900 dark:text-white"></i>
                            <span>{{ \Carbon\Carbon::parse($news->newsDatetime)->format('d/m/Y') }}</span>
                        </div>
                        <h3 class="text-base md:text-lg font-black text-stone-900 dark:text-white font-display group-hover:text-stone-600 dark:group-hover:text-stone-900 dark:hover:text-white transition-colors leading-snug mb-2">
                            {{ $news->newsTitle }}
                        </h3>
                        @if(!empty($news->newsSubtitle))
                            <p class="text-xs text-stone-600 dark:text-stone-400 font-medium line-clamp-2 leading-relaxed mb-4">
                                {{ $news->newsSubtitle }}
                            </p>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <div class="pt-2 border-t border-stone-100 dark:border-stone-800/80">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-stone-100 text-stone-800 dark:bg-stone-900 dark:text-stone-200 group-hover:bg-primary text-black dark:bg-stone-800 dark:text-white group-hover:text-black dark:group-hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:group-hover:text-black transition-all border border-stone-200/80 dark:border-stone-800">
                            Llegir notícia <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- PAGINACIÓ EN CATALÀ (Amb Primera i Última - Estil Càpsula Apple Sports) -->
    @if($newsListTop->hasPages())
        <div class="my-10 flex flex-wrap items-center justify-center gap-2 font-display text-xs">
            
            {{-- Botó Primera Pàgina (Només Icona) --}}
            @if (!$newsListTop->onFirstPage())
                <a href="{{ $newsListTop->url(1) }}" class="w-9 h-9 rounded-full bg-stone-100 hover:bg-primary text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black font-black flex items-center justify-center transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs" title="Primera pàgina">
                    <i class="fa-solid fa-angles-left text-xs"></i>
                </a>
            @endif

            {{-- Botó Anterior --}}
            @if ($newsListTop->onFirstPage())
                <span class="px-4 py-2 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-400 dark:text-stone-600 font-bold opacity-50 cursor-not-allowed border border-stone-200/50 dark:border-stone-800/50">
                    « Anterior
                </span>
            @else
                <a href="{{ $newsListTop->previousPageUrl() }}" class="px-4 py-2 rounded-full bg-stone-100 hover:bg-primary text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black font-black transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
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
                <a href="{{ $newsListTop->nextPageUrl() }}" class="px-4 py-2 rounded-full bg-stone-100 hover:bg-primary text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black font-black transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs">
                    Següent »
                </a>
            @else
                <span class="px-4 py-2 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-400 dark:text-stone-600 font-bold opacity-50 cursor-not-allowed border border-stone-200/50 dark:border-stone-800/50">
                    Següent »
                </span>
            @endif

            {{-- Botó Última Pàgina (Només Icona) --}}
            @if ($newsListTop->currentPage() < $newsListTop->lastPage())
                <a href="{{ $newsListTop->url($newsListTop->lastPage()) }}" class="w-9 h-9 rounded-full bg-stone-100 hover:bg-primary text-stone-800 hover:text-white dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black font-black flex items-center justify-center transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs" title="Última pàgina">
                    <i class="fa-solid fa-angles-right text-xs"></i>
                </a>
            @endif
        </div>
    @endif

@else
    <div class="font-display text-xs md:text-sm text-stone-500 dark:text-stone-400 text-center py-12 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl">
        <i class="fa-regular fa-newspaper text-3xl text-stone-400 mb-2 block"></i>
        No hi ha notícies disponibles actualment.
    </div>
@endif

@endsection
