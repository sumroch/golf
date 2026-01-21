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

        <div id="print-area">
            @foreach ($paces as $session => $tees)
                @foreach ($tees as $teeIndex => $teeGroups)
                    <div class="w-full flex items-stretch justify-center gap-4 flex-wrap mb-4">
                        <div class="w-full flex items-center justify-between">
                            <p class="text-xl font-bold">PACE OF PLAY - {{ strtoupper($session) }}</p>
                        </div>
                        @foreach ($teeGroups as $groups)
                            <div class="w-full">
                                <div class="rounded-box border border-base-content/5 bg-base-100 w-full">
                                    <table class="table text-center border border-gray-200">
                                        <!-- head -->
                                        <thead class="text-black">
                                            <tr>
                                                <th class="bg-white font-normal group-column">Time Allowed</th>
                                                <th class="bg-white font-normal">00:02:08</th>
                                                @foreach ($teeIndex == 1 ? $tee_one : $tee_ten as $hole)
                                                    <th class="bg-white font-normal">({{ $hole->allowed_time }})</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th class="bg-white group-column">START TEE {{ $teeIndex }}</th>
                                                <th class="bg-white">Start</th>
                                                @foreach ($teeIndex == 1 ? $tee_one : $tee_ten as $hole)
                                                    <th class="bg-white">{{ $hole->number }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groups as $key => $group)
                                                <tr>
                                                    <td class="bg-white group-column">{{ $group['name'] }}</td>
                                                    <td class="bg-white">{{ $group['time'] }}</td>
                                                    @foreach ($group['paces'] as $pace)
                                                        <td class="bg-white">{{ $pace->time }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="page-break"></div>
                @endforeach
            @endforeach
        </div>

        <script>
            window.onload = function() {
                window.print();

                setTimeout(() => {
                    window.close();
                }, 500);
            };
        </script>
    </body>

</html>
