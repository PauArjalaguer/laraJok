@extends('layout.mainlayout')
@section('title',"Noticies :: JOK.cat ")
@section('content')
<div class="w-full text-neutral-700 text-xl mb-4 font-bold pb-2 border-b border-neutral-400">Notícies</div>

@foreach($newsListTop as $news)
<div onClick='location.href = "/noticies/detall/{{$news->idNew}}/{{$news->newsTitle}}"' class='md:flex border border-solid border-0 border-b-[0px] border-b-neutral-300  md:pb-4 md:mt-4 '>
    <div class=" relative mt-2 hover:shadow-xl shadow-neutral-400 z-10 relative  w-full  md:w-1/2 lg:w-1/4 
    bg-cover bg-center flex justify-center items-center  overflow-hidden  transition-all duration-1000  
    ease-in-out ">
        <div class='bg-left-top rounded-sm bg-neutral-100 w-full h-full  
        p-16 md:p-24 lg:p-24 transition-all duration-1000 ease-in-out transform bg-center bg-cover hover:scale-105 
        cursor-pointer' style="background-image: url({{$news->newsImage}}); display: block;">

        </div>
    </div>
    <div class=' md:w-1/2 lg:w-3/4 md:pl-4 mt-2 lg:mt-0'>
        <div class='text-sm lg:text-xl text-neutral-900 font-bold flex-nowrap md:float-left mb-1 mt-4 md:mt-0 '>
            <a class="active:text-neutral-300" href='/noticies/detall/{{$news->idNew}}/{{$news->newsTitle}}'>
                <h1>{{$news->newsTitle}}</h1>
            </a></div>

        <div class='hidden lg:block float-right text-neutral-600 text-sm text-right'>{{ \Carbon\Carbon::parse($news->newsDatetime)->format('d-m-Y')}} </div>
        <div class='clear-both mt-1 lg:mt-4'></div>
        <h2 class='mb-1 text-neutral-800 text-xs lg:text-base'>{{$news->newsSubtitle}}</h2>
        <div class='text-neutral-600 text-sm lg:text-base pb-2'>

            <p class='block md:hidden '>{!! substr(nl2br($news->newsContent),0,200) !!}
                @if(strlen($news->newsContent)>200)
                <a class="active:text-neutral-300" href='/noticies/detall/{{$news->idNew}}/{{$news->newsTitle}}' class='inline'>...
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </a>
                @endif
            </p>
            <p class='hidden md:block'>{!! substr(nl2br($news->newsContent),0,600) !!}
                @if(strlen($news->newsContent)>600)
                <a class="active:text-neutral-300" href='/noticies/detall/{{$news->idNew}}/{{$news->newsTitle}}' class='inline'>...
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </a>
                @endif
            </p>
        </div>

    </div>


</div>
<div class="h-[1px]  lg:bg-gradient-to-l from-neutral-400 to-transparent"></div>
@endforeach
<div class="my-2 lg:my-12">
    {{ $newsListTop->onEachSide(5)->links() }}
</div>
@endsection
