@extends('layout.mainlayout')
@section('title', $newsDetail[0]->newsTitle." :: JOK.cat ")
@section('content')

@php
    $news = $newsDetail[0];
    $imageUrl = Str::contains($news->newsImage, 'http') ? $news->newsImage : (isset($_SERVER['SERVER_NAME']) ? 'http://' . $_SERVER['SERVER_NAME'] . '/' . $news->newsImage : $news->newsImage);
@endphp

<!-- BACK TO NEWS BUTTON -->
<div class="mb-5">
    <a href="/noticies" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black bg-stone-100 dark:bg-stone-900 text-stone-800 dark:text-stone-200 hover:bg-[#d4ff00] hover:text-black dark:hover:bg-[#d4ff00] dark:hover:text-black transition-all border border-stone-200/80 dark:border-stone-800 shadow-xs font-display">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Torna a Notícies
    </a>
</div>

<!-- ARTICLE HEADER CARD -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-6 md:p-8 mb-7 shadow-xs font-display">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
        <div class="flex items-center gap-2">
            <span class="hallmark-stamp bg-[#d4ff00] text-black font-black">ACTUALITAT</span>
            <span class="hallmark-stamp bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 border border-stone-200/80 dark:border-stone-800">
                <i class="fa-regular fa-calendar text-[#d4ff00] mr-1"></i>
                {{ \Carbon\Carbon::parse($news->newsDatetime)->format('d/m/Y') }}
            </span>
        </div>
    </div>

    <h1 class="text-2xl md:text-4xl font-black text-stone-900 dark:text-white leading-tight tracking-tight mb-4">
        {{ $news->newsTitle }}
    </h1>

    @if(!empty($news->newsSubtitle))
        <p class="text-sm md:text-lg font-bold text-stone-600 dark:text-stone-300 leading-relaxed border-l-4 border-[#d4ff00] pl-4 py-1 bg-stone-50 dark:bg-stone-900/50 rounded-r-xl">
            {{ $news->newsSubtitle }}
        </p>
    @endif
</div>

<!-- FEATURED IMAGE -->
@if(!empty($news->newsImage))
    <div class="mb-8 rounded-3xl overflow-hidden border border-stone-200 dark:border-stone-800 shadow-xs bg-stone-100 dark:bg-stone-900">
        <img id="newsImage" src="{{ $imageUrl }}" alt="{{ $news->newsTitle }}" class="w-full max-h-[500px] object-cover" />
    </div>
@endif

<!-- ARTICLE CONTENT BODY -->
<div class="bg-white dark:bg-[#121215] border border-stone-200 dark:border-stone-800/90 rounded-3xl p-6 md:p-10 shadow-xs mb-8">
    <div class="prose dark:prose-invert max-w-none text-stone-800 dark:text-stone-200 font-display text-sm md:text-base leading-relaxed space-y-4">
        {!! nl2br($news->newsContent) !!}
    </div>

    @if(!empty($news->externalLink))
        <div class="mt-8 pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center gap-2">
            <i class="fa-solid fa-link text-[#d4ff00]"></i>
            <span class="text-xs font-bold text-stone-500 dark:text-stone-400">Enllaç extern:</span>
            <a href="{{ $news->externalLink }}" target="_blank" class="text-xs md:text-sm font-black text-stone-900 dark:text-stone-100 hover:text-[#d4ff00] underline truncate">
                {{ $news->externalLink }}
            </a>
        </div>
    @endif
</div>

@endsection
