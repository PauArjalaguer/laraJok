@extends('layout.mainlayout')
@section('title', $anunci->titol . ' :: JOK.cat')

@section('content')

@php
$estatColors = [
    'Nou'       => ['bg' => '#16a34a', 'light' => '#dcfce7', 'text' => '#15803d'],
    'Usat'      => ['bg' => '#2563eb', 'light' => '#dbeafe', 'text' => '#1d4ed8'],
    'Molt usat' => ['bg' => '#ea580c', 'light' => '#ffedd5', 'text' => '#c2410c'],
    'Per peces' => ['bg' => '#dc2626', 'light' => '#fee2e2', 'text' => '#b91c1c'],
];
$estat = $estatColors[$anunci->estat->nom_estat] ?? ['bg' => '#6b7280', 'light' => '#f3f4f6', 'text' => '#374151'];
$fotos = $anunci->fotos;
$totalFotos = $fotos->count();
@endphp

{{-- ── BREADCRUMB ──────────────────────────────────────────────────────── --}}
<nav class="flex items-center gap-1.5 text-xs text-neutral-400 mt-2 mb-5 min-w-0" aria-label="breadcrumb">
    <a href="{{ route('anuncis.index') }}" class="hover:text-neutral-700 transition flex-none whitespace-nowrap">Segona Mà</a>
    <i class="fa-solid fa-chevron-right text-[9px] flex-none"></i>
    <span class="text-neutral-500 flex-none whitespace-nowrap">{{ $anunci->tipus->nom_tipus }}</span>
    <i class="fa-solid fa-chevron-right text-[9px] flex-none"></i>
    <span class="text-neutral-700 font-medium truncate min-w-0">{{ $anunci->titol }}</span>
</nav>

