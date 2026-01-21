@extends('layouts.admin')

@section('page-title', 'Referee')

@section('page-script')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    data: {{ Js::from($referees->items()) }},
                    form: {
                        id: '',
                        name: '',
                        acronym: '',
                        position: '',
                        phone_number: '',
                        email: '',
                        roles: [{
                            name: ''
                        }],
                        username: '',
                        password: '',
                    },
                }
            },
            methods: {
                addHole() {
                    this.holes.push({
                        par: '',
                        allowed_time: '',
                    });
                },
                showData(index) {
                    this.form = this.data[index];
                    referee_update_modal.showModal();

                    console.log(this.form);
                },
                generateSecurePassword(type = 'add') {
                    let length = 12
                    const charset =
                        'abcdefghijklmnopqrstuvwxyz' +
                        'ABCDEFGHIJKLMNOPQRSTUVWXYZ' +
                        '0123456789' +
                        '!@#$%^&*()_+-='

                    const values = new Uint32Array(length)
                    crypto.getRandomValues(values)

                    if (type == 'add') {
                        document.getElementById('passinput').value = Array.from(values).map(x => charset[x % charset.length]).join('');
                    } else {
                        document.getElementById('passinput-update').value = Array.from(values).map(x => charset[x % charset.length]).join('');
                    }
                },

            },
        });

        app.mount('#app');
    </script>

@endsection

