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
                    activeObserverNumber: '{{ $holes[0]->number ?? '' }}',
                    activeObserverType: '{{ $holes[0]->observer_type ?? '' }}',
                    activePace: {},
                    activePaceKey: 0,
                    nextPaceDisable: false,
                    prevPaceDisable: true,
                    nextObserverDisable: false,
                    prevObserverDisable: true,
                    oldObserverKey: 0,
                    member: [],
                    memberHeadTail: [],
                    collapseActive: false,
                    groupsModal: false,
                    finishTimeNow: '',
                    diffTimeFinish: '',
                    timeDiffEdit: 0,
                    selectedObserver: null,
                    selectedPace: null,
                }
            },
            mounted() {
                let el = document.getElementById('clock');
                const stopClock = createClock(el, '{{ $timezone }}');

                this.getData();

                setTimeout(() => {
                    this.polling();
                }, 5000);
            },
            beforeUnmount() {
                stopClock();
            },
            methods: {
                getData() {
                    this.preloader(true);
                    axios.get(`/referee/${this.activeObserver}`)
                        .then(response => {
                            this.member = response.data.data.member;
                            this.memberHeadTail = response.data.data.groups;

                            this.oldObserverKey = this.activeObserverKey;

                            this.activePaceKey = this.findNextValidIndex(this.member, -1, 'next')
                            this.activePace = response.data.data.member[this.activePaceKey];

                            this.preloader(false);
                        })
                        .catch(error => {
                            console.error('There was an error!', error);
                            this.preloader(false);
                        });
                },
                checkUpdate() {
                    axios.get(`/referee/${this.activeObserver}?check_update=1`)
                        .then(response => {
                            this.member = response.data.data.member;
                            this.activePace = response.data.data.member[this.activePaceKey];
                        })
                        .catch(error => {
                            console.error('There was an error!', error);
                        });
                },
                async polling() {
                    try {
                        await this.checkUpdate(); // Tunggu sampai selesai
                    } catch (err) {
                        console.error(err);
                    } finally {
                        setTimeout(() => this.polling(), 5000); // Baru jadwalkan request berikutnya
                    }
                },
                changeActiveObserver(index) {
                    this.activeObserver = index;
                    let selectedObserver = this.observer.find((item) => item.id == index);
                    this.activeObserverName = selectedObserver?.name;
                    this.activeObserverNumber = selectedObserver?.number;
                    this.activeObserverType = selectedObserver?.observer_type;

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
                            this.activeObserverNumber = this.observer[this.activeObserverKey]?.number;
                            this.activeObserverType = this.observer[this.activeObserverKey]?.observer_type;
                        }
                    } else if (type === 'prev') {

                        if (this.activeObserverKey - 1 >= 0) {
                            this.activeObserverKey = this.activeObserverKey - 1;
                            this.activeObserver = this.observer[this.activeObserverKey]?.id;
                            this.activeObserverName = this.observer[this.activeObserverKey]?.name;
                            this.activeObserverNumber = this.observer[this.activeObserverKey]?.number;
                            this.activeObserverType = this.observer[this.activeObserverKey]?.observer_type;
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
                    // let currentTime = moment(this.finishTimeNow, 'HH:mm');
                    // let newTime = currentTime.add(time, 'minutes').format('HH:mm');
                    // this.finishTimeNow = newTime;

                    this.timeDiffEdit += time;
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
                handleJumpObserverModal() {
                    jump_observer_modal.showModal();
                },
                handleJumpPaceModal() {
                    jump_pace_modal.showModal();
                },
                handleJumpObserverAction() {
                    console.log(this.selectedObserver);
                    this.changeActiveObserver(this.selectedObserver);

                    jump_observer_modal.close();
                },
                handleJumpPaceAction() {
                    console.log(this.selectedPace);
                    this.changeActivePace(this.selectedPace);

                    jump_pace_modal.close();
                },
                editFinish() {
                    this.finishTimeNow = this.activePace?.finish_time === '-' ? moment().format('HH:mm') : this.activePace?.finish_time;
                    this.timeDiffEdit = 0;
                    edit_modal.showModal();
                },
                submitFinish() {
                    let diff = moment(moment().format('HH:mm:ss'), 'HH:mm:ss').diff(moment(this.activePace?.time, 'HH:mm:ss'), 'seconds');

                    this.diffTimeFinish = (diff > 0 ? '+' : '') + Math.ceil(diff / 60);
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
                            time: moment(this.finishTimeNow, 'HH:mm').add(this.timeDiffEdit, 'minutes').format('HH:mm:00'),
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

           <div class="w-full px-3 py-2 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-start text-green-700 m-0 capitalize">{{ auth()->user()->name }} <span class="text-base">{{ $observer_target }}</span></h2>
                    <hr class="border mt-1 border-green-700">
                    <h2 class="text-lg text-start text-green-700 m-0">{{ $course_name }}</h2>
                </div>
                <div class="flex items-center justify-center">
                    <img class="w-8 me-6 h-auto cursor-pointer" src="{{ asset('img/icon/conference-green.svg') }}" alt="conference" v-on:click="handleGroupsModal">
                    <div class="dropdown dropdown-left">
                        <img class="w-5 h-auto cursor-pointer" src="{{ asset('img/icon/bar-green.svg') }}" alt="bar menu" role="button" tabindex="0">
                        <form action="{{ route('logout') }}" method="POST">@csrf
                            <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-md ms-2" tabindex="-1">
                                <li>
                                    <button class="w-full text-start" type="submit">Logout</button>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
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

            {{-- Header Section --}}

            <section class="w-full flex flex-wrap items-center justify-between p-1 mt-2 px-3">
                <div class="w-full rounded-xl shadow-lg py-2 px-3 pb-3 text-center bg-green-700">
                    <div class="flex items-start justify-between text-green-700">
                        <div class="text-start">
                            <p class="text-2xl text-white font-bold" v-text="activeObserverName"></p>
                            <p class="text-lg text-white" v-text="activePace?.par"></p>
                        </div>
                        <div class="text-lg text-end">
                            <p class="text-2xl font-bold text-white">Time Allowed</p>
                            <h2 class="text-4xl font-bold text-white" v-text="activePace?.allowed_time"></h2>
                        </div>
                    </div>
                </div>

                <div class="w-full rounded-xl shadow-lg py-2 px-3 pb-3 text-center bg-green-700 mt-3">
                    <div class="flex items-center justify-between text-green-700">
                        <p class="text-2xl text-white font-bold" v-text="activePace?.name"></p>
                        <div class="text-lg text-end">
                            <p class="text-2xl font-bold text-white">Target Time</p>
                            <h2 class="text-4xl font-bold text-white" v-text="activePace?.time"></h2>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Fixed Button Section --}}

            <section class="w-full max-w-[500px] flex items-center justify-between p-1 mt-2 fixed bottom-14 px-3">
                <div class="w-1/2 pe-1.5">
                    <div class="w-full rounded-xl shadow-lg pb-3 pt-1 text-center bg-white border border-green-700">
                        <p class="text-center mb-1 text-lg text-green-700 font-bold" v-text="activeObserverType"></p>
                        <p class="flex items-center justify-between px-2.5 text-green-700">
                            <span class="border border-gray-300 rounded bg-gray-100">
                                <svg class="h-7 w-7 fill-current rtl:rotate-180 cursor-pointer" :class="{ 'text-gray-300': activeObserverKey == 0 }" v-on:click="changeActiveObserverArrow('prev')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path>
                                </svg>
                            </span>
                            <span class="text-balance">
                                <span class="text-2xl font-bold" v-text="activeObserverNumber"></span>
                            </span>
                            <span class="border border-gray-300 rounded bg-gray-100">
                                <svg class="h-7 w-7 fill-current rtl:rotate-180 cursor-pointer" :class="{ 'text-gray-300': activeObserverKey == observer.length - 1 }" v-on:click="changeActiveObserverArrow('next')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path>
                                </svg>
                            </span>
                        </p>
                        <div class="px-2.5">
                            <p class="border border-gray-300 rounded ps-2 pe-1 text-start text-sm font-bold flex justify-between items-center w-full mt-3 py-1 cursor-pointer" v-on:click="handleJumpObserverModal">
                                <span class="pb-0.5" v-text="'Jump To ' + activeObserverType"></span>
                                <svg class="h-6 w-6 fill-current rotate-90 cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path>
                                </svg>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="w-1/2 ps-1.5">
                    <div class="w-full rounded-xl shadow-lg pb-3 pt-1 text-center bg-white border border-green-700">
                        <p class="text-center mb-1 text-lg text-green-700 font-bold" v-text="activePace?.observer_type"></p>
                        <p class="flex items-center justify-between px-2.5 text-green-700">
                            <span class="border border-gray-300 rounded bg-gray-100">
                                <svg class="h-7 w-7 fill-current rtl:rotate-180 cursor-pointer" :class="{ 'text-gray-300': activePaceKey == 0 }" v-on:click="changeActivePaceArrow('prev')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path>
                                </svg>
                            </span>
                            <span class="text-balance">
                                <span class="text-2xl font-bold" v-text="activePace?.number"></span>
                            </span>
                            <span class="border border-gray-300 rounded bg-gray-100">
                                <svg class="h-7 w-7 fill-current rtl:rotate-180 cursor-pointer" :class="{ 'text-gray-300': activePaceKey == member.length - 1 }" v-on:click="changeActivePaceArrow('next')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path>
                                </svg>
                            </span>
                        </p>
                        <div class="px-2.5">
                            <p class="border border-gray-300 rounded ps-2 pe-1 text-start text-sm font-bold flex justify-between items-center w-full mt-3 py-1 cursor-pointer" v-on:click="handleJumpPaceModal">
                                <span class="pb-0.5" v-text="'Jump To ' + activePace?.observer_type"></span>
                                <svg class="h-6 w-6 fill-current rotate-90 cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path>
                                </svg>
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="w-full flex items-center justify-center mb-6 mt-4">
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
                    <button class="w-25 rounded-full border border-green-700 px-6 py-8 bg-white shadow-lg active:scale-95 hover:bg-green-100 transition cursor-pointer" v-on:click="editFinish">
                        <img class="w-full h-auto" src="{{ asset('img/icon/edit.svg') }}" alt="Play Button">
                    </button>
                    <p class="mt-2">EDIT</p>
                </div>

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
                        <button class="w-full flex items-center justify-between py-3 px-4 border-bottom border-b border-gray-400 cursor-pointer" type="button" :class="{ 'bg-gray-300': item.id == activePace?.id }" v-for="item in member" :key="item.id" v-on:click="changeActivePace(item.id)">
                            <p class="w-1/3 text-start" v-text="item.name"></p>
                            <p class="w-1/3" v-text="item.time"></p>
                            <p class="w-1/3 text-center" :class="item.finish_text_class" v-text="item.finish_time"></p>
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
                        <p :class="{ 'text-green-700': diffTimeFinish < 0, 'text-orange-700': diffTimeFinish > 0 && diffTimeFinish <= 3, 'text-red-700': diffTimeFinish > 3 }" v-text="diffTimeFinish"></p>
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
                    <h3 class="text-3xl font-bold text-center" v-text="activeObserverName"></h3>
                    <img class="w-max-full h-auto mx-auto my-2" src="{{ asset('img/time.png') }}" alt="">
                    <h3 class="text-xl font-bold text-center" v-text="activePace?.name"></h3>

                    <div class="w-60 mx-auto mt-4">
                        <p class="text-gray-800">Current Time</p>
                        <p class="border border-gray-300 shadow py-2 px-4 mt-1 rounded-lg bg-white text-gray-400 flex items-center justify-between">
                            <span v-text='finishTimeNow'></span>
                            <span class="text-gray-400 text-sm" v-text="timeDiffEdit" v-if="timeDiffEdit <= 0"></span>
                            <span class="text-gray-400 text-sm" v-text="'+' + timeDiffEdit" v-else></span>
                        </p>

                        <p class="text-gray-800 mt-4">Adjust Time</p>
                        <div class="grid-cols-4 gap-2 text-xs mt-1 flex items-center justify-between">
                            <button class="w-auto border border-green-700 p-2 text-center rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="incrementTime(-1)">
                                <span class="text-6xl font-bold text-green-700 leading-6 px-1.5">-</span>
                            </button>
                            <span class="text-7xl -mt-2.5 text-green-700 font-bold" v-text="timeDiffEdit"></span>
                            <button class="w-auto border border-green-700 p-2 text-center rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="incrementTime(1)">
                                <span class="text-6xl font-bold text-green-700 leading-6">+</span>
                            </button>
                        </div>
                    </div>

                    <div class="text-center mt-6">
                        <button class="w-60 bg-green-700 text-white p-4 rounded-xl hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="editTimer()">
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

            <dialog class="modal items-end" id="jump_observer_modal">
                <div class="modal-box rounded-t-3xl rounded-b-none w-full p-0">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 rounded-full bg-green-700 text-white">✕</button>
                    </form>
                    <form v-on:submit.prevent="handleJumpObserverAction()">
                        <div class="p-6">
                            <h3 class="text-lg font-bold my-6">Select Observer</h3>

                            <div class="grid grid-cols-3 gap-4">
                                <label class="w-full border border-gray-300 p-1.5 text-center rounded cursor-pointer" v-for="item in observer" :class="{ 'border-green-700 bg-green-700/15 cursor-not-allowed': selectedObserver === item.id }" :key="item.id">
                                    <input class="hidden" name="observer" type="radio" :id="'observer_' + item.id" :value="item.id" v-model="selectedObserver" :disabled="selectedObserver === item.id">
                                    <span class="text-sm font-bold px-1.5" v-text="item.name"></span>
                                </label>
                            </div>
                        </div>

                        <div class="text-center w-full shadow-[0_2px_12px_rgba(0,0,0,0.5)] py-3 px-6 bg-gray-100">
                            <button class="w-full bg-green-700 text-white p-4 rounded-xl hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer">
                                Select
                            </button>
                        </div>
                    </form>

                </div>
                <form class="modal-backdrop" method="dialog">
                    <button>close</button>
                </form>
            </dialog>

            <dialog class="modal items-end" id="jump_pace_modal">
                <div class="modal-box rounded-t-3xl rounded-b-none w-full p-0 overflow-hidden">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 rounded-full bg-green-700 text-white">✕</button>
                    </form>
                    <form v-on:submit.prevent="handleJumpPaceAction()">
                        <div class="p-6 pb-3">
                            <h3 class="text-lg font-bold my-6">Select Pace</h3>

                            <div class="max-h-[70vh] overflow-y-auto">
                                <div class="mb-3" v-for="(session, key) in memberHeadTail " :key="key">
                                    <div class="grid grid-cols-3 gap-x-2 gap-y-2 mb-3 border border-gray-300 rounded-lg p-2" v-for="(tee, key2) in session" :key="key2">
                                        <label class="w-full border border-green-700 text-green-700 p-1.5 rounded cursor-pointer relative" :class="{ 'border-blue-700! text-blue-700!': key == 1, 'bg-green-700/50': item.id === selectedPace }" v-for="item in tee" :key="item.id">
                                            <input class="hidden" name="pace" type="radio" :id="'pace_' + item.id" :value="item.id" v-model="selectedPace" :disabled="item.id === selectedPace">
                                            <p class="text-sm font-bold py-1" v-text="item.name"></p>
                                            <span class="text-[10px] absolute top-0 right-0 font-bold text-black px-1.5" v-text="item.head"></span>
                                            <span class="text-[10px] absolute top-0 right-0 font-bold text-black px-1.5" v-text="item.tail"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center w-full shadow-[0_2px_12px_rgba(0,0,0,0.5)] py-3 px-6 bg-gray-100">
                            <button class="w-full bg-green-700 text-white p-4 rounded-xl hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" v-on:click="handleJumpPaceAction()">
                                Select
                            </button>
                        </div>
                    </form>

                </div>
                <form class="modal-backdrop" method="dialog">
                    <button>close</button>
                </form>
            </dialog>

        </div>
    </div>
@endsection
