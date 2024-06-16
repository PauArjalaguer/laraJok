@extends('layout.mainlayout')
@section('title',$newsDetail[0]->newsTitle." :: JOK.cat ")
@section('content')
<div class="text-slate-700 text-xl mb-4 font-bold pb-2 border-b border-slate-400 ">
    <div class='float-left'>{{$newsDetail[0]->newsTitle}}</div>
    <div class="text-sm lg:text-xl lg:float-right text-slate-500">
        {{ \Carbon\Carbon::parse($newsDetail[0]->newsDatetime)->format('d-m-Y')}}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline pb-1 cursor-pointer hover:scale-110">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
        </svg> </div>
    <div class='clear-both'></div>
</div>
<div>
    <img id='newsImage' src='http://{{$_SERVER['SERVER_NAME']}}/{{$newsDetail[0]->newsImage}}' class='w-full rounded-xl shadow-xl transition-all ease-in-out duration-1000 ' />
</div>
<h2 class='mt-6 mb-1 text-slate-800 font-bold'>{{$newsDetail[0]->newsSubtitle}}</h2>
<div class=' justify-center mt-2'>
    <p class=' mt-4 text-slate-800 antialiased'>{!! nl2br($newsDetail[0]->newsContent) !!}</p>
</div>
@endsection
