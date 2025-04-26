@extends('layout.mainlayout')
@section('title',"Anuncis :: JOK.cat ")
@section('content')
<script>
    const lat2 = 41.536275;
    const lon2 = 2.083333;

    function calcular_distancia(ubicacio) {

        const l = ubicacio.toString().split(',');
        const lat1 = parseFloat(l[0]);
        const lon1 = parseFloat(l[1]);

        const R = 6371; // Radi de la Terra en km
        const rad = Math.PI / 180;
        const dLat = (lat2 - lat1) * rad;
        const dLon = (lon2 - lon1) * rad;

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * rad) * Math.cos(lat2 * rad) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        const distancia = R * c;
        document.write(distancia.toFixed(1) + " km");
    }

</script>
<div class="w-full text-neutral-700 text-xl mb-2 font-bold  border-b border-neutral-400"> Anuncis</div>
<div class='flex flex-wrap'>

    <div class='border-b border-neutral-300 flex w-full pb-2'>
        <div class="flex flex-wrap">
            {{-- @for($i = 0; $i < 110; $i++) <x-anuncis-component>
                </x-anuncis-component>
                @endfor --}}
            @foreach($anuncis_llista as $anunci)
            <x-anuncis-component :anunci="$anunci"></x-anuncis-component>
            @endforeach
        </div>
    </div> <div style="clear:both">
</div>
<div class="clear"></div>
<div class="text-lg text-neutral-700 text-center w-full flex justify-center mt-12">
    <a href="https://www.latostadora.com/shop/jokcat/?ord=reciente#shop" target="_blank">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    </a>
</div>
<div class="clear-both"></div>



@endsection
