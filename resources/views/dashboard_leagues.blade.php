<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Gestió de Lligues
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Associa categories a les lligues i crea noves categories fàcilment.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="leaguesManager({{ json_encode($categories) }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Status Message if redirected -->
            @if (session('status'))
                <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Filter & Search Bar -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('dashboard.leagues') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    
                    <!-- Search input -->
                    <div class="md:col-span-4">
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Cercar Lliga</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nom de la lliga..."
                                class="pl-9 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150">
                        </div>
                    </div>

                    <!-- Season filter -->
                    <div class="md:col-span-3">
                        <label for="season" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Temporada</label>
                        <select name="season" id="season" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150">
                            <option value="">Totes les temporades</option>
                            @foreach ($seasons as $s)
                                <option value="{{ $s->idSeason }}" {{ (string)$seasonId === (string)$s->idSeason ? 'selected' : '' }}>
                                    {{ $s->seasonName ?? ('Temporada ' . $s->idSeason) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category filter -->
                    <div class="md:col-span-3">
                        <label for="category" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Categoria</label>
                        <select name="category" id="category" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150">
                            <option value="">Totes les categories</option>
                            <option value="0" {{ (string)$categoryId === '0' ? 'selected' : '' }}>⚠️ Categoria 0 (Sense assignar)</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->idCategory }}" {{ (string)$categoryId === (string)$cat->idCategory ? 'selected' : '' }}>
                                    {{ $cat->categoryName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action buttons -->
                    <div class="md:col-span-2 flex items-center gap-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                            Filtrar
                        </button>
                        @if(!empty($search) || !empty($seasonId) || $categoryId !== null)
                            <a href="{{ route('dashboard.leagues') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Netejar filtres">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Category 0 Highlight Banner / Quick Info -->
            @php
                $unassignedCount = $leagues->getCollection()->where('idCategory', 0)->count();
            @endphp
            @if($unassignedCount > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center justify-between gap-4 text-amber-900 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 text-amber-700 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-amber-900">Atenció:</span> Hi ha 
                            <span class="font-extrabold text-amber-800 bg-amber-200/80 px-2 py-0.5 rounded-md">{{ $unassignedCount }}</span> 
                            lliga/es sense categoria (Categoria 0) en aquesta pàgina. Apareixen destacades a dalt de la llista.
                        </div>
                    </div>
                    @if($categoryId !== '0')
                        <a href="{{ route('dashboard.leagues', array_merge(request()->query(), ['category' => '0'])) }}" class="text-xs font-bold text-amber-800 underline hover:text-amber-950 whitespace-nowrap">
                            Veure només Categoria 0 &rarr;
                        </a>
                    @endif
                </div>
            @endif

            <!-- Leagues Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">Temporada</th>
                                <th class="py-3.5 px-4 w-20">ID</th>
                                <th class="py-3.5 px-4">Nom de la Lliga</th>
                                <th class="py-3.5 px-4">Estat / Categoria actual</th>
                                <th class="py-3.5 px-4 min-w-[280px]">Assignar Categoria</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse ($leagues as $league)
                                <tr x-data="categorySelectRow({ idLeague: {{ $league->idLeague }}, idCategory: {{ (int)$league->idCategory }}, categoryName: {{ json_encode($league->categoryName ?? '') }} })"
                                    :class="currentIdCategory == 0 ? 'bg-amber-50/70 hover:bg-amber-100/60 border-l-4 border-amber-500 transition-colors' : 'hover:bg-gray-50/80 border-l-4 border-transparent transition-colors'">
                                    
                                    <!-- Season -->
                                    <td class="py-4 px-4 font-semibold text-gray-700 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $league->seasonName ?? ('ID ' . $league->idSeason) }}
                                        </span>
                                    </td>

                                    <!-- ID League -->
                                    <td class="py-4 px-4 text-gray-500 font-mono text-xs">
                                        #{{ $league->idLeague }}
                                    </td>

                                    <!-- League Name -->
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-gray-900">{{ $league->leagueName }}</div>
                                    </td>

                                    <!-- Category Badge / Status -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <template x-if="currentIdCategory == 0">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 shadow-sm animate-pulse">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                ⚠️ Categoria 0 (Sense assignar)
                                            </span>
                                        </template>
                                        <template x-if="currentIdCategory != 0">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span x-text="currentCategoryName || ('ID ' + currentIdCategory)"></span>
                                            </span>
                                        </template>
                                    </td>

                                    <!-- Searchable & Creatable Category Selector -->
                                    <td class="py-4 px-4 relative" @click.outside="open = false">
                                        <div class="relative">
                                            <!-- Button / Select Trigger -->
                                            <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()); }"
                                                class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm bg-white border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150"
                                                :class="currentIdCategory == 0 ? 'border-amber-300 hover:border-amber-400 text-amber-900 font-medium' : 'border-gray-300 hover:border-gray-400 text-gray-800'">
                                                <span class="truncate" x-text="currentIdCategory == 0 ? '-- Selecciona o escriu una categoria --' : (currentCategoryName || 'Categoria #' + currentIdCategory)"></span>
                                                <div class="flex items-center gap-1">
                                                    <!-- Loading spinner -->
                                                    <svg x-show="saving" class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <!-- Success checkmark flash -->
                                                    <svg x-show="savedSuccess" x-cloak class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <svg class="h-4 w-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </div>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 left-0 mt-1 z-30 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden text-sm">
                                                
                                                <!-- Search Input Box -->
                                                <div class="p-2 border-b border-gray-100 bg-gray-50">
                                                    <input x-ref="searchInput" type="text" x-model="search"
                                                        @keydown.enter.prevent="handleEnterKey()"
                                                        placeholder="Cerca o escriu una categoria nova..."
                                                        class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                                    <p class="text-[10px] text-gray-400 mt-1 px-1">Escriu per cercar. Si no existeix, prem <kbd class="font-mono bg-gray-200 text-gray-700 px-1 py-0.5 rounded">Enter</kbd> per crear-la.</p>
                                                </div>

                                                <!-- Categories List -->
                                                <div class="max-h-56 overflow-y-auto divide-y divide-gray-50">
                                                    
                                                    <!-- Category 0 option (Unassigned) -->
                                                    <button type="button" @click="selectCategory({ idCategory: 0, categoryName: '' })"
                                                        class="w-full text-left px-3 py-2 hover:bg-amber-50 flex items-center justify-between text-amber-800 text-xs font-semibold"
                                                        :class="{ 'bg-amber-100/70': currentIdCategory == 0 }">
                                                        <span>⚠️ Categoria 0 (Sense assignar)</span>
                                                        <span x-show="currentIdCategory == 0" class="text-amber-600 font-bold">✓</span>
                                                    </button>

                                                    <!-- Filtered existing categories -->
                                                    <template x-for="cat in filteredCategories" :key="cat.idCategory">
                                                        <button type="button" @click="selectCategory(cat)"
                                                            class="w-full text-left px-3 py-2 hover:bg-indigo-50 flex items-center justify-between text-gray-700 text-xs"
                                                            :class="{ 'bg-indigo-100/70 font-bold text-indigo-900': currentIdCategory == cat.idCategory }">
                                                            <span x-text="cat.categoryName"></span>
                                                            <span x-show="currentIdCategory == cat.idCategory" class="text-indigo-600 font-bold">✓</span>
                                                        </button>
                                                    </template>

                                                    <!-- Create option if search query has no exact match -->
                                                    <template x-if="search.trim().length > 0 && !exactMatch">
                                                        <button type="button" @click="createNewCategory()"
                                                            class="w-full text-left px-3 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                            <span>Crear i assignar categoria "<span x-text="search.trim()"></span>"</span>
                                                        </button>
                                                    </template>

                                                    <div x-show="filteredCategories.length === 0 && search.trim().length === 0" class="px-3 py-4 text-center text-xs text-gray-400">
                                                        No s'han trobat categories.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Error message notification -->
                                        <div x-show="errorMessage" x-cloak class="text-[11px] text-red-600 font-semibold mt-1">
                                            <span x-text="errorMessage"></span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 px-4 text-center text-gray-500">
                                        No s'ha trobat cap lliga amb els filtres seleccionats.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($leagues->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $leagues->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Alpine.js Manager Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('leaguesManager', (initialCategories) => ({
                categories: initialCategories || [],

                updateGlobalCategories(newCategories) {
                    if (Array.isArray(newCategories)) {
                        this.categories = newCategories;
                    }
                }
            }));

            Alpine.data('categorySelectRow', (config) => ({
                idLeague: config.idLeague,
                currentIdCategory: config.idCategory,
                currentCategoryName: config.categoryName,
                open: false,
                search: '',
                saving: false,
                savedSuccess: false,
                errorMessage: '',

                get manager() {
                    return this.$data;
                },

                get allCategories() {
                    // Try to get updated categories from parent component or fallback
                    return (this.$data && this.$data.categories) ? this.$data.categories : [];
                },

                get filteredCategories() {
                    const list = this.allCategories;
                    if (!this.search.trim()) return list;
                    const q = this.search.toLowerCase().trim();
                    return list.filter(c => c.categoryName && c.categoryName.toLowerCase().includes(q));
                },

                get exactMatch() {
                    if (!this.search.trim()) return true;
                    const q = this.search.toLowerCase().trim();
                    return this.allCategories.some(c => c.categoryName && c.categoryName.toLowerCase().trim() === q);
                },

                selectCategory(cat) {
                    this.saving = true;
                    this.open = false;
                    this.errorMessage = '';

                    fetch('{{ route("dashboard.leagues.update-category") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            idLeague: this.idLeague,
                            idCategory: cat.idCategory
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.saving = false;
                        if (data.success) {
                            this.currentIdCategory = data.idCategory;
                            this.currentCategoryName = data.categoryName;
                            this.savedSuccess = true;
                            this.search = '';
                            setTimeout(() => this.savedSuccess = false, 2500);
                        } else {
                            this.errorMessage = data.message || 'Error en guardar.';
                        }
                    })
                    .catch(err => {
                        this.saving = false;
                        this.errorMessage = 'Error en la comunicació amb el servidor.';
                    });
                },

                handleEnterKey() {
                    if (this.search.trim().length > 0 && !this.exactMatch) {
                        this.createNewCategory();
                    } else if (this.filteredCategories.length > 0) {
                        this.selectCategory(this.filteredCategories[0]);
                    }
                },

                createNewCategory() {
                    const name = this.search.trim();
                    if (!name) return;

                    this.saving = true;
                    this.open = false;
                    this.errorMessage = '';

                    fetch('{{ route("dashboard.leagues.create-category") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            categoryName: name,
                            idLeague: this.idLeague
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.saving = false;
                        if (data.success) {
                            this.currentIdCategory = data.category.idCategory;
                            this.currentCategoryName = data.category.categoryName;
                            
                            // If server returned updated categories list, update Alpine state
                            if (data.allCategories) {
                                const parent = this.$root.closest('[x-data*="leaguesManager"]');
                                if (parent && parent._x_dataStack) {
                                    parent._x_dataStack[0].categories = data.allCategories;
                                }
                            }

                            this.savedSuccess = true;
                            this.search = '';
                            setTimeout(() => this.savedSuccess = false, 2500);
                        } else {
                            this.errorMessage = data.message || 'Error en crear la categoria.';
                        }
                    })
                    .catch(err => {
                        this.saving = false;
                        this.errorMessage = 'Error en la comunicació amb el servidor.';
                    });
                }
            }));
        });
    </script>
</x-app-layout>
