@extends('layout.mainlayout')
@section('title', "Pavellons i Pistes :: JOK.cat ")
@section('content')

@php
    $pavellonsJson = $pavellons->map(function($p) {
        return [
            'id' => $p->idPlace,
            'placeName' => $p->placeName ?? '',
            'placeAddress' => Str::limit($p->placeAddress ?? '', 100),
            'placeLat' => $p->lat ? (float)$p->lat : null,
            'placeLon' => $p->lon ? (float)$p->lon : null,
            'matches' => count($p->matches ?? []),
        ];
    })->values();
@endphp

<!-- UNIFIED HEADER (Ultra-Clean Apple Sports with Live Search) -->
<div class="w-full mt-2 mb-6 font-display">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight">
                Pavellons i Pistes d'Hoquei
            </h1>
        </div>

        <!-- SEARCH INPUT -->
        <div class="relative w-full md:w-72">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-xs"></i>
            <input type="text" id="searchPavello" placeholder="Cerca per nom o municipi..." class="w-full pl-9 pr-4 py-2 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-900 dark:text-white border border-stone-200 dark:border-stone-800 focus:outline-none focus:border-[#1c1917] dark:focus:border-[#1c1917] text-xs font-display font-medium shadow-xs transition-colors" oninput="filterPavellons()" />
        </div>
    </div>
</div>

<!-- PAVELLES TABLE CONTAINER -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl overflow-hidden shadow-xs mb-6 font-display">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="agenda">
            <thead class="bg-primary text-primary-text dark:bg-black text-[10px] uppercase font-black tracking-wider">
                <tr>
                    <th class="py-3 px-4">Pavelló i Adreça</th>
                    <th class="py-3 px-3 text-center">Distància</th>
                    <th class="py-3 px-3 text-center">Partits Avui</th>
                    <th class="py-3 px-4 text-right"><span class="hidden md:inline">Com anar-hi</span><span class="md:hidden">Mapa</span></th>
                </tr>
            </thead>
            <tbody id="pavellonsTbody" class="divide-y divide-stone-100 dark:divide-stone-800/80">
            </tbody>
        </table>
    </div>
</div>

<!-- DISCLAIMER FOOTER CARD -->
<div class="bg-stone-50 dark:bg-stone-900/40 border border-stone-200/80 dark:border-stone-800 rounded-2xl p-4 text-xs text-stone-500 dark:text-stone-400 font-display leading-relaxed mb-6">
    <i class="fa-solid fa-circle-info text-stone-900 dark:text-white mr-1"></i>
    La distància es calcula en quilòmetres lineals respecte la teva ubicació actual. Les adreces s'obtenen de forma automatitzada; assegura't de confirmar-les abans de desplaçar-te al pavelló.
</div>

