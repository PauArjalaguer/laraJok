<section id="merchandising" class="bg-[#f5f5f5] text-gray-900 font-bold clear-both  border-solid border-y-[0px]  border-orange-400 p-0 mt-12 lg:mt-24">
    @foreach($merchandisingList as $merch)<a href={{$merch->assetUrl}} target="_blank" rel="noreferer" aria-label={{$merch->assetName}}><img  alt={{$merch->assetName}} class="aspect-square inline w-1/6 lg:w-1/12 h-vh cursor-pointer" src={{$merch->assetThumbnail}}></a>@endforeach
    
</section>