@extends('layouts.admin')

@section('page-title', 'Dashboard')

{{-- @section('page-script')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    //
                }
            },
            methods: {
                preview() {
                    axios.get('/admin/group/{{ $round->id }}/preview')
                        .then(response => {
                            console.log(response.data);
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            },
            mounted() {
                //
            }
        });

        app.mount('#app');
    </script>
@endsection --}}

@section('page-content')
    <div class="w-full max-h-screen h-full overflow-auto bg-gray-100" id="app">
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
        <div class="w-full px-4 pb-4">
            <div class="p-4 bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 pb-2 mb-2">
                    <p class="text-2xl text-green-700 font-bold">Group</p>
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
                <div class="w-full pt-2 flex items-center justify-end">
                    <form action="{{ route('round.group.delete', $round->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('DELETE')
                        <button class="bg-white rounded-lg shadow px-4 py-1 text-sm me-3 border border-red-300 flex items-center text-red-700 cursor-pointer">
                            <img class="me-2 w-3 h-3" src="{{ asset('img/icon/trash.svg') }}" alt="">
                            <span>Delete Group</span>
                        </button>
                    </form>
                    <button class="bg-white rounded-lg shadow px-4 py-1 text-sm me-3 border border-gray-300 flex items-center cursor-pointer">
                        <img class="me-2 w-4 h-4" src="{{ asset('img/icon/download.svg') }}" alt="">
                        <span>Download Template</span>
                    </button>
                    <label class="bg-green-700 rounded-lg shadow text-white px-4 py-1 text-sm flex items-center cursor-pointer" for="input-file">
                        <img class="me-2 w-3 h-3" src="{{ asset('img/icon/table-icon.svg') }}" alt="">
                        <span>Import From Excel</span>
                    </label>
                    <form action="{{ route('round.group.store', $round->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input class="hidden" id="input-file" name="file" type="file" onchange="this.form.submit()" />
                    </form>
                </div>

                <div class="w-full">
                    @foreach ($round->group_maps as $session => $groups)
                        <h3 class="text-xl my-2 font-bold">{{ ucwords($session) }} Session</h3>
                        @foreach ($groups as $tee => $teeGroups)
                            <div class="overflow-x-auto rounded-box border border-green-700 bg-base-100 mb-4">
                                <table class="table">
                                    <!-- head -->
                                    <thead>
                                        <tr class="bg-green-700 text-white">
                                            <th class="text-center w-1/12">GROUP</th>
                                            <th class="text-center w-1/12">TIME</th>
                                            <th class="text-center w-1/12">TEE</th>
                                            <th>NAME</th>
                                            <th>ORIGIN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($teeGroups as $item)
                                            <tr>
                                                <th class="text-center border border-green-700/50 py-2" rowspan="4">{{ $item->name }}</th>
                                                <td class="text-center border border-green-700/50 py-2" rowspan="4">{{ $item->time }}</td>
                                                <td class="text-center border border-green-700/50 py-2" rowspan="4">{{ $item->tee }}</td>
                                                <td class="border border-green-700/50 py-2">{{ $item->players[0]->name }}</td>
                                                <td class="border border-green-700/50 py-2">{{ $item->players[0]->origin }}</td>
                                            </tr>
                                            @foreach ($item->players as $player)
                                                @if ($loop->first)
                                                    @continue
                                                @endif
                                                <tr>
                                                    <td class="border border-green-700/50 py-2">{{ $player->name }}</td>
                                                    <td class="border border-green-700/50 py-2">{{ $player->origin }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-between">
                            <p class="text-sm">Showing Drawing Sheet - {{ ucwords(request()->input('session', 'morning')) }}</p>
                            <div class="join grid grid-cols-2">
                                <a class="join-item btn btn-sm btn-outline rounded-s-xl px-5 font-bold {{ request()->input('session', 'morning') == 'morning' ? 'btn-active' : '' }}" href="{{ route('round.group', ['round' => $round->id, 'session' => 'morning']) }}">Morning</a>
                                <a class="join-item btn btn-sm btn-outline rounded-e-xl px-5 font-bold {{ request()->input('session', 'morning') == 'afternoon' ? 'btn-active' : '' }}" href="{{ route('round.group', ['round' => $round->id, 'session' => 'afternoon']) }}">Afternoon</a>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="w-full p-4 flex items-center justify-center mt-4">
                {{-- <button class="bg-white rounded-lg shadow px-4 py-2 me-3 border border-gray-300 cursor-pointer">Print</button> --}}
                <a class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" href="{{ route('round.pace', ['round' => $round->id]) }}">Continue</a>
            </div>
        </div>
    </div>
@endsection
