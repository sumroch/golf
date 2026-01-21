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
                            <div class="flex items-center">
                                <label class="label text-sm me-4">
                                    <div class="h-6 w-6 rounded bg-gray-300 me-1 shadow"></div>
                                    <span>Completed</span>
                                </label>
                                <label class="label text-sm me-4">
                                    <div class="h-6 w-6 rounded bg-red-300 me-1 shadow"></div>
                                    <span>Going</span>
                                </label>
                                <label class="label text-sm">
                                    <div class="h-6 w-6 rounded bg-white me-1 shadow"></div>
                                    <span>Unstarted</span>
                                </label>
                            </div>
                        </div>
                        @foreach ($teeGroups as $groups)
                            <div class="w-full">
                                <div class="rounded-box border border-base-content/5 bg-base-100 w-full">
                                    <table class="table text-center border border-gray-200">
                                        <!-- head -->
                                        <thead class="text-black">
                                            <tr>
                                                <th class="bg-white font-normal group-column border-s border-s-gray-300/50 border-b-transparent">Time Allowed</th>
                                                <th class="bg-white font-normal border-s border-s-gray-300/50 border-b-transparent">00:02:08</th>
                                                @foreach ($teeIndex == 1 ? $tee_one : $tee_ten as $hole)
                                                    <th class="bg-white font-normal border-s border-s-gray-300/50 border-b-transparent">({{ $hole->allowed_time }})</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th class="bg-white group-column border-s border-s-gray-300/50 border-b-transparent">START TEE {{ $teeIndex }}</th>
                                                <th class="bg-white border-s border-s-gray-300/50 border-b-transparent">Start</th>
                                                @foreach ($teeIndex == 1 ? $tee_one : $tee_ten as $hole)
                                                    <th class="bg-white border-s border-s-gray-300/50 border-b-transparent">{{ $hole->number }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groups as $key => $group)
                                                <tr>
                                                    <td class="bg-white group-column border-s border-s-gray-300/50 border-b-transparent">{{ $group['name'] }}</td>
                                                    <td class="bg-white border-s border-s-gray-300/50 border-b-transparent">{{ $group['time'] }}</td>
                                                    @foreach ($group['paces'] as $pace)
                                                        <td class="{{ $pace->progress_class }} border-s border-s-gray-300/50 border-b-transparent relative">
                                                            @if ($pace->finish_at)
                                                                <div class="font-bold h-2 w-2 rounded-full bg-black absolute top-2 right-2 {{ $pace->finish_class }}"></div>
                                                            @endif
                                                            {{ $pace->time }}
                                                        </td>
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
