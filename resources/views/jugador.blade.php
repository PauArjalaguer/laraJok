@extends('layout.mainlayout')
@section('title', $playerInfo[0]->playerName . " :: JOK.cat")

@section('content')

<script>
    const leagueShow = (league) => {
        document.querySelectorAll(".leagueContainer").forEach(el => el.style.display = 'none');
        document.querySelectorAll(".leagueButton").forEach(el => el.classList.remove("bg-neutral-900"));
        document.getElementById(league).style.display = "block";
        document.getElementById(league + "_button").classList.add("bg-neutral-900");
    }
</script>

<!-- Header jugador -->
<div class="w-full mb-4 border-b border-neutral-300 pb-3 flex items-center justify-between">
    <div class="text-neutral-800 text-2xl font-bold flex items-center gap-3">

        <a href="/desa/jugador/{{$playerInfo[0]->idPlayer}}">
            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="{{ $checkIfSaved==1 ? 'currentColor' : 'none' }}"
                 viewBox="0 0 24 24"
                 stroke-width="1.5"
                 stroke="currentColor"
                 class="w-7 h-7 cursor-pointer transition {{ $checkIfSaved==1 ? 'text-red-700' : 'text-neutral-500 hover:text-neutral-700' }}">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
        </a>

        {{$playerInfo[0]->playerName}}
    </div>
</div>

<div class="md:flex gap-4">

    <!-- ESQUERRA: Partits jugador -->
    <div class="w-full md:w-2/3">

        <!-- Botons de temporada -->
        <div class="flex flex-wrap gap-2 mb-4">
            @php $currentSeason=1; $counter=0; @endphp

            @foreach($playerMatchesList as $match)
                @if ($currentSeason != $match->seasonName)
                    <div id="{{$match->seasonName}}_button"
                         class="leagueButton bg-neutral-700 hover:bg-neutral-900 text-white text-sm md:text-base px-4 py-1 rounded-full cursor-pointer shadow-sm transition"
                         onclick="leagueShow('{{$match->seasonName}}')">
                        {{$match->seasonName}}
                    </div>
                @endif

                @php
                    $currentSeason = $match->seasonName;
                    $counter++;
                @endphp
            @endforeach
        </div>

        <!-- Contenidor de partits -->
        <div id="season0">
            @php $currentSeason = 1; $counter = 0; @endphp

            @foreach($playerMatchesList as $match)

                @if ($currentSeason != $match->seasonName)
        </div>

        <div id="{{$match->seasonName}}"
             class="leagueContainer @if($counter!=0) hidden @endif">
            @endif

            <!-- COMPONENT DE PARTIT (ja redissenyat) -->
            <x-matches-component :match="$match" />

            @php
                $currentSeason = $match->seasonName;
                $counter++;
            @endphp

            @endforeach
        </div>
    </div>

    <!-- DRETA: Estadístiques -->
    <div class="w-full md:w-1/3">

        <h1 class="text-neutral-700 font-bold text-xl mb-3">Estadístiques</h1>

        @foreach($playerStats as $stats)
            <div class="mb-4">

                <!-- Capçalera temporada -->
                <div class="bg-neutral-700 text-white text-center py-1 px-4 rounded-t-xl border border-neutral-700 mt-5">
                    <span class="font-semibold text-sm">{{ $stats->seasonName }}</span>
                </div>

                <!-- Cos estadístiques -->
                <div class="bg-white border border-neutral-200 rounded-b-xl p-2 shadow hover:shadow-md transition-all">

                    <div class="py-1">
                        <span class="font-semibold text-neutral-700">Partits jugats:</span>
                        <span class="text-neutral-600">{{ $stats->match_count }}</span>
                    </div>

                    <div class="py-1">
                        <span class="font-semibold text-neutral-700">Gols:</span>
                        <span class="text-neutral-600">{{ $stats->total_goals }}</span>
                    </div>

                    <div class="py-1">
                        <span class="font-semibold text-neutral-700">Targetes blaves:</span>
                        <span class="text-neutral-600">{{ $stats->total_blue }}</span>
                    </div>

                    <div class="py-1">
                        <span class="font-semibold text-neutral-700">Targetes vermelles:</span>
                        <span class="text-neutral-600">{{ $stats->total_red }}</span>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>

@endsection
