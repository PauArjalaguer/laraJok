@extends('layout.mainlayout')
@section('content')

<div class="w-full text-slate-700 text-xl my-4 font-bold pb-2 border-b border-slate-400">

    Merchandising
</div>
<div class='flex flex-wrap '>


    @foreach ($merchandisingListAll as $merch)
    <div class="p-2 w-1/3 rounded-md">
        <div class="bg-white   shadow-md  rounded-md  hover:bg-slate-50 transition-all shadow-slate-700  text-center ">

            <a href="{{$merch->assetUrl}}" target="_blank" rel="noreferer">
                <img class='rounded-t-md aspect-square' src={{$merch->assetThumbnail}} />
            </a>

            {{-- <a href="{{$merch->assetUrl}}" target="_blank" rel="noreferer" class="p-4 text-red-100">
            {{ucwords(mb_strtolower($merch->assetName))}}
            </a> --}}

        </div>
    </div>
    @endforeach

    <div class="text-lg text-slate-700 text-center w-full flex justify-center">
        <a href="https://www.latostadora.com/shop/jokcat/?ord=reciente#shop" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

        </a>
    </div>
    <div class="clear-both"></div>

</div>
@endsection
