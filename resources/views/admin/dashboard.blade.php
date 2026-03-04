@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    round: [],
                    holes: [],
                    tees: [],
                    activeRound: null,
                    selectedTee: '',
                    selectedSession: 'morning',
                    updatedAt: '',
                }
            },
            mounted() {
                this.getDatas();

                let el = document.getElementById('clock');
                const stopClock = this.createClock(el, 'Asia/Jakarta');

                let date = document.getElementById('date_start');
                flatpickr(date, {
                    dateFormat: "Y-m-d",
                });

                setInterval(() => {
                    this.getDatas();
                }, 5000); // Refresh data every 5 seconds
            },
            methods: {
                getDatas() {
                    axios.get('{{ url()->current() }}', {
                            headers: {
                                'Accept': 'application/json'
                            },
                            params: {
                                tee: this.selectedTee,
                                session: this.selectedSession,
                            }
                        })
                        .then(response => {
                            console.log(response.data);

                            this.round = response.data.data.round;
                            this.holes = response.data.data.holes;
                            this.tees = response.data.data.tees;
                            this.activeRound = response.data.data.round.id;
                            this.updatedAt = response.data.data.updated_at;
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                        });
                },
                changeround() {
                    window.location.href = '{{ url('admin/dashboard') }}/' + this.round.id;
                },
                createClock(element, timeZone) {
                    const formatter = new Intl.DateTimeFormat('id-ID', {
                        timeZone,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    })

                    const hm = document.querySelector('.hm')
                    const sec = document.querySelector('.sec')

                    let timerId

                    function tick() {
                        const now = Date.now()
                        const parts = formatter.formatToParts(now)

                        hm.textContent =
                            `${parts.find(p => p.type === 'hour').value}:` +
                            `${parts.find(p => p.type === 'minute').value}`
                        sec.textContent =
                            `${parts.find(p => p.type === 'second').value}`

                        timerId = setTimeout(tick, 1000 - (now % 1000))
                    }

                    tick()

                    // cleanup
                    return () => clearTimeout(timerId)
                }
            },
        });

        app.mount('#app');
    </script>
@endsection

