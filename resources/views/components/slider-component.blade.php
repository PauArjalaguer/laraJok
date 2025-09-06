<h1 class="font-bold text-xl text-slate-700">Notícies</h1>
<div class="w-full">
    @php
    $counter=0;
    @endphp
    @foreach($newsListTop as $news)
    <div id="slider{{$counter}}" class="rounded-xl relative mt-2 shadow-xl relative  w-full bg-cover bg-center flex justify-center items-center  overflow-hidden  transition-all duration-500  ease-in-out ">
        <div class='bg-slate-100 w-full h-full  p-24 lg:p-56 transition-all duration-1000 ease-in-out transform bg-center bg-cover hover:scale-105 cursor-pointer'
        style="background-image: url({{$news->newsImage}}); display: block;">

        </div>
        <div class="absolute flex flex-col justify-center items-center w-full text-xl font-black transition-all duration-500 ease-in-out transform text-gray-50 top-12 lg:top-48 ">
            <a href='' class="text-center text-xl lg:text-3xl text-white font-bold [text-shadow:_0_2px_2px_rgb(0_0_0_/_100%)] shadow-slate-700 lg:mb-8 ">
                {{substr($news->newsTitle,0,120)}}</a>
            <a class="shadow-md shadow-slate-700 bg-slate-600 px-4 lg:px-12 py-2 bg-gradient-to-t from-slate-400 to-slate-500 hover:from-slate-400 hover:to-slate-600 text-sm lg:text-xl text-white font-semibold drop-shadow-lg rounded-full" href="/noticies/detall/{{$news->idNew}}/El%20j%C3%BAnior%20a%20la%20final%20de%20la%20Copa%20Federaci%C3%B3">Veure notícia</a>

        </div>
    </div>
    @php
    $counter++;
    @endphp
    @endforeach
</div>
<div class='mt-2'>
    <div id='sliderBall_0' class='border border-solid border-slate-400 bg-slate-700 w-[12px] h-[12px] text-center rounded-[50%] float-left mr-1 transition-all duration-1500 ease-in-out transform'>&nbsp;</div>
    <div id='sliderBall_1' class='border border-solid border-slate-400 bg-slate-700 w-[12px] h-[12px] text-center rounded-[50%] float-left mr-1 transition-all duration-1500 ease-in-out transform'>&nbsp;</div>
    <div id='sliderBall_2' class='border border-solid border-slate-400 bg-slate-700 w-[12px] h-[12px] text-center rounded-[50%] float-left mr-1 transition-all duration-1500 ease-in-out transform'>&nbsp;</div>
    <div id='sliderBall_3' class='border border-solid border-slate-400 bg-slate-700 w-[12px] h-[12px] text-center rounded-[50%] float-left transition-all duration-1500 ease-in-out transform'>&nbsp;</div>
    <div class='clear-both'></div>
</div>

<script>
    const sliderChangeStatus = (slider, status) => {
        if (status == 'inactive') {
            document.getElementById("slider" + slider).style.display = "none";
            document.getElementById("sliderBall_" + slider).style.backgroundColor = "rgb(51 65 85)";
        } else {
            document.getElementById("slider" + slider).style.display = "block";
            document.getElementById("sliderBall_" + slider).style.backgroundColor = "rgb(255 255 255)";
        

        }
    }
    let headerCounter = 0;

    sliderChangeStatus(0, 'active');
    sliderChangeStatus(1, 'inactive');
    sliderChangeStatus(2, 'inactive');

    sliderChangeStatus(3, 'inactive');

    const interval = setInterval(() => {
        if (headerCounter === 0) {
            sliderChangeStatus(0, 'active');
            sliderChangeStatus(1, 'inactive');
            sliderChangeStatus(2, 'inactive');
            sliderChangeStatus(3, 'inactive');
        }

        if (headerCounter === 1) {
            sliderChangeStatus(0, 'inactive');
            sliderChangeStatus(1, 'active');
            sliderChangeStatus(2, 'inactive');
            sliderChangeStatus(3, 'inactive');
        }

        if (headerCounter === 2) {
            sliderChangeStatus(0, 'inactive');
            sliderChangeStatus(1, 'inactive');
            sliderChangeStatus(2, 'active');
            sliderChangeStatus(3, 'inactive');
        }
        if (headerCounter === 3) {
            sliderChangeStatus(0, 'inactive');
            sliderChangeStatus(1, 'inactive');
            sliderChangeStatus(2, 'inactive');
            sliderChangeStatus(3, 'active');
        }


        if (headerCounter < 3) {
            headerCounter++;
            console.log("Header: " + headerCounter);
        } else {
            headerCounter = 0;
            //  console.log("Header: " + headerCounter);
        }
    }, 6000);
    interval();

</script>
