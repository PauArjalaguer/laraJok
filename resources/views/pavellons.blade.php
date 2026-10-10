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

<!-- MAPA DE PAVELLONS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
    #pavellonsMap { height: 460px; z-index: 0; font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
    @media (max-width: 768px) { #pavellonsMap { height: 340px; } }

    /* Pins: gris apagat per defecte, fosc (color principal) si hi ha partits avui */
    .jok-pin { background: transparent; border: 0; }
    .jok-pin .pin {
        width: 22px; height: 22px; border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg); background: #a8a29e; border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,.25); display: flex; align-items: center; justify-content: center;
        transition: transform .15s ease;
    }
    .jok-pin .pin::after { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #fff; }
    .jok-pin.has-matches .pin { width: 28px; height: 28px; background: var(--color-primary, #1e293b); box-shadow: 0 3px 10px rgba(0,0,0,.35); }
    .jok-pin.has-matches .pin::after { width: 9px; height: 9px; }
    html.dark .jok-pin .pin { background: #57534e; border-color: #1c1917; }
    html.dark .jok-pin .pin::after { background: #1c1917; }
    html.dark .jok-pin.has-matches .pin { background: #f5f5f4; }
    .jok-pin:hover .pin { transform: rotate(-45deg) scale(1.15); }
    .jok-user .dot { width: 16px; height: 16px; border-radius: 50%; background: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 6px rgba(37,99,235,.25); }

    /* Popup */
    #pavellonsMap .leaflet-popup-content-wrapper {
        border-radius: 18px; padding: 0; border: 1px solid #e7e5e4;
        box-shadow: 0 10px 30px -8px rgba(0,0,0,.25);
    }
    #pavellonsMap .leaflet-popup-content {
        margin: 0; padding: 16px 18px 14px; box-sizing: border-box;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; font-size: 12px; line-height: 1.4; color: #1c1917;
    }
    #pavellonsMap .leaflet-popup-close-button { top: 8px; right: 8px; width: 22px; height: 22px; font-size: 18px; color: #a8a29e; }
    #pavellonsMap .leaflet-popup-close-button:hover { color: #1c1917; }
    .jok-popup-title { display: block; padding-right: 18px; font-weight: 800; font-size: 14px; line-height: 1.25; letter-spacing: -.01em; color: inherit !important; text-decoration: none; }
    .jok-popup-title:hover { text-decoration: underline; text-underline-offset: 2px; }
    .jok-popup-address { margin-top: 4px; font-size: 11px; font-weight: 500; color: #78716c; }
    .jok-popup-badge { display: inline-flex; align-items: center; gap: 5px; margin-top: 10px; padding: 3px 9px; border-radius: 999px; background: #f5f5f4; border: 1px solid #e7e5e4; color: #1c1917; font-size: 10px; font-weight: 800; }
    .jok-popup-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--color-primary, #1e293b); }
    .jok-popup-actions { display: flex; gap: 6px; margin-top: 12px; }
    .jok-popup-actions a { flex: 1; text-align: center; padding: 7px 8px; border-radius: 999px; font-size: 11px; font-weight: 800; text-decoration: none; transition: background-color .15s ease; }
    .jok-popup-btn-primary { background: var(--color-primary, #1e293b); color: var(--color-primary-text, #fff) !important; }
    .jok-popup-btn-primary:hover { background: var(--color-primary-hover, #334155); }
    .jok-popup-btn-ghost { border: 1px solid #e7e5e4; color: #1c1917 !important; }
    .jok-popup-btn-ghost:hover { background: #f5f5f4; }

    html.dark #pavellonsMap .leaflet-popup-content-wrapper, html.dark #pavellonsMap .leaflet-popup-tip { background: #1c1917; border-color: #292524; }
    html.dark #pavellonsMap .leaflet-popup-content { color: #f5f5f4; }
    html.dark .jok-popup-address { color: #a8a29e; }
    html.dark .jok-popup-badge { background: #292524; border-color: #44403c; color: #f5f5f4; }
    html.dark .jok-popup-badge::before { background: #f5f5f4; }
    html.dark .jok-popup-btn-primary { background: #f5f5f4; color: #1c1917 !important; }
    html.dark .jok-popup-btn-primary:hover { background: #e7e5e4; }
    html.dark .jok-popup-btn-ghost { border-color: #44403c; color: #f5f5f4 !important; }
    html.dark .jok-popup-btn-ghost:hover { background: #292524; }

    html.dark .leaflet-container { background: #121215; }
    #pavellonsMap .leaflet-tile-pane { filter: grayscale(.85) contrast(.95) brightness(1.03); }
    html.dark #pavellonsMap .leaflet-tile-pane { filter: grayscale(1) invert(1) brightness(.85) contrast(.9); }
</style>
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl overflow-hidden shadow-xs mb-6 font-display">
    <div id="pavellonsMap"></div>
    <div class="flex items-center gap-4 px-4 py-2.5 text-[11px] font-bold text-stone-500 dark:text-stone-400 border-t border-stone-100 dark:border-stone-800">
        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-primary dark:bg-stone-100"></span> Partits avui</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-stone-400 dark:bg-stone-600"></span> Sense partits avui</span>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    const pavellons = @json($pavellonsJson);

    // ---------- MAPA ----------
    let pavMap = null;
    let userMarker = null;
    const pavMarkers = {}; // id -> marker

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function popupHtml(p) {
        const detailUrl = `/pavellons/${p.id}/${encodeURIComponent(p.placeName)}`;
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const mapUrl = isIOS
            ? `https://maps.apple.com/?q=${p.placeLat},${p.placeLon}`
            : `https://www.google.com/maps/search/?api=1&query=${p.placeLat},${p.placeLon}`;
        const matchesBadge = p.matches > 0
            ? `<span class="jok-popup-badge">${p.matches} ${p.matches === 1 ? 'partit' : 'partits'} avui</span>`
            : '';
        return `
            <div>
                <a href="${detailUrl}" class="jok-popup-title">${escapeHtml(toTitle(p.placeName))}</a>
                <div class="jok-popup-address">${escapeHtml(p.placeAddress)}</div>
                ${matchesBadge}
                <div class="jok-popup-actions">
                    <a href="${detailUrl}" class="jok-popup-btn-primary">Veure pavelló</a>
                    <a href="${mapUrl}" target="_blank" rel="noopener" class="jok-popup-btn-ghost">Com anar-hi</a>
                </div>
            </div>`;
    }

    // Converteix noms en majúscules ("PAVELLÓ D ESPORTS") a format títol
    function toTitle(s) {
        const str = String(s ?? '');
        if (str !== str.toUpperCase()) return str;
        return str.toLowerCase().replace(/(^|[\s\-'’(])(\p{L})/gu, (m, sep, ch) => sep + ch.toUpperCase());
    }

    function initMap() {
        if (typeof L === 'undefined' || !document.getElementById('pavellonsMap')) return;

        pavMap = L.map('pavellonsMap', { scrollWheelZoom: false }).setView([41.7, 1.75], 8);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(pavMap);

        // Activa el zoom amb roda només després de clicar el mapa (evita segrestar l'scroll)
        pavMap.on('click', () => pavMap.scrollWheelZoom.enable());
        pavMap.on('mouseout', () => pavMap.scrollWheelZoom.disable());

        const bounds = [];
        pavellons.forEach(p => {
            if (!p.placeLat || !p.placeLon) return;
            const icon = L.divIcon({
                className: 'jok-pin' + (p.matches > 0 ? ' has-matches' : ''),
                html: '<div class="pin"></div>',
                iconSize: [26, 26],
                iconAnchor: [13, 30],
                popupAnchor: [0, -28]
            });
            const marker = L.marker([p.placeLat, p.placeLon], {
                icon,
                title: p.placeName,
                zIndexOffset: p.matches > 0 ? 1000 : 0
            }).bindPopup(popupHtml(p), { minWidth: 250, maxWidth: 250 }).addTo(pavMap);
            marker._searchText = `${p.placeName} ${p.placeAddress}`.toLowerCase();
            pavMarkers[p.id] = marker;
            bounds.push([p.placeLat, p.placeLon]);
        });

        if (bounds.length) pavMap.fitBounds(bounds, { padding: [30, 30], maxZoom: 12 });
    }

    function filterMarkers(query) {
        if (!pavMap) return;
        const visible = [];
        Object.values(pavMarkers).forEach(m => {
            const show = !query || m._searchText.includes(query);
            if (show) {
                if (!pavMap.hasLayer(m)) m.addTo(pavMap);
                visible.push(m.getLatLng());
            } else if (pavMap.hasLayer(m)) {
                pavMap.removeLayer(m);
            }
        });
        if (query && visible.length) pavMap.fitBounds(visible, { padding: [30, 30], maxZoom: 13 });
    }

    function showUserOnMap(coords) {
        if (!pavMap) return;
        const ll = [coords.lat, coords.lon];
        if (userMarker) { userMarker.setLatLng(ll); return; }
        userMarker = L.marker(ll, {
            icon: L.divIcon({ className: 'jok-user', html: '<div class="dot"></div>', iconSize: [16, 16], iconAnchor: [8, 8] }),
            zIndexOffset: 2000
        }).bindPopup('<strong>Ets aquí</strong>').addTo(pavMap);
    }

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

        filterMarkers(query);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMap();
        renderPavellons(null);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const coords = {
                        lat: position.coords.latitude,
                        lon: position.coords.longitude
                    };
                    renderPavellons(coords);
                    showUserOnMap(coords);
                },
                (error) => {},
                { timeout: 5000 }
            );
        }
    });
</script>
@endsection