@section('page-content')
    <div class="w-full max-h-screen h-full p-4 bg-base-200 flex-wrap gap-4 overflow-auto" id="app">
        <div class="p-6 w-full bg-white rounded-2xl shadow-md flex items-stretch justify-between">
            <div class="w-3/4 font-bold flex flex-col justify-between">
                <h2 class="text-4xl font-bold mb-3" v-text="round.name"></h2>
                <div class="text-2xl leading-8">
                    <p><span v-text="round.course"></span>-<span v-text="round.total_hole"></span> Holes</p>
                    <p><span v-text="round.date_start"></span></p>
                    <p><span v-text="round.ball"></span> | <span v-text="round.transportation"></span></p>
                </div>
            </div>
            <div class="w-auto">
                <select class="select mb-2 rounded-lg cursor-pointer" v-model="activeRound" @change="changeround()">
                    <option v-for="roundItem in round.rounds" :value="roundItem.id" :selected="roundItem.id === round.id" v-text="roundItem.name"></option>
                </select>
                <button class="bg-green-600 rounded-lg px-6 py-10 flex items-center text-white font-bold hover:bg-green-700 cursor-pointer" onclick="pause_modal.show()" v-if="round.status === 'referee'">
                    <span class="-mt-1 ms-2">Start Tournament</span>
                </button>
                <button class="bg-green-600 rounded-lg px-6 py-10 flex items-center text-white font-bold hover:bg-green-700 cursor-pointer" onclick="pause_modal.show()" v-if="round.status === 'pause'">
                    <img src="{{ asset('img/icon/resume.svg') }}" alt="">
                    <span class="-mt-1 ms-2">Resume Tournament</span>
                </button>
                <div v-if="round.status === 'active'">
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
                </div>
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
        <div class="w-full flex items-center justify-end my-4">
            <a class="cursor-pointer" :href="'{{ url('admin/dashboard-table') }}/' + round.id">
                <img src="{{ asset('img/icon/table.svg') }}" alt="">
            </a>
        </div>
        <form class="w-full flex items-stretch justify-center gap-4" action="{{ url()->current() }}" method="GET">
            {{-- Pace Section --}}
            <section class="w-2/3 flex items-center gap-4 flex-wrap">
                <div class="grow bg-linear-to-t to-green-700 from-green-500 p-4 text-white rounded-2xl text-center">
                    <h2 class="text-xl mb-2">Current Time</h2>
                    <h1 class="text-6xl font-bold tracking-widest" id="clock"><span class="hm"></span><span class="text-2xl sec">27</span></h1>
                </div>
                <div class="w-1/3 relative p-4 bg-linear-to-t to-green-700 from-green-500 text-white rounded-2xl">
                    <button class="rounded-full bg-white w-8 h-8 flex items-center justify-center absolute top-2 right-2 shadow-md cursor-pointer" type="button" onclick="group_modal.show()">
                        <img class="w-3 h-3" src="{{ asset('img/icon/arrow-top-right.svg') }}" alt="">
                    </button>
                    <p class="text-xl mb-2">Group Total</p>
                    <p class="text-6xl font-bold" v-text="round.group_total"></p>
                </div>
                <div class="w-full bg-neutral-500 rounded-2xl shadow-md p-4 flex items-center justify-center h-full" v-if="round.status == 'pause'">
                    <h1 class="text-white text-6xl text-center">
                        TOURNAMENT
                        <br class="my-1">
                        SUSPENDED
                    </h1>
                </div>

                <div class="w-full bg-white rounded-2xl shadow-md p-4" v-else>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl">Flight Now</h2>
                        <select class="select rounded-lg w-auto select-sm" name="tee" v-model="selectedTee" v-on:change="getDatas()">
                            <option value="" selected>All</option>
                            <option value="1">Tee 1</option>
                            <option value="10">Tee 10</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-gray-100" v-for="item in tees" :key="item.id">
                            <div class="flex items-center justify-between">
                                <p v-text="item.name"></p>
                                <p class="font-bold" v-text="'Hole ' + item.hole_number"></p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p>Allowed Time</p>
                                <p class="font-bold" v-text="item.allowed_time"></p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p>Target Time</p>
                                <p class="font-bold" v-text="item.time"></p>
                            </div>
                            <div class="flex items-center justify-between mt-2" v-if="item.progress == 'ontime'">
                                <p class="mb-0">Ontime</p>
                                <p class="text-green-700 mb-0 font-bold" v-text="item.time_diff"></p>
                            </div>
                            <progress class="progress progress-success w-full" :value="item.time_percentage" max="100" v-if="item.progress == 'ontime'"></progress>
                            <div class="flex items-center justify-between mt-2" v-else-if="item.progress == 'late'">
                                <p class="mb-0">Ontime</p>
                                <p class="text-yellow-700 mb-0 font-bold" v-text="item.time_diff"></p>
                            </div>
                            <progress class="progress progress-warning w-full" :value="item.time_percentage" max="100" v-else-if="item.progress == 'late'"></progress>
                            <div class="flex items-center justify-between mt-2" v-else>
                                <p class="mb-0">Ontime</p>
                                <p class="text-red-700 mb-0 font-bold" v-text="item.time_diff"></p>
                            </div>
                            <progress class="progress progress-error w-full" :value="item.time_percentage" max="100" v-else></progress>
                            {{-- <p>{{ $item['time_percentage'] }}</p> --}}
                        </div>
                    </div>
                </div>

            </section>


            {{-- History Section --}}
            <section class="w-1/3">
                <div class="bg-white rounded-2xl shadow-md p-4 max-h-175 overflow-auto">
                    <div class="flex items-center justify-end">
                        <p class="text-xs italic mb-1 text-gray-600" v-text="'Last Updated ' + updatedAt"></p>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl">Flight History</h2>
                        <select class="select rounded-lg w-auto select-sm cursor-pointer" name="session" v-model="selectedSession" v-on:change="getDatas()">
                            <option value="morning">Morning</option>
                            <option value="afternoon">Afternoon</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="w-full collapse collapse-arrow rounded-xl bg-base-100 border-base-300 border" v-for="(items, key) in holes" :key="key">
                            <input type="checkbox" />
                            <div class="collapse-title py-2 text-lg rounded-none text-white bg-linear-to-t to-green-700 from-green-500" v-text="'Hole ' + key"></div>
                            <div class="collapse-content bg-gray-100 pb-0 text-sm px-0 row-start-3 col-start-1">
                                <button class="w-full flex items-center justify-between py-2 px-4 font-bold" v-for="item in items" :key="item.id">
                                    <p v-text="item.name"></p>
                                    <p class="text-red-600" v-if="item.status == 'unmonitored'">UNMONITORED</p>
                                    <p class="text-green-600" v-else-if="item.progress == 'ontime'" v-text="item.time_diff"></p>
                                    <p class="text-yellow-600" v-else-if="item.progress == 'late'" v-text="item.time_diff"></p>
                                    <p class="text-red-600" v-else v-text="item.time_diff"></p>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>

        <dialog class="modal" id="group_modal">
            <div class="modal-box w-11/12 max-w-4xl max-h-11/12">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-xl font-bold mb-0">Total Groups</h3>
                <p>List off all groups and their players</p>
                <div class="w-full grid grid-cols-2 gap-4 mt-4">
                    <div class="rounded shadow bg-gray-50 border-gray-200 border" v-for="group in round.groups" :key="group.id">
                        <div class="border-b rounded-t shadow border-gray-200 bg-green-700 px-4 py-2 font-bold text-white" v-text="group.name"></div>
                        <div class="ps-8 pe-4">
                            <ul class="py-2 list-disc">
                                <li v-for="player in group.players" :key="player.id" v-text="player . name"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <form class="modal-backdrop" method="dialog">
                <button>close</button>
            </form>
        </dialog>

        <dialog class="modal" id="pause_modal">
            <div class="modal-box w-11/12 max-w-4xl max-h-11/12">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <div class="w-full text-center pt-12 pb-8" v-show="round . status === 'referee'">
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
                        <form :action="'{{ url('admin/start') }}' + '/' + round.id" method="GET">
                            <button class="bg-green-700 text-white rounded-lg shadow-sm py-2 px-4 cursor-pointer" type="submit">Start Tournament</button>
                        </form>
                    </div>
                </div>

                <div class="w-full text-center pt-12 pb-8" v-show="round . status === 'pause'">
                    <div class="flex items-center justify-center mb-8">
                        <div class="w-0 h-0 border-y-48 border-y-transparent border-l-80 border-l-green-700">
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold mb-3">Resume Tournament</h3>
                    <div class="mt-4 flex items-center justify-center flex-wrap">
                        <form class="w-2/3 mx-auto" :action="'{{ url('admin/resume') }}' + '/' + round.id" method="GET">
                            <div class="grid grid-cols-2 gap-x-4 w-full">
                                <p class="col-span-2 text-start mt-4 mb-1 text-sm">Tournament Paused at</p>
                                <input class="input rounded-xl bg-gray-300/50 border-transparent text-black shadow w-full" type="text" :value="round.updated_at_date" placeholder="00" disabled />
                                <input class="input rounded-xl bg-gray-300/50 border-transparent text-black shadow w-full" type="text" :value="round.updated_at_time" placeholder="00" disabled />

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

                <div class="w-full text-center pt-12 pb-8" v-show="round . status === 'active'">
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
                        <form :action="'{{ url('admin/pause') }}' + '/' + round.id" method="GET">
                            <button class="bg-red-700 text-white rounded-lg shadow-sm py-2 px-4 cursor-pointer" type="submit">Pause Tournament</button>
                        </form>
                    </div>
                </div>
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
                        <form :action="'{{ url('admin/stop') }}' + '/' + round.id" method="GET">
                            <button class="bg-red-700 text-white rounded-lg shadow-sm py-2 px-4 cursor-pointer" type="submit">Stop Tournament</button>
                        </form>
                    </div>
                </div>
            </div>
            <form class="modal-backdrop" method="dialog">
                <button>close</button>
            </form>
        </dialog>
    </div>
@endsection
