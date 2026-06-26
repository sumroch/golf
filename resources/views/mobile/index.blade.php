@extends('layouts.mobile')

@section('page-title', 'Referee Timer')

@section('page-script')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script>
        function createClock(element, timeZone) {
            const formatter = new Intl.DateTimeFormat('id-ID', {
                timeZone,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            })
            console.log(formatter.formatToParts(Date.now()));

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
                    activeObserverKey: 0,
                    activeObserverName: '{{ $holes[0]->name ?? '' }}',
                    activePace: {},
                    activePaceKey: 0,
                    nextPaceDisable: false,
                    prevPaceDisable: true,
                    nextObserverDisable: false,
                    prevObserverDisable: true,
                    oldObserverKey: 0,
                    member: [],
                    memberFirst: [],
                    memberSecond: [],
                    collapseActive: false,
                    groupsModal: false,
                    finishTimeNow: '',
                    diffTimeFinish: '',
                }
            },
            mounted() {
                let el = document.getElementById('clock');
                const stopClock = createClock(el, '{{ $timezone }}');

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

                            this.oldObserverKey = this.activeObserverKey;

                            // if (!this.activePace?.id || (this.activePace?.id && this.activeObserverKey != this.oldObserverKey)) {
                            this.activePaceKey = this.findNextValidIndex(this.member, -1, 'next')
                            this.activePace = response.data.data.all[this.activePaceKey];

                            // } else {
                            // this.activePace = response.data.data.all[this.activePaceKey];
                            // }

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

                    // if (this.member[index].status === 'finish' || this.member[index].status === 'unmonitored') {
                    //     this.preloader(false);
                    //     return;
                    // }

                    this.activePace = this.member[index];
                    this.activePaceKey = index;

                    setTimeout(() => {
                        this.preloader(false);
                    }, 300);
                },
                changeActiveObserverArrow(type = 'next') {

                    if (type === 'next') {

                        if (this.activeObserverKey + 1 <= this.observer.length - 1) {
                            this.activeObserverKey = this.activeObserverKey + 1;
                            this.activeObserver = this.observer[this.activeObserverKey]?.id;
                            this.activeObserverName = this.observer[this.activeObserverKey]?.name;
                        }
                    } else if (type === 'prev') {

                        if (this.activeObserverKey - 1 >= 0) {
                            this.activeObserverKey = this.activeObserverKey - 1;
                            this.activeObserver = this.observer[this.activeObserverKey]?.id;
                            this.activeObserverName = this.observer[this.activeObserverKey]?.name;
                        }
                    }

                    this.activePaceKey = 0;
                    this.activePace = this.member[0];

                    this.getData();
                },
                changeActivePaceArrow(type = 'next') {
                    let validIndex = this.findNextValidIndex(this.member, this.activePaceKey, type === 'next' ? 'next' : 'prev');

                    if (validIndex == this.activePaceKey) {
                        return;
                    }
                    this.preloader(true);

                    this.activePaceKey = validIndex;
                    this.activePace = this.member[this.activePaceKey];

                    setTimeout(() => {
                        this.preloader(false);
                    }, 300);
                },
                findNextValidIndex(items, startIndex, direction = 'next') {
                    let i = startIndex;

                    while (true) {
                        i += direction === 'next' ? 1 : -1;

                        if (i < 0 || i >= items.length) return startIndex;

                        if (items[i].status !== 'unmonitored' && items[i].status !== 'finish') return i;
                    }
                },
                incrementTime(time) {
                    let currentTime = moment(this.finishTimeNow, 'HH:mm');
                    let newTime = currentTime.add(time, 'minutes').format('HH:mm');
                    this.finishTimeNow = newTime;
                },
                handleGroupsModal() {
                    this.groupsModal = true;

                    Vue.nextTick(() => {
                        el = document.getElementById(`group_${this.activePace?.group_id}`);
                        container = document.getElementById('groupsList');

                        container.scrollTo({
                            top: el.offsetTop - 65,
                            behavior: 'smooth'
                        })
                    });

                },
                editFinish() {
                    this.finishTimeNow = this.activePace?.time || moment().format('HH:mm');
                    edit_modal.showModal();
                },
                submitFinish() {
                    $diff = moment(moment().format('HH:mm:ss'), 'HH:mm:ss').diff(moment(this.activePace?.time, 'HH:mm:ss'), 'seconds');

                    this.diffTimeFinish = ($diff > 0 ? '+' : '') + Math.ceil($diff / 60);
                    submit_modal.showModal();
                },
                finishTimer() {
                    this.preloader(true);
                    submit_modal.close();

                    axios.post(`/referee/${this.activePace.id}/finish`, {
                            _method: 'POST',
                            time: moment(this.activePace.time, 'HH:mm').format('HH:mm:00'),
                        })
                        .then(response => {
                            this.getData();
                            this.changeActivePaceArrow('next');
                        })
                        .catch(error => {
                            this.preloader(false);
                        });

                },
                editTimer() {
                    this.preloader(true);
                    edit_modal.close();

                    axios.post(`/referee/${this.activePace.id}/edited`, {
                            _method: 'POST',
                            time: moment(this.finishTimeNow, 'HH:mm').format('HH:mm:00'),
                        })
                        .then(response => {
                            this.getData();
                            this.changeActivePaceArrow('next');
                        })
                        .catch(error => {
                            this.preloader(false);
                        });

                },
                unmonitoredTimer() {
                    this.preloader(true);
                    axios.post(`/referee/${this.activePace.id}/unmonitored`, {
                            _method: 'POST',
                        })
                        .then(response => {
                            this.getData();
                            this.changeActivePaceArrow('next');
                        })
                        .catch(error => {
                            this.preloader(false);
                        });
                },
                preloader: function(param = false) {
                    if (document.querySelector('#loading-screen')) {
                        document.querySelector('#loading-screen').style.display = param ? 'flex' : 'none';
                    }
                },
                collapse() {
                    this.collapseActive = !this.collapseActive;
                }
            }
        });
        app.mount('#app');
    </script>
