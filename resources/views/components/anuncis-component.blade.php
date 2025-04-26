<div class="w-1/3 my-2">
    <div class=" bg-white m-2 rounded-2xl shadow-md overflow-hidden transition hover:shadow-lg ">
        <img src={{ $anunci->foto_ruta ?? 'https://via.placeholder.com/400x250' }} alt="-" class="w-full h-96 object-cover" />

        <div class="p-4 space-y-2">
            <div class="flex justify-between items-start">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{$anunci->titol}}
                </h3>
                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">
                    {{$anunci->estat}}
                </span>
            </div>
            <p class="text-xl font-bold text-green-600">
                {{$anunci->preu}} €
            </p>
            {{-- <div class="flex items-center text-sm text-gray-500">
                 <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657l4.243 4.243a2 2 0 002.828 0l4.243-4.243a2 2 0 000-2.828z" />
                     <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                 </svg>                
                 <script>calcular_distancia('{{$anunci->ubicacio}}')</script>
        </div> --}}
        <p>&nbsp;</p>
        <p class="text-sm text-gray-900">
            <span class="font-bold">Marca: </span>{{$anunci->marca}}
            <span class="font-bold">Talla: </span>{{$anunci->mida}}
        </p>
        <p>&nbsp;</p>
        <p class="text-sm text-gray-600 line-clamp-2">
            {{$anunci->descripcio}}
        </p>

        <a class="block mt-2 w-full bg-neutral-600 text-white text-sm font-medium text-center py-2 rounded-xl hover:bg-neutral-700 transition pointer" href={{route("anuncis.detall",$anunci->id)}}>
            Veure detall
        </a>
    </div>
</div>
</div>
