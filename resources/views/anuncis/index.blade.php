@extends('layout.mainlayout')
@section('title', 'Segona Mà :: JOK.cat')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════
     CAPÇALERA
══════════════════════════════════════════════════════════════════════ --}}
<div class="w-full mt-2 mb-4">
    <div class="flex items-center justify-between border-b border-neutral-200 pb-3">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 font-['Comfortaa']">
                <i class="fa-solid fa-tags text-neutral-500 mr-2"></i>Segona Mà
                @if($proximitatActiva)
                    <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full align-middle">
                        <i class="fa-solid fa-location-dot mr-0.5"></i>Proximitat activa
                    </span>
                @endif
            </h1>
            <p class="text-sm text-neutral-500 mt-0.5">
                Material d'hoquei patins de segona mà
            </p>
        </div>
        <div class="text-right">
            <span class="text-sm text-neutral-500">
                <span class="font-semibold text-neutral-700">{{ $anuncis->total() }}</span> anunci{{ $anuncis->total() != 1 ? 's' : '' }} trobat{{ $anuncis->total() != 1 ? 's' : '' }}
            </span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     CERCADOR + FILTRES
══════════════════════════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('anuncis.index') }}" id="filtresForm">

    {{-- Cercador de text --}}
    <div class="relative mb-3">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <i class="fa-solid fa-magnifying-glass text-neutral-400 text-sm"></i>
        </div>
        <input
            type="text"
            name="cerca"
            id="cerca"
            value="{{ request('cerca') }}"
            placeholder="Buscar per títol, descripció o marca..."
            class="w-full pl-9 pr-4 py-2.5 border border-neutral-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-neutral-400 focus:border-transparent bg-white transition"
        />
        @if(request('cerca'))
        <button type="button" onclick="clearField('cerca')" class="absolute inset-y-0 right-3 flex items-center text-neutral-400 hover:text-neutral-700">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
        @endif
    </div>
    <input type="hidden" name="lat" id="filter-lat" value="{{ request('lat') }}">
    <input type="hidden" name="lng" id="filter-lng" value="{{ request('lng') }}">

    {{-- Fila de filtres + Botó Nou --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex flex-wrap gap-2">

        {{-- Tipus --}}
        <div class="relative" id="dropTipus-wrap">
            <button type="button" onclick="toggleDrop('dropTipus')"
                class="filter-btn {{ count(request()->get('tipus', [])) ? 'filter-btn-active' : '' }}"
                id="dropTipus-toggle">
                <i class="fa-solid fa-layer-group mr-1 text-xs"></i>
                Tipus
                @if(count(request()->get('tipus', [])))
                    <span class="filter-badge">{{ count(request()->get('tipus', [])) }}</span>
                @endif
                <i class="fa-solid fa-chevron-down ml-1 text-[10px]"></i>
            </button>
            <div id="dropTipus" class="filter-dropdown hidden">
                @foreach($tipus as $t)
                <label class="filter-option">
                    <input type="checkbox" name="tipus[]" value="{{ $t->id }}"
                        {{ in_array($t->id, request()->get('tipus', [])) ? 'checked' : '' }}
                        onchange="submitForm()">
                    {{ $t->nom_tipus }}
                </label>
                @endforeach
            </div>
        </div>

        {{-- Marca (múltiple) --}}
        <div class="relative" id="dropMarca-wrap">
            <button type="button" onclick="toggleDrop('dropMarca')"
                class="filter-btn {{ count(request()->get('marques', [])) ? 'filter-btn-active' : '' }}"
                id="dropMarca-toggle">
                <i class="fa-solid fa-tag mr-1 text-xs"></i>
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
                    <input type="checkbox" name="marques[]" value="{{ $m->id }}"
                        {{ in_array($m->id, request()->get('marques', [])) ? 'checked' : '' }}
                        onchange="submitForm()">
                    {{ $m->nom_marca }}
                </label>
                @endforeach
                </div>
            </div>
        </div>

        {{-- Estat --}}
        <div class="relative" id="dropEstat-wrap">
            <button type="button" onclick="toggleDrop('dropEstat')"
                class="filter-btn {{ request('estat') ? 'filter-btn-active' : '' }}"
                id="dropEstat-toggle">
                <i class="fa-solid fa-circle-half-stroke mr-1 text-xs"></i>
                Estat
                @if(request('estat'))
                    <span class="filter-badge">1</span>
                @endif
                <i class="fa-solid fa-chevron-down ml-1 text-[10px]"></i>
            </button>
            <div id="dropEstat" class="filter-dropdown hidden">
                @foreach($estats as $e)
                <label class="filter-option">
                    <input type="radio" name="estat" value="{{ $e->id }}"
                        {{ request('estat') == $e->id ? 'checked' : '' }}
                        onchange="submitForm()">
                    {{ $e->nom_estat }}
                </label>
                @endforeach
                @if(request('estat'))
                <div class="border-t border-neutral-100 mt-1 pt-1">
                    <button type="button" onclick="clearField('estat');submitForm();" class="text-xs text-neutral-500 hover:text-neutral-700 px-3 py-1">
                        <i class="fa-solid fa-xmark mr-1"></i>Treure filtre
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Mida (múltiple) --}}
        <div class="relative" id="dropMida-wrap">
            <button type="button" onclick="toggleDrop('dropMida')"
                class="filter-btn {{ count(request()->get('mides', [])) ? 'filter-btn-active' : '' }}"
                id="dropMida-toggle">
                <i class="fa-solid fa-ruler mr-1 text-xs"></i>
                Mida
                @if(count(request()->get('mides', [])))
                    <span class="filter-badge">{{ count(request()->get('mides', [])) }}</span>
                @endif
                <i class="fa-solid fa-chevron-down ml-1 text-[10px]"></i>
            </button>
            <div id="dropMida" class="filter-dropdown hidden">
                <div class="max-h-52 overflow-y-auto">
                    <div class="px-3 py-1 text-[10px] font-semibold text-neutral-400 uppercase tracking-wider">Samarreta</div>
                    @foreach($mides->where('tipus_mida','samarreta') as $mida)
                    <label class="filter-option">
                        <input type="checkbox" name="mides[]" value="{{ $mida->id }}"
                            {{ in_array($mida->id, request()->get('mides', [])) ? 'checked' : '' }}
                            onchange="submitForm()">
                        {{ $mida->nom_mida }}
                    </label>
                    @endforeach
                    <div class="px-3 py-1 text-[10px] font-semibold text-neutral-400 uppercase tracking-wider mt-1">Calçat</div>
                    @foreach($mides->where('tipus_mida','calcat') as $mida)
                    <label class="filter-option">
                        <input type="checkbox" name="mides[]" value="{{ $mida->id }}"
                            {{ in_array($mida->id, request()->get('mides', [])) ? 'checked' : '' }}
                            onchange="submitForm()">
                        {{ $mida->nom_mida }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Botó netejar tots els filtres --}}
        @if($filtresActius > 0 || $proximitatActiva)
        <a href="{{ route('anuncis.index') }}"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-red-200 bg-red-50 text-red-600 text-sm hover:bg-red-100 transition">
            <i class="fa-solid fa-filter-slash text-xs"></i>
            Netejar filtres
            @if($filtresActius > 0)
                <span class="bg-red-500 text-white rounded-full text-[10px] w-4 h-4 flex items-center justify-center font-bold">{{ $filtresActius }}</span>
            @endif
        </a>
        @endif

        </div><!-- /filtres -->

        {{-- Botó Proximitat + Nou Anunci --}}
        <div class="flex items-center gap-2">
            @if($proximitatActiva)
            <a href="{{ route('anuncis.index', request()->except(['lat','lng'])) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 transition">
                <i class="fa-solid fa-location-crosshairs"></i> Proximitat activa
                <i class="fa-solid fa-xmark ml-0.5"></i>
            </a>
            @else
            <button type="button" onclick="demanaUbicacio()"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-neutral-300 bg-white text-neutral-600 hover:bg-neutral-50 transition">
                <i class="fa-solid fa-location-crosshairs"></i> Ordre per proximitat
            </button>
            @endif

            <a href="{{ route('dashboard.anuncis.new') }}"
               class="inline-flex items-center px-4 py-2 bg-neutral-800 text-white text-sm font-semibold rounded-xl hover:bg-neutral-700 transition-all duration-200 shadow-sm active:scale-95">
                <i class="fa-solid fa-plus mr-1.5"></i>
                Nou Anunci
            </a>
        </div>

    </div><!-- /fila-filtres -->
