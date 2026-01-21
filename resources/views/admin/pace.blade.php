@extends('layouts.admin')

@section('page-title', 'Pace of Play')

@section('page-content')
    <div class="w-full max-h-screen h-full overflow-auto bg-gray-100">
        <div class="w-full px-1">
            <div class="p-6 w-full bg-white shadow-md flex items-stretch justify-between">
                <ul class="timeline w-full px-4">
                    <li class="w-1/6 text-start">
                        <hr class="bg-green-700" />
                        <div class="timeline-start"></div>
                        <div class="timeline-middle col-start-1">
                            <div class="inline-block rounded-full shadow-md -ms-2">
                                <img src="{{ asset('img/icon/setup-step.svg') }}" alt="">
                            </div>
                        </div>
                        <hr class="bg-green-700" />
                    </li>
                    <li class="w-2/6">
                        <hr class="bg-green-700" />
                        <div class="timeline-start"></div>
                        <div class="timeline-middle">
                            <div class="inline-block rounded-full shadow-md">
                                <img src="{{ asset('img/icon/group-step.svg') }}" alt="">
                            </div>
                        </div>
                        <hr class="bg-green-700" />
                    </li>
                    <li class="w-2/6">
                        <hr class="bg-green-700" />
                        <div class="timeline-start"></div>
                        <div class="timeline-middle">
                            <div class="inline-block rounded-full shadow-md">
                                <img src="{{ asset('img/icon/pace-step.svg') }}" alt="">
                            </div>
                        </div>
                        <hr />
                    </li>
                    <li class="w-1/6">
                        <hr />
                        <div class="timeline-start"></div>
                        <div class="timeline-middle col-start-3 grid justify-end relative">
                            <div class="inline-block bg-white p-2 rounded-full shadow-md z-1">
                                <img src="{{ asset('img/icon/referee.svg') }}" alt="">
                            </div>
                        </div>
                        <hr class="col-start-2" />
                    </li>
                </ul>
            </div>
        </div>
        @foreach ($paces as $session => $tees)
            @foreach ($tees as $teeIndex => $teeGroups)
                <div class="w-full flex items-stretch justify-center gap-4 flex-wrap mb-4 px-4 mt-8">
                    <div class="w-full flex items-center justify-between">
                        <p class="text-xl font-bold">PACE OF PLAY - {{ strtoupper($session) }}</p>
                        <div class="flex items-center">
                            <button class="bg-green-700 rounded-lg text-white me-5 px-4 py-2 text-sm cursor-pointer">
                                Edit Table
                            </button>
                            <a class="bg-white rounded-lg shadow px-4 py-2 border border-gray-300 text-sm cursor-pointer" href="{{ route('round.pace-print', $round->id) }}" target="_blank">Print</a>
                        </div>
                    </div>
                    @foreach ($teeGroups as $groups)
                        <div class="w-full">
                            <div class="rounded-box border border-base-content/5 bg-base-100 w-full">
                                <table class="table text-center border border-gray-200">
                                    <!-- head -->
                                    <thead class="text-black">
                                        <tr>
                                            <th class="bg-white font-normal" colspan="2">Time Allowed</th>
                                            <th class="bg-white font-normal">00:02:08</th>
                                            @foreach ($teeIndex == 1 ? $tee_one : $tee_ten as $hole)
                                                <th class="bg-white font-normal">({{ $hole->allowed_time }})</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <th class="bg-white">Group</th>
                                            <th class="bg-white">START TEE 1</th>
                                            <th class="bg-white">Start</th>
                                            @foreach ($teeIndex == 1 ? $tee_one : $tee_ten as $hole)
                                                <th class="bg-white">{{ $hole->number }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groups as $key => $group)
                                            <tr>
                                                <th class="bg-white font-normal">{{ $key + 1 }}</th>
                                                <th class="bg-white w-50 font-normal">
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
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2 " d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </th>
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
            @endforeach
        @endforeach

        <div class="w-full p-4 flex items-center justify-center my-4">
            <a class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" href="{{ route('round.referee', ['round' => $round->id]) }}">Continue</a>
        </div>
    </div>
@endsection
