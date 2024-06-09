<!-- <div class="block  sm:hidden md:hidden lg:hidden xl:hidden">al</div>
  <div class="hidden sm:block  md:hidden lg:hidden xl:hidden">sm</div>
  <div class="hidden sm:hidden md:block  lg:hidden xl:hidden">md</div>
  <div class="hidden sm:hidden md:hidden lg:block  xl:hidden">lg</div>
  <div class="hidden sm:hidden md:hidden lg:hidden xl:block">xl</div>
</div> -->

<nav class="flex">
    <div class="py-6 w-2/12">
        <h1><a href="/" class="webtitle font-['Comfortaa'] text-bold text-4xl text-slate-700 font-bold">JOK.cat</a></h1>
    </div>
    <div class=" py-6 w-10/12 text-right text-slate-700 bg-slate ">
        <ul>
            <!-- <li class="inline p-2 cursor-pointer font-bold">Competicions</li> -->
            <li class="inline p-2 cursor-pointer font-bold inline ">
                <a href='/dashboard'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                </a>
            </li>
            <li class="inline p-2 cursor-pointer font-bold">Notícies</li>
            <li class="inline p-2 cursor-pointer font-bold">Merchandising</li>
            <li class="inline p-2 cursor-pointer font-bold">Contacte</li>
          
            @if (!Auth::check())            
            <li class="inline p-2 cursor-pointer font-bold bg-slate-700 rounded-xl text-white">
                <a href='/dashboard'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg> Login
                </a>
            </li>
            @else
            <li class="inline p-2 cursor-pointer font-bold border-solid border-2 border-slate-700 rounded-xl text-slate-700">
                <a href='/dashboard'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline my-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg> {{Auth::user()->name}}  </a>|    <a href='/logout'><span class="font-bold text-slate-900 ">Sortir</span></a>
               
            </li>
            @endif

        </ul>
    </div>
</nav>
