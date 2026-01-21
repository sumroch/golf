@extends('layouts.admin')

@section('page-title', 'Assign Referee')

@section('page-script')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    generate: {
                        observer_type: '',
                        duty: '',
                    },
                    datas: {{ Js::from(old('referees', $listRefereeDuties)) }},
                }
            },
            methods: {
                removeHole(index) {
                    this.datas.splice(index, 1);
                },
                createReferee() {
                    if (this.datas.length === this.generate.duty) {
                        referee_modal.close();
                    } else if (this.datas.length > this.generate.duty) {
                        let difference = this.datas.length - this.generate.duty;
                        this.datas.splice(-difference, difference);
                    } else if (this.datas.length < this.generate.duty) {
                        let difference = this.generate.duty - this.datas.length;
                        for (let i = 0; i < difference; i++) {
                            this.datas.push({
                                user_id: '',
                                observer_type: this.generate.observer_type,
                                observer_id: [],
                            });
                        }
                    }

                    referee_modal.close();
                },
            },
        });

        app.mount('#app');
    </script>
@endsection

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
                        <hr class="bg-green-700" />
                    </li>
                    <li class="w-1/6">
                        <hr class="bg-green-700" />
                        <div class="timeline-start"></div>
                        <div class="timeline-middle col-start-3 grid justify-end relative">
                            <div class="inline-block rounded-full shadow-md z-1">
                                <img src="{{ asset('img/icon/referee-step.svg') }}" alt="">
                            </div>
                        </div>
                        <hr class="col-start-2 bg-green-700" />
                    </li>
                </ul>
            </div>
        </div>

        <form class="w-full px-4 pb-4" action="{{ route('round.referee.store', $round->id) }}" method="POST">
            @csrf
            <div class="p-4 bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 pb-2 mb-2">
                    <p class="text-2xl text-green-700 font-bold">Assign Referee</p>
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

                <div class="w-full pb-4 pt-2 px-2 flex items-center justify-end">
                    <button class="bg-green-700 rounded-lg shadow text-white px-4 py-1 text-sm flex items-center cursor-pointer" type="button" onclick="referee_modal.showModal()">
                        Add Referee
                    </button>
                </div>
                <div class="w-full grid grid-cols-2 gap-6 px-2 pb-2">

                    <div class="p-8 rounded-2xl bg-white shadow-[0px_1px_4px_0px_rgba(0,0,0,0.2)] relative" v-for="(data, index) in datas" :key="index">
                        <span class="absolute right-3 top-3 rounded-full bg-red-700 text-white px-2 cursor-pointer" @click="removeHole(index)">x</span>
                        <div class="flex items-center justify-start gap-4 flex-wrap">
                            <fieldset class="fieldset w-full">
                                <legend class="fieldset-legend">Select Referee <span class="text-red-700">*</span></legend>
                                <select class="select rounded-xl w-full" :name="'referees[' + index + '][user_id]'" v-model="data.user_id">
                                    <option disabled selected>Pick Referee</option>
                                    @foreach ($referees as $referee)
                                        <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                                    @endforeach
                                </select>
                            </fieldset>

                            <fieldset class="fieldset w-full">
                                <input type="hidden" :name="'referees[' + index + '][observer_type]'" v-model="data.observer_type" />
                                <legend class="fieldset-legend">Select Hole <span class="text-red-700">*</span></legend>
                                <div class="grid grid-cols-4 gap-4" v-if="data.observer_type != 'group'">
                                    <select class="select rounded-xl w-full" :name="'referees[' + index + '][observer_id][]'" v-model="data.observer_id[0]">
                                        <option disabled selected>Hole</option>
                                        @foreach ($holes as $hole)
                                            <option value="{{ $hole->id }}">{{ $hole->number }}</option>
                                        @endforeach
                                    </select>
                                    <select class="select rounded-xl w-full" :name="'referees[' + index + '][observer_id][]'" v-model="data.observer_id[1]">
                                        <option disabled selected>Hole</option>
                                        @foreach ($holes as $hole)
                                            <option value="{{ $hole->id }}">{{ $hole->number }}</option>
                                        @endforeach
                                    </select>
                                    <select class="select rounded-xl w-full" :name="'referees[' + index + '][observer_id][]'" v-model="data.observer_id[2]">
                                        <option disabled selected>Hole</option>
                                        @foreach ($holes as $hole)
                                            <option value="{{ $hole->id }}">{{ $hole->number }}</option>
                                        @endforeach
                                    </select>
                                    <select class="select rounded-xl w-full" :name="'referees[' + index + '][observer_id][]'" v-model="data.observer_id[3]">
                                        <option disabled selected>Hole</option>
                                        @foreach ($holes as $hole)
                                            <option value="{{ $hole->id }}">{{ $hole->number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-4 gap-4" v-if="data.observer_type == 'group'">
                                    <select class="select rounded-xl w-full" :name="'referees[' + index + '][observer_id][]'" v-model="data.observer_id[0]">
                                        <option disabled selected>Group</option>
                                        @foreach ($groups as $key => $group)
                                            <option value="{{ $key }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                    <select class="select rounded-xl w-full" :name="'referees[' + index + '][observer_id][]'" v-model="data.observer_id[1]">
                                        <option disabled selected>Group</option>
                                        @foreach ($groups as $key => $group)
                                            <option value="{{ $key }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                    <select class="select rounded-xl w-full" :name="'referees[' + index + '][observer_id][]'" v-model="data.observer_id[2]">
                                        <option disabled selected>Group</option>
                                        @foreach ($groups as $key => $group)
                                            <option value="{{ $key }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                    <select class="select rounded-xl w-full" :name="'referees[' + index + '][observer_id][]'" v-model="data.observer_id[3]">
                                        <option disabled selected>Group</option>
                                        @foreach ($groups as $key => $group)
                                            <option value="{{ $key }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                </div>
            </div>

            <div class="w-full p-4 flex items-center justify-center mt-4" v-if="datas.length > 0">
                <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer">Save</button>
            </div>
        </form>

        <dialog class="modal" id="referee_modal">
            <div class="modal-box p-0">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <div class="w-full bg-slate-50 p-6">
                    <h3 class="text-2xl font-bold mb-0">Add Referee</h3>
                    <p class="text-gray-500">Setup a referee to assign in round</p>
                </div>
                <form class="p-6" @submit.prevent="createReferee">
                    <div class="flex items-center mt-6">
                        <p class="w-1/2 font-bold">Observe to</p>
                        <div class="flex items-center gap-4 w-full">
                            <label class="label cursor-pointer">
                                <input class="radio" name="transportation" type="radio" value="hole" v-model="generate.observer_type" required />
                                <span class="label-text text-black ms-2">Hole</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input class="radio" name="transportation" type="radio" value="group" v-model="generate.observer_type" />
                                <span class="label-text text-black ms-2">Group</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center mt-3">
                        <p class="w-1/2 font-bold">Referee Duty</p>
                        <select class="select rounded-xl w-full" name="duty" v-model="generate.duty" required>
                            <option disabled selected>Select Duty</option>
                            @foreach (range(1, 10) as $item)
                                <option value="{{ $item }}">{{ $item }} Person</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end mt-12">
                        <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" type="submit">Create</button>
                    </div>
                </form>
            </div>
            <form class="modal-backdrop" method="dialog">
                <button>close</button>
            </form>
        </dialog>

        @include('layouts.modal', [
            'id' => 'success',
            'modal_title' => 'Success!',
            'modal_description' => 'Your Settings Tournament on Round ... Has Been Created',
            'modal_button_text' => 'Go To Dashboard',
            'modal_url' => url('admin/dashboard'),
            'modal_icon' => asset('img/icon/success.svg'),
        ])
    </div>
@endsection