@section('page-content')
    <div class="w-full max-h-screen h-full overflow-auto bg-gray-100" id="app">
        <div class="w-full px-4 pb-4">
            <div class="bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 py-2 mb-2 px-4">
                    <p class="text-2xl text-green-700 font-bold">Referee</p>
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

                <div class="w-full flex items-center justify-end px-4 pt-3">
                    <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 me-4 cursor-pointer" onclick="send_modal.showModal()">
                        <img class="w-6 h-6" src="{{ asset('img/icon/user.svg') }}" alt="">
                    </button>
                    <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" onclick="referee_modal.showModal()">Add Referee</button>
                </div>

                <div class="w-full p-4">
                    <div class="rounded-box border border-green-700 bg-base-100 mb-4">
                        <table class="table text-center">
                            <!-- head -->
                            <thead>
                                <tr class="text-white">
                                    <th class="bg-green-700 text-white rounded-tl-lg">NO</th>
                                    <th class="bg-green-700">NAME</th>
                                    <th class="bg-green-700">POSITION</th>
                                    <th class="bg-green-700">USERNAME</th>
                                    <th class="bg-green-700 text-white rounded-tr-lg">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($referees as $key => $item)
                                    <tr>
                                        <td class="border border-green-700/50 w-20">{{ $referees->firstItem() + $key }}</td>
                                        <td class="border border-green-700/50">{{ $item->name }}</td>
                                        <td class="border border-green-700/50">{{ ucwords($item->roles[0]->name ?? '') }}</td>
                                        <td class="border border-green-700/50">{{ $item->username }}</td>
                                        <td class="border border-green-700/50">

                                            <div class="dropdown dropdown-end">
                                                <div class="m-1 cursor-pointer hover:bg-green-700 rounded-lg" role="button" tabindex="0">
                                                    <div class="flex items-center justify-center gap-1 p-2">
                                                        <div class="w-2 h-2 rounded-full bg-black"></div>
                                                        <div class="w-2 h-2 rounded-full bg-black"></div>
                                                        <div class="w-2 h-2 rounded-full bg-black"></div>
                                                    </div>
                                                </div>
                                                <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-[0px_0px_4px_0px_rgba(0,0,0,0.2)]" tabindex="-1">
                                                    <li><button v-on:click="showData({{ $key }})">Edit</button></li>
                                                    <li><a href="{{ route('referee.destroy', $item->id) }}">Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $referees->links() }}
                </div>
            </div>

            <dialog class="modal" id="referee_modal">
                <div class="modal-box p-0 w-11/12 max-w-4xl">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <div class="w-full bg-slate-50 p-6">
                        <h3 class="text-2xl font-bold mb-0">Add Referee</h3>
                        <p class="text-gray-500">Add data for the referee who will fill the match</p>
                    </div>
                    <form class="p-6" action="{{ route('referee.store') }}" method="POST">
                        @csrf
                        <div class="w-full grid grid-cols-2 gap-18">
                            <div class="w-full flex items-center gap-4 mt-4">
                                <legend class="fieldset-legend w-1/2 flex justify-start">Full Name<span class="text-red-700">*</span></legend>
                                <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" name="name" type="text" required placeholder="Full Name" />
                            </div>

                            <div class="w-full flex items-center gap-4 mt-4">
                                <legend class="fieldset-legend w-1/2 flex justify-start">Akronim<span class="text-red-700">*</span></legend>
                                <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" name="acronym" type="text" required placeholder="Akronim" />
                            </div>
                        </div>
                        <div class="w-full flex items-center gap-4 mt-4">
                            <legend class="fieldset-legend w-1/6 flex justify-start">Position<span class="text-red-700">*</span></legend>
                            <select class="select rounded-xl w-full" name="position">
                                <option disabled selected>Pick Referee</option>
                                <option value="technician">Tech</option>
                                <option value="director">Tour Director</option>
                                <option value="chief">Chief Referee</option>
                                <option value="referee">Referee</option>
                                <option value="observer">Observer</option>
                            </select>
                        </div>
                        <div class="w-full flex items-center gap-4 mt-4">
                            <legend class="fieldset-legend w-1/6 flex justify-start">Whatsapp<span class="text-red-700">*</span></legend>
                            <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" name="phone_number" type="text" required placeholder="Whatsapp" />
                        </div>
                        <div class="w-full flex items-center gap-4 mt-4">
                            <legend class="fieldset-legend w-1/6 flex justify-start">Email<span class="text-red-700">*</span></legend>
                            <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" name="email" type="email" placeholder="Email" />
                        </div>
                        <div class="w-full flex items-center gap-4 mt-4">
                            <legend class="fieldset-legend w-1/6 flex justify-start">Password<span class="text-red-700">*</span></legend>
                            <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" id="passinput" name="password" type="text" required placeholder="Password" />
                        </div>
                        <p class="text-xs underline underline-offset-1 cursor-pointer w-full text-end mt-1" v-on:click="generateSecurePassword('add')">Generate Password</p>
                        <p class="underline underline-offset-1 cursor-pointer w-full text-center mt-2">Generate QR Code for Login</p>

                        <div class="flex items-center justify-end mt-6">
                            <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" type="submit">Save</button>
                        </div>
                    </form>
                </div>
                <form class="modal-backdrop" method="dialog">
                    <button>close</button>
                </form>
            </dialog>

            <dialog class="modal" id="referee_update_modal">
                <div class="modal-box p-0 w-11/12 max-w-4xl">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <div class="w-full bg-slate-50 p-6">
                        <h3 class="text-2xl font-bold mb-0">Add Referee</h3>
                        <p class="text-gray-500">Add data for the referee who will fill the match</p>
                    </div>
                    <form class="p-6" :action="'{{ url('admin/master/referee') }}/' + form.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="w-full grid grid-cols-2 gap-18">
                            <div class="w-full flex items-center gap-4 mt-4">
                                <legend class="fieldset-legend w-1/2 flex justify-start">Full Name<span class="text-red-700">*</span></legend>
                                <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" name="name" type="text" required placeholder="Full Name"  v-model="form.name"/>
                            </div>

                            <div class="w-full flex items-center gap-4 mt-4">
                                <legend class="fieldset-legend w-1/2 flex justify-start">Akronim<span class="text-red-700">*</span></legend>
                                <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" name="acronym" type="text" required placeholder="Akronim" v-model="form.acronym" />
                            </div>
                        </div>
                        <div class="w-full flex items-center gap-4 mt-4">
                            <legend class="fieldset-legend w-1/6 flex justify-start">Position<span class="text-red-700">*</span></legend>
                            <select class="select rounded-xl w-full" name="position" v-model="form.roles[0].name">
                                <option disabled selected>Pick Referee</option>
                                <option value="technician">Tech</option>
                                <option value="director">Tour Director</option>
                                <option value="chief">Chief Referee</option>
                                <option value="referee">Referee</option>
                                <option value="observer">Observer</option>
                            </select>
                        </div>
                        <div class="w-full flex items-center gap-4 mt-4">
                            <legend class="fieldset-legend w-1/6 flex justify-start">Whatsapp<span class="text-red-700">*</span></legend>
                            <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" name="phone_number" type="text" required placeholder="Whatsapp" v-model="form.phone_number" />
                        </div>
                        <div class="w-full flex items-center gap-4 mt-4">
                            <legend class="fieldset-legend w-1/6 flex justify-start">Email<span class="text-red-700">*</span></legend>
                            <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" name="email" type="email" placeholder="Email" v-model="form.email" />
                        </div>
                        <div class="w-full flex items-center gap-4 mt-4">
                            <legend class="fieldset-legend w-1/6 flex justify-start">Password<span class="text-red-700">*</span></legend>
                            <input class="input rounded-xl validator placeholder:text-gray-500 placeholder:italic w-full" id="passinput-update" name="password" type="text" placeholder="Password"/>
                        </div>
                        <p class="text-xs underline underline-offset-1 cursor-pointer w-full text-end mt-1" v-on:click="generateSecurePassword('update')">Generate Password</p>
                        <p class="underline underline-offset-1 cursor-pointer w-full text-center mt-2">Generate QR Code for Login</p>

                        <div class="flex items-center justify-end mt-6">
                            <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" type="submit">Save</button>
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

    </div>
@endsection
