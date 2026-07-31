@if(isset($merchandisingList) && count($merchandisingList) > 0)
<section id="merchandising" class="w-full bg-white dark:bg-[#09090b] py-6 transition-colors duration-300">
    <div class="w-full px-6">
        <!-- Small elegant title -->
        <div class="flex items-center justify-center gap-2 mb-5">
            <span class="h-[1px] w-6 bg-stone-200 dark:bg-stone-800"></span>
            <span class="text-[9px] font-black tracking-widest text-stone-400 dark:text-stone-500 uppercase font-display flex items-center gap-1.5">
                <i class="fa-solid fa-shirt text-[10px]"></i> BOTIGA OFICIAL JOK.CAT
            </span>
            <span class="h-[1px] w-6 bg-stone-200 dark:bg-stone-800"></span>
        </div>
        
        <!-- Product row with horizontal scroll on mobile, centered on desktop -->
        <div class="flex items-center justify-start md:justify-center gap-4 overflow-x-auto scrollbar-hide py-2 px-1">
            @foreach($merchandisingList as $merch)
                <a href="{{ $merch->assetUrl }}" target="_blank" rel="noreferrer" 
                   style="transition-delay: {{ $loop->index * 120 }}ms;"
                   class="merch-card opacity-0 translate-x-8 transition-all duration-700 ease-out group relative flex-shrink-0 w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 bg-white dark:bg-stone-900 border border-stone-200/80 dark:border-stone-800/60 rounded-2xl p-2.5 shadow-sm hover:border-[#ffcc00] dark:hover:border-[#ffcc00] hover:shadow-md hover:-translate-y-1.5 flex items-center justify-center">
                    <img class="max-w-full max-h-full object-contain rounded-xl transition-transform duration-350 group-hover:scale-105" src="{{ $merch->assetThumbnail }}" alt="{{ $merch->assetName }}">
                    
                    <!-- Premium minimal tooltip shown on hover -->
                    <div class="absolute bottom-1.5 left-1.5 right-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-stone-950/90 dark:bg-stone-900/95 text-white dark:text-stone-200 text-[9px] font-extrabold text-center py-1 rounded-xl truncate px-1.5 pointer-events-none border border-stone-850 shadow-xs">
                        {{ $merch->assetName }}
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<script>
(function() {
    const initObserver = () => {
        const cards = document.querySelectorAll('.merch-card');
        if (!cards.length) return;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('opacity-0', 'translate-x-8');
                    entry.target.classList.add('opacity-100', 'translate-x-0');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.05,
            rootMargin: '0px 0px -40px 0px'
        });
        cards.forEach(card => observer.observe(card));
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initObserver);
    } else {
        initObserver();
    }
})();
</script>
@endif