</form>

{{-- ══════════════════════════════════════════════════════════════════════
     GRID D'ANUNCIS
══════════════════════════════════════════════════════════════════════ --}}
@if($anuncis->isEmpty())
    <div class="text-center py-20 text-neutral-400">
        <i class="fa-solid fa-box-open text-5xl mb-4 block"></i>
        <p class="text-lg">No s'han trobat anuncis amb aquests filtres.</p>
        <a href="{{ route('anuncis.index') }}" class="mt-4 inline-block text-sm text-neutral-500 underline">Veure tots els anuncis</a>
    </div>
@else
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-6">
    @foreach($anuncis as $anunci)
    <a href="{{ route('anuncis.show', $anunci->id) }}"
       class="anunci-card bg-white border border-neutral-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 flex flex-col"
       data-fotos="{{ $anunci->fotos->pluck('foto_ruta')->toJson() }}">

        {{-- ── Zona de fotos amb slider en hover ── --}}
        <div class="anunci-foto-wrap relative overflow-hidden rounded-t-2xl bg-neutral-100"
             style="padding-top: 75%;">

            {{-- Imatge activa --}}
            <img
                src="{{ $anunci->fotos->first()?->foto_ruta ?? 'https://picsum.photos/seed/default/600/450' }}"
                alt="{{ $anunci->titol }}"
                class="anunci-img absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                loading="lazy"
            />

            {{-- Dots indicadors (si té >1 foto) --}}
            @if($anunci->fotos->count() > 1)
            <div class="anunci-dots absolute bottom-1.5 left-0 right-0 flex justify-center gap-1 z-10">
                @foreach($anunci->fotos as $i => $foto)
                <span class="anunci-dot w-1.5 h-1.5 rounded-full bg-white transition-all duration-200 {{ $i === 0 ? 'opacity-100' : 'opacity-50' }}"></span>
                @endforeach
            </div>
            @endif

            {{-- Badge tipus --}}
            <div class="absolute top-2 left-2 z-10">
                <span class="bg-neutral-800 bg-opacity-80 text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">
                    {{ $anunci->tipus->nom_tipus }}
                </span>
            </div>

            {{-- Badge estat --}}
            <div class="absolute top-2 right-2 z-10">
                @php
                    $estatColors = [
                        'Nou'       => 'bg-green-500',
                        'Usat'      => 'bg-blue-500',
                        'Molt usat' => 'bg-orange-500',
                        'Per peces' => 'bg-red-500',
                    ];
                    $color = $estatColors[$anunci->estat->nom_estat] ?? 'bg-neutral-500';
                @endphp
                <span class="text-white text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $color }}">
                    {{ $anunci->estat->nom_estat }}
                </span>
            </div>
        </div>

        {{-- ── Info de l'anunci ── --}}
        <div class="p-3 flex flex-col flex-1">
            <p class="text-[11px] text-neutral-400 font-medium mb-0.5">{{ $anunci->marca->nom_marca }}</p>
            <h2 class="text-sm font-semibold text-neutral-800 leading-snug mb-1 line-clamp-2">{{ $anunci->titol }}</h2>
            <p class="text-[11px] text-neutral-400 mb-2">
                <i class="fa-solid fa-ruler-combined mr-0.5"></i>{{ $anunci->mida->nom_mida }}
                @if($proximitatActiva && $anunci->distancia !== null)
                    <span class="ml-1 text-green-600 font-medium">
                        &middot; <i class="fa-solid fa-location-dot mr-0.5"></i>{{ number_format($anunci->distancia, 0, ',', '.') }} km
                    </span>
                @endif
            </p>
            <div class="mt-auto flex items-center justify-between">
                @if($anunci->preu)
                    <span class="text-base font-bold text-neutral-900">{{ number_format($anunci->preu, 0, ',', '.') }} €</span>
                @else
                    <span class="text-sm text-neutral-400 italic">Sense preu</span>
                @endif
                <div class="text-right">
                    <span class="text-[10px] text-neutral-300 block">{{ $anunci->created_at->diffForHumans() }}</span>
                    @if($anunci->visites > 0)
                        <span class="text-[9px] text-neutral-400">
                            <i class="fa-solid fa-eye"></i> {{ $anunci->visites }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     PAGINACIÓ
══════════════════════════════════════════════════════════════════════ --}}
@if($anuncis->hasPages())
<div class="flex justify-center items-center gap-1.5 py-6">

    {{-- Anterior --}}
    @if($anuncis->onFirstPage())
        <span class="pag-btn pag-btn-disabled"><i class="fa-solid fa-chevron-left text-xs"></i></span>
    @else
        <a href="{{ $anuncis->previousPageUrl() }}" class="pag-btn"><i class="fa-solid fa-chevron-left text-xs"></i></a>
    @endif

    {{-- Números de pàgina --}}
    @foreach($anuncis->getUrlRange(1, $anuncis->lastPage()) as $page => $url)
        @if($page == $anuncis->currentPage())
            <span class="pag-btn pag-btn-active">{{ $page }}</span>
        @elseif($page == 1 || $page == $anuncis->lastPage() || abs($page - $anuncis->currentPage()) <= 2)
            <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
        @elseif(abs($page - $anuncis->currentPage()) == 3)
            <span class="pag-btn pag-btn-disabled">…</span>
        @endif
    @endforeach

    {{-- Següent --}}
    @if($anuncis->hasMorePages())
        <a href="{{ $anuncis->nextPageUrl() }}" class="pag-btn"><i class="fa-solid fa-chevron-right text-xs"></i></a>
    @else
        <span class="pag-btn pag-btn-disabled"><i class="fa-solid fa-chevron-right text-xs"></i></span>
    @endif

</div>
@endif

@endif {{-- /isEmpty --}}

{{-- ══════════════════════════════════════════════════════════════════════
     ESTILS INLINE
══════════════════════════════════════════════════════════════════════ --}}
<style>
/* Filtres */
.filter-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.85rem;
    border: 1px solid #d1d5db;
    border-radius: 9999px;
    background: #fff;
    font-size: 0.8125rem;
    color: #374151;
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
    user-select: none;
}
.filter-btn:hover { background: #f3f4f6; border-color: #9ca3af; }
.filter-btn-active { background: #1f2937; color: #fff; border-color: #1f2937; }
.filter-btn-active:hover { background: #374151; }
.filter-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.1rem;
    height: 1.1rem;
    border-radius: 9999px;
    background: #e5e7eb;
    color: #374151;
    font-size: 0.625rem;
    font-weight: 700;
    margin-left: 0.35rem;
}
.filter-btn-active .filter-badge { background: #4b5563; color: #fff; }
.filter-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    z-index: 50;
    min-width: 180px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,.1);
    padding: 0.5rem 0;
}
.filter-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.85rem;
    font-size: 0.8125rem;
    color: #374151;
    cursor: pointer;
    transition: background .15s;
}
.filter-option:hover { background: #f9fafb; }
.filter-option input { accent-color: #1f2937; }

/* Paginació */
.pag-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    font-size: 0.8125rem;
    color: #374151;
    text-decoration: none;
    transition: all .2s;
}
.pag-btn:hover { background: #f3f4f6; border-color: #9ca3af; }
.pag-btn-active { background: #1f2937 !important; color: #fff !important; border-color: #1f2937 !important; font-weight: 700; }
.pag-btn-disabled { color: #d1d5db; cursor: default; }
.pag-btn-disabled:hover { background: #fff; border-color: #e5e7eb; }

/* Card */
.anunci-card { cursor: pointer; }
.anunci-foto-wrap { cursor: pointer; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

{{-- ══════════════════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════════════════ --}}
<script>
// ── Filtres: obrir/tancar dropdowns ──────────────────────────────────────────
function toggleDrop(id) {
    const el = document.getElementById(id);
    const isHidden = el.classList.contains('hidden');
    // Tanca tots
    document.querySelectorAll('.filter-dropdown').forEach(d => d.classList.add('hidden'));
    if (isHidden) el.classList.remove('hidden');
}
// Tanca dropdowns en fer clic fora
document.addEventListener('click', (e) => {
    if (!e.target.closest('[id$="-wrap"]')) {
        document.querySelectorAll('.filter-dropdown').forEach(d => d.classList.add('hidden'));
    }
});

// ── Enviar formulari en canviar filtre ────────────────────────────────────────
function submitForm() {
    document.getElementById('filtresForm').submit();
}
function clearField(name) {
    const el = document.querySelector(`[name="${name}"]`);
    if (el) { el.value = ''; }
}

// ── Hover slider de fotos ─────────────────────────────────────────────────────
document.querySelectorAll('.anunci-card').forEach(card => {
    const fotosJson = card.dataset.fotos;
    let fotos = [];
    try { fotos = JSON.parse(fotosJson); } catch(e) { fotos = []; }

    if (fotos.length <= 1) return; // res a fer si només hi ha 1 foto

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

// ── View Transition: marca la imatge de la card clicada com a "hero" ──────────
// La pàgina de detall té view-transition-name:anunci-hero a la imatge principal.
// Quan fem clic a una card, posem el mateix nom a la imatge de la card
// perquè el navegador pugui animar-la com a element compartit (Chrome 126+).
document.querySelectorAll('a.anunci-card').forEach(card => {
    card.addEventListener('click', function() {
        const img = this.querySelector('.anunci-img');
        if (img) img.style.viewTransitionName = 'anunci-hero';
    });
});

function demanaUbicacio() {
    if (!navigator.geolocation) {
        alert('El teu navegador no suporta geolocalització.');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            document.getElementById('filter-lat').value = lat;
            document.getElementById('filter-lng').value = lng;
            document.getElementById('filtresForm').submit();
        },
        function(error) {
            let missatge = 'No s\'ha pogut obtenir la ubicació.';
            if (error.code === 1) missatge = 'Permís de ubicació denegat. Activa\'l al navegador per ordenar per proximitat.';
            if (error.code === 2) missatge = 'Ubicació no disponible.';
            if (error.code === 3) missatge = 'Temps d\'espera esgotat.';
            alert(missatge);
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>

@endsection
