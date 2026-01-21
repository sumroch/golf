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
        <div class="h-screen overflow-y-hidden bg-white-100 container mx-auto">
            @yield('page-content')
        </div>

        @yield('page-script')
    </body>

</html>
