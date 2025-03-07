<section id="selectSection" class="flex mb-2">
    <div class="w-1/2 md:w-2/3 border-solid border border-neutral-400" id="container">
        <div class="relative w-full md:p-2 text-xs lg:text-base text-gray-700">            
            <div class="relative">
                <input type="text" id="searchLeague" placeholder="Busca una competició" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700" onkeyup="filterLeagues()" onBlur="hideSelectors()">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M15 10a5 5 0 10-10 0 5 5 0 0010 0z" />
                </svg>
            </div>
            <select id="leagueSelect" aria-label="Selecciona una competició" class="hidden w-full mt-1 border border-gray-300 bg-white rounded-lg py-2 px-3 appearance-none text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer" onchange="handleLeagueChange(this.value, this.options[this.selectedIndex].innerHTML)">
            </select>
        </div>
    </div>
    <div class="w-1/2 md:w-1/3 border-solid border-[1px] border-neutral-400">
    <div class="relative w-full md:p-2 text-xs lg:text-base text-gray-700">
   
        <div class="relative">
            <input type="text" id="searchClubs" placeholder="Busca un club" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700" onkeyup="filterClubs()" onBlur="hideSelectors()">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M15 10a5 5 0 10-10 0 5 5 0 0010 0z" />
            </svg>
        </div>
       
        <select id="clubSelect" aria-label="Selecciona un club" class="hidden w-full mt-1 border border-gray-300 bg-white rounded-lg py-2 px-3 appearance-none text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer" onChange="handleClubChange(this.options[this.selectedIndex].value,this.options[this.selectedIndex].innerHTML)">
            @foreach( $clubsList as $club)
            <option value={{$club->value}}>{{$club->label}}</option>
            @endforeach
        </select>
    </div>
    </div>
</section>
<script>
    const handleClubChange = (value, label) => {
        window.location.href = "/club/" + value + "/" + encodeURIComponent(label);
    }
    const handleLeagueChange = (value, label) => {
        window.location.href = "/competicio/" + value + "/" + encodeURIComponent(label);
    }

    const clubs = [
        @foreach($clubsList as $club)
            {id: {{$club->value}}, label: "{{Str::of($club->label)->trim()}}"},
        @endforeach
    ];

    function populateSelectClubs() {
        let select = document.getElementById("clubSelect");
        select.innerHTML = "";
        clubs.forEach(club => {
            let option = document.createElement("option");
            option.value = club.id;
            option.text = club.label;
            select.appendChild(option);
        });
    }

    function filterClubs() {
        let input = document.getElementById("searchClubs").value.toLowerCase();
        let select = document.getElementById("clubSelect");

       let option = document.createElement("option");
            option.text = "123";
            option.value = 0;
            select.appendChild(option);
        select.innerHTML = "";
        let filteredClubs = clubs.filter(club => club.label.toLowerCase().includes(input.trim().toLowerCase()));

        filteredClubs.forEach(club => {
            let option = document.createElement("option");
            option.text = club.label;
            option.value = club.id;
            select.appendChild(option);
        });
        if(filteredClubs.length===1){
            document.getElementById("searchClubs").disabled=true;
            handleClubChange(filteredClubs[0].id,filteredClubs[0].label);
        }
        select.classList.toggle("hidden", filteredClubs.length === 0);
        }
    populateSelectClubs();

    const leagues = [
        @foreach($leaguesList as $league)
            {id:{{$league->value}}, label: "{{Str::of($league->label)->trim()}}"},
        @endforeach
    ];

    function populateSelectLeagues() {
        let select = document.getElementById("leagueSelect");
        select.innerHTML = "";

        leagues.forEach(league => {
            let option = document.createElement("option");
            option.text = league.label;
            option.value = league.value;
            select.appendChild(option);
        });
    }

    function filterLeagues() {
        let input = document.getElementById("searchLeague").value.toLowerCase();
        let select = document.getElementById("leagueSelect");

        select.innerHTML = "";

        let filteredLeagues = leagues.filter(league => league.label.toLowerCase().includes(input.trim().toLowerCase()));

        filteredLeagues.forEach(league => {
            let option = document.createElement("option");
            option.text = league.label;
            option.value = league.id;
            select.appendChild(option);
        });
          if(filteredLeagues.length===1){
            handleLeagueChange(filteredLeagues[0].id,filteredLeagues[0].label);
        }

        select.classList.toggle("hidden", filteredLeagues.length === 0);
    }
    populateSelectLeagues();

    function hideSelectors(event) {

        if (!event.relatedTarget || !container.contains(event.relatedTarget)) {
            document.getElementById("searchLeague").value = "";
            document.getElementById("searchClubs").value = "";
            document.getElementById("clubSelect").classList.add("hidden");
            document.getElementById("leagueSelect").classList.add("hidden");
        }
    }

    searchClubs.addEventListener("blur", hideSelectors);
    searchLeague.addEventListener("blur", hideSelectors);
    clubSelect.addEventListener("blur", hideSelectors);
    leagueSelect.addEventListener("blur", hideSelectors);

</script>
