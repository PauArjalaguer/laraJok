@extends('layout.mainlayout')
@section('title', 'Segona Mà :: JOK.cat')

@section('content')

<!-- UNIFIED HEADER (Ultra-Clean Apple Sports) -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight">
                Segona Mà
            </h1>
            <p class="text-xs md:text-sm text-stone-500 dark:text-stone-400 mt-1 font-medium">
                Material i equipament d'hoquei patins de segona mà
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="hallmark-stamp bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 border border-stone-200/80 dark:border-stone-800">
                {{ $anuncis->total() }} {{ $anuncis->total() == 1 ? 'ANUNCI' : 'ANUNCIS' }}
            </span>
            <a href="{{ route('dashboard.anuncis.new') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-primary-text dark:bg-stone-800 dark:text-white hover:bg-primary-hover dark:hover:bg-stone-700 text-black font-black text-xs uppercase tracking-wider rounded-full transition-all shadow-xs active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Publica el teu Anunci</span>
            </a>
        </div>
    </div>
</div>

<!-- SEARCH & FILTERS -->
<form method="GET" action="{{ route('anuncis.index') }}" id="filtresForm" class="mb-7 font-display">
    <input type="hidden" name="lat" id="filter-lat" value="{{ request('lat') }}">
    <input type="hidden" name="lng" id="filter-lng" value="{{ request('lng') }}">

    <!-- Search Input -->
    <div class="relative w-full mb-4">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-xs"></i>
        <input type="text" name="cerca" id="cerca" value="{{ request('cerca') }}" placeholder="Cerca per títol, descripció o marca..." class="w-full pl-9 pr-10 py-2.5 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-900 dark:text-white border border-stone-200 dark:border-stone-800 focus:outline-none focus:border-[#1c1917] dark:focus:border-[#1c1917] text-xs font-medium shadow-xs transition-colors" />
        @if(request('cerca'))
            <button type="button" onclick="clearField('cerca');submitForm();" class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 dark:hover:text-white">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        @endif
    </div>

    <!-- Filter Buttons Row -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">

            <!-- Tipus -->
            <div class="relative" id="dropTipus-wrap">
                <button type="button" onclick="toggleDrop('dropTipus')" class="filter-btn {{ count(request()->get('tipus', [])) ? 'filter-btn-active' : '' }}">
                    <i class="fa-solid fa-layer-group text-xs mr-1"></i>
                    Tipus
                    @if(count(request()->get('tipus', [])))
                        <span class="filter-badge">{{ count(request()->get('tipus', [])) }}</span>
                    @endif
                    <i class="fa-solid fa-chevron-down ml-1 text-[10px]"></i>
                </button>
                <div id="dropTipus" class="filter-dropdown hidden">
                    @foreach($tipus as $t)
                    <label class="filter-option">
                        <input type="checkbox" name="tipus[]" value="{{ $t->id }}" {{ in_array($t->id, request()->get('tipus', [])) ? 'checked' : '' }} onchange="submitForm()">
                        <span>{{ $t->nom_tipus }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Marca -->
            <div class="relative" id="dropMarca-wrap">
                <button type="button" onclick="toggleDrop('dropMarca')" class="filter-btn {{ count(request()->get('marques', [])) ? 'filter-btn-active' : '' }}">
                    <i class="fa-solid fa-tag text-xs mr-1"></i>
                    Marca
                    @if(count(request()->get('marques', [])))
                        <span class="filter-badge">{{ count(request()->get('marques', [])) }}</span>
                    @endif
                    <i class="fa-solid fa-chevron-down ml-1 text-[10px]"></i>
                </button>
                <div id="dropMarca" class="filter-dropdown hidden">
                    <div class="max-h-52 overflow-y-auto">
                    @foreach($marques as $m)
                    <label class="filter-option">
                        <input type="checkbox" name="marques[]" value="{{ $m->id }}" {{ in_array($m->id, request()->get('marques', [])) ? 'checked' : '' }} onchange="submitForm()">
                        <span>{{ $m->nom_marca }}</span>
                    </label>
                    @endforeach
                    </div>
                </div>
            </div>

            <!-- Estat -->
            <div class="relative" id="dropEstat-wrap">
                <button type="button" onclick="toggleDrop('dropEstat')" class="filter-btn {{ request('estat') ? 'filter-btn-active' : '' }}">
                    <i class="fa-solid fa-circle-half-stroke text-xs mr-1"></i>
                    Estat
                    @if(request('estat'))
                        <span class="filter-badge">1</span>
                    @endif
                    <i class="fa-solid fa-chevron-down ml-1 text-[10px]"></i>
                </button>
                <div id="dropEstat" class="filter-dropdown hidden">
                    @foreach($estats as $e)
                    <label class="filter-option">
                        <input type="radio" name="estat" value="{{ $e->id }}" {{ request('estat') == $e->id ? 'checked' : '' }} onchange="submitForm()">
                        <span>{{ $e->nom_estat }}</span>
                    </label>
                    @endforeach
                    @if(request('estat'))
                    <div class="border-t border-stone-200 dark:border-stone-800 mt-1 pt-1">
                        <button type="button" onclick="clearField('estat');submitForm();" class="text-xs text-stone-500 hover:text-stone-900 dark:hover:text-white px-3 py-1 font-bold">
                            <i class="fa-solid fa-xmark mr-1"></i>Treure filtre
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Mida -->
            <div class="relative" id="dropMida-wrap">
                <button type="button" onclick="toggleDrop('dropMida')" class="filter-btn {{ count(request()->get('mides', [])) ? 'filter-btn-active' : '' }}">
                    <i class="fa-solid fa-ruler text-xs mr-1"></i>
                    Mida
                    @if(count(request()->get('mides', [])))
                        <span class="filter-badge">{{ count(request()->get('mides', [])) }}</span>
                    @endif
                    <i class="fa-solid fa-chevron-down ml-1 text-[10px]"></i>
                </button>
                <div id="dropMida" class="filter-dropdown hidden">
                    <div class="max-h-52 overflow-y-auto">
                        <div class="px-3 py-1 text-[10px] font-black text-stone-400 dark:text-stone-500 uppercase tracking-wider">Samarreta</div>
                        @foreach($mides->where('tipus_mida','samarreta') as $mida)
                        <label class="filter-option">
                            <input type="checkbox" name="mides[]" value="{{ $mida->id }}" {{ in_array($mida->id, request()->get('mides', [])) ? 'checked' : '' }} onchange="submitForm()">
                            <span>{{ $mida->nom_mida }}</span>
                        </label>
                        @endforeach
                        <div class="px-3 py-1 text-[10px] font-black text-stone-400 dark:text-stone-500 uppercase tracking-wider mt-1">Calçat</div>
                        @foreach($mides->where('tipus_mida','calcat') as $mida)
                        <label class="filter-option">
                            <input type="checkbox" name="mides[]" value="{{ $mida->id }}" {{ in_array($mida->id, request()->get('mides', [])) ? 'checked' : '' }} onchange="submitForm()">
                            <span>{{ $mida->nom_mida }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Reset Filters Button -->
            @if($filtresActius > 0 || $proximitatActiva)
            <a href="{{ route('anuncis.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-red-500/30 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-extrabold hover:bg-red-100 dark:hover:bg-red-900/50 transition shadow-xs">
                <i class="fa-solid fa-filter-circle-xmark text-xs"></i>
                <span>Netejar filtres</span>
                @if($filtresActius > 0)
                    <span class="bg-red-500 text-white rounded-full text-[10px] w-4 h-4 flex items-center justify-center font-black">{{ $filtresActius }}</span>
                @endif
            </a>
            @endif

        </div>

        <!-- Proximity Button -->
        <div>
            @if($proximitatActiva)
            <a href="{{ route('anuncis.index', request()->except(['lat','lng'])) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-black rounded-full border border-[#1c1917]/40 bg-stone-900 text-stone-900 dark:text-white dark:bg-black transition shadow-xs">
                <i class="fa-solid fa-location-dot"></i> Proximitat activa
                <i class="fa-solid fa-xmark text-[10px] ml-0.5"></i>
            </a>
            @else
            <button type="button" onclick="demanaUbicacio()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full border border-stone-200 dark:border-stone-800 bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 hover:border-primary dark:hover:border-stone-600 dark:hover:border-primary dark:hover:border-stone-600 transition shadow-xs">
                <i class="fa-solid fa-location-crosshairs text-xs"></i> Ordre per proximitat
            </button>
            @endif
        </div>
    </div>
</form>

<!-- ANUNCIS GRID -->
@if($anuncis->isEmpty())
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-12 text-center shadow-xs mb-8 font-display">
        <i class="fa-solid fa-box-open text-4xl text-stone-300 dark:text-stone-700 mb-3 block"></i>
        <h3 class="text-base font-black text-stone-900 dark:text-white">No s'han trobat anuncis</h3>
        <p class="text-xs text-stone-500 dark:text-stone-400 mt-1 font-medium">Prova de canviar els filtres de cerca o la ubicació</p>
        <a href="{{ route('anuncis.index') }}" class="mt-4 inline-flex items-center gap-1 px-4 py-2 rounded-full bg-primary text-primary-text dark:bg-black dark:text-white font-black text-xs uppercase tracking-wider hover:bg-primary text-black dark:bg-stone-800 dark:text-white hover:text-black dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black transition-all">
            Veure tots els anuncis
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-8 font-display">
        @foreach($anuncis as $anunci)
        <a href="{{ route('anuncis.show', $anunci->id) }}" class="anunci-card group bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl overflow-hidden shadow-xs hover:border-primary dark:hover:border-primary dark:hover:border-stone-600 transition-all flex flex-col justify-between" data-fotos="{{ $anunci->fotos->pluck('foto_ruta')->toJson() }}">
            
            <!-- Image Wrap -->
            <div class="relative aspect-[4/3] w-full overflow-hidden bg-stone-100 dark:bg-stone-900 border-b border-stone-100 dark:border-stone-800/60">
                <img src="{{ $anunci->fotos->first()?->foto_ruta ?? 'https://picsum.photos/seed/'.$anunci->id.'/600/450' }}" alt="{{ $anunci->titol }}" class="anunci-img w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />

                <!-- Hover Dots (Multi-photo) -->
                @if($anunci->fotos->count() > 1)
                <div class="anunci-dots absolute bottom-2 left-0 right-0 flex justify-center gap-1 z-10">
                    @foreach($anunci->fotos as $i => $foto)
                    <span class="anunci-dot w-1.5 h-1.5 rounded-full bg-white transition-all duration-200 {{ $i === 0 ? 'opacity-100 scale-125' : 'opacity-50' }}"></span>
                    @endforeach
                </div>
                @endif

                <!-- Type Badge -->
                <div class="absolute top-3 left-3 z-10">
                    <span class="bg-stone-900/90 text-white dark:bg-black/90 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full backdrop-blur-xs border border-stone-800/50">
                        {{ $anunci->tipus->nom_tipus }}
                    </span>
                </div>

                <!-- State Badge -->
                <div class="absolute top-3 right-3 z-10">
                    <span class="bg-white/90 dark:bg-stone-900/90 text-stone-900 dark:text-stone-100 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full backdrop-blur-xs border border-stone-200/80 dark:border-stone-800">
                        {{ $anunci->estat->nom_estat }}
                    </span>
                </div>
            </div>

            <!-- Content Info -->
            <div class="p-4 flex flex-col flex-1 justify-between">
                <div>
                    <div class="flex items-center justify-between text-[11px] font-extrabold text-stone-400 dark:text-stone-500 mb-1 uppercase tracking-wider">
                        <span>{{ $anunci->marca->nom_marca }}</span>
                        <span>{{ $anunci->mida->nom_mida }}</span>
                    </div>

                    <h2 class="text-sm font-black text-stone-900 dark:text-white leading-snug group-hover:text-stone-600 dark:group-hover:text-stone-900 dark:hover:text-white transition-colors line-clamp-2">
                        {{ $anunci->titol }}
                    </h2>
                </div>

                <div class="mt-4 pt-3 border-t border-stone-100 dark:border-stone-800/60 flex items-center justify-between">
                    <div>
                        @if($anunci->preu)
                            <span class="text-base font-black text-stone-900 dark:text-white">
                                {{ number_format($anunci->preu, 0, ',', '.') }} €
                            </span>
                        @else
                            <span class="text-xs font-bold text-stone-400 italic">Consultar</span>
                        @endif
                    </div>

                    <div class="text-right">
                        @if($proximitatActiva && $anunci->distancia !== null)
                            <span class="text-[11px] font-black text-stone-900 dark:text-white block">
                                <i class="fa-solid fa-location-dot mr-0.5"></i>{{ number_format($anunci->distancia, 0, ',', '.') }} km
                            </span>
                        @else
                            <span class="text-[10px] font-extrabold text-stone-400 dark:text-stone-500 block">
                                {{ $anunci->created_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- PAGINATION -->
    @if($anuncis->hasPages())
    <div class="flex justify-center items-center gap-2 py-4 font-display">
        @if($anuncis->onFirstPage())
            <span class="w-9 h-9 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-400 dark:text-stone-600 flex items-center justify-center text-xs font-black cursor-not-allowed">
                <i class="fa-solid fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $anuncis->previousPageUrl() }}" class="w-9 h-9 rounded-full bg-stone-100 dark:bg-stone-900 hover:bg-primary text-black dark:bg-stone-800 dark:text-white hover:text-black dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black text-stone-800 dark:text-stone-200 flex items-center justify-center text-xs font-black transition-all border border-stone-200/80 dark:border-stone-800">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        @endif

        @foreach($anuncis->getUrlRange(1, $anuncis->lastPage()) as $page => $url)
            @if($page == $anuncis->currentPage())
                <span class="w-9 h-9 rounded-full bg-primary text-primary-text dark:bg-primary text-black dark:bg-stone-800 dark:text-white dark:text-black flex items-center justify-center text-xs font-black shadow-xs">
                    {{ $page }}
                </span>
            @elseif($page == 1 || $page == $anuncis->lastPage() || abs($page - $anuncis->currentPage()) <= 2)
                <a href="{{ $url }}" class="w-9 h-9 rounded-full bg-stone-100 dark:bg-stone-900 hover:bg-primary text-black dark:bg-stone-800 dark:text-white hover:text-black dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black text-stone-800 dark:text-stone-200 flex items-center justify-center text-xs font-black transition-all border border-stone-200/80 dark:border-stone-800">
                    {{ $page }}
                </a>
            @elseif(abs($page - $anuncis->currentPage()) == 3)
                <span class="text-stone-400 text-xs font-black px-1">...</span>
            @endif
        @endforeach

        @if($anuncis->hasMorePages())
            <a href="{{ $anuncis->nextPageUrl() }}" class="w-9 h-9 rounded-full bg-stone-100 dark:bg-stone-900 hover:bg-primary text-black dark:bg-stone-800 dark:text-white hover:text-black dark:hover:bg-primary text-black dark:bg-stone-800 dark:text-white dark:hover:text-black text-stone-800 dark:text-stone-200 flex items-center justify-center text-xs font-black transition-all border border-stone-200/80 dark:border-stone-800">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <span class="w-9 h-9 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-400 dark:text-stone-600 flex items-center justify-center text-xs font-black cursor-not-allowed">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
        @endif
    </div>
    @endif
@endif

<!-- STYLES & SCRIPTS -->
<style>
.filter-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.85rem;
    border-radius: 9999px;
    background-color: var(--tw-bg-opacity, #f5f5f4);
    border: 1px solid rgba(229, 231, 235, 0.8);
    font-size: 0.75rem;
    font-weight: 700;
    color: #44403c;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.dark .filter-btn {
    background-color: #1c1917;
    border-color: #292524;
    color: #d6d3d1;
}
.filter-btn:hover { border-color: #1c1917; }
.filter-btn-active { background-color: #1c1917; color: #ffffff; border-color: #1c1917; }
.dark .filter-btn-active { background-color: #1c1917; color: #000000; border-color: #1c1917; }

.filter-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.1rem;
    height: 1.1rem;
    border-radius: 9999px;
    background: #e7e5e4;
    color: #1c1917;
    font-size: 0.625rem;
    font-weight: 900;
    margin-left: 0.35rem;
}
.dark .filter-badge { background: #292524; color: #1c1917; }
.filter-btn-active .filter-badge { background: #1c1917; color: #000; }

.filter-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    z-index: 50;
    min-width: 190px;
    background: #ffffff;
    border: 1px solid #e7e5e4;
    border-radius: 1rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    padding: 0.5rem 0;
}
.dark .filter-dropdown {
    background: #18181b;
    border-color: #27272a;
    box-shadow: 0 10px 25px rgba(0,0,0,0.5);
}
.filter-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.85rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: #292524;
    cursor: pointer;
    transition: background 0.15s;
}
.dark .filter-option { color: #e7e5e4; }
.filter-option:hover { background: #f5f5f4; }
.dark .filter-option:hover { background: #27272a; }
.filter-option input { accent-color: #1c1917; }
</style>

<script>
function toggleDrop(id) {
    const el = document.getElementById(id);
    const isHidden = el.classList.contains('hidden');
    document.querySelectorAll('.filter-dropdown').forEach(d => d.classList.add('hidden'));
    if (isHidden) el.classList.remove('hidden');
}
document.addEventListener('click', (e) => {
    if (!e.target.closest('[id$="-wrap"]')) {
        document.querySelectorAll('.filter-dropdown').forEach(d => d.classList.add('hidden'));
    }
});

function submitForm() {
    document.getElementById('filtresForm').submit();
}
function clearField(name) {
    const el = document.querySelector(`[name="${name}"]`);
    if (el) { el.value = ''; }
}

// Photo hover slider
document.querySelectorAll('.anunci-card').forEach(card => {
    const fotosJson = card.dataset.fotos;
    let fotos = [];
    try { fotos = JSON.parse(fotosJson); } catch(e) { fotos = []; }
    if (fotos.length <= 1) return;

    const img   = card.querySelector('.anunci-img');
    const dots  = card.querySelectorAll('.anunci-dot');
    let current = 0;
    let interval = null;

    const showFoto = (idx) => {
        img.style.opacity = '0';
        setTimeout(() => {
            img.src = fotos[idx];
            img.style.opacity = '1';
        }, 150);
        dots.forEach((d, i) => {
            d.style.opacity = i === idx ? '1' : '0.5';
            d.style.transform = i === idx ? 'scale(1.3)' : 'scale(1)';
        });
    };

    card.addEventListener('mouseenter', () => {
        current = 0;
        interval = setInterval(() => {
            current = (current + 1) % fotos.length;
            showFoto(current);
        }, 900);
    });

    card.addEventListener('mouseleave', () => {
        clearInterval(interval);
        interval = null;
        current = 0;
        img.style.opacity = '0';
        setTimeout(() => {
            img.src = fotos[0];
            img.style.opacity = '1';
        }, 150);
        dots.forEach((d, i) => {
            d.style.opacity = i === 0 ? '1' : '0.5';
            d.style.transform = 'scale(1)';
        });
    });
});

function demanaUbicacio() {
    if (!navigator.geolocation) {
        alert('El teu navegador no suporta geolocalització.');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            document.getElementById('filter-lat').value = pos.coords.latitude;
            document.getElementById('filter-lng').value = pos.coords.longitude;
            document.getElementById('filtresForm').submit();
        },
        function(error) {
            let missatge = 'No s\'ha pogut obtenir la ubicació.';
            if (error.code === 1) missatge = 'Permís d\'ubicació denegat.';
            alert(missatge);
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>

@endsection
