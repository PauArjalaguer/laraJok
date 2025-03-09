<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar producte
        </h2>
    </x-slot>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                @if(session('status'))
                <div class="bg-green-500 rounded-md p-2 text-white font-bold mb-4 w-full block">{{session('status')}}</div>
                @endif
                <form method="POST" action="/dashboard/merchandising/save/{{ $merch[0]->idAsset}}" id="newsForm">
                    @csrf @method('PUT')
                    <input type='hidden' name="idAsset" id="idAsset" value="{{$merch[0]->idAsset}}" />
                    <label to="assetName">Títol:</label>

                    <input class='w-full  rounded-md border-slate-300 mb-3' type='text' name='assetName' id="assetName" value="{{$merch[0]->assetName}}" />
                    <label to="assetThumbnail">Imatge:</label>
                    <input class='w-full rounded-md border-slate-300 mb-3' type='text' name='assetThumbnail' id="assetThumbnail" value="{{$merch[0]->assetThumbnail}}" />

                    <label to="assetUrl">URL:</label>
                    <input class='w-full rounded-md border-slate-300 mb-3' type='text' name='assetUrl' id="assetUrl" value="{{$merch[0]->assetUrl}}" />

                    <label to="assetCategory">Categoria:</label>
                    <input class='w-full rounded-md border-slate-300 mb-3' type='text' name='assetCategory' id="assetCategory" value="{{$merch[0]->assetCategory}}" />

                    <label to="assetPrice">Preu:</label>
                    <input pattern="^\d*(\.\d{0,1})?$" class='w-full rounded-md border-slate-300 mb-3' type='text' name='assetPrice' id="assetPrice" value="{{$merch[0]->assetPrice}}" />

                    <x-input-error :messages="$errors->get('message')" />
                    <x-primary-button>Guardar</x-primary-button>

                </form>
                {{-- <div style="margin-top:20px;display:col-lg-12" id="newsUploaderContainer">
                    <form action="{{ route('dropzoneFileUpload') }}" class="dropzone" id="my-dropzone" enctype="multipart/form-data"> @csrf</form>
            </div> --}}
        </div>
    </div>
    </div>
    </div>
    <script>
        Dropzone.options.myDropzone = {
            // Configuration options go here
            dictDefaultMessage: "<span style='font-size:32px; color:#2a6496;'>\n\<i style='font-size:32px;' class=\"fas fa-cloud fa-fw\"></i> Insertar foto de la notícia."
            , success: function(file, response) {
                document.getElementById('newsImage').value = "images/" + response.success;
            }

        };

    </script>


</x-app-layout>
