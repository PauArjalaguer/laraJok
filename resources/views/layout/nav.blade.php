@php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    if(isset($userAgent) && $userAgent == 'iOSWebView'){
        echo "<div class='mt-20'>&nbsp;</div>";
    }
@endphp
<nav class="sticky top-0 z-40 flex items-center justify-between py-3.5 px-4 md:px-6 border-b border-stone-200 dark:border-stone-800/80 mb-6 bg-white/90 dark:bg-black/90 backdrop-blur-xl shadow-xs">
    <!-- Logo Original (Comfortaa) -->
    <div class="flex items-center">
        <a href="/" class="webtitle font-['Comfortaa'] font-bold text-2xl md:text-3xl tracking-tight text-stone-900 dark:text-white flex items-center gap-0.5">
            JOK.cat
        </a>
    </div>

    <!-- Center Navigation Links (Apple Sports Style) -->
    <div class="hidden lg:flex items-center gap-6">
        <ul class="flex items-center gap-6 font-display text-sm font-bold text-stone-700 dark:text-stone-300">
            <li>
                <a href="/competicions" class="py-1.5 hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors {{ request()->is('competicions*') ? 'border-b-2 border-stone-900 dark:border-[#d4ff00] text-stone-900 dark:text-[#d4ff00]' : '' }}">
                    Competicions
                </a>
            </li>
            <li>
                <a href="/noticies" class="py-1.5 hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors {{ request()->is('noticies*') ? 'border-b-2 border-stone-900 dark:border-[#d4ff00] text-stone-900 dark:text-[#d4ff00]' : '' }}">
                    Notícies
                </a>
            </li>
            <li>
                <a href="/pavellons" class="py-1.5 hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors {{ request()->is('pavellons*') ? 'border-b-2 border-stone-900 dark:border-[#d4ff00] text-stone-900 dark:text-[#d4ff00]' : '' }}">
                    Pavellons
                </a>
            </li>
            <li>
                <a href="/agenda" class="py-1.5 hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors {{ request()->is('agenda*') || request()->is('/') ? 'border-b-2 border-stone-900 dark:border-[#d4ff00] text-stone-900 dark:text-[#d4ff00]' : '' }}">
                    Agenda
                </a>
            </li>
            <li>
                <a href="/anuncis" class="py-1.5 hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors {{ request()->is('anuncis*') ? 'border-b-2 border-stone-900 dark:border-[#d4ff00] text-stone-900 dark:text-[#d4ff00]' : '' }} flex items-center gap-1.5">
                    Segona Mà
                    <span class="bg-[#d4ff00] text-black text-[9px] font-black px-2 py-0.5 rounded-full uppercase shadow-xs">Nou</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Search Bar -->
        <div class="relative flex items-center bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-full px-3 py-1.5 w-32 md:w-48 transition-all focus-within:border-stone-400 dark:focus-within:border-[#d4ff00]">
            <input type="text" placeholder="Cerca..." spellcheck="false" autocomplete="off" class="w-full bg-transparent border-0 p-0 text-xs md:text-sm text-stone-800 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-0 focus:outline-none" onKeyUp="search(this.value)" />
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-stone-400 dark:text-stone-500 ml-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>

        <!-- App Store Download Button (Negre sobre Verd-Lima Apple Sports) -->
        <a href="https://apps.apple.com/ca/app/jok/id6743651881" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full font-black text-xs md:text-sm bg-[#d4ff00] text-black hover:bg-stone-900 hover:text-white dark:bg-[#d4ff00] dark:text-black dark:hover:bg-stone-900 dark:hover:text-[#d4ff00] transition-all shadow-xs">
            <i class="fa-brands fa-apple text-sm"></i> Descarrega l'App
        </a>

        <!-- Login / User Action -->
        @if (!Auth::check())
            <a href="/login" class="text-xs md:text-sm font-extrabold text-stone-700 dark:text-stone-300 hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors">
                Login
            </a>
        @else
            <a href="/dashboard" class="text-xs md:text-sm font-extrabold text-stone-700 dark:text-stone-300 hover:text-stone-900 dark:hover:text-[#d4ff00] transition-colors">
                {{ Auth::user()->name }}
            </a>
        @endif

        <!-- Theme Toggler -->
        <button onClick="toggleTheme()" class="p-2 rounded-full text-stone-600 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors focus:outline-none flex items-center justify-center" aria-label="Toggle theme">
            <span class="dark:hidden flex items-center"><i class="fa-solid fa-sun text-lg text-amber-500"></i></span>
            <span class="hidden dark:flex items-center"><i class="fa-solid fa-moon text-lg text-[#d4ff00]"></i></span>
        </button>

        <!-- Mobile Menu Toggle -->
        <button onClick="toggleMenu()" class="p-2 inline lg:hidden text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 rounded-full" id="menuButton">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>
</nav>

@if($userAgent != 'iOSWebView')
<div class="flex rounded-xl my-3 bg-stone-100 dark:bg-neutral-900 border border-stone-200/60 dark:border-neutral-800/80 p-3 px-4 hidden" id='appStore'>
    <div class="w-11/12 flex items-center">
        <h1 class="font-bold text-xs md:text-sm text-stone-700 dark:text-neutral-300">
            <a href="https://apps.apple.com/ca/app/jok/id6743651881" class='hover:underline text-stone-900 dark:text-white'>Descarrega't ja l'aplicació per a iOS</a>
        </h1>
    </div>
    <div class="w-1/12 text-right flex justify-end cursor-pointer text-stone-400 dark:text-neutral-500 hover:text-stone-600 dark:hover:text-neutral-300" onClick="document.getElementById('appStore').style.display='none';">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    </div>
</div>
@endif

@if (!Auth::check())
<div class="flex rounded-full my-3 bg-stone-100 dark:bg-stone-900 border border-stone-200/60 dark:border-stone-800/80 p-3 px-5 shadow-xs items-center justify-between" id='userSavedDataBanner'>
    <div class="flex items-center gap-2">
        <h1 class="font-medium text-xs md:text-sm text-stone-600 dark:text-stone-300">
            <a href="/register" class="text-stone-900 dark:text-white font-black hover:text-[#d4ff00] transition-colors underline decoration-[#d4ff00] underline-offset-2">Registra't</a> o <a href="/login" class="text-stone-900 dark:text-white font-black hover:text-[#d4ff00] transition-colors underline decoration-stone-400 dark:decoration-stone-600 underline-offset-2">accedeix</a> per a guardar els teus accessos directes.
        </h1>
    </div>
    <div class="cursor-pointer text-stone-400 dark:text-neutral-500 hover:text-stone-600 dark:hover:text-neutral-300" onClick="document.getElementById('userSavedDataBanner').style.display='none';">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    </div>
</div>
@endif

<x-shortcuts-component :userSavedData="$userSavedData" />

<!-- Search results area -->
<div id="search" class="hidden bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/80 rounded-3xl my-6 p-5 shadow-xl font-display">
    <div class="text-xs font-bold text-stone-500 dark:text-stone-400 mb-3">
        Resultats de cerca per: <span class='font-black text-stone-900 dark:text-white' id="searchValue"></span>
    </div>
    <div id="searchReturn" class="flex flex-wrap -mx-2"></div>
</div>

<script>
    var searchTimeout = null;
    let totalDataLength = 0;
    const search = (value) => {
        totalDataLength = 0;
        clearTimeout(searchTimeout);
        const searchDiv = document.getElementById('search');
        if (!value || value.length < 3) {
            if (searchDiv) searchDiv.classList.add('hidden');
            return;
        }
        searchTimeout = setTimeout(function() {
            if (searchDiv) searchDiv.classList.remove('hidden');
            const searchValEl = document.getElementById('searchValue');
            if (searchValEl) searchValEl.innerHTML = value;
            
            const teamsFetch = fetch("https://jok.cat/api/search/teams/" + encodeURIComponent(value)).then(response => response.json());
            const playersFetch = fetch("https://jok.cat/api/search/players/" + encodeURIComponent(value)).then(response => response.json());

            Promise.all([teamsFetch, playersFetch])
                .then(([teamsData, playersData]) => {
                    const searchReturn = document.getElementById('searchReturn');
                    if (!searchReturn) return;
                    searchReturn.innerHTML = "";
                    
                    // Teams
                    totalDataLength += teamsData.length;
                    searchReturn.insertAdjacentHTML('beforeend', "<div class='block w-full mx-2 my-3 font-black text-[#d4ff00] text-xs uppercase tracking-wider'>" + teamsData.length + " equips trobats</div>");
                    
                    let lastSeason = null;
                    teamsData.forEach((team) => {
                        if (team.idSeason !== lastSeason) {
                            searchReturn.insertAdjacentHTML(
                                'beforeend',
                                `<div class="w-full px-2 py-1 text-[10px] font-black uppercase text-stone-400 dark:text-stone-500" >` + team.seasonName + `</div>`
                            );
                            lastSeason = team.idSeason;
                        }
                        searchReturn.insertAdjacentHTML('beforeend', `<div class='p-2 w-full sm:w-1/2 md:w-1/4'><div class='bg-stone-50 dark:bg-stone-900 border border-stone-200/60 dark:border-stone-800 rounded-2xl p-4 cursor-pointer hover:border-[#d4ff00] transition-all' ><a class='text-xs font-black text-stone-900 dark:text-white hover:text-[#d4ff00]' href='/equip/` + team.idTeam + `/` + encodeURIComponent(team.teamName) + `'>` + team.teamName + `<br /><small class='text-stone-500 dark:text-stone-400 font-extrabold text-[10px]'>` + team.categoryName + `</small></a></div></div>`);
                    });

                    // Players
                    totalDataLength += playersData.length;
                    searchReturn.insertAdjacentHTML('beforeend', "<div class='block w-full mx-2 my-3 font-black text-[#d4ff00] text-xs uppercase tracking-wider'>" + playersData.length + " jugadors trobats</div>");
                    
                    playersData.forEach((player) => {
                        searchReturn.insertAdjacentHTML('beforeend', `<div class='p-2 w-full sm:w-1/2 md:w-1/4'><div class='bg-stone-50 dark:bg-stone-900 border border-stone-200/60 dark:border-stone-800 rounded-2xl p-4 cursor-pointer hover:border-[#d4ff00] transition-all' ><a class='text-xs font-black text-stone-900 dark:text-white hover:text-[#d4ff00]' href='/jugador/` + player.idPlayer + `/` + encodeURIComponent(player.playerName) + `'>` + player.playerName.substr(0, 36) + `</a></div></div>`);
                    });
                })
                .catch(error => {
                    console.error("Error en la cerca:", error);
                });
        }, 300);
    };

    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('-translate-x-full');
        }
    }
</script>
