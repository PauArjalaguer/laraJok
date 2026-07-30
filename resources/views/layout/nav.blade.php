@php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    if(isset($userAgent) && $userAgent == 'iOSWebView'){
        echo "<div class='mt-20'>&nbsp;</div>";
    }
@endphp
<nav class="flex items-center justify-between py-4 border-b border-stone-200 dark:border-stone-800/80 mb-6 bg-transparent">
    <!-- Logo Original (Comfortaa) -->
    <div class="flex items-center">
        <a href="/" class="webtitle font-['Comfortaa'] font-bold text-2xl md:text-3xl tracking-tight text-stone-900 dark:text-white flex items-center gap-0.5">
            JOK.cat
        </a>
    </div>

    <!-- Center Navigation Links (Modern Clean Style) -->
    <div class="hidden lg:flex items-center gap-6">
        <ul class="flex items-center gap-6 font-display text-sm font-semibold text-stone-700 dark:text-stone-300">
            <li>
                <a href="/competicions" class="py-1.5 hover:text-[#f5c310] transition-colors {{ request()->is('competicions*') ? 'border-b-2 border-[#f5c310] text-[#f5c310] dark:text-[#f5c310]' : '' }}">
                    Competicions
                </a>
            </li>
            <li>
                <a href="/noticies" class="py-1.5 hover:text-[#f5c310] transition-colors {{ request()->is('noticies*') ? 'border-b-2 border-[#f5c310] text-[#f5c310] dark:text-[#f5c310]' : '' }}">
                    Notícies
                </a>
            </li>
            <li>
                <a href="/pavellons" class="py-1.5 hover:text-[#f5c310] transition-colors {{ request()->is('pavellons*') ? 'border-b-2 border-[#f5c310] text-[#f5c310] dark:text-[#f5c310]' : '' }}">
                    Pavellons
                </a>
            </li>
            <li>
                <a href="/agenda" class="py-1.5 hover:text-[#f5c310] transition-colors {{ request()->is('agenda*') || request()->is('/') ? 'border-b-2 border-[#f5c310] text-[#f5c310] dark:text-[#f5c310]' : '' }}">
                    Agenda
                </a>
            </li>
            <li>
                <a href="/anuncis" class="py-1.5 hover:text-[#f5c310] transition-colors {{ request()->is('anuncis*') ? 'border-b-2 border-[#f5c310] text-[#f5c310] dark:text-[#f5c310]' : '' }} flex items-center gap-1.5">
                    Segona Mà
                    <span class="bg-[#f5c310] text-black text-[10px] font-bold px-1.5 py-0.5 rounded-full uppercase">Nou</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Search Bar -->
        <div class="relative flex items-center bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl px-3 py-1.5 w-32 md:w-48 transition-all focus-within:border-[#f5c310]">
            <input type="text" placeholder="Cerca..." class="w-full bg-transparent border-0 p-0 text-xs md:text-sm text-stone-800 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500 focus:ring-0 focus:outline-none" onKeyUp="search(this.value)" />
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-stone-400 dark:text-stone-500 ml-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>

        <!-- App Store Download Button -->
        <a href="https://apps.apple.com/ca/app/jok/id6743651881" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg font-bold text-xs md:text-sm bg-[#f5c310] text-black hover:bg-amber-400 transition-all shadow-sm">
            <i class="fa-brands fa-apple text-sm"></i> Descarrega l'App
        </a>

        <!-- Login / User Action -->
        @if (!Auth::check())
            <a href="/login" class="text-xs md:text-sm font-bold text-stone-700 dark:text-stone-300 hover:text-[#f5c310] transition-colors">
                Login
            </a>
        @else
            <a href="/dashboard" class="text-xs md:text-sm font-bold text-stone-700 dark:text-stone-300 hover:text-[#f5c310] transition-colors">
                {{ Auth::user()->name }}
            </a>
        @endif

        <!-- Theme Toggler -->
        <button onClick="toggleTheme()" class="p-2 rounded-xl text-stone-600 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors focus:outline-none" aria-label="Toggle theme">
            <i class="fa-solid fa-sun dark:hidden text-lg"></i>
            <i class="fa-solid fa-moon hidden dark:block text-lg text-[#f5c310]"></i>
        </button>

        <!-- Mobile Menu Toggle -->
        <button onClick="toggleMenu()" class="p-2 inline lg:hidden text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 rounded-xl" id="menuButton">
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
<div class="flex rounded-xl my-3 bg-stone-100 dark:bg-neutral-900 border border-stone-200/60 dark:border-neutral-800/80 p-3 px-4 shadow-sm" id='userSavedDataBanner'>
    <div class="w-11/12 flex items-center">
        <h1 class="font-bold text-xs md:text-sm text-stone-700 dark:text-neutral-300">
            <a href="/register" class='text-[#f5c310] hover:underline font-black'>Registra't</a> o <a href="/login" class='text-stone-900 dark:text-white hover:underline'>accedeix</a> per a guardar els teus accessos directes.
        </h1>
    </div>
    <div class="w-1/12 text-right flex justify-end cursor-pointer text-stone-400 dark:text-neutral-500 hover:text-stone-600 dark:hover:text-neutral-300" onClick="document.getElementById('userSavedDataBanner').style.display='none';">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    </div>
</div>
@endif

<x-shortcuts-component :userSavedData="$userSavedData" />

<!-- Search results area -->
<div id="search" class="hidden bg-white dark:bg-neutral-900 border border-stone-200 dark:border-neutral-800/80 rounded-xl my-6 p-4 shadow-xl">
    <div class="text-sm text-stone-500 dark:text-neutral-400 mb-3">
        Resultats de cerca per: <span class='font-bold text-stone-900 dark:text-white' id="searchValue"></span>
    </div>
    <div id="searchReturn" class="flex flex-wrap -mx-2"></div>
</div>

<script>
    let length = 0;

    function sleep(ms) {
        document.getElementById('searchReturn').innerHTML = "";
        return new Promise(resolve => setTimeout(resolve, ms));
    }
    var timeout = null;
    let totalDataLength = 0;
    const search = (value) => {
        totalDataLength = 0;
        clearTimeout(timeout);
        if (!value || value.length < 4) {
            document.getElementById('search').style.display = 'none';
            return;
        }
        timeout = setTimeout(function() {
            document.getElementById('search').style.display = 'block';
            document.getElementById('searchValue').innerHTML = value;
            
            const teamsFetch = fetch("https://jok.cat/api/search/teams/" + value).then(response => response.json());
            const playersFetch = fetch("https://jok.cat/api/search/players/" + value).then(response => response.json());

            Promise.all([teamsFetch, playersFetch])
                .then(([teamsData, playersData]) => {
                    const searchReturn = document.getElementById('searchReturn');
                    searchReturn.innerHTML = ""; // Clear results
                    
                    // Process Teams
                    totalDataLength += teamsData.length;
                    searchReturn.insertAdjacentHTML('beforeend', "<div class='block w-full mx-2 my-3 font-bold text-stone-900 dark:text-white'>" + teamsData.length + " equips</div>");
                    
                    let lastSeason = null;
                    teamsData.map((team) => {
                        if (team.idSeason !== lastSeason) {
                            searchReturn.insertAdjacentHTML(
                                'beforeend',
                                `<div class="w-full px-2 py-1 text-xs font-semibold text-stone-400 dark:text-neutral-500" >` + team.seasonName + `</div>`
                            );
                            lastSeason = team.idSeason;
                        }
                        searchReturn.insertAdjacentHTML('beforeend', `<div class='p-2 w-full sm:w-1/2 md:w-1/4'><div class='bg-stone-50 dark:bg-neutral-800 border border-stone-200/60 dark:border-neutral-700/60 rounded-xl p-4 cursor-pointer hover:border-[#f5c310] dark:hover:border-[#f5c310] transition-all' ><a class='text-sm font-semibold text-stone-850 dark:text-neutral-100' href='/equip/` + team.idTeam + `/` + team.teamName + `'>` + team.teamName + `<br /><small class='text-stone-500 dark:text-neutral-400 font-normal'>` + team.categoryName + `</small></a></div></div>`)
                    });

                    // Process Players
                    totalDataLength += playersData.length;
                    searchReturn.insertAdjacentHTML('beforeend', "<div class='block w-full mx-2 my-3 font-bold text-stone-900 dark:text-white'>" + playersData.length + " jugadors</div>");
                    
                    playersData.map((player) => {
                        searchReturn.insertAdjacentHTML('beforeend', `<div class='p-2 w-full sm:w-1/2 md:w-1/4'><div class='bg-stone-50 dark:bg-neutral-800 border border-stone-200/60 dark:border-neutral-700/60 rounded-xl p-4 cursor-pointer hover:border-[#f5c310] dark:hover:border-[#f5c310] transition-all' ><a class='text-sm font-semibold text-stone-850 dark:text-neutral-100' href='/jugador/` + player.idPlayer + `/` + player.playerName + `'>` + player.playerName.substr(0, 36) + `</a></div></div>`)
                    });

                    // Update Sidebar Visibility
                    const sidebarSearchResults = document.getElementById("sidebarSearchResults");
                    if (sidebarSearchResults) {
                         if (totalDataLength > 0) {
                            sidebarSearchResults.style.display = 'block';
                        } else {
                            sidebarSearchResults.style.display = 'none';
                        }
                        sidebarSearchResults.innerHTML = totalDataLength + " resultats trobats";
                    }
                })
                .catch(error => {
                    console.error("Error fetching search results:", error);
                });
        }, 750);
    }

    function toggleMenu() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
    }
</script>