@endsection

@section('page-content')
    <div class="bg-gray-100 h-screen">
        <div class="w-full max-w-[500px] flex items-start justify-center flex-wrap mx-auto" id="app">

            <div class="pb-4 bg-white h-screen shadow" id="groupsModal" v-show="groupsModal">
                <div class="w-full fixed h-screen left-0 top-0 bg-black opacity-50 z-2" v-on:click="groupsModal = false"></div>
                <div class="w-full absolute bottom-0 top-0 flex flex-col z-10 bg-white shadow-lg pt-2 pb-4 max-w-[400px] mx-auto">
                    <div class="border-b border-gray-200 flex items-center justify-end pb-2 px-4">
                        <p class="text-lg font-bold text-gray-700 rounded-full h-8 w-8 m-0 border border-gray-200 p-1 cursor-pointer flex items-center justify-center mb-0 pb-2.5" v-on:click="groupsModal = !groupsModal">x</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 overflow-y-auto h-full mt-4 px-4" id="groupsList">
                        @foreach ($groups as $group)
                            <div class="shadow border border-gray-200 rounded-lg" id="group_{{ $group->id }}" :class="{ 'bg-green-700 text-white': activePace?.group_id == '{{ $group->id }}' }">
                                <div class="font-bold text-lg px-3 pt-2">
                                    {{ $group->name }}
                                </div>
                                <ul class="pl-8 pr-3 pb-2.5 list-disc mt-1">
                                    @foreach ($group->players as $player)
                                        <li class="text-sm">{{ $player->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="w-full bg-green-700 px-6 py-2 flex items-center justify-between">
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
                <div>
                    <h2 class="text-xl text-center text-white m-0">{{ auth()->user()->name }} <span class="text-base">{{ $observer_target }}</span></h2>
                    <hr class="border mt-1 border-white">
                    <h2 class="text-xl text-center text-white m-0">{{ $course_name }}</h2>
                </div>
                <img class="w-7 h-auto cursor-pointer" src="{{ asset('img/icon/conference.svg') }}" alt="conference" v-on:click="handleGroupsModal">
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

            <section class="w-full flex items-center justify-between p-1 mt-2">
                <div class="w-1/2 pe-1">
                    <div class="w-full rounded-2xl shadow-lg py-3 text-center bg-white">
                        <p class="flex items-center justify-between mb-1 px-1 text-green-700">
                            <svg class="h-10 w-10 fill-current md:h-8 md:w-8 rtl:rotate-180 cursor-pointer" :class="{ 'text-gray-300': activeObserverKey == 0 }" v-on:click="changeActiveObserverArrow('prev')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path>
                            </svg>
                            <span class="text-balance">
                                <span class="text-base me-2 font-bold" v-text="activeObserverName"></span>
                                <span class="text-sm" v-text="activePace?.par"></span>
                            </span>
                            <svg class="h-10 w-10 fill-current md:h-8 md:w-8 rtl:rotate-180 cursor-pointer" :class="{ 'text-gray-300': activeObserverKey == observer.length - 1 }" v-on:click="changeActiveObserverArrow('next')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path>
                            </svg>
                        </p>
                        <p class="text-lg px-4">Time Allowed</p>
                        <h2 class="text-4xl mb-1 px-4" v-text="activePace?.allowed_time"></h2>
                    </div>
                </div>

                <div class="w-1/2 ps-1">
                    <div class="w-full rounded-2xl shadow-lg py-3 text-center bg-white">
                        <p class="flex items-center justify-between mb-1 px-1 text-green-700">
                            <svg class="h-10 w-10 fill-current md:h-8 md:w-8 rtl:rotate-180 cursor-pointer" :class="{ 'text-gray-300': activePaceKey == 0 }" v-on:click="changeActivePaceArrow('prev')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path>
                            </svg>
                            <span class="text-balance">
                                <span class="text-base me-2 font-bold" v-text="activePace?.name"></span>
                            </span>
                            <svg class="h-10 w-10 fill-current md:h-8 md:w-8 rtl:rotate-180 cursor-pointer" :class="{ 'text-gray-300': activePaceKey == member.length - 1 }" v-on:click="changeActivePaceArrow('next')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path>
                            </svg>
                        </p>
                        <p class="text-lg px-4">Target</p>
                        <h2 class="text-4xl mb-1 px-4" v-text="activePace?.time"></h2>
                    </div>
                </div>
            </section>

            <section class="w-full flex items-center justify-center mb-14 mt-6">
                <h1 class="max-[350px]:text-[4.5rem] max-[400px]:text-[5rem] max-[500px]:text-[5.5rem] max-[640px]:text-[6rem] sm:text-8xl md:text-8xl font-bold" id="clock">
                    <span class="hm me-1"></span>
                    <span class="text-5xl sec">27</span>
                </h1>
            </section>

            <section class="w-full flex items-end justify-around mb-12" v-if="activePace">
                <div class="text-center" v-if="activePace?.status !== 'finish' && activePace?.status !== 'unmonitored'">
                    <button class="w-25 rounded-full border border-green-700 p-8 bg-white shadow-lg active:scale-95 hover:bg-green-100 transition cursor-pointer" v-on:click="submitFinish">
                        <img class="w-full h-auto" src="{{ asset('img/flag.png') }}" alt="Play Button">
                    </button>
                    <p class="mt-2">FINISH</p>
                </div>

                <div class="text-center" v-else>
                    <button class="w-25 rounded-full border border-green-700 p-8 bg-white shadow-lg active:scale-95 hover:bg-green-100 transition cursor-pointer" v-on:click="editFinish">
                        <img class="w-full h-auto" src="{{ asset('img/icon/edit.svg') }}" alt="Play Button">
                    </button>
                    <p class="mt-2">EDIT</p>
                </div>

                {{-- <div class="text-center">
                    <div class="w-fit inline-block">
                        <button class="w-25 h-25 flex items-center justify-center rounded-full p-6 bg-red-700 shadow-lg active:scale-95 hover:bg-green-100 transition cursor-pointer" v-on:click="unmonitoredTimer()" v-if="activePace?.status !== 'finish' && activePace?.status !== 'unmonitored'">
                            <img class="w-2/3 h-auto" src="{{ asset('img/unlink.png') }}" alt="Play Button">
                        </button>
                        <button class="w-25 h-25 flex items-center justify-center rounded-full p-6 bg-gray-700 shadow-lg active:scale-95 transition cursor-pointer" v-else>
                            <img class="w-2/3 h-auto" src="{{ asset('img/unlink.png') }}" alt="Play Button">
                        </button>
                    </div>
                    <p class="mt-2 text-red-700">UNMONITORED</p>
                </div> --}}
            </section>

            <section class="w-full max-w-[500px] flex items-center justify-center flex-wrap bottom-0 absolute">
                <div class="w-full rounded-none bg-base-100 border-base-300 border">
                    <div class="py-2 text-2xl rounded-none text-center font-bold text-white bg-green-700 relative cursor-pointer" v-on:click="collapse">
                        <span v-text="activeObserverName"></span>
                        <svg class="h-10 w-10 fill-current md:h-8 md:w-8 cursor-pointer text-white absolute -top-1 right-2 translate-y-1/4 rotate-90" :class="{ 'rotate-90': collapseActive, 'rotate-270': collapseActive }" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path>
                        </svg>
                    </div>
                    <div class="pb-0 text-sm px-0 row-start-2 col-start-1 max-h-90 overflow-auto h-0 transition-all duration-300 ease-in-out transform" :class="{ 'h-90': collapseActive }">
                        <button class="w-full flex items-center justify-between py-3 px-4 border-bottom border-b border-gray-400 cursor-pointer" type="button" :class="{ 'bg-gray-300': item.id == activePace?.id }" v-for="item in memberFirst" :key="item.id" v-on:click="changeActivePace(item.id)">
                            <p class="w-1/3 text-start" v-text="item.name"></p>
                            <p class="w-1/3 text-center" v-text="item.time"></p>
                            <p class="w-1/3 text-center" :class="item.finish_text_class" v-text="item?.finish_time"></p>
                            <p class="w-1/3 text-end" :class="item.finish_text_class" v-if="item.status !=='unmonitored'" v-text="item.time_diff"></p>
                            <p class="w-1/3 text-end text-xs text-red-700" v-else>UNMONITORED</p>
                        </button>
                        <button class="w-full flex items-center justify-between py-3 px-4 border-bottom border-b border-gray-400 cursor-pointer" type="button" :class="{ 'bg-gray-300': item.id == activePace?.id }" v-for="item in memberSecond" :key="item.id" v-on:click="changeActivePace(item.id)">
                            <p class="w-1/3 text-start" v-text="item.name"></p>
                            <p class="w-1/3 text-center" v-text="item.time"></p>
                            <p class="w-1/3 text-center" :class="item.finish_text_class" v-text="item?.finish_time"></p>
                            <p class="w-1/3 text-end" :class="item.finish_text_class" v-if="item.status !=='unmonitored'" v-text="item.time_diff"></p>
                            <p class="w-1/3 text-end text-xs text-red-700" v-else>UNMONITORED</p>
                        </button>
                    </div>
                    <div class="collapse-content pb-0 text-sm px-0 row-start-3 col-start-1">
                    </div>
                </div>
            </section>

            <dialog class="modal" id="submit_modal">
                <div class="modal-box rounded-3xl">
                    <img class="w-max-full h-auto mx-auto my-4" src="{{ asset('img/time.png') }}" alt="">
                    <h3 class="text-xl font-bold text-center">Finish Hole</h3>
                    <p class="py-4 text-center">Are you sure <br> You want to finish this hole ?<br> </p>

                    <div class="flex items-center justify-between w-60 mx-auto mb-6">
                        <p v-text="activePace?.name"></p>
                        <p v-text="activeObserverName"></p>
                        <p :class="{'text-green-700': diffTimeFinish < 0, 'text-orange-700': diffTimeFinish > 0 && diffTimeFinish <= 3, 'text-red-700': diffTimeFinish > 3}" v-text="diffTimeFinish"></p>
                    </div>

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

            <dialog class="modal" id="edit_modal">
                <div class="modal-box rounded-3xl">
                    <img class="w-max-full h-auto mx-auto my-4" src="{{ asset('img/time.png') }}" alt="">
                    <h3 class="text-xl font-bold text-center">Edit Hole</h3>

                    <div class="w-60 mx-auto mt-4">
                        <p class="text-gray-400">Finish Time</p>
                        <p class="border border-gray-300 shadow py-2 px-4 mt-1 rounded-lg bg-white text-gray-400" v-text='activePace?.finish_time'></p>

                        <p class="text-gray-400 mt-4">Finish Now</p>
                        <p class="border border-green-700 shadow py-1.5 px-4 mt-1 rounded-lg bg-white text-green-700 font-bold text-xl text-center" v-text='finishTimeNow'></p>

                        <div class="grid grid-cols-4 gap-2 mt-4 text-xs">
                            <button class="w-full border border-green-700 text-gray-700 py-2 px-1 text-center rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="incrementTime(-2)">
                                -2<br>min
                            </button>
                            <button class="w-full border border-green-700 text-gray-700 py-2 px-1 text-center rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="incrementTime(-1)">
                                -1<br>min
                            </button>
                            <button class="w-full border border-green-700 text-gray-700 py-2 px-1 text-center rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="incrementTime(1)">
                                +1<br>min
                            </button>
                            <button class="w-full border border-green-700 text-gray-700 py-2 px-1 text-center rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="incrementTime(2)">
                                +2<br>min
                            </button>

                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="w-60 bg-green-700 text-white py-2 px-4 rounded-xl hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="editTimer()">
                            Confirm
                        </button>
                        <form method="dialog">
                            <button class="w-60 mt-2 border border-green-700 text-green-700 py-2 px-4 rounded-xl hover:bg-green-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer">
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
