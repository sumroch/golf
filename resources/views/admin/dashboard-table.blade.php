@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date_start", {
            dateFormat: "Y-m-d",
        });
    </script>
@endsection

@section('page-content')
    <div class="w-full max-h-screen md:flex items-start justify-center relative p-4 bg-base-200 flex-wrap gap-4 overflow-auto">
        <div class="p-6 w-full bg-white rounded-2xl shadow-md flex items-stretch justify-between no-print">
            <div class="w-3/4 font-bold flex flex-col justify-between">
                <h2 class="text-4xl font-bold mb-3">{{ $round['name'] }}</h2>
                <div class="text-2xl leading-8">
                    <p>{{ $round['course'] }} - {{ $round['total_hole'] }} Holes</p>
                    <p>{{ $round['date_start'] }}</p>
                    <p>{{ $round['ball'] }} | {{ $round['transportation'] }}</p>
                </div>
            </div>
            <div class="w-auto">
                <select class="select mb-2 rounded-lg cursor-pointer" onchange="window.location.href = '{{ url('admin/dashboard-table', '') }}/' + this.value">
                    @foreach ($round['rounds'] as $roundItem)
                        <option value="{{ $roundItem['id'] }}" {{ $roundItem['id'] == $round['id'] ? 'selected' : '' }}>{{ $roundItem['name'] }}</option>
                    @endforeach
                </select>
                @if ($round['status'] === 'referee')
                    <button class="bg-green-600 rounded-lg px-6 py-10 flex items-center text-white font-bold hover:bg-green-700 cursor-pointer" onclick="pause_modal.show()">
                        <span class="-mt-1 ms-2">Start Tournament</span>
                    </button>
                @elseif ($round['status'] === 'pause')
                    <button class="bg-green-600 rounded-lg px-6 py-10 flex items-center text-white font-bold hover:bg-green-700 cursor-pointer" onclick="pause_modal.show()">
                        <img src="{{ asset('img/icon/resume.svg') }}" alt="">
                        <span class="-mt-1 ms-2">Resume Tournament</span>
                    </button>
                @elseif ($round['status'] === 'active')
                    <button class="bg-white rounded-lg px-6 py-3 flex items-center border border-red-600 hover:bg-red-700 cursor-pointer" onclick="pause_modal.show()">
                        <div class="flex items-center justify-center">
                            <div class="h-5 bg-gray-500 w-2 rounded-sm me-1"></div>
                            <div class="h-5 bg-gray-500 w-2 rounded-sm"></div>
                        </div>
                        <span class="ms-3">Pause Tournament</span>
                    </button>
                    <button class="bg-red-600 rounded-lg px-4 py-6 flex items-center justify-center text-white font-bold hover:bg-red-700 cursor-pointer w-full mt-2" onclick="stop_modal.show()">
                        <span>Stop Tournament</span>
                    </button>
                @endif
            </div>
        </div>

        @if ($errors->any())
            <div class="grid grid-cols-2 gap-4 py-4 px-6 border border-red-300 bg-red-100 rounded-lg w-full">
                <ul class="list-disc">
                    @foreach ($errors->all() as $message)
                        <li class="text-red-700 text-xs">{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="w-full flex items-center justify-end no-print">
            <a class="bg-gray-100 rounded-lg me-5 px-8 py-2 text-sm shadow border-2 border-gray-300 cursor-pointer hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 cursor-pointer" href="{{ route('dashboard-table-print', $round['id']) }}" target="_blank">
                Print
            </a>
            <a class="bg-green-700 rounded-lg text-white p-1 text-sm cursor-pointer hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" href="{{ route('dashboard', $round['id']) }}">
                <img class="h-7 w-7" src="{{ asset('img/icon/record.svg') }}" alt="">
            </a>
        </div>

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
                    @foreach ($teeGroups as $keyGroups => $groups)
                        <div class="w-full">
                            <div class="rounded-box border border-base-content/5 bg-base-100 w-full">
                                <table class="table text-center border border-gray-200">
                                    <!-- head -->
                                    <thead class="text-black">
                                        <tr>
                                            <th class="bg-white font-normal border-b-transparent" colspan="2">Time Allowed</th>
                                            <th class="bg-white border-s border-s-gray-300/50 border-b-transparent font-normal">{{ $teeIndex == 1 ? $total_one : $total_ten }}</th>
                                            @foreach ($teeIndex == 1 ? $tee_one : $tee_ten as $hole)
                                                <th class="bg-white border-s border-s-gray-300/50 border-b-transparent font-normal">({{ $hole->allowed_time }})</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <th class="bg-white border-b-transparent">Group</th>
                                            <th class="bg-white border-s border-s-gray-300/50 border-b-transparent">
                                                @if ($keyGroups == 0)
                                                    START TEE {{ $teeIndex }}
                                                @else
                                                    CROSSOVER from TEE {{ $teeIndex }}
                                                @endif
                                            </th>
                                            <th class="bg-white border-s border-s-gray-300/50 border-b-transparent">Start</th>
                                            @foreach ($teeIndex == 1 ? $tee_one : $tee_ten as $hole)
                                                <th class="bg-white border-s border-s-gray-300/50 border-b-transparent">{{ $hole->number }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groups as $key => $group)
                                            <tr>
                                                <td class="bg-white border-b-transparent">{{ $key + 1 }}</td>
                                                <td class="bg-white border-s border-s-gray-300/50 border-b-transparent w-50">
                                                    <div class="flex items-center justify-between relative">
                                                        <span>{{ $group['name'] }}</span>

                                                        <div class="tooltip tooltip-right bg-white after:bg-white">
                                                            <div class="tooltip-content text-black bg-white shadow absolute p-4 rounded-4xl border border-gray-200 w-48 {{ $key == 0 ? 'rounded-tl-none h-fit translate-x-[2%] translate-y-[40%]' : 'rounded-bl-none h-fit -translate-x-[2%] -translate-y-[45%]' }}">
                                                                <div class="">
                                                                    <h2 class="text-lg text-start font-bold underline underline-offset-1 ps-6">{{ $group['name'] }}</h2>
                                                                    <ul class="list-disc ps-6 font-normal text-start ms-2">
                                                                        @foreach ($group['players'] as $player)
                                                                            <li class="mt-2">{{ $player->name }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <button class="cursor-pointer">
                                                                <svg class="stroke-black h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="bg-white border-s border-s-gray-300/50 border-b-transparent">{{ $group['time'] }}</td>
                                                @foreach ($group['paces'] as $pace)
                                                    <td class="{{ $pace->progress_class }} border-s border-s-gray-300/50 border-b-transparent relative">
                                                        {{ $pace->time }}
                                                        @if ($pace->finish_at)
                                                            <div class="font-bold h-2 w-2 rounded-full bg-black absolute top-2 right-2 {{ $pace->finish_class }}"></div>

                                                            <span class="text-xs {{ $pace->finish_text_class }}">
                                                                ({{ $pace->time_diff_integer }})
                                                            </span>
                                                        @endif
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
            @endforeach
        @endforeach
    </div>

    <dialog class="modal" id="pause_modal">
        <div class="modal-box w-11/12 max-w-4xl max-h-11/12">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            @if ($round['status'] === 'referee')
                <div class="w-full text-center pt-12 pb-8">
                    <div class="flex items-center justify-center mb-8">
                        <div class="w-0 h-0 border-y-48 border-y-transparent border-l-80 border-l-green-700">
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold mb-3">Start Tournament</h3>
                    <p class="text-gray-400">Are you sure you want to start the tournament?</p>
                    <div class="mt-4 flex items-center justify-center">
                        <form method="dialog">
                            <button class="bg-white rounded-lg border border-gray-400 shadow-sm py-2 px-8 me-2 cursor-pointer">Cancel</button>
                        </form>
                        <form action="{{ route('round.start', $round['id']) }}" method="GET">
                            <button class="bg-green-700 text-white rounded-lg shadow-sm py-2 px-4 cursor-pointer" type="submit">Start Tournament</button>
                        </form>
                    </div>
                </div>
            @elseif ($round['status'] === 'pause')
                <div class="w-full text-center pt-12 pb-8">
                    <div class="flex items-center justify-center mb-8">
                        <div class="w-0 h-0 border-y-48 border-y-transparent border-l-80 border-l-green-700">
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold mb-3">Resume Tournament</h3>
                    <div class="mt-4 flex items-center justify-center flex-wrap">
                        <form class="w-2/3 mx-auto" action="{{ route('round.resume', $round['id']) }}" method="GET">
                            <div class="grid grid-cols-2 gap-x-4 w-full">
                                <p class="col-span-2 text-start mt-4 mb-1 text-sm">Tournament Paused at</p>
                                <input class="input rounded-xl bg-gray-300/50 border-transparent text-black shadow w-full" type="text" value="{{ $round['updated_at_date'] }}" placeholder="00" disabled />
                                <input class="input rounded-xl bg-gray-300/50 border-transparent text-black shadow w-full" type="text" value="{{ $round['updated_at_time'] }}" placeholder="00" disabled />

                                <div class="mt-4">
                                    <p class="text-start mb-1 text-sm">Start Date</p>
                                    <input class="input rounded-xl shadow w-full placeholder:text-gray-400" id="date_start" name="start_date" type="text" placeholder="00" required />
                                </div>
                                <div class="mt-4">
                                    <p class="text-start mb-1 text-sm">Start Time</p>
                                    <div class="grid grid-cols-2 gap-x-4">
                                        <select class="select rounded-xl w-full" name="start_hour">
                                            <option disabled selected>Pick Hour</option>
                                            @foreach (range(0, 23) as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                        <select class="select rounded-xl w-full" name="start_minute">
                                            <option disabled selected>Pick Minute</option>
                                            @foreach (range(0, 59) as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-center mt-12 w-full">
                                <button class="bg-white rounded-lg border border-gray-400 shadow-sm py-2 px-8 me-4 cursor-pointer" type="button" onclick="document.getElementById('closedialog').submit()">Cancel</button>
                                <button class="bg-green-700 text-white rounded-lg shadow-sm py-2 px-4 cursor-pointer me-4" type="submit">Resume Tournament</button>
                            </div>
                        </form>
                        <form class="mt-4" id="closedialog" method="dialog"></form>
                    </div>
                </div>
            @elseif ($round['status'] === 'active')
                <div class="w-full text-center pt-12 pb-8">
                    <div class="flex items-center justify-center mb-8">
                        <div class="h-25 bg-red-700 w-10 rounded-lg me-8"></div>
                        <div class="h-25 bg-red-700 w-10 rounded-lg"></div>
                    </div>
                    <h3 class="text-3xl font-bold mb-3">Pause Tournament</h3>
                    <p class="text-gray-400">Are you sure you want to pause the tournament?</p>
                    <div class="mt-4 flex items-center justify-center">
                        <form method="dialog">
                            <button class="bg-white rounded-lg border border-gray-400 shadow-sm py-2 px-8 me-2 cursor-pointer">Cancel</button>
                        </form>
                        <form action="{{ route('round.pause', $round['id']) }}" method="GET">
                            <button class="bg-red-700 text-white rounded-lg shadow-sm py-2 px-4 cursor-pointer" type="submit">Pause Tournament</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        <form class="modal-backdrop" method="dialog">
            <button>close</button>
        </form>
    </dialog>

    <dialog class="modal" id="stop_modal">
        <div class="modal-box w-11/12 max-w-4xl max-h-11/12">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <div class="w-full text-center pt-12 pb-8">
                <div class="flex items-center justify-center mb-8">
                    <div class="w-0 h-0 border-y-48 border-y-transparent border-l-80 border-l-red-700">
                    </div>
                </div>
                <h3 class="text-3xl font-bold mb-3">Stop Tournament</h3>
                <p class="text-gray-400">Are you sure you want to Stop the tournament?</p>
                <div class="mt-4 flex items-center justify-center">
                    <form method="dialog">
                        <button class="bg-white rounded-lg border border-gray-400 shadow-sm py-2 px-8 me-2 cursor-pointer">Cancel</button>
                    </form>
                    <form action="{{ route('round.stop', $round['id']) }}" method="GET">
                        <button class="bg-red-700 text-white rounded-lg shadow-sm py-2 px-4 cursor-pointer" type="submit">Stop Tournament</button>
                    </form>
                </div>
            </div>
        </div>
        <form class="modal-backdrop" method="dialog">
            <button>close</button>
        </form>
    </dialog>
@endsection
