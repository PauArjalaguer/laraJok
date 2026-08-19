@extends('layout.mainlayout')
@section('title', 'Botiga Oficial :: JOK.cat')
@section('content')

{{-- UNIFIED HEADER --}}
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight">
                Botiga Oficial
            </h1>
            <p class="text-xs md:text-sm text-stone-500 dark:text-stone-400 mt-1 font-medium">
                Productes oficials del JOK.cat
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="hallmark-stamp bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 border border-stone-200/80 dark:border-stone-800">
                {{ count($merchandisingListAll) }} {{ count($merchandisingListAll) == 1 ? 'PRODUCTE' : 'PRODUCTES' }}
            </span>
            <a href="https://www.latostadora.com/shop/jokcat/?ord=reciente#shop" target="_blank" rel="noreferrer"
               class="inline-flex items-center gap-1.5 px-4 py-[0.22rem] bg-primary text-primary-text hover:bg-primary-hover font-black text-[0.62rem] uppercase tracking-wider rounded-full transition-all shadow-xs active:scale-95">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>Veure tot</span>
            </a>
        </div>
    </div>
</div>

{{-- CATEGORY FILTER PILLS --}}
<div class="mb-6 font-display" id="categoryFilters">
    <div class="flex flex-wrap items-center gap-2">
        <button onclick="showAllCategories()"
                id="pill-all"
                class="category-pill active px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border transition-all">
            <i class="fa-solid fa-grip mr-1"></i> Totes
        </button>
        @foreach($merchandisingReturnCategories as $category)
            <button onclick="showCategory('{{ $category->assetCategory }}')"
                    id="pill-{{ Str::slug($category->assetCategory) }}"
                    class="category-pill px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border transition-all">
                {{ $category->assetCategory }}
            </button>
        @endforeach
    </div>
</div>

{{-- PRODUCTS GRID --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 font-display" id="productsGrid">
    @foreach($merchandisingListAll as $merch)
        <div class="merch-item" data-category="{{ $merch->assetCategory }}">
            <a href="{{ $merch->assetUrl }}" target="_blank" rel="noreferrer"
               aria-label="{{ $merch->assetName }}"
               class="group flex flex-col bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl overflow-hidden shadow-xs hover:border-primary dark:hover:border-primary transition-all duration-300">

                {{-- Product Image --}}
                <div class="relative aspect-square w-full overflow-hidden bg-stone-100 dark:bg-stone-900">
                    <img src="{{ $merch->assetThumbnail }}"
                         alt="{{ $merch->assetName }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />

                    {{-- Category Badge --}}
                    @if($merch->assetCategory)
                        <span class="absolute top-3 left-3 bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 border border-stone-200/80 dark:border-stone-800 font-black text-[9px] uppercase px-2.5 py-0.5 rounded-full shadow-xs tracking-wider">
                            {{ $merch->assetCategory }}
                        </span>
                    @endif

                    {{-- Out of stock overlay --}}
                    @if(!$merch->assetPrice)
                        <div class="absolute inset-0 bg-black/30 dark:bg-black/50 flex items-center justify-center">
                            <span class="bg-stone-900/90 text-white font-black text-[10px] uppercase px-3 py-1 rounded-full tracking-widest">
                                Sense stock
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Product Info --}}
                <div class="flex items-center justify-between p-4 border-t border-stone-100 dark:border-stone-800/80">
                    <div>
                        <p class="text-xs font-black text-stone-900 dark:text-white leading-snug">{{ $merch->assetName }}</p>
                        @if($merch->assetPrice)
                            <p class="text-sm font-black text-stone-700 dark:text-stone-300 mt-0.5">{{ $merch->assetPrice }} €</p>
                        @else
                            <p class="text-xs font-bold text-stone-400 dark:text-stone-600 mt-0.5">No disponible</p>
                        @endif
                    </div>
                    @if($merch->assetPrice)
                        <span class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-black bg-stone-100 text-stone-800 dark:bg-stone-900 dark:text-stone-200 group-hover:bg-primary group-hover:text-primary-text transition-all border border-stone-200/80 dark:border-stone-800 whitespace-nowrap">
                            Comprar <i class="fa-solid fa-arrow-right text-[9px] group-hover:translate-x-0.5 transition-transform"></i>
                        </span>
                    @endif
                </div>
            </a>
        </div>
    @endforeach
</div>

{{-- EMPTY STATE --}}
@if(count($merchandisingListAll) === 0)
    <div class="font-display text-xs md:text-sm text-stone-500 dark:text-stone-400 text-center py-16 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl">
        <i class="fa-solid fa-shirt text-3xl text-stone-400 mb-3 block"></i>
        No hi ha productes disponibles actualment.
    </div>
@endif

{{-- LINK TO EXTERNAL SHOP --}}
<div class="mt-10 mb-2 flex justify-center font-display">
    <a href="https://www.latostadora.com/shop/jokcat/?ord=reciente#shop" target="_blank" rel="noreferrer"
       class="group inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-full text-xs font-black text-stone-700 dark:text-stone-300 hover:border-primary dark:hover:border-primary hover:text-stone-900 dark:hover:text-white transition-all shadow-xs">
        <i class="fa-solid fa-shirt text-[11px]"></i>
        Veure tota la botiga a La Tostadora
        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
    </a>
</div>

<style>
    .category-pill {
        background-color: rgb(245 245 244); /* stone-100 */
        color: rgb(68 64 60); /* stone-700 */
        border-color: rgba(214 211 209 / 0.8); /* stone-200/80 */
    }
    .dark .category-pill {
        background-color: rgb(28 25 23); /* stone-900 */
        color: rgb(214 211 209); /* stone-300 */
        border-color: rgb(41 37 36); /* stone-800 */
    }
    .category-pill:hover,
    .category-pill.active {
        background-color: var(--color-primary);
        color: var(--color-primary-text);
        border-color: var(--color-primary);
    }
    .dark .category-pill:hover,
    .dark .category-pill.active {
        background-color: var(--color-primary);
        color: var(--color-primary-text);
        border-color: var(--color-primary);
    }
    .merch-item {
        transition: opacity 0.35s ease, transform 0.35s ease;
        transform-origin: center;
    }
</style>

<script>
    const allCategories = @json($merchandisingReturnCategories->pluck('assetCategory'));

    const setActivePill = (activeId) => {
        document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
        const pill = document.getElementById(activeId);
        if (pill) pill.classList.add('active');
    };

    const showCategory = (category) => {
        setActivePill('pill-' + category.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, ''));

        // Primer amaguem els que no toquen
        document.querySelectorAll('.merch-item').forEach(item => {
            if (item.dataset.category !== category) {
                item.style.transform = 'scale(0)';
                item.style.opacity = '0';
                setTimeout(() => { item.style.display = 'none'; }, 320);
            }
        });

        // Després mostrem els que toquen
        document.querySelectorAll('.merch-item').forEach(item => {
            if (item.dataset.category === category) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.transform = 'scale(1)';
                    item.style.opacity = '1';
                }, 50);
            }
        });
    };

    const showAllCategories = () => {
        setActivePill('pill-all');
        document.querySelectorAll('.merch-item').forEach(item => {
            item.style.display = 'block';
            setTimeout(() => {
                item.style.transform = 'scale(1)';
                item.style.opacity = '1';
            }, 50);
        });
    };
</script>

@endsection
