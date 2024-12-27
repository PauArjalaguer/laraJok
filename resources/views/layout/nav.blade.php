<nav class="flex">
    <div class="py-2 lg:py-2 w-2/12">
        <h1><a href="/" class="webtitle font-['Comfortaa'] text-bold text-2xl md:text-4xl  font-bold">JOK.cat</a></h1>
    </div>

    <div class="py-1 w-10/12 text-right text-neutral-700 bg-graeen-700 flex justify-end">
        <div class="p-2 inline lg:hidden" id="menuButton">
            <svg onClick="showMenu()" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </div>
        <ul class="pt-2 block  hidden lg:inline" id="menu">
            <li class="block lg:inline lg:p-2 cursor-pointer font-bold text-base "><a href="/noticies">Notícies</a></li>
            <li class="block lg:inline lg:p-2 cursor-pointer font-bold text-base "><a href="/agenda">Agenda</a></li>
            <li class="block lg:inline lg:p-2 cursor-pointer font-bold text-base "><a href="/merchandising">Merchandising</a></li>
            <li class="lg:p-2 cursor-pointer font-bold text-base hidden ">Contacte</li>
            <li class="block lg:inline p-  cursor-pointer font-bold mt-2 lg:mr-2 ">
                <div class="hidden bg-neutral-200 rounded-xl  p-2  w-full   inline text-center" id="searchBarButton" onClick="(()=>{this.style.display='none'; document.getElementById('searchBar').style.display='inline'})()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline ">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="bg-neutral-400 rounded-xl p-3 md:p-2 w-full inline border-solid border-2 border-neutral-500" id="searchBar">
                    <input type="text" class='border-0 bg-transparent' onKeyUp="search(this.value)" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                </div>
            </li>

            @if (!Auth::check())
            <li class="block lg:inline mt-4 p-2 cursor-pointer font-bold bg-neutral-700 rounded-xl  border-2 border-neutral-700 text-white text-center">
                <a href='/dashboard'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg> Login
                </a>
            </li>
            @else
            <li class="block lg:inline mt-4 p-2 cursor-pointer font-bold border-solid border-2 border-neutral-700 rounded-xl text-neutral-700 text-center mb-5">
                <a href='/dashboard' class='inline'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline my-">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg> {{Auth::user()->name}} | </a> <a href='/logout'><span class="font-bold text-neutral-900 ">Sortir</span>
                </a>
            </li>
            @endif

        </ul>
    </div>
</nav>
@if (!Auth::check())
<div class="flex rounded-xl my-1 bg-neutral-200 p-2 px-4" id='userSavedDataBanner'>
    <div class="w-11/12">
        <h1 class="font-bold font-sm md:text-xl text-neutral-700"><a href="/register" class='text-neutral-900'>Registra't</a> o <a href="/login" class='text-neutral-900'>accedeix</a> per a guardar els teus accessos directes.</h1>
    </div>
    <div class="w-1/12 text-right flex justify-end cursor-pointer" onClick="document.getElementById('userSavedDataBanner').style.display='none';">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    </div>
</div>
@endif
<x-shortcuts-component :userSavedData="$userSavedData" />
<div id="search" class="hidden bg-neutral-100 rounded-xl my-6 p-2">Resultats de cerca per: <span class='font-bold' id="searchValue"></span>
    <div id="searchReturn" class="flex flex-wrap"></div>
</div>
<script>
    let length = 0;

    function sleep(ms) {
        document.getElementById('searchReturn').innerHTML = "";
        return new Promise(resolve => setTimeout(resolve, ms));

    }
    var timeout = null;
    const search = (value) => {
        clearTimeout(timeout);

        timeout = setTimeout(function() {
            document.getElementById('search').style.display = 'block';
            document.getElementById('searchValue').innerHTML = value;
            console.log(value);
            fetch("https://jok.cat/api/search/teams/" + value)
                .then(response => {
                    document.getElementById('searchReturn').innerHTML = "";
                    console.log(value + " netejo formulari")
                    return response.json()
                })
                .then(data => {
                    document.getElementById('searchReturn').innerHTML = "";
                    document.getElementById('searchReturn').insertAdjacentHTML('beforeend', "<div class='block w-full m-2 font-bold'>" + data.length + " equips</div>");
                    // console.log(data);
                    data.map((team) => {
                        console.log(team);
                        document.getElementById('searchReturn').insertAdjacentHTML('beforeend', "<div class='p-1 w-1/4'><div class='bg-neutral-200 rounded-xl p-4 cursor-pointer' ><a class='text-sm' href='/equip/" + team.idTeam + "/" + team.teamName + "'>" + (team.teamName + " " + team.categoryName).substr(0, 36) + "</a></div></div>")
                    })

                });
            /* fetch("http://larajok.test/api/search/clubs/" + value)
             .then(response => {
                 return response.json()
             })
             .then(data => {
                 console.log(data)
             })*/
            fetch("https://jok.cat/api/search/players/" + value)
                .then(response => {
                    return response.json()
                })
                .then(data => {
                    document.getElementById('searchReturn').insertAdjacentHTML('beforeend', "<div class='block w-full m-2 font-bold'>" + data.length + " jugadors</div>");
                    data.map((player) => {

                        document.getElementById('searchReturn').insertAdjacentHTML('beforeend', "<div class='p-1 w-1/4'><div class='bg-neutral-200 rounded-xl p-4 cursor-pointer' ><a  class='text-sm'  href='/jugador/" + player.idPlayer + "/" + player.playerName + "'>" + player.playerName.substr(0, 36) + "</a></div></div>")
                    })

                });
        }, 500);
    }

    function showMenu() {
        document.getElementById("menuButton").style.display = "none";
        document.getElementById("menu").style.display = "block";
    }

</script>
