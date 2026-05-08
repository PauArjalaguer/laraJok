<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Moderació de Fotos
            </h2>
            <a href="{{ route('dashboard.anuncis') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-bold">
                Tornar a gestió d'anuncis
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="bg-green-500 rounded-md p-3 text-white font-bold mb-4 shadow-sm">
                    {{session('status')}}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                <div id="photos-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($fotos as $foto)
                        <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-md">
                            <img src="{{ $foto->foto_ruta }}" class="h-full w-full object-cover lazy-img" loading="lazy">
                            
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex flex-col justify-between p-3 opacity-0 group-hover:opacity-100">
                                <div class="flex justify-end">
                                    <a href="{{ route('dashboard.anuncis.delete-foto', $foto->id) }}" onclick="return confirm('Segur que vols esborrar aquesta foto?')" class="bg-red-500 text-white p-1.5 rounded-full hover:bg-red-600 shadow-lg" title="Esborrar foto">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="text-white">
                                    <p class="text-[10px] font-bold truncate">{{ $foto->anunci->titol }}</p>
                                    <a href="{{ route('dashboard.anuncis.edit', $foto->anunci->id) }}" class="text-[9px] underline hover:text-indigo-200">Veure anunci</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="loading-trigger" class="py-10 flex justify-center">
                    @if($fotos->hasMorePages())
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    @else
                        <p class="text-gray-400 text-sm italic">No hi ha més fotos per carregar.</p>
                    @endif
                </div>
                
                <!-- Paginació oculta per al JS -->
                <div class="hidden">
                    {{ $fotos->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let nextPageUrl = "{{ $fotos->nextPageUrl() }}";
            const grid = document.getElementById('photos-grid');
            const trigger = document.getElementById('loading-trigger');
            let loading = false;

            if (!nextPageUrl) return;

            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !loading && nextPageUrl) {
                    loadMorePhotos();
                }
            }, { threshold: 0.1 });

            observer.observe(trigger);

            async function loadMorePhotos() {
                loading = true;
                try {
                    const response = await fetch(nextPageUrl);
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newPhotos = doc.querySelectorAll('#photos-grid > div');
                    newPhotos.forEach(photo => grid.appendChild(photo));
                    
                    nextPageUrl = doc.querySelector('a[rel="next"]')?.href;
                    
                    if (!nextPageUrl) {
                        trigger.innerHTML = '<p class="text-gray-400 text-sm italic">No hi ha més fotos per carregar.</p>';
                        observer.unobserve(trigger);
                    }
                } catch (error) {
                    console.error('Error carregant més fotos:', error);
                } finally {
                    loading = false;
                }
            }
        });
    </script>
</x-app-layout>
