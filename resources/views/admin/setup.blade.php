@extends('layouts.admin')

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new TomSelect(".select-tom", {
                plugins: ['remove_button'],
                onItemAdd: function() {
                    this.setTextboxValue('');
                    this.refreshOptions();
                },
                render: {
                    option: function(data, escape) {
                        return '<div class="d-flex"><span>' + escape(data.value) + '</span></div>';
                    },
                    item: function(data, escape) {
                        return '<div>' + escape(data.value) + '</div>';
                    }
                },
            });

        });
    </script>
@endsection

@section('page-title', 'Dashboard')

@section('page-content')
    <div class="w-full max-h-screen h-full overflow-auto bg-gray-100">
        <div class="w-full px-1">
            <div class="p-6 w-full bg-white shadow-md flex items-stretch justify-between">
                <ul class="timeline w-full px-4">
                    <li class="w-1/6 text-start">
                        <hr />
                        <div class="timeline-start"></div>
                        <div class="timeline-middle col-start-1">
                            <div class="inline-block rounded-full shadow-md -ms-2">
                                <img src="{{ asset('img/icon/setup-step.svg') }}" alt="">
                            </div>
                        </div>
                        <hr />
                    </li>
                    <li class="w-2/6">
                        <hr />
                        <div class="timeline-start"></div>
                        <div class="timeline-middle">
                            <div class="inline-block bg-white p-2 rounded-full shadow-md">
                                <img src="{{ asset('img/icon/group.svg') }}" alt="">
                            </div>
                        </div>
                        <hr />
                    </li>
                    <li class="w-2/6">
                        <hr />
                        <div class="timeline-start"></div>
                        <div class="timeline-middle">
                            <div class="inline-block bg-white p-2 rounded-full shadow-md">
                                <img src="{{ asset('img/icon/pace.svg') }}" alt="">
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
        <form class="w-full px-4 pb-4" action="{{ route('round.setup.store', $round->id) }}" method="POST">
            @csrf
            <div class="p-4 bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 pb-2 mb-2">
                    <p class="text-2xl text-green-700 font-bold">Setup</p>
                </div>
                @if ($errors->any())
                    <div class="grid grid-cols-2 gap-4 py-4 px-6 border border-red-300 bg-red-100 rounded-lg">
                        <ul class="list-disc">
                            @foreach ($errors->all() as $message)
                                <li class="text-red-700 text-xs">{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-x-32 gap-y-2">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Start Interval <span class="text-red-700">*</span></legend>
                        <div class="grid grid-cols-2 gap-x-4">
                            <input class="input rounded-xl shadow w-full" name="start_interval_hour" type="text" value="{{ $round->start_interval_hour }}" placeholder="00" required />
                            <input class="input rounded-xl shadow w-full" name="start_interval_minute" type="text" value="{{ $round->start_interval_minute }}" placeholder="00" required />
                        </div>
                    </fieldset>
                    <fieldset class="fieldset">
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Start Morning Session</legend>
                        <div class="grid grid-cols-2 gap-4">
                            <input class="input rounded-xl shadow w-full" name="morning_hour" type="text" value="{{ $round->morning_hour }}" placeholder="00" required />
                            <input class="input rounded-xl shadow w-full" name="morning_minute" type="text" value="{{ $round->morning_minute }}" placeholder="00" required />
                        </div>
                    </fieldset>
                    
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Start Afternoon Session</legend>
                        <div class="grid grid-cols-2 gap-4">
                            <input class="input rounded-xl shadow w-full" name="afternoon_hour" type="text" value="{{ $round->afternoon_hour }}" placeholder="00" required />
                            <input class="input rounded-xl shadow w-full" name="afternoon_minute" type="text" value="{{ $round->afternoon_minute }}" placeholder="00" required />
                        </div>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Cross Over Hole 9 to 10</legend>
                        <div class="grid grid-cols-2 gap-4">
                            <input class="input rounded-xl shadow w-full" name="crossover_one_hour" type="text" value="{{ $round->crossover_one_hour }}" placeholder="00" required />
                            <input class="input rounded-xl shadow w-full" name="crossover_one_minute" type="text" value="{{ $round->crossover_one_minute }}" placeholder="00" required />
                        </div>
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Cross Over Hole 18 to 1</legend>
                        <div class="grid grid-cols-2 gap-4">
                            <input class="input rounded-xl shadow w-full" name="crossover_ten_hour" type="text" value="{{ $round->crossover_ten_hour }}" placeholder="00" required />
                            <input class="input rounded-xl shadow w-full" name="crossover_ten_minute" type="text" value="{{ $round->crossover_ten_minute }}" placeholder="00" required />
                        </div>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Select Ball Type</legend>
                        <div class="flex items-center gap-4">
                            <label class="label cursor-pointer">
                                <input class="radio" name="ball" type="radio" value="2" {{ $round->ball == 2 ? 'checked' : '' }} required />
                                <span class="label-text text-black ms-2">2 Balls</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input class="radio" name="ball" type="radio" value="3" {{ $round->ball == 3 ? 'checked' : '' }} />
                                <span class="label-text text-black ms-2">3 Balls</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input class="radio" name="ball" type="radio" value="4" {{ $round->ball == 4 ? 'checked' : '' }} />
                                <span class="label-text text-black ms-2">4 Balls</span>
                            </label>
                        </div>
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Transportation</legend>
                        <div class="flex items-center gap-4">
                            <label class="label cursor-pointer">
                                <input class="radio" name="transportation" type="radio" value="walk" {{ $round->transportation == 'walk' ? 'checked' : '' }} required />
                                <span class="label-text text-black ms-2">Full walking</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input class="radio" name="transportation" type="radio" value="cart" {{ $round->transportation == 'cart' ? 'checked' : '' }} />
                                <span class="label-text text-black ms-2">Cart</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input class="radio" name="transportation" type="radio" value="combine" {{ $round->transportation == 'combine' ? 'checked' : '' }} />
                                <span class="label-text text-black ms-2">Combine</span>
                            </label>
                        </div>
                    </fieldset>

                </div>
            </div>

            <div class="w-full p-4 flex items-center justify-center mt-4">
                <button class="bg-white rounded-lg shadow px-4 py-2 me-3 border border-gray-300 cursor-pointer">Cancel</button>
                <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer">Continue</button>
            </div>
    </div>
    </div>
@endsection
