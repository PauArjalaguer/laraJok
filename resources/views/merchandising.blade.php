@extends('layout.mainlayout')
@section('title',"Merchandising :: JOK.cat ")
@section('content')

<div class="w-full text-slate-700 text-xl mb-4 font-bold pb-2 border-b border-slate-400">

    Merchandising
</div>
<div class='flex flex-wrap'>
    @foreach($merchandisingReturnCategories as $category)
    <div class="p-2 bg-slate-200  inline  rounded-xl mr-1 text-[12px] md:text-sm flex  flex-col items-end cursor-pointer" onClick="showCategory('{{$category->assetCategory}}')">{{$category->assetCategory}}</div>

    @endforeach

    @php
    $currentType="a";
    $counter=0;
    @endphp
    @foreach ($merchandisingListAll as $merch)
    @if ($currentType!=$merch->assetCategory)
    <div class="rounded-t-xl my-2 text-slate-700 font-bold block w-full {{$merch->assetCategory}}">{{$merch->assetCategory}}</div>
    @endif
    <div class="w-full lg:w-1/3 rounded-md p-2 transition-all duration-1000 {{$merch->assetCategory}}" onClick="window.open('{{$merch->assetUrl}}')">
        <div class="relative mt-2 hover:shadow-xl shadow-slate-400 z-10 relative  w-full bg-cover bg-center flex justify-center items-center  overflow-hidden  transition-all duration-1000 rounded-t-xl ease-in-out">
            <div class="bg-left-top rounded-sm bg-slate-100 w-full h-full p-60 transition-all duration-1000 ease-in-out transform bg-center bg-cover hover:scale-110 cursor-pointer" style="background-image: url({{$merch->assetThumbnail}}); display: block;">
            </div>

        </div>
        <div class="flex items-center bg-gray-100 border border-t-slate-300">
            <div class="text-slate-500 p-5 text-left  w-1/2">{{$merch->assetPrice ? $merch->assetPrice." €" : "Sense stock"}}</div>
            <div class="w-1/2  text-right flex justify-end p-4">
                <div class=" cursor-pointer text-right ">
                    @if($merch->assetPrice)
                    <a href="{{$merch->assetUrl}}" target="_blank" rel="noreferer" aria-label={{$merch->assetName}} class="bg-slate-600 p-2 rounded-xl font-bold text-white ">Comprar</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
    $currentType=$merch->assetCategory;
    $counter++;
    @endphp
    @endforeach
    <div class="text-lg text-slate-700 text-center w-full flex justify-center mt-12">
        <a href="https://www.latostadora.com/shop/jokcat/?ord=reciente#shop" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </a>
    </div>
    <div class="clear-both"></div>

</div>
<script>
    const categories = [];
    @foreach($merchandisingReturnCategories as $category)
    categories.push('{{$category->assetCategory}}')

    @endforeach
    const showCategory = (category) => {

        let c = document.getElementsByClassName(category);
        for (let i = 0; i < c.length; i++) {

            c[i].style.display = "block";
            setTimeout(() => {
                c[i].style.transform = "scale(1)";
                c[i].style.opacity = "100";
            }, 100);
        }
        const hideCategories = categories.filter((element) => element != category);




        hideCategories.map((cat) => {
            let c = document.getElementsByClassName(cat);
            console.log(c);
            for (let i = 0; i < c.length; i++) {
                c[i].style.transform = "scale(0)";
                c[i].style.opacity = "0";
                setTimeout(() => {
                    c[i].style.display = "none";
                }, 300);
            }
        });


    }

</script>
@endsection
