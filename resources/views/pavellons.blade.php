@extends('layout.mainlayout')
@section('title',"Pavellons :: JOK.cat ")

@section('content')

@section('name', 'content')
@php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
   
@endphp
<table class="w-full border-collapse bg-white shadow-sm" id="agenda">
    <thead>
        <tr class="bg-neutral-700">
            <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Pavelló</th>
            <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50 hidden lg:table-cell">Adreça</th>
            <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50 text-center">Distancia</th>
            <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">&nbsp;</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200"><tr><td>Carregant</td></tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base">La distància es calcula respecte la teva ubicació actual i en km lineals. Has de tenir la localització activada. Totes les ubicacions han estat obtingudes de forma automatitzada, assegura abans d' anar-hi que estiguin correctes.</td>
        </tr>
</table>

<script>
    const pavellons = [
        @foreach($pavellons as $pavello) {
            id: {{ $pavello -> idPlace}}
            , placeName: "{{$pavello->placeName}}"
            , placeAddress: "{{Str::substrReplace($pavello->placeAddress, '...', 100)}}"
            , placeLat: "{{$pavello->lat}}"
            , placeLon: "{{$pavello->lon}}"
            , matches: {{count($pavello -> matches)}}
        }
        , @endforeach
    ];
    const pavellonsAmbDistancia = [];

    function ubicacioActual(callback) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    callback({
                        lat: latitude
                        , lon: longitude
                    });
                }
                , function(error) { // Error callback
                    alert("No s'ha pogut obtenir la ubicació. Assegura't que la geolocalització està activada.");
                     const table = document.querySelector('#agenda tbody');
                    
                    pavellons.forEach(pavello => {
                        const row = document.createElement('tr');
                       // pavello.distance = pavello.distance.toFixed(1) + ' km';
                        row.classList.add('hover:bg-neutral-50');
                        row.innerHTML = `
                <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base"><a href='/pavellons/${pavello.id}/${pavello.placeName}'>${pavello.placeName}</a></td>
                <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base  hidden lg:table-cell ">${pavello.placeAddress}</td>
                <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base text-center">--</td>
                <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base">
                <div class="bg-neutral-700 text-white rounded-xl p-2 text-center mb-1 ${pavello.matches == 0 ? 'hidden' : ''} "><a href='/pavellons/${pavello.id}/${pavello.placeName}'>${pavello.matches} partits avuia.</a></div>
                <div class="bg-neutral-700 text-white rounded-xl p-2 text-center">
                <i class="fa-solid fa-map-location-dot"></i>`;
                @if(isset($userAgent) && $userAgent == 'iOSWebView')
                  r_innerHTML += `<a href="https://maps.apple.com/?q=${pavello.latitude},${pavello.longitude}" target="_blank">Mapa</a>`;
                @else
                    r_innerHTML += `<a href="https://www.google.com/maps/search/?api=1&query=${pavello.latitude},${pavello.longitude}" target="_blank">Mapa</a>`;
                @endif

                  r_innerHTML +=`</div></td>`;
                row.innerHTML = r_innerHTML;
                        table.appendChild(row);
                    });
                });
        } else {
            alert("Has de tenir la geolocalització activada per veure la distància als pavellons");
        }
    }


    ubicacioActual(function(coords) {
        pavellons.forEach(pavello => {
            let distancia = calcularDistancia(coords.lat, coords.lon, pavello.placeLat, pavello.placeLon);
            pavellonsAmbDistancia.push({
                id: pavello.id,
                placeName: pavello.placeName
                , placeAddress: pavello.placeAddress
                , latitude: pavello.placeLat
                , longitude: pavello.placeLon
                , distance: distancia
                , matches: pavello.matches
            });
        });
        pavellonsAmbDistancia.sort((a, b) => a.distance - b.distance);
        const table = document.querySelector('#agenda tbody');
        table.innerHTML = '';
        pavellonsAmbDistancia.forEach(pavello => {
            const row = document.createElement('tr');
            pavello.distance = pavello.distance.toFixed(1) + ' km';
            row.classList.add('hover:bg-neutral-50');
            let r_innerHTML = `
                <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base"><span class="text-bold">${pavello.placeName}</span><br />${pavello.placeAddress}</td>
                <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base hidden lg:table-cell"></td>
                <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base text-center">${pavello.distance}</td>
                <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base">
                <div class="bg-neutral-700 text-white rounded-xl p-2 text-center mb-1 ${pavello.matches == 0 ? 'hidden' : ''} "><a href='pavellons/${pavello.id}/${pavello.placeName}'>${pavello.matches} partits avui</a></div>
                <div class="bg-neutral-700 text-white rounded-xl p-2 text-center">
                <i class="fa-solid fa-map-location-dot"></i>`;
                @if(isset($userAgent) && $userAgent == 'iOSWebView')
                  r_innerHTML += `<a href="https://maps.apple.com/?q=${pavello.latitude},${pavello.longitude}" target="_blank">Mapa</a>`;
                @else
                    r_innerHTML += `<a href="https://www.google.com/maps/search/?api=1&query=${pavello.latitude},${pavello.longitude}" target="_blank">Mapa</a>`;
                @endif

                  r_innerHTML +=`</div></td>`;
                row.innerHTML = r_innerHTML;
            table.appendChild(row);
        });
    });

    function calcularDistancia(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radio de la Tierra en km

        // Convertir grados a radianes
        const rad = Math.PI / 180;
        const dLat = (lat2 - lat1) * rad;
        const dLon = (lon2 - lon1) * rad;

        // Convertir coordenadas iniciales a radianes
        const lat1Rad = lat1 * rad;
        const lat2Rad = lat2 * rad;

        // Fórmula de Haversine
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1Rad) * Math.cos(lat2Rad) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c; // Distancia en km
    }

</script>
@endsection
