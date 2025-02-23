<section id="selectSection" class="flex mb-2">
    <div class="w-1/2 md:w-2/3 border-solid border border-neutral-400" id="container">
        <div class="relative w-full md:p-2 text-xs lg:text-base text-gray-700">
            <!-- Search Input with Icon -->
            <div class="relative">
                <input type="text" id="searchLeague" placeholder="Busca una competició" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700" onkeyup="filterLeagues()" onBlur="hideSelectors()">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M15 10a5 5 0 10-10 0 5 5 0 0010 0z" />
                </svg>
            </div>

            <!-- Select Dropdown (Styled to Look Like a Single Component) -->
            <select id="leagueSelect" aria-label="Selecciona una competició" class="hidden w-full mt-1 border border-gray-300 bg-white rounded-lg py-2 px-3 appearance-none text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer" onchange="handleLeagueChange(this.value, this.options[this.selectedIndex].innerHTML)">
                <option value=""></option>
                @foreach($leaguesList as $league)
                <option value="{{$league->value}}">
                    @php
                    if(strpos($league->label, 'Grup únic')) {
                    echo $league->leagueName . " " . $league->label;
                    } else {
                    echo $league->label;
                    }
                    @endphp
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="w-1/2 md:w-1/3 border-solid border-[1px] border-neutral-400">
        {{-- <div class="w-full md:p-2 text-xs lg:text-base text-gray-700">
            <select aria-label="Selecciona un club" class="w-full border-0" onChange="handleClubChange(this.options[this.selectedIndex].value,this.options[this.selectedIndex].innerHTML)">
                <option>Busca un club</option>
                @foreach( $clubsList as $club)
                <option value={{$club->value}}>{{$club->label}}</option>
        @endforeach
        </select>
    </div> --}}
    <div class="relative w-full md:p-2 text-xs lg:text-base text-gray-700">
        <!-- Search Input with Icon -->
        <div class="relative">
            <input type="text" id="searchClubs" placeholder="Busca un club" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700" onkeyup="filterClubs()" onBlur="hideSelectors()">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M15 10a5 5 0 10-10 0 5 5 0 0010 0z" />
            </svg>
        </div>
        <!-- Select Dropdown (Styled to Look Like a Single Component) -->
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

    function filterLeagues() {
        let input = document.getElementById("searchLeague").value.toLowerCase();
        let select = document.getElementById("leagueSelect");
        if (input.length > 0) {
            select.classList.remove("hidden");
        } else {
            select.classList.add("hidden");
        }
        let options = select.getElementsByTagName("option");

        for (let i = 0; i < options.length; i++) {
            let text = options[i].innerText.toLowerCase();
            if (text.includes(input)) {
                options[i].style.display = "";
                options[i].disabled = false;
            } else {
                options[i].style.display = "none";
                options[i].disabled = true;
            }
        }
    }

    function filterClubs() {
        let input = document.getElementById("searchClubs").value.toLowerCase();

        let select = document.getElementById("clubSelect");
        if (input.length > 0) {
            select.classList.remove("hidden");
        } else {
             select.classList.add("hidden");
        }
        let options = select.getElementsByTagName("option");

        for (let i = 0; i < options.length; i++) {
            let text = options[i].innerText.toLowerCase();
            if (text.includes(input)) {
                options[i].style.display = "";
            } else {
                options[i].style.display = "none";
            }
        }
    }

    function hideSelectors(event) {
       
        if (!event.relatedTarget || !container.contains(event.relatedTarget)) {
        document.getElementById("searchLeague").value="";
        document.getElementById("searchClubs").value="";
        document.getElementById("clubSelect").classList.add("hidden");
        document.getElementById("leagueSelect").classList.add("hidden");
        }
    }

    searchClubs.addEventListener("blur", hideSelectors);
        searchLeague.addEventListener("blur", hideSelectors);
        clubSelect.addEventListener("blur", hideSelectors);
        leagueSelect.addEventListener("blur", hideSelectors);

</script>
