@extends('layout.mainlayout')
@section('title', (count($partits_pavello) > 0 ? $partits_pavello[0]->placeName : 'Pavelló') . " :: JOK.cat ")
@section('content')

<!-- BACK BUTTON -->
<div class="mb-5">
    <a href="/pavellons" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black bg-stone-100 dark:bg-stone-900 text-stone-800 dark:text-stone-200 hover:bg-[#d4ff00] hover:text-black dark:hover:bg-[#d4ff00] dark:hover:text-black transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs font-display">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Torna a Pavellons
    </a>
</div>

<!-- PAVELLÓ DETAIL HERO HEADER -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-5 md:p-6 mb-7 shadow-xs font-display">
    <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white tracking-tight">
        {{ count($partits_pavello) > 0 ? $partits_pavello[0]->placeName : 'Pavelló' }}
    </h1>
    @if(count($partits_pavello) > 0 && !empty($partits_pavello[0]->placeAddress))
        <p class="text-xs md:text-sm font-bold text-stone-500 dark:text-stone-400 mt-1">
            <i class="fa-solid fa-location-dot text-[#d4ff00] mr-1"></i>
            {{ $partits_pavello[0]->placeAddress }}
        </p>
    @endif
</div>

<!-- MATCHES BY DATE LIST -->
@if(count($partits_pavello) > 0)
    @php $dia = ''; @endphp
    <div class="space-y-4">
        @foreach($partits_pavello as $key => $match)
            @if($dia != $match->matchDate)
                <div class="flex items-center gap-2 pt-3 border-b border-stone-200 dark:border-stone-800 pb-2 mb-3">
                    <i class="fa-regular fa-calendar text-[#d4ff00]"></i>
                    <h3 class="font-display font-black text-xs md:text-sm uppercase tracking-wider text-stone-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($match->matchDate)->locale('ca')->isoFormat('LL') }}
                    </h3>
                </div>
            @endif
            <x-matches-component :match="$match" />
            @php $dia = $match->matchDate; @endphp
        @endforeach
    </div>
@else
    <div class="font-display text-xs md:text-sm text-stone-500 dark:text-stone-400 text-center py-12 bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl">
        <i class="fa-solid fa-map-location-dot text-3xl text-stone-400 mb-2 block"></i>
        No hi ha partits programats en aquest pavelló pròximament.
    </div>
@endif

@endsection
