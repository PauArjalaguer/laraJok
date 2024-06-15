<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar notícia
        </h2>
    </x-slot>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @error('newsTitle') <div class="bg-red-500 rounded-md p-2 text-white font-bold mb-4">{{ $message }}</div> @enderror
                    @error('newsImage') <div class="bg-red-500 rounded-md p-2 text-white font-bold mb-4">{{ $message }} </div> @enderror
                    @error('newsContent')<div class="bg-red-500 rounded-md p-2 text-white font-bold mb-4">{{ $message }} </div> @enderror
                    @if(session('status'))
                    <div class="bg-green-500 rounded-md p-2 text-white font-bold mb-4">{{session('status')}}</div>
                    @endif
                    <form method="POST" action="/dashboard/news/save/{{ $news[0]->idNew}}" id="newsForm">
                        @csrf @method('PUT')
                        <input type='hidden' name="idNew" id="idNew" value="{{$news[0]->idNew}}" />
                        <label to="newsTitle">Títol:</label>

                        <input class='w-full  rounded-md border-slate-300 mb-3' type='text' name='newsTitle' id="newsTitle" value="{{$news[0]->newsTitle}}" />
                        <label to="newsSubtitle">Subtítol:</label>
                        <input class='w-full rounded-md border-slate-300 mb-3' type='text' name='newsSubtitle' id="newsSubtitle" value="{{$news[0]->newsSubtitle}}" />
                        <label to="newsImage">Imatge:</label>
                        <input class='w-full rounded-md border-slate-300 mb-3' type='text' name='newsImage' id="newsImage" value="{{$news[0]->newsImage}}" />

                        <label to="newsContent">Subtítol:</label>
                        <textarea rows=20 class='w-full rounded-md border-slate-300 mb-1 h-96' type='text' name='newsContent' id="newsContent" contentEditable>{{$news[0]->newsContent}}</textarea>
                        <x-input-error :messages="$errors->get('message')" />
                        <x-primary-button>Guardar</x-primary-button>

                    </form>
                    <div style="margin-top:20px;display:col-lg-12" id="newsUploaderContainer">
                        <form action="{{ route('dropzoneFileUpload') }}" class="dropzone" id="my-dropzone" enctype="multipart/form-data"> @csrf</form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        Dropzone.options.myDropzone = {
            // Configuration options go here
            dictDefaultMessage: "<span style='font-size:32px; color:#2a6496;'>\n\<i style='font-size:32px;' class=\"fas fa-cloud fa-fw\"></i> Insertar foto de la notícia."
            , success: function(file, response) {
                //console.log(response.success);
                document.getElementById('newsImage').value = "images/"+response.success;

                document.getElementById("newsForm").submit();
            }

        };

        /* $('#datepicker').datepicker({
             dateFormat: 'yy-mm-dd'
         }).val(); */

    </script>
    <script>
        // Note that the name "myDropzone" is the camelized
        // id of the form.

    </script>

</x-app-layout>
