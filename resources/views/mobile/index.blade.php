@extends('layouts.mobile')

@section('page-title', 'Referee Timer')

@section('page-script')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        function createClock(element, timeZone) {
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

        const app = Vue.createApp({
            data() {
                return {
                    observer: {{ Js::from($holes) }},
                    activeObserver: {{ $holes[0]->id ?? 0 }},
                    activeObserverName: '{{ $holes[0]->name ?? '' }}',
                    activePace: {},
                    member: [],
                    memberFirst: [],
                    memberSecond: [],
                }
            },
            mounted() {
                let el = document.getElementById('clock');
                const stopClock = createClock(el, 'Asia/Jakarta');

                this.getData();
            },
            beforeUnmount() {
                stopClock();
            },
            methods: {
                getData() {
                    this.preloader(true);
                    axios.get(`/referee/${this.activeObserver}`)
                        .then(response => {
                            this.member = response.data.data.all;
                            this.memberFirst = response.data.data.first;
                            this.memberSecond = response.data.data.second;

                            if (!this.activePace?.id) {
                                this.activePace = response.data.data.all[0];
                            }
                            this.preloader(false);
                        })
                        .catch(error => {
                            console.error('There was an error!', error);
                            this.preloader(false);
                        });
                },
                changeActiveObserver(index) {
                    this.activeObserver = index;
                    let selectedObserver = this.observer.find((item) => item.id == index);
                    this.activeObserverName = selectedObserver?.name;

                    this.getData();
                },
                changeActivePace(id) {
                    this.preloader(true);

                    let index = this.member.findIndex((item) => item.id == id);
                    this.activePace = this.member[index];

                    setTimeout(() => {
                        this.preloader(false);
                    }, 500);
                },
                finishTimer() {
                    this.preloader(true);
                    my_modal_2.close();

                    axios.post(`/referee/${this.activePace.id}/finish`, {
                            _method: 'POST',
                        })
                        .then(response => {
                            this.getData();
                        })
                        .catch(error => {
                            this.preloader(false);

                            console.error('There was an error!', error);
                        });
                },
                unmonitoredTimer() {
                    this.preloader(true);
                    axios.post(`/referee/${this.activePace.id}/unmonitored`, {
                            _method: 'POST',
                        })
                        .then(response => {
                            this.getData();
                        })
                        .catch(error => {
                            this.preloader(false);
                            console.error('There was an error!', error);
                        });
                },
                preloader: function(param = false) {
                    if (document.querySelector('#loading-screen')) {
                        document.querySelector('#loading-screen').style.display = param ? 'flex' : 'none';
                    }
                }
            }
        });
        app.mount('#app');
    </script>

@endsection

@section('page-content')
    <div class="bg-gray-100 h-screen">
        <div class="w-full md:w-1/3 flex items-start justify-center flex-wrap mx-auto" id="app">

            <div class="w-full bg-green-700 p-6 flex items-center justify-between">
                <div class="dropdown dropdown-right">
                    <img class="w-5 h-auto cursor-pointer" src="{{ asset('img/icon/bar.svg') }}" alt="bar menu" role="button" tabindex="0">
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-md ms-2" tabindex="-1">
                            <li>
                                <button class="w-full text-start" type="submit">Logout</button>
                            </li>
                        </ul>
                    </form>
                </div>
                <h2 class="text-xl text-center text-white m-0">{{ auth()->user()->name }}</h2>
                <img class="w-7 h-auto cursor-pointer" src="{{ asset('img/icon/notification.svg') }}" alt="notification">
            </div>

            @if ($status_pause)
                <div class="toast toast-top toast-end mt-24 w-full md:w-1/3 mx-auto left-4">
                    <div class="alert alert-white bg-white border border-red-600">
                        <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mb-1">
                            <span class="w-full text-xl text-red-600">Tournament Paused</span>
                            <br>
                            <span>The tournament was paused due to force majeure</span>
                        </p>
                    </div>
                </div>
            @endif

            <section class="w-full flex items-center justify-between p-3 mt-2">
                <div class="w-1/2 pe-2">
                    <div class="w-full rounded-2xl shadow-lg p-4 text-center bg-white">
                        <p class="text-xl">Time Allowed</p>
                        <h2 class="text-4xl mt-2 mb-1" v-text="activePace.allowed_time"></h2>
                    </div>
                </div>

                <div class="w-1/2 ps-2">
                    <div class="w-full rounded-2xl shadow-lg p-4 text-center bg-white">
                        <p class="text-xl">Target</p>
                        <h2 class="text-4xl mt-2 mb-1" v-text="activePace.time"></h2>
                    </div>
                </div>
            </section>

            <section class="w-full flex items-center justify-between p-2 mt-3">
                <div class="w-full flex items-start justify-between">
                    <div class="w-full px-1 text-center cursor-pointer text-xl text-gray-500" v-for="item in observer" :key="item.id">
                        <a :class="{ 'text-2xl font-bold text-green-700': activeObserver === item.id }" v-on:click="changeActiveObserver(item.id)" v-text="item.name"></a>
                    </div>
                </div>
            </section>

            <section class="w-full flex items-center justify-center mb-14 mt-6">
                <h1 class="text-8xl font-bold" id="clock"><span class="hm"></span><span class="text-6xl sec">27</span></h1>
            </section>

            <section class="w-full flex items-end justify-around mb-12">
                <div class="text-center">
                    <button class="w-27 rounded-full border border-green-700 p-8 bg-white shadow-lg active:scale-95 hover:bg-green-100 transition cursor-pointer" onclick="my_modal_2.showModal()">
                        <img class="w-full h-auto" src="{{ asset('img/flag.png') }}" alt="Play Button">
                    </button>
                    <p class="mt-2">FINISH</p>
                </div>

                <div class="text-center">
                    <button class="w-20 rounded-full p-6 bg-red-700 shadow-lg active:scale-95 hover:bg-green-100 transition cursor-pointer" v-on:click="unmonitoredTimer()">
                        <img class="w-full h-auto" src="{{ asset('img/unlink.png') }}" alt="Play Button">
                    </button>
                    <p class="mt-2 text-red-700">UNMONITORED</p>
                </div>
            </section>

            <section class="w-full md:w-1/3 flex items-center justify-center flex-wrap bottom-0 absolute">
                <div class="w-full collapse collapse-arrow rounded-none bg-base-100 border-base-300 border">
                    <input type="checkbox" />
                    <div class="collapse-title py-2 text-2xl rounded-none text-center font-bold text-white bg-green-700" v-text="activeObserverName"></div>
                    <div class="collapse-content pb-0 text-sm px-0 row-start-2 col-start-1 max-h-45 overflow-auto">
                        <button class="w-full flex items-center justify-between py-3 px-4 border-bottom border-b border-gray-400 cursor-pointer" type="button" :class="{ 'bg-gray-300': item.id == activePace.id }" v-for="item in memberFirst" :key="item.id" v-on:click="changeActivePace(item.id)">
                            <p class="w-1/3 text-start" v-text="item.name"></p>
                            <p class="w-1/3" v-text="item.time"></p>
                            <p class="w-1/3 text-end" :class="{ 'text-green-700': item.progress == 'ontime', 'text-red-700': item.progress == 'late' }" v-if="item.status !=='unmonitored'" v-text="item.time_diff"></p>
                            <p class="w-1/3 text-end text-red-700" v-else>UNMONITORED</p>
                        </button>
                        <button class="w-full flex items-center justify-between py-3 px-4 border-bottom border-b border-gray-400 cursor-pointer" type="button" :class="{ 'bg-gray-300': item.id == activePace.id }" v-for="item in memberSecond" :key="item.id" v-on:click="changeActivePace(item.id)">
                            <p class="w-1/3 text-start" v-text="item.name"></p>
                            <p class="w-1/3" v-text="item.time"></p>
                            <p class="w-1/3 text-end" :class="{ 'text-green-700': item.progress == 'ontime', 'text-red-700': item.progress == 'late' }" v-if="item.status !=='unmonitored'" v-text="item.time_diff"></p>
                            <p class="w-1/3 text-end text-red-700" v-else>UNMONITORED</p>
                        </button>
                    </div>
                    <div class="collapse-content pb-0 text-sm px-0 row-start-3 col-start-1">
                    </div>
                </div>
            </section>

            <dialog class="modal" id="my_modal_2">
                <div class="modal-box rounded-3xl">
                    <img class="w-max-full h-auto mx-auto my-4" src="{{ asset('img/time.png') }}" alt="">
                    <h3 class="text-xl font-bold text-center">Finish Hole</h3>
                    <p class="py-4 text-center">Are you sure <br> You want to finish this hole ?<br> </p>

                    <div class="text-center mt-4">
                        <button class="w-50 bg-green-700 text-white py-2 px-4 rounded-xl hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="finishTimer()">
                            Confirm
                        </button>
                        <form method="dialog">
                            <button class="w-50 mt-2 border border-green-700 text-green-700 py-2 px-4 rounded-xl hover:bg-green-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer">
                                Cancel
                            </button>
                        </form>
                    </div>

                </div>
                <form class="modal-backdrop" method="dialog">
                    <button>close</button>
                </form>
            </dialog>

        </div>
    </div>
@endsection
