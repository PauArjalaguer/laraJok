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

<!-- BACK BUTTON & BREADCRUMB -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-200 dark:border-stone-800 pb-4">
        <a href="{{ route('anuncis.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-stone-100 dark:bg-stone-900 hover:bg-primary hover:text-primary-text dark:hover:bg-primary dark:hover:text-primary-text text-stone-700 dark:text-stone-300 font-display text-xs font-black rounded-full transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs group">
            <i class="fa-solid fa-arrow-left text-[10px] group-hover:-translate-x-0.5 transition-transform"></i>
            <span>Tornar als anuncis</span>
        </a>

        <nav class="flex items-center gap-1.5 text-xs text-stone-400 dark:text-stone-500 font-bold" aria-label="breadcrumb">
            <a href="{{ route('anuncis.index') }}" class="hover:text-stone-700 dark:hover:text-stone-200 transition">Segona Mà</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-stone-600 dark:text-stone-300">{{ $anunci->tipus->nom_tipus }}</span>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-stone-900 dark:text-white font-black truncate max-w-[200px]">{{ $anunci->titol }}</span>
        </nav>
    </div>
</div>

<!-- MAIN CONTENT LAYOUT -->
<div class="lg:flex lg:gap-8 mb-12 font-display">

    <!-- LEFT COLUMN: GALLERY -->
    <div class="lg:w-[55%] xl:w-[58%]">
        <!-- Main Image Card -->
        <div class="relative rounded-3xl overflow-hidden bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 mb-3 shadow-xs" style="aspect-ratio:4/3;">
            <img id="mainPhoto" src="{{ $fotos->first()?->foto_ruta ?? 'https://picsum.photos/seed/'.$anunci->id.'/800/600' }}" alt="{{ $anunci->titol }}" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300" style="view-transition-name: anunci-hero;" />

            <!-- State Badge -->
            <div class="absolute top-4 left-4 z-10">
                <span class="bg-white/90 dark:bg-stone-900/90 text-stone-900 dark:text-stone-100 font-black text-xs uppercase px-3 py-1 rounded-full border border-stone-200/80 dark:border-stone-800 shadow-md backdrop-blur-xs">
                    {{ $anunci->estat->nom_estat }}
                </span>
            </div>

            <!-- Counter Badge -->
            @if($totalFotos > 1)
            <div class="absolute bottom-4 right-4 z-10 bg-black/70 text-white font-black text-xs px-3 py-1 rounded-full flex items-center gap-1.5 backdrop-blur-xs shadow-md">
                <i class="fa-solid fa-camera text-[10px] text-stone-900 dark:text-white"></i>
                <span id="photoCounter">1</span>/{{ $totalFotos }}
            </div>
            @endif

            <!-- Navigation Arrows -->
            @if($totalFotos > 1)
            <button id="prevBtn" onclick="changePhoto(-1)" class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 dark:bg-stone-900/90 shadow-md flex items-center justify-center text-stone-900 dark:text-white hover:bg-primary hover:text-primary-text dark:hover:bg-primary dark:hover:text-primary-text transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <button id="nextBtn" onclick="changePhoto(1)" class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 dark:bg-stone-900/90 shadow-md flex items-center justify-center text-stone-900 dark:text-white hover:bg-primary hover:text-primary-text dark:hover:bg-primary dark:hover:text-primary-text transition-all">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
            @endif
        </div>

        <!-- Thumbnail Row -->
        @if($totalFotos > 1)
        <div class="flex items-center gap-2.5 overflow-x-auto pb-2 scrollbar-none">
            @foreach($fotos as $index => $foto)
            <button onclick="selectPhoto({{ $index }}, '{{ $foto->foto_ruta }}')" class="photo-thumb relative w-16 h-16 md:w-20 md:h-20 rounded-2xl overflow-hidden border-2 transition-all flex-shrink-0 {{ $index === 0 ? 'border-primary shadow-xs' : 'border-stone-200 dark:border-stone-800 opacity-60 hover:opacity-100' }}">
                <img src="{{ $foto->foto_ruta }}" alt="" class="w-full h-full object-cover" />
            </button>
            @endforeach
        </div>
        @endif
    </div>

    <!-- RIGHT COLUMN: DETAILS & ACTIONS -->
    <div class="lg:w-[45%] xl:w-[42%] flex flex-col justify-between mt-6 lg:mt-0">
        <div>
            <!-- Header Badges -->
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="hallmark-stamp bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 border border-stone-200/80 dark:border-stone-800">
                    {{ $anunci->tipus->nom_tipus }}
                </span>
                <span class="text-xs font-bold text-stone-400 dark:text-stone-500">
                    Publicat {{ $anunci->created_at->diffForHumans() }}
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight leading-tight mb-2">
                {{ $anunci->titol }}
            </h1>

            <!-- Price -->
            <div class="mb-6">
                @if($anunci->preu)
                    <span class="text-3xl font-black text-stone-900 dark:text-white tracking-tight">
                        {{ number_format($anunci->preu, 2, ',', '.') }} €
                    </span>
                @else
                    <span class="text-lg font-bold text-stone-400 italic">Preu a consultar</span>
                @endif
            </div>

            <!-- Main Specs Grid -->
            <div class="bg-stone-50 dark:bg-stone-900/50 border border-stone-200/80 dark:border-stone-800 rounded-3xl p-5 mb-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-stone-400 dark:text-stone-500 mb-0.5">Marca</p>
                        <p class="text-sm font-black text-stone-900 dark:text-white">{{ $anunci->marca->nom_marca }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-stone-400 dark:text-stone-500 mb-0.5">Mida</p>
                        <p class="text-sm font-black text-stone-900 dark:text-white">{{ $anunci->mida->nom_mida }}</p>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col gap-3 mb-6">
                @auth
                    <button type="button" onclick="showContactModal()" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-full bg-primary text-primary-text hover:bg-primary-hover dark:bg-stone-800 dark:text-white dark:hover:bg-stone-700 font-black text-xs uppercase tracking-wider transition-all shadow-xs active:scale-[0.98]">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Contactar amb el venedor</span>
                    </button>
                @else
                    <a href="{{ route('login') }}?redirect_url={{ urlencode(request()->fullUrl()) }}" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-full bg-primary text-primary-text hover:bg-primary-hover dark:bg-stone-800 dark:text-white dark:hover:bg-stone-700 font-black text-xs uppercase tracking-wider transition-all shadow-xs active:scale-[0.98]">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Contactar amb el venedor</span>
                    </a>
                @endauth

                @if(session('status'))
                    <div class="p-3.5 bg-green-100 dark:bg-green-900/40 border border-green-300 dark:border-green-800 text-green-800 dark:text-green-200 rounded-2xl text-xs font-bold text-center">
                        {{ session('status') }}
                    </div>
                @endif
                
                <button onclick="shareAnunci()" class="w-full flex items-center justify-center gap-2 py-3 rounded-full border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 text-stone-700 dark:text-stone-300 font-black text-xs uppercase tracking-wider hover:border-primary dark:hover:border-stone-600 transition-all shadow-xs">
                    <i class="fa-solid fa-share-nodes text-xs"></i>
                    <span>Compartir anunci</span>
                </button>
            </div>

            <!-- Seller Card -->
            @if($anunci->usuari)
            <div class="flex items-center gap-3.5 p-4 rounded-3xl bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 shadow-xs mb-5">
                <div class="w-10 h-10 rounded-full bg-stone-900 text-white dark:bg-black font-black flex items-center justify-center flex-none text-sm">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] text-stone-400 font-extrabold uppercase tracking-wider">Venedor</p>
                    <p class="text-sm font-black text-stone-900 dark:text-white truncate">{{ $anunci->usuari->name }}</p>
                </div>
                <div class="text-right flex-none">
                    <p class="text-[10px] text-stone-400 font-extrabold uppercase tracking-wider">Publicat</p>
                    <p class="text-xs text-stone-700 dark:text-stone-300 font-extrabold">{{ $anunci->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- DESCRIPTION SECTION -->
@if($anunci->descripcio)
<div class="mb-12 font-display">
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-6 md:p-8 shadow-xs">
        <h3 class="text-lg font-black text-stone-900 dark:text-white mb-4 tracking-tight">Descripció del Producte</h3>
        <div class="text-sm text-stone-700 dark:text-stone-300 leading-relaxed font-medium">
            {!! nl2br(e($anunci->descripcio)) !!}
        </div>
    </div>
</div>
@endif

<!-- RELATED ITEMS GRID -->
@if($relacionats->count() > 0)
<div class="mb-12 font-display">
    <div class="flex items-center justify-between mb-4 border-b border-stone-200 dark:border-stone-800 pb-3">
        <h2 class="text-lg font-black text-stone-900 dark:text-white tracking-tight">
            Més {{ $anunci->tipus->nom_tipus }} de Segona Mà
        </h2>
        <a href="{{ route('anuncis.index', ['tipus' => [$anunci->id_tipus]]) }}" class="text-xs font-black text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-white transition-colors flex items-center gap-1">
            <span>Veure tots</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($relacionats as $rel)
        <a href="{{ route('anuncis.show', $rel->id) }}" class="group bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl overflow-hidden shadow-xs hover:border-primary dark:hover:border-stone-600 transition-all flex flex-col justify-between">
            <div class="relative aspect-[4/3] w-full overflow-hidden bg-stone-100 dark:bg-stone-900">
                <img src="{{ $rel->fotos->first()?->foto_ruta ?? 'https://picsum.photos/seed/'.$rel->id.'/400/300' }}" alt="{{ $rel->titol }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                <span class="absolute top-3 right-3 bg-white/90 dark:bg-stone-900/90 text-stone-900 dark:text-stone-100 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full border border-stone-200/80 dark:border-stone-800 backdrop-blur-xs">
                    {{ $rel->estat->nom_estat }}
                </span>
            </div>
            <div class="p-4 flex flex-col flex-1 justify-between">
                <div>
                    <p class="text-[10px] text-stone-400 dark:text-stone-500 font-extrabold uppercase tracking-wider mb-1">{{ $rel->marca->nom_marca }}</p>
                    <h4 class="text-xs font-black text-stone-900 dark:text-white leading-snug group-hover:text-stone-900 dark:hover:text-white transition-colors line-clamp-2">{{ $rel->titol }}</h4>
                </div>
                <div class="mt-3 pt-2 border-t border-stone-100 dark:border-stone-800/60 flex items-center justify-between">
                    @if($rel->preu)
                        <span class="text-sm font-black text-stone-900 dark:text-white">{{ number_format($rel->preu, 0, ',', '.') }} €</span>
                    @else
                        <span class="text-xs font-bold text-stone-400 italic">Consultar</span>
                    @endif
                    <span class="text-[10px] font-extrabold text-stone-400 dark:text-stone-500">{{ $rel->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- CONTACT MODAL -->
<div id="contact-modal" class="fixed inset-0 bg-black/70 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 font-display">
    <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-3xl max-w-sm w-full p-6 md:p-8 shadow-2xl">
        <div class="text-center mb-5">
            <div class="w-12 h-12 rounded-full bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black flex items-center justify-center mx-auto mb-3 text-lg">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h3 class="text-lg font-black text-stone-900 dark:text-white">Contactar amb el venedor</h3>
        </div>
        <p class="text-xs text-stone-600 dark:text-stone-300 text-center mb-6 leading-relaxed font-medium">
            <strong>Important:</strong> S'enviaran les teves dades de contacte (<strong>{{ auth()->user()->email ?? '' }}</strong>) per correu al venedor.
        </p>
        <div class="flex gap-3">
            <button onclick="closeContactModal()" class="flex-1 px-4 py-2.5 border border-stone-200 dark:border-stone-800 rounded-full text-stone-700 dark:text-stone-300 font-extrabold text-xs hover:bg-stone-100 dark:hover:bg-primary transition-colors">
                Cancel·lar
            </button>
            <form action="{{ route('anuncis.contact', $anunci->id) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-2.5 bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black rounded-full text-xs hover:bg-primary-hover dark:hover:bg-stone-700 transition-colors uppercase tracking-wider">
                    Enviar
                </button>
            </form>
        </div>
    </div>
</div>

<script>
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
        document.querySelectorAll('.thumb-btn').forEach((btn, i) => {
            btn.classList.toggle('border-[#1c1917]', i === idx);
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

document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft')  changePhoto(-1);
    if (e.key === 'ArrowRight') changePhoto(1);
});

function showContactModal() {
    document.getElementById('contact-modal').classList.remove('hidden');
}
function closeContactModal() {
    document.getElementById('contact-modal').classList.add('hidden');
}

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