<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>
            @yield('page-title', 'My Golf App')
        </title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @yield('page-css')

    </head>

    <body class="font-inter">
        <div class="h-screen overflow-y-hidden flex items-start bg-white-100 mx-auto">
            <div class="w-full h-screen fixed flex justify-center items-center z-5000 bg-gray-900/90" id="loading-screen">
                <div class="loading loading-infinity w-30 text-white"></div>
            </div>
            @include('layouts.sidebar')
            @yield('page-content')
        </div>

        <script>
            window.addEventListener('load', function() {
                let loader = document.querySelector('#loading-screen');

                setTimeout(() => {
                    if (loader) {
                        loader.style.display = 'none';
                    }
                }, 250);
            });
        </script>
        @yield('page-script')
    </body>

</html>
