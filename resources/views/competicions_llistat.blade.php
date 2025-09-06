@extends('layout.mainlayout')
@section('title',"Competicions :: JOK.cat ")
@section('content')
    <div class="w-full text-neutral-700 text-xl mb-4 font-bold pb-2 border-b border-neutral-400">Competicions</div>
    @php
        $lastSeason = null;
        $lastCategory = null;
    @endphp

    @foreach($leaguesList as $league)
        {{-- Quan canvïa la season --}}
        @if($lastSeason !== $league->seasonName)
            @if($lastSeason !== null)
                </div> {{-- tanquem grid de la category anterior --}}
   {{-- tanquem grid de la season anterior --}}
    @endif

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ $league->seasonName }}</h2>
    @php
        $lastSeason = $league->seasonName;
        $lastCategory = null; // reiniciem categories
    @endphp
    @endif

    {{-- Quan canvïa la category --}}
    @if($lastCategory !== $league->categoryName)
        @if($lastCategory !== null)
            </div> {{-- tanquem grid anterior --}}
    @endif

    <h3 class="text-lg font-semibold mt-4 mb-2"> {{ $league->categoryName }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @php $lastCategory = $league->categoryName; @endphp
        @endif

        {{-- Contingut --}}
        <div class="bg-neutral-200 p-5 rounded-xl place-content-center">
            <a href="{{ url('competicio/' . $league->value . '/' . Str::slug($league->label)) }}">
                {{ $league->label }}
            </a>
        </div>

        @endforeach

        {{-- Tancar últimes grids --}}
    </div>


@endsection
