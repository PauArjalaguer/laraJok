<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($anunci->id) Editar @else Nou @endif
            anunci
        </h2>
    </x-slot>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- @error('newsTitle') <div class="bg-red-500 rounded-md p-2 text-white font-bold mb-4">{{ $message }}</div> @enderror
                @error('newsImage') <div class="bg-red-500 rounded-md p-2 text-white font-bold mb-4">{{ $message }} </div> @enderror
                @error('newsContent')<div class="bg-red-500 rounded-md p-2 text-white font-bold mb-4">{{ $message }} </div> @enderror --}}
                @if(session('status'))
                <div class="bg-green-500 rounded-md p-2 text-white font-bold mb-4">{{session('status')}}</div>
                @endif
                <form method="POST" action="/dashboard/anuncis/save/{{ $anunci->id}}" id="anunciForm">
                    @csrf @method('PUT')
                    <input type='hidden' name="id_anunci" id="id_anunci" value="{{$anunci->id}}" />
                    <label to="anunci_title">Títol:</label>
                    <input class='w-full  rounded-md border-neutral-300 mb-3' type='text' name='anunci_title' id="anunci_title" value="{{$anunci->titol}}" />

                    <label to="anunci_descripcio">Subtítol:</label>
                    <textarea rows=20 class='w-full rounded-md border-neutral-300 mb-1 h-96' type='text' name='anunci_descripcio' id="anunci_descripcio" contentEditable>{{$anunci->descripcio}}</textarea>
                    <x-input-error :messages="$errors->get('message')" />
                    @if($anunci->id)
                    <div class="flex items-end gap-2">
                        <div class="w-1/3">
                            <label for="anunci_preu">Preu:</label>
                            <input class="w-full rounded-md border-neutral-300 mb-0" type="number" name="anunci_preu" id="anunci_preu" value="{{$anunci->preu}}" />
                        </div>
                        <div class="w-1/3">
                            <label for="anunci_tipus">Tipus:</label>
                            <select id="anunci_tipus" name="anunci_tipus" class="w-full rounded-md border-neutral-300 mb-0">
                                @foreach($llista_tipus as $key => $tipus)
                                <option value={{$tipus->id_tipus}} {{ $tipus->id_tipus == $anunci->id_tipus ? 'selected' : '' }}>{{$tipus->tipus}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-1/3">
                            <label for="anunci_marca">Marca:</label>
                            <select id="anunci_marca" name="anunci_marca" class="w-full rounded-md border-neutral-300 mb-0">
                                @foreach($llista_marques as $key => $marca)
                                <option value={{$marca->id_marca}} {{ $marca->id_marca == $anunci->id_marca ? 'selected' : '' }}>{{$marca->marca}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="flex items-end gap-2">
                        <div class="w-1/3">
                            <label for="anunci_mida ">Mida:</label>
                            <select id="anunci_mida" name="anunci_mida" class="w-full rounded-md border-neutral-300 mb-0">
                                @foreach($llista_mides as $key => $mida)
                                <option value={{$mida->id_mida}} {{ $mida->id_mida == $anunci->id_mida ? 'selected' : '' }}>{{$mida->mida}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-1/3">
                            <label for="anunci_estat">Estat:</label>
                            <select id="anunci_estat" name="anunci_estat" class="w-full rounded-md border-neutral-300 mb-0">
                                @foreach($llista_estats as $key => $estat)
                                <option value={{$estat->id_estat}} {{ $estat->id_estat == $anunci->id_estat ? 'selected' : '' }}>{{$estat->estat}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    @endif
                    <x-primary-button>Guardar</x-primary-button>
                </form>

                <div style="margin-top:20px;display:col-lg-12" id="newsImageContainer">
                    @foreach($anunci_fotos as $anunci_foto)
                    <div class="relative inline-block m-1">
                        <img src="../../../{{$anunci_foto->foto_ruta}}" class="float-left w-[100px] h-[100px] mr-2" />
                        <button onclick="eliminar_foto('{{$anunci_foto->foto_ruta}}')" id='{{$anunci_foto->foto_ruta}}' class="absolute top-0 right-0 bg-red-700 text-white rounded-full w-5 h-5 flex items-center justify-center cursor-pointer p-0 shadow-xl hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 ease-in-out">
                            ×
                        </button>
                    </div>
                    @endforeach
                </div>

                <div style="clear:both;"></div>
                @if($anunci->id)
                <div style="margin-top:20px;display:col-lg-12" id="newsUploaderContainer">
                    <form action="{{ route('anuncis_file_upload') }}" class="dropzone" id="my-dropzone" enctype="multipart/form-data"> @csrf
                        <input type="hidden" name="id_anunci" value="{{ $anunci->id ?? '' }}">
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
    </div>
    <script>
        Dropzone.options.myDropzone = {
            // Configuration options go here
            dictDefaultMessage: "<span style='font-size:32px; color:#2a6496;'>\n\<i style='font-size:32px;' class=\"fas fa-cloud fa-fw\"></i> Insertar fotos."
            , success: function(file, response) {
                document.getElementById('newsImageContainer').innerHTML += `
  <div class="relative inline-block m-1">
    <img src='../../../images/anuncis/${response.success}' class="float-left w-[100px] h-[100px] mr-2" />
    <button onclick='eliminar_foto()'
      class="absolute top-0 right-0 bg-red-700 text-white rounded-full w-5 h-5 flex items-center justify-center cursor-pointer p-0 shadow-xl hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 ease-in-out">
      ×
    </button>
  </div>`;
            }
        };

        function eliminar_foto(foto) {
            let f = foto.split('/');
            let foto_url = f[f.length - 1];
            fetch(`/dashboard/anuncis/esborra_foto/${foto_url}`, {
                    method: 'GET'
                    , headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        , 'Accept': 'application/json'
                        , 'Content-Type': 'application/json'
                    }
                , }).then(document.getElementById(foto).parentNode.remove())
                .catch(error => console.error('Error:', error));
        }

    </script>
</x-app-layout>
