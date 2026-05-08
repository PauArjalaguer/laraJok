@extends('layout.mainlayout')
@section('title',"Competicions :: JOK.cat ")
@section('content')
<div class="w-full mt-2 mb-4">
    <div class="flex items-center justify-between border-b border-neutral-200 pb-3">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 font-['Comfortaa']">
                <i class="fa-solid fa-trophy text-neutral-500 mr-2"></i>Competicions
            </h1>
            <p class="text-sm text-neutral-500 mt-0.5">Totes les competicions disponibles</p>
        </div>
    </div>
</div>

    <div class="mb-6">
        <input type="text" id="leagueSearch" placeholder="Cerca competició..." class="w-full p-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-neutral-700">
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

            <div class="season-section mb-8" data-season="{{ $league->seasonName }}">
                <h2 class="text-2xl font-bold mb-4 season-title">{{ $league->seasonName }}</h2>
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

            <div class="category-section mb-6" data-category="{{ $league->categoryName }}">
                <h3 class="text-lg font-semibold mt-4 mb-2 category-title"> {{ $league->categoryName }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @php $lastCategory = $league->categoryName; @endphp
        @endif

        {{-- Contingut --}}
        <div class="bg-neutral-200 p-5 rounded-xl place-content-center league-item" data-label="{{ strtolower($league->label) }}">
            <a href="{{ url('competicio/' . $league->value . '/' . Str::slug($league->label)) }}" class="league-link">
                {{ $league->label }}
            </a>
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