<script>
    const pavellons = @json($pavellonsJson);

    function calcularDistancia(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const rad = Math.PI / 180;
        const dLat = (lat2 - lat1) * rad;
        const dLon = (lon2 - lon1) * rad;
        const lat1Rad = lat1 * rad;
        const lat2Rad = lat2 * rad;

        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1Rad) * Math.cos(lat2Rad) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function renderPavellons(userCoords = null) {
        const tbody = document.getElementById('pavellonsTbody');
        if (!tbody) return;

        let list = [...pavellons];

        if (userCoords && userCoords.lat && userCoords.lon) {
            list.forEach(p => {
                if (p.placeLat && p.placeLon) {
                    p.distance = calcularDistancia(userCoords.lat, userCoords.lon, p.placeLat, p.placeLon);
                } else {
                    p.distance = null;
                }
            });
            list.sort((a, b) => {
                if (a.distance === null) return 1;
                if (b.distance === null) return -1;
                return a.distance - b.distance;
            });
        } else {
            list.sort((a, b) => a.placeName.localeCompare(b.placeName));
        }

        tbody.innerHTML = '';

        list.forEach(pavello => {
            const tr = document.createElement('tr');
            tr.className = 'pavello-row hover:bg-stone-50 dark:hover:bg-primary/50 transition-colors text-xs font-display';

            const distanceText = (pavello.distance !== undefined && pavello.distance !== null) 
                ? `${pavello.distance.toFixed(1)} km` 
                : '--';

            const encodedName = encodeURIComponent(pavello.placeName);
            const detailUrl = `/pavellons/${pavello.id}/${encodedName}`;

            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            const mapUrl = (pavello.placeLat && pavello.placeLon) 
                ? (isIOS ? `https://maps.apple.com/?q=${pavello.placeLat},${pavello.placeLon}` : `https://www.google.com/maps/search/?api=1&query=${pavello.placeLat},${pavello.placeLon}`)
                : `https://www.google.com/maps/search/?api=1&query=${encodedName}`;

            tr.innerHTML = `
                <td class="p-3.5 border-b border-stone-100 dark:border-stone-850">
                    <a href="${detailUrl}" class="font-black text-sm text-stone-900 dark:text-stone-100 hover:text-stone-900 dark:hover:text-white transition-colors block leading-snug">
                        ${pavello.placeName}
                    </a>
                    <div class="text-[11px] font-medium text-stone-500 dark:text-stone-400 mt-0.5">${pavello.placeAddress || ''}</div>
                </td>
                <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 text-center font-extrabold text-stone-700 dark:text-stone-300">
                    <span class="inline-block bg-stone-100 dark:bg-stone-900 px-2.5 py-1 rounded-full text-xs font-black border border-stone-200/60 dark:border-stone-800">
                        ${distanceText}
                    </span>
                </td>
                <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 text-center">
                    ${pavello.matches > 0 
                        ? `<a href="${detailUrl}" class="inline-flex items-center gap-1 bg-primary text-primary-text dark:bg-stone-800 dark:text-white dark:border dark:border-stone-700 font-black text-xs px-2.5 py-1 rounded-full shadow-xs hover:scale-105 transition-transform">${pavello.matches} partits avui</a>` 
                        : `<span class="text-stone-400 font-bold text-[11px]">Sense partits</span>`}
                </td>
                <td class="p-3.5 border-b border-stone-100 dark:border-stone-850 text-right">
                    <a href="${mapUrl}" target="_blank" title="Com anar-hi" class="inline-flex items-center justify-center gap-1.5 px-2.5 md:px-3 py-1.5 rounded-full bg-primary text-primary-text hover:bg-primary-hover dark:bg-stone-800 dark:text-white dark:hover:bg-stone-700 transition-all text-xs font-black shadow-xs">
                        <i class="fa-solid fa-map-location-dot"></i><span class="hidden md:inline"> Com anar-hi</span>
                    </a>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Re-apply filter if user already typed in search input
        filterPavellons();
    }

    function filterPavellons() {
        const query = (document.getElementById('searchPavello')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#pavellonsTbody tr.pavello-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // No results feedback
        let noResultsTr = document.getElementById('noResultsTr');
        if (visibleCount === 0 && rows.length > 0) {
            if (!noResultsTr) {
                noResultsTr = document.createElement('tr');
                noResultsTr.id = 'noResultsTr';
                noResultsTr.innerHTML = `
                    <td colspan="4" class="p-8 text-center text-xs font-bold text-stone-500 dark:text-stone-400">
                        <i class="fa-solid fa-magnifying-glass text-2xl text-stone-400 mb-2 block"></i>
                        No s'ha trobat cap pavelló que coincideixi amb la cerca.
                    </td>
                `;
                document.getElementById('pavellonsTbody').appendChild(noResultsTr);
            }
            noResultsTr.style.display = '';
        } else if (noResultsTr) {
            noResultsTr.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderPavellons(null);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    renderPavellons({
                        lat: position.coords.latitude,
                        lon: position.coords.longitude
                    });
                },
                (error) => {},
                { timeout: 5000 }
            );
        }
    });
</script>
@endsection
