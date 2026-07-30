@extends('layout.mainlayout')
@section('title',"Competicions :: JOK.cat ")
@section('content')
<div class="w-full mt-2 mb-6">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white font-display tracking-tight">
                Competicions
            </h1>
            <p class="text-xs md:text-sm text-stone-500 dark:text-stone-400 mt-1 font-display">
                Explora totes les lligues, categories i grups disponibles
            </p>
        </div>
    </div>
</div>

<div class="mb-8">
    <div class="relative max-w-xl">
        <input type="text" id="leagueSearch" placeholder="Cerca competició o categoria..." class="w-full p-3.5 pl-11 border border-stone-200 dark:border-stone-800 rounded-full bg-white dark:bg-[#121215] text-stone-800 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:outline-none focus:border-[#d4ff00] shadow-xs font-display text-sm transition-all">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"></i>
    </div>
</div>

@php
    $lastSeason = null;
    $lastCategory = null;
@endphp

@foreach($leaguesList as $league)
    {{-- Quan canvïa la season --}}
    @if($lastSeason !== $league->seasonName)
        @if($lastSeason !== null)
                    </div> {{-- tancar grid --}}
                </div> {{-- tancar category-section --}}
            </div> {{-- tancar season-section --}}
        @endif

        <div class="season-section mb-10" data-season="{{ $league->seasonName }}">
            <div class="flex items-center gap-2 pb-2 border-b-2 border-stone-900 dark:border-[#d4ff00] mb-6">
                <i class="fa-solid fa-trophy text-[#d4ff00] text-lg"></i>
                <h2 class="text-xl md:text-2xl font-black text-stone-900 dark:text-white font-display uppercase tracking-tight season-title">
                    Temporada {{ $league->seasonName }}
                </h2>
            </div>
            @php
                $lastSeason = $league->seasonName;
                $lastCategory = null; // reiniciem categories
            @endphp
    @endif

    {{-- Quan canvïa la category --}}
    @if($lastCategory !== $league->categoryName)
        @if($lastCategory !== null)
                </div> {{-- tancar grid --}}
            </div> {{-- tancar category-section --}}
        @endif

        <div class="category-section mb-8" data-category="{{ $league->categoryName }}">
            <h3 class="text-sm font-black text-stone-500 dark:text-stone-400 uppercase tracking-wider mt-4 mb-3 font-display flex items-center gap-2 category-title">
                <span class="w-2 h-2 rounded-full bg-[#d4ff00]"></span>
                {{ $league->categoryName }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                @php $lastCategory = $league->categoryName; @endphp
    @endif

    {{-- Contingut targeta de competició --}}
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 p-4 rounded-2xl shadow-xs hover:border-[#d4ff00] hover:shadow-sm transition-all group league-item flex items-center justify-between" data-label="{{ strtolower($league->label) }}">
        <a href="{{ url('competicio/' . $league->value . '/' . Str::slug($league->label)) }}" class="league-link font-display text-xs md:text-sm font-extrabold text-stone-800 dark:text-stone-200 group-hover:text-[#d4ff00] transition-colors truncate pr-2">
            {{ $league->label }}
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-stone-400 group-hover:text-[#d4ff00] group-hover:translate-x-0.5 transition-all flex-shrink-0"></i>
    </div>

@endforeach

@if($lastSeason !== null)
            </div> {{-- tancar grid --}}
        </div> {{-- tancar category-section --}}
    </div> {{-- tancar season-section --}}
@endif

<script>
    document.getElementById('leagueSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const leagueItems = document.querySelectorAll('.league-item');
        const categorySections = document.querySelectorAll('.category-section');
        const seasonSections = document.querySelectorAll('.season-section');

        leagueItems.forEach(item => {
            const label = item.getAttribute('data-label');
            if (label.includes(searchTerm)) {
                item.classList.remove('llistat-hidden');
            } else {
                item.classList.add('llistat-hidden');
            }
        });

        // Amagar categories que no tinguin items visibles
        categorySections.forEach(section => {
            const visibleItems = section.querySelectorAll('.league-item:not(.llistat-hidden)');
            if (visibleItems.length === 0) {
                section.classList.add('llistat-hidden');
            } else {
                section.classList.remove('llistat-hidden');
            }
        });

        // Amagar temporades que no tinguin categories visibles
        seasonSections.forEach(section => {
            const visibleCategories = section.querySelectorAll('.category-section:not(.llistat-hidden)');
            if (visibleCategories.length === 0) {
                section.classList.add('llistat-hidden');
            } else {
                section.classList.remove('llistat-hidden');
            }
        });
    });
</script>

<style>
    .llistat-hidden {
        display: none !important;
    }
</style>

@endsection
