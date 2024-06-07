<section id="selectSection" class="flex mb-2">
    <div class="w-2/3 border-solid border-[1px] border-slate-400">
        <div class="w-full p-2 text-xs md:text-base text-gray-700">
            <select class='w-full border-0' onChange="handleLeagueChange(this.options[this.selectedIndex].value,this.options[this.selectedIndex].innerHTML)">
                <option>Selecciona una competició</option>
                @foreach( $leaguesList as $league)
                <option value={{$league->value}}>{{$league->label}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="w-1/3 border-solid border-[1px] border-slate-400">
        <div class="w-full p-2 text-xs md:text-base text-gray-700">
            <select class='w-full border-0' onChange="handleClubChange(this.options[this.selectedIndex].value,this.options[this.selectedIndex].innerHTML)">
                <option>Selecciona un club</option>
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
</script>