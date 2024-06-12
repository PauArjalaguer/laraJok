<nav class="flex">
    <div class="py-2 lg:py-4 w-2/12">
        <h1><a href="/" class="webtitle font-['Comfortaa'] text-bold text-2xl md:text-4xl text-slate-700 font-bold">JOK.cat</a></h1>
    </div>

    <div class=" py-1 w-10/12 text-right text-slate-700 bg-graeen-700 flex justify-end">
        <div class="p-2 inline md:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </div>
        <ul class="p-2 block  hidden md:inline">
            <li class="block lg:inline p-2 cursor-pointer font-bold text-sm md:text-base">Notícies</li>
            <li class=" block lg:inline  p-2 cursor-pointer font-bold text-sm md:text-base"><a href="/merchandising">Merchandising</a></li>
            <li class="hidden  p-2 cursor-pointer font-bold text-sm md:text-base">Contacte</li>

            <li class="inline p-2 cursor-pointer font-bold inline" onClick="(()=>{this.style.display='none'; document.getElementById('searchBar').style.display='inline'})()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </li>
            <div class='bg-slate-400 rounded-xl hidden p-2  mt-4 lg:mr-2 lg:m-0 w-full  ' id="searchBar">
                <input type="text" class='border-0 bg-transparent' onKeyUp="search(this.value)" />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

            </div>
            @if (!Auth::check())
            <li class="inline p-2 cursor-pointer font-bold bg-slate-700 rounded-xl text-white">
                <a href='/dashboard'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg> Login
                </a>
            </li>
            @else
            <li class="inline p-2 cursor-pointer font-bold border-solid border-2 border-slate-700 rounded-xl text-slate-700">
                <a href='/dashboard' class='hidden lg:inline'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline my-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg> {{Auth::user()->name}} | </a> <a href='/logout'><span class="font-bold text-slate-900 ">Sortir</span></a>

            </li>

            @endif

        </ul>
    </div>
</nav>

@if (!Auth::check())
<h1 class="font-bold text-xl text-slate-700 mt-4 bg-slate-200 p-2 px-4 rounded-xl"><a href="/register" class='text-slate-900'>Registra't</a> o <a href="/login" class='text-slate-900'>accedeix</a> per a guardar els teus accessos directes.</h1>
@endif
<div class="w-full flex my-2">
    @foreach($userSavedData as $userData)
    @php

    if($userData->category=='club'){
    $label= $userData->clubName;
    }else if($userData->category=='equip'){
    $label= $userData->teamName;
    }else if($userData->category=='competicio'){
    $label= $userData->groupName;
    }else if($userData->category=='jugador'){
    $label= $userData->playerName;
    }else{
    $label="--";
    }

    @endphp
    <div class='p-2 bg-slate-200  inline  rounded-xl mr-1 text-sm '>
        <a href="/{{$userData->category}}/{{$userData->idItem}}/{{$label}}">
            @if ($userData->category=='jugador')
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 hidden md:inline">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            @endif

            @if ($userData->category=='equip')
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 hidden md:inline">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>

            @endif
            @if ($userData->category=='competicio')
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 hidden md:inline">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
            </svg>@endif

            {{App\Http\Controllers\TeamsController::teamFormat($label)}}</a>
    </div>
    @endforeach
</div>
<div id="search" class="hidden bg-slate-100 rounded-xl my-6 p-2">Resultats de cerca per: <span class='font-bold' id="searchValue"></span>
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
            fetch("http://larajok.test/api/search/teams/" + value)
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
                        document.getElementById('searchReturn').insertAdjacentHTML('beforeend', "<div class='p-1 w-1/4'><div class='bg-slate-200 rounded-xl p-4 cursor-pointer' ><a class='text-sm' href='/equip/" + team.idTeam + "/" + team.teamName + "'>" + (team.teamName + " " + team.categoryName).substr(0,36) + "</a></div></div>")
                    })

                });
            /* fetch("http://larajok.test/api/search/clubs/" + value)
             .then(response => {
                 return response.json()
             })
             .then(data => {
                 console.log(data)
             })*/
            fetch("http://larajok.test/api/search/players/" + value)
                .then(response => {
                    return response.json()
                })
                .then(data => {
                    document.getElementById('searchReturn').insertAdjacentHTML('beforeend', "<div class='block w-full m-2 font-bold'>" + data.length + " jugadors</div>");
                    data.map((player) => {

                        document.getElementById('searchReturn').insertAdjacentHTML('beforeend', "<div class='p-1 w-1/4'><div class='bg-slate-200 rounded-xl p-4 cursor-pointer' ><a  class='text-sm'  href='/jugador/" + player.idPlayer + "/" + player.playerName + "'>" + player.playerName.substr(0,36) + "</a></div></div>")
                    })

                });
        }, 500);
    }

</script>
