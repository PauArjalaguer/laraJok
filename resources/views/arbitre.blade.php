@extends('layout.mainlayout')
@section('title', 'Àrbitre: '.App\Http\Controllers\TeamsController::teamFormat($refereeName).' :: JOK.cat')
@section('content')

<div class="w-full mt-2 mb-6 font-display">
    <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-stone-900 dark:text-white font-display flex items-center gap-2 tracking-tight">
                <i class="fa-solid fa-user-shield text-stone-900 dark:text-white text-xl md:text-2xl"></i>
                {{ App\Http\Controllers\TeamsController::teamFormat($refereeName) }}
            </h1>
        </div>
    </div>
</div>

<div class="w-full max-w-4xl mx-auto font-display">
    @forelse($matchesList as $match)
        <x-matches-component :match="$match" type="result" />
    @empty
        <div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800 rounded-3xl p-8 text-center text-xs md:text-sm font-display text-stone-500 dark:text-stone-400">
            <i class="fa-solid fa-user-shield text-2xl text-stone-400 mb-2 block"></i>
            No s'han trobat partits registrats per a aquest àrbitre.
        </div>
    @endforelse
</div>

@endsection