{{-- ── LAYOUT PRINCIPAL ────────────────────────────────────────────────── --}}
<div class="lg:flex lg:gap-8 mb-12">

    {{-- ════════════════════════════════════════════════════════
         COLUMNA ESQUERRA: GALERIA
    ════════════════════════════════════════════════════════ --}}
    <div class="lg:w-[55%] xl:w-[58%]">

        {{-- Imatge principal --}}
        <div class="relative rounded-2xl overflow-hidden bg-neutral-100 mb-3 shadow-sm" style="aspect-ratio:4/3;">
            <img
                id="mainPhoto"
                src="{{ $fotos->first()?->foto_ruta ?? 'https://picsum.photos/seed/'.$anunci->id.'/800/600' }}"
                alt="{{ $anunci->titol }}"
                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                style="view-transition-name: anunci-hero;"
            />

            {{-- Badge estat sobre la imatge --}}
            <div class="absolute top-3 left-3 z-10">
                <span class="text-white text-xs font-bold px-3 py-1 rounded-full shadow"
                      style="background:{{ $estat['bg'] }};">
                    {{ $anunci->estat->nom_estat }}
                </span>
            </div>

            {{-- Comptador fotos --}}
            @if($totalFotos > 1)
            <div class="absolute bottom-3 right-3 z-10 bg-black bg-opacity-50 text-white text-xs px-2.5 py-1 rounded-full flex items-center gap-1.5">
                <i class="fa-solid fa-camera text-[10px]"></i>
                <span id="photoCounter">1</span>/{{ $totalFotos }}
            </div>
            @endif

            {{-- Fletxes navegació --}}
            @if($totalFotos > 1)
            <button id="prevBtn" onclick="changePhoto(-1)"
                    class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white bg-opacity-90 shadow-md flex items-center justify-center hover:bg-opacity-100 transition">
                <i class="fa-solid fa-chevron-left text-neutral-700 text-sm"></i>
            </button>
            <button id="nextBtn" onclick="changePhoto(1)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white bg-opacity-90 shadow-md flex items-center justify-center hover:bg-opacity-100 transition">
                <i class="fa-solid fa-chevron-right text-neutral-700 text-sm"></i>
            </button>
            @endif
        </div>

        {{-- Thumbnails --}}
        @if($totalFotos > 1)
        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
            @foreach($fotos as $i => $foto)
            <button
                onclick="selectPhoto({{ $i }})"
                id="thumb-{{ $i }}"
                class="thumb-btn flex-none w-16 h-16 md:w-20 md:h-20 rounded-xl overflow-hidden border-2 transition-all duration-200 {{ $i === 0 ? 'border-neutral-800' : 'border-transparent opacity-60 hover:opacity-100' }}"
            >
                <img src="{{ $foto->foto_ruta }}" alt="Foto {{ $i+1 }}"
                     class="w-full h-full object-cover"/>
            </button>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════
         COLUMNA DRETA: INFORMACIÓ
    ════════════════════════════════════════════════════════ --}}
    <div class="lg:w-[45%] xl:w-[42%] mt-6 lg:mt-0">
        <div class="lg:sticky lg:top-4">

            {{-- Marca + Tipus --}}
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-semibold uppercase tracking-widest text-neutral-400">{{ $anunci->marca->nom_marca }}</span>
                <span class="text-neutral-300">·</span>
                <span class="text-xs bg-neutral-100 text-neutral-600 px-2 py-0.5 rounded-full font-medium">
                    {{ $anunci->tipus->nom_tipus }}
                </span>
            </div>

            {{-- Títol --}}
            <h1 class="text-2xl font-bold text-neutral-900 leading-tight mb-3 font-['Comfortaa']">
                {{ $anunci->titol }}
            </h1>

            {{-- Preu --}}
            <div class="flex items-end gap-3 mb-5">
                @if($anunci->preu)
                    <span class="text-4xl font-extrabold text-neutral-900 tracking-tight">
                        {{ number_format($anunci->preu, 0, ',', '.') }} €
                    </span>
                @else
                    <span class="text-2xl text-neutral-400 italic font-light">Preu a consultar</span>
                @endif
            </div>

            {{-- Card condició / mida --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-4 mb-4 shadow-sm">
                <div class="grid grid-cols-2 gap-3">
                    {{-- Estat --}}
                    <div class="rounded-xl p-3" style="background:{{ $estat['light'] }};">
                        <p class="text-[10px] font-semibold uppercase tracking-widest mb-1" style="color:{{ $estat['text'] }};">Estat</p>
                        <p class="text-sm font-bold" style="color:{{ $estat['bg'] }};">{{ $anunci->estat->nom_estat }}</p>
                    </div>
                    {{-- Mida --}}
                    <div class="rounded-xl bg-neutral-50 p-3 border border-neutral-100">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400 mb-1">Mida</p>
                        <p class="text-sm font-bold text-neutral-800">{{ $anunci->mida->nom_mida }}</p>
                        <p class="text-[10px] text-neutral-400 capitalize">{{ $anunci->mida->tipus_mida }}</p>
                    </div>
                </div>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col gap-2.5 mb-5">
                <a href="mailto:{{ $anunci->usuari?->email ?? 'info@jok.cat' }}?subject=Interès en: {{ urlencode($anunci->titol) }}"
                   class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-neutral-900 text-white font-semibold text-sm hover:bg-neutral-700 active:scale-[0.98] transition-all duration-200 shadow-md">
                    <i class="fa-solid fa-envelope"></i>
                    Contactar amb el venedor
                </a>
                <button onclick="shareAnunci()"
                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl border border-neutral-200 bg-white text-neutral-700 font-medium text-sm hover:bg-neutral-50 hover:border-neutral-300 active:scale-[0.98] transition-all duration-200">
                    <i class="fa-solid fa-share-nodes"></i>
                    Compartir anunci
                </button>
            </div>

            {{-- Info venedor --}}
            @if($anunci->usuari)
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-neutral-50 border border-neutral-100 mb-4">
                <div class="w-10 h-10 rounded-full bg-neutral-200 flex items-center justify-center flex-none">
                    <i class="fa-solid fa-user text-neutral-500"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-400 font-medium">Venedor</p>
                    <p class="text-sm font-semibold text-neutral-800 truncate">{{ $anunci->usuari->name }}</p>
                </div>
                <div class="ml-auto text-right flex-none">
                    <p class="text-[10px] text-neutral-400">Publicat</p>
                    <p class="text-xs text-neutral-600 font-medium">{{ $anunci->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endif

            {{-- Tags info --}}
            <div class="flex flex-wrap gap-2">
                <span class="info-tag"><i class="fa-solid fa-layer-group mr-1"></i>{{ $anunci->tipus->nom_tipus }}</span>
                <span class="info-tag"><i class="fa-solid fa-tag mr-1"></i>{{ $anunci->marca->nom_marca }}</span>
                <span class="info-tag"><i class="fa-solid fa-ruler mr-1"></i>{{ $anunci->mida->nom_mida }}</span>
                <span class="info-tag"><i class="fa-regular fa-clock mr-1"></i>{{ $anunci->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ── DESCRIPCIÓ ──────────────────────────────────────────────────────── --}}
@if($anunci->descripcio)
<div class="mb-10">
    <div class="border-b border-neutral-200 mb-5 flex gap-6">
        <button class="desc-tab desc-tab-active" id="tab-desc" onclick="showTab('desc')">
            Descripció
        </button>
        <button class="desc-tab" id="tab-detalls" onclick="showTab('detalls')">
            Detalls
        </button>
    </div>

    <div id="panel-desc" class="prose prose-neutral max-w-none text-sm text-neutral-700 leading-relaxed">
        {!! nl2br(e($anunci->descripcio)) !!}
    </div>

    <div id="panel-detalls" class="hidden">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="detail-row">
                <span class="detail-label">Marca</span>
                <span class="detail-value">{{ $anunci->marca->nom_marca }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Estat</span>
                <span class="detail-value">{{ $anunci->estat->nom_estat }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Mida</span>
                <span class="detail-value">{{ $anunci->mida->nom_mida }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tipus mida</span>
                <span class="detail-value capitalize">{{ $anunci->mida->tipus_mida }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tipus</span>
                <span class="detail-value">{{ $anunci->tipus->nom_tipus }}</span>
            </div>
            @if($anunci->latitud && $anunci->longitud)
            <div class="detail-row">
                <span class="detail-label">Ubicació</span>
                <a href="https://www.google.com/maps?q={{ $anunci->latitud }},{{ $anunci->longitud }}" target="_blank"
                   class="detail-value text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <i class="fa-solid fa-location-dot"></i>
                    {{ $anunci->nom_ubicacio ?: 'Veure al mapa' }}
                </a>
            </div>
            @endif
            @if($anunci->preu)
            <div class="detail-row">
                <span class="detail-label">Preu</span>
                <span class="detail-value font-bold">{{ number_format($anunci->preu, 0, ',', '.') }} €</span>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ── ANUNCIS RELACIONATS ─────────────────────────────────────────────── --}}
@if($relacionats->count() > 0)
<div class="mb-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-neutral-800 font-['Comfortaa']">
            <i class="fa-solid fa-layer-group text-neutral-400 mr-2 text-base"></i>Més {{ $anunci->tipus->nom_tipus }}
        </h2>
        <a href="{{ route('anuncis.index', ['tipus' => [$anunci->id_tipus]]) }}"
           class="text-xs text-neutral-500 hover:text-neutral-800 transition flex items-center gap-1">
            Veure tots <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach($relacionats as $rel)
        @php
            $relColor = $estatColors[$rel->estat->nom_estat] ?? ['bg' => '#6b7280', 'light' => '#f3f4f6', 'text' => '#374151'];
        @endphp
        <a href="{{ route('anuncis.show', $rel->id) }}"
           class="related-card bg-white border border-neutral-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col group">
            <div class="relative overflow-hidden" style="padding-top:75%;">
                <img
                    src="{{ $rel->fotos->first()?->foto_ruta ?? 'https://picsum.photos/seed/'.$rel->id.'/400/300' }}"
                    alt="{{ $rel->titol }}"
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                />
                <span class="absolute top-2 right-2 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow"
                      style="background:{{ $relColor['bg'] }};">
                    {{ $rel->estat->nom_estat }}
                </span>
            </div>
            <div class="p-3 flex flex-col flex-1">
                <p class="text-[10px] text-neutral-400 font-semibold uppercase tracking-wider mb-0.5">{{ $rel->marca->nom_marca }}</p>
                <p class="text-xs font-semibold text-neutral-800 leading-snug line-clamp-2 mb-auto">{{ $rel->titol }}</p>
                <div class="mt-2 pt-2 border-t border-neutral-100 flex items-center justify-between">
                    @if($rel->preu)
                        <span class="text-sm font-extrabold text-neutral-900">{{ number_format($rel->preu, 0, ',', '.') }} €</span>
                    @else
                        <span class="text-xs text-neutral-400 italic">A consultar</span>
                    @endif
                    <span class="text-[10px] text-neutral-400">{{ $rel->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ── BACK BUTTON ─────────────────────────────────────────────────────── --}}
<div class="flex justify-center mb-6">
    <a href="{{ route('anuncis.index') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-neutral-200 bg-white text-neutral-600 text-sm font-medium hover:bg-neutral-50 hover:border-neutral-300 transition-all duration-200">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        Tornar als anuncis
    </a>
</div>

{{-- ── ESTILS ──────────────────────────────────────────────────────────── --}}
<style>
.info-tag {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.75rem;
    background: #f3f4f6;
    color: #4b5563;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
    border: 1px solid #e5e7eb;
}
.desc-tab {
    padding: 0.6rem 0.25rem;
    margin-bottom: -1px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #9ca3af;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    transition: all .2s;
    background: none;
    border-left: none; border-right: none; border-top: none;
}
.desc-tab:hover { color: #374151; }
.desc-tab-active { color: #111827 !important; border-bottom-color: #111827 !important; font-weight: 600; }

.detail-row {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    padding: 0.75rem 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
}
.detail-label { font-size: 0.7rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
.detail-value { font-size: 0.875rem; color: #1f2937; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

{{-- ── JAVASCRIPT ──────────────────────────────────────────────────────── --}}
<script>
// ── Galeria de fotos ─────────────────────────────────────────────────────────
const fotos = @json($fotos->pluck('foto_ruta'));
let currentIdx = 0;

function selectPhoto(idx) {
    const img = document.getElementById('mainPhoto');
    img.style.opacity = '0';
    setTimeout(() => {
        currentIdx = idx;
        img.src = fotos[idx] ?? img.src;
        img.style.opacity = '1';
        const counter = document.getElementById('photoCounter');
        if (counter) counter.textContent = idx + 1;
        // Actualitza thumbs
        document.querySelectorAll('.thumb-btn').forEach((btn, i) => {
            btn.classList.toggle('border-neutral-800', i === idx);
            btn.classList.toggle('opacity-60', i !== idx);
            btn.classList.toggle('border-transparent', i !== idx);
        });
    }, 150);
}

function changePhoto(dir) {
    if (fotos.length <= 1) return;
    const next = (currentIdx + dir + fotos.length) % fotos.length;
    selectPhoto(next);
}

// Teclat
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft')  changePhoto(-1);
    if (e.key === 'ArrowRight') changePhoto(1);
});

// Swipe mòbil
let touchStartX = 0;
const mainPhoto = document.getElementById('mainPhoto');
if (mainPhoto) {
    mainPhoto.parentElement.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, {passive: true});
    mainPhoto.parentElement.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) changePhoto(diff > 0 ? 1 : -1);
    }, {passive: true});
}

// ── Tabs descripció ──────────────────────────────────────────────────────────
function showTab(tab) {
    ['desc', 'detalls'].forEach(t => {
        document.getElementById('panel-' + t)?.classList.toggle('hidden', t !== tab);
        document.getElementById('tab-' + t)?.classList.toggle('desc-tab-active', t === tab);
    });
}

// ── Share ────────────────────────────────────────────────────────────────────
function shareAnunci() {
    if (navigator.share) {
        navigator.share({
            title: '{{ addslashes($anunci->titol) }}',
            text: 'Mira aquest anunci de Segona Mà a JOK.cat',
            url: window.location.href
        });
    } else {
        navigator.clipboard?.writeText(window.location.href);
        alert('Enllaç copiat al porta-retalls!');
    }
}
</script>

@endsection
