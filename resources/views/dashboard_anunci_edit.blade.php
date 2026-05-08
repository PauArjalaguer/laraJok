<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $isEdit ? 'Editar Anunci' : 'Nou Anunci' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                <form action="{{ $isEdit ? route('dashboard.anuncis.update', $anunci->id) : route('dashboard.anuncis.store') }}" method="POST" id="anunci-form">
                    @csrf
                    @if($isEdit)
                        @method('POST')
                    @endif

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Columna Esquerra: Dades Bàsiques -->
                            <div class="space-y-6">
                                <div>
                                    <label for="titol" class="block text-sm font-semibold text-gray-700 mb-1">Títol de l'anunci</label>
                                    <input type="text" name="titol" id="titol" value="{{ old('titol', $anunci->titol) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" placeholder="Ex: Patins Reno talla 42 en bon estat" required>
                                    @error('titol') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="descripcio" class="block text-sm font-semibold text-gray-700 mb-1">Descripció detallada</label>
                                    <textarea name="descripcio" id="descripcio" rows="6" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" placeholder="Descriu el producte, estat, motiu de venda, etc." required>{{ old('descripcio', $anunci->descripcio) }}</textarea>
                                    @error('descripcio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="preu" class="block text-sm font-semibold text-gray-700 mb-1">Preu (€)</label>
                                        <div class="relative">
                                            <input type="number" step="0.01" name="preu" id="preu" value="{{ old('preu', $anunci->preu) }}" class="w-full rounded-lg border-gray-300 pl-8 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" placeholder="0,00">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">€</div>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1">Deixa en blanc per "A consultar"</p>
                                        @error('preu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="id_estat" class="block text-sm font-semibold text-gray-700 mb-1">Estat</label>
                                        <select name="id_estat" id="id_estat" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" required>
                                            <option value="">Selecciona estat</option>
                                            @foreach($estats as $estat)
                                                <option value="{{ $estat->id }}" {{ old('id_estat', $anunci->id_estat) == $estat->id ? 'selected' : '' }}>{{ $estat->nom_estat }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Dreta: Selectors i Imatges -->
                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="id_marca" class="block text-sm font-semibold text-gray-700 mb-1">Marca</label>
                                        <select name="id_marca" id="id_marca" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" required>
                                            <option value="">Selecciona marca</option>
                                            @foreach($marques as $marca)
                                                <option value="{{ $marca->id }}" {{ old('id_marca', $anunci->id_marca) == $marca->id ? 'selected' : '' }}>{{ $marca->nom_marca }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="id_tipus" class="block text-sm font-semibold text-gray-700 mb-1">Tipus de producte</label>
                                        <select name="id_tipus" id="id_tipus" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" required>
                                            <option value="">Selecciona tipus</option>
                                            @foreach($tipus as $t)
                                                @php
                                                    $category = 'samarreta';
                                                    if (in_array($t->nom_tipus, ['Patins'])) {
                                                        $category = 'calcat';
                                                    }
                                                @endphp
                                                <option value="{{ $t->id }}" data-category="{{ $category }}" {{ old('id_tipus', $anunci->id_tipus) == $t->id ? 'selected' : '' }}>{{ $t->nom_tipus }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div>
                                    <label for="id_mida" class="block text-sm font-semibold text-gray-700 mb-1">Mida / Talla</label>
                                    <select name="id_mida" id="id_mida" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" required>
                                        <option value="">Primer selecciona un tipus</option>
                                        @foreach($mides as $mida)
                                            <option value="{{ $mida->id }}" data-type="{{ $mida->tipus_mida }}" {{ old('id_mida', $anunci->id_mida) == $mida->id ? 'selected' : '' }}>{{ $mida->nom_mida }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fotos del producte</label>
                                    <div id="dropzone-area" class="border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-gray-100 hover:border-indigo-400 transition-all duration-300 cursor-pointer flex flex-col items-center justify-center min-h-[200px]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-12 text-gray-400 mb-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-600">Arrossega les fotos aquí o fes clic</p>
                                        <p class="text-xs text-gray-400 mt-1">Màxim 5 fotos, format WebP automàtic</p>
                                    </div>
                                    <div id="previews" class="grid grid-cols-3 sm:grid-cols-5 gap-4 mt-4">
                                        <!-- Aquí aniran les fotos pujades -->
                                        @if($isEdit)
                                            @foreach($anunci->fotos as $foto)
                                                <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm preview-item">
                                                    <img src="{{ $foto->foto_ruta }}" class="h-full w-full object-cover">
                                                    <input type="hidden" name="fotos[]" value="{{ $foto->foto_ruta }}">
                                                    <button type="button" class="absolute top-1 right-1 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200 remove-photo">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center space-x-4">
                        <a href="{{ route('dashboard.anuncis') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">Cancel·lar</a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-white shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            {{ $isEdit ? 'Guardar Canvis' : 'Publicar Anunci' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dropzone JS -->
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

    <style>
        .dz-preview { display: none !important; }
        .dropzone.dz-clickable { border: none; background: transparent; padding: 0; min-height: unset; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipusSelect = document.getElementById('id_tipus');
            const midaSelect = document.getElementById('id_mida');
            const midesOptions = Array.from(midaSelect.options);

            // Funció per filtrar mides
            function filterMides() {
                const selectedOption = tipusSelect.options[tipusSelect.selectedIndex];
                const category = selectedOption.getAttribute('data-category');
                
                if (!category) {
                    midaSelect.innerHTML = '<option value="">Primer selecciona un tipus</option>';
                    return;
                }

                midaSelect.innerHTML = '<option value="">Selecciona mida</option>';
                midesOptions.forEach(opt => {
                    if (opt.getAttribute('data-type') === category) {
                        midaSelect.appendChild(opt.cloneNode(true));
                    }
                });
                
                // Si estem editant, mantenim la selecció si coincideix
                @if($isEdit)
                const currentMidaId = "{{ $anunci->id_mida }}";
                if (currentMidaId) {
                    midaSelect.value = currentMidaId;
                }
                @endif
            }

            tipusSelect.addEventListener('change', filterMides);
            if (tipusSelect.value) filterMides();

            // Dropzone Logic
            const dropzoneArea = document.getElementById('dropzone-area');
            const previewsContainer = document.getElementById('previews');
            
            const myDropzone = new Dropzone("#dropzone-area", { 
                url: "{{ route('dashboard.anuncis.upload-image') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                maxFiles: 5,
                acceptedFiles: "image/*",
                createImageThumbnails: false,
                init: function() {
                    this.on("success", function(file, response) {
                        if (response.success) {
                            addPreview(response.path);
                        }
                    });
                }
            });

            function addPreview(path) {
                const div = document.createElement('div');
                div.className = "relative group aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm preview-item";
                div.innerHTML = `
                    <img src="${path}" class="h-full w-full object-cover">
                    <input type="hidden" name="fotos[]" value="${path}">
                    <button type="button" class="absolute top-1 right-1 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200 remove-photo">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                previewsContainer.appendChild(div);
            }

            // Eliminar fotos
            previewsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-photo')) {
                    e.target.closest('.preview-item').remove();
                }
            });
        });
    </script>
</x-app-layout>
