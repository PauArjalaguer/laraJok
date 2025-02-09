<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css')
    <title>@yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <script src="https://kit.fontawesome.com/0b5c081e1f.js" crossorigin="anonymous"></script>
</head>

<body class="antialiased">
    <div class="relative">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed left-0 top-0 w-64 h-full bg-gray-800 text-white z-50 -translate-x-full transition-transform duration-500">
            <div class="text-gray-100 text-xl">
                <div class="p-2.5 mt-1 flex items-center rounded-md ">
                    <h1 class="text-[15px]  ml-3 text-xl text-white font-bold  font-['Comfortaa']">Jok.cat</h1>
                    <i class="fa-solid fa-circle-xmark ml-24 cursor-pointer lg:hidden" onClick="toggleMenu()"></i>
                </div>
                <hr class="my-2 text-gray-600">

                <div>
                    <div class="p-2.5 mt-3 flex items-center rounded-md 
        px-4 duration-300 cursor-pointer ">
                        <i class="fa-brands fa-searchengin"></i>
                        <input class="text-[15px] ml-4 w-full bg-transparent focus:outline-none" placeholder="Buscar" onKeyUp="search(this.value)" />
                    </div>

                    <div class="p-2.5 mt-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-blue-600">
                        <i class="fa-solid fa-house-laptop"></i>
                        <span class="text-[15px] ml-4 text-white"><a href="/">Home</a></span>
                    </div>
                    <div class="p-2.5 mt-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-blue-600">
                        <i class="fa-regular fa-newspaper"></i>
                        <span class="text-[15px] ml-4 text-white"><a href="/noticies">Notícies</a></span>
                    </div>
                    <hr class="my-4 text-gray-600">
                    <div class="p-2.5 mt-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-blue-600">
                        <i class="fa-regular fa-calendar-days text-white"></i>
                        <span class="text-[15px] ml-4 text-white"><a href="/agenda">Agenda</a></span>
                    </div>
                    <div class="p-2.5 mt-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-blue-600">
                        <i class="fa-sharp-duotone fa-solid fa-shirt"></i>
                        <span class="text-[15px] ml-4 text-white"><a href="/merchandising">Merchandising</a></span>
                    </div>

                    <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-blue-600">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="text-[15px] ml-4 text-white"><a href='/logout'>Logout</a></span>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div id="container" class="min-h-screen">
        <div class="w-full lg:w-3/4 p-2 lg:p-0 mx-auto my-0 ">
            @include('layout.nav')
            @include('layout.select-section')
            @yield('content')
        </div>
        <footer class="w-full justify-center  sticky top-[100vh] bg-neutral-900">
            @include('layout.merchandising')
            <div class="flex w-full justify-center py-8 ">
                <div class="w-3/4 flex">
                    <div class="w-1/3 text-white text-left"><span class="jok">JOK.cat</span><br>http://www.jok.cat<br>jok@jok.cat</div>
                    <div class="w-1/3 text-white">&nbsp;</div>
                    <div class="w-1/3  text-white">&nbsp;</div>
                </div>
            </div>
        </footer>
    </div>

</body>

</html>
