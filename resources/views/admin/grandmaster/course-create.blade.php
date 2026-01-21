@extends('layouts.admin')

@section('page-title', 'Course & Holes')

@section('page-script')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    holes: {{ Js::from(old('holes', [])) }},
                }
            },
            methods: {
                addHole() {
                    this.holes.push({
                        par: '',
                        allowed_time: '',
                    });
                },
                removeHole(index) {
                    this.holes.splice(index, 1);
                },
            },
        });

        app.mount('#app');
    </script>
@endsection

@section('page-content')
    <div class="w-full max-h-screen h-full overflow-auto bg-gray-100" id="app">
        <form class="w-full px-4 pb-4" action="{{ route('course.store') }}" method="POST">
            @csrf
            <div class="p-4 bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 pb-2 mb-2">
                    <p class="text-2xl text-green-700 font-bold">Course & Holes</p>
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

                <div class="w-full grid grid-cols-2 gap-6 px-6">
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Course Name <span class="text-red-700">*</span></legend>
                        <input class="input rounded-xl w-full placeholder:text-gray-500 placeholder:italic" name="name" type="text" required placeholder="Course Name" value="{{ old('name') }}" />
                    </fieldset>
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Location <span class="text-red-700">*</span></legend>
                        <select class="select rounded-xl w-full" name="location">
                            <option disabled selected>Pick Location</option>
                            <option {{ old('location') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                            <option {{ old('location') == 'Bogor' ? 'selected' : '' }}>Bogor</option>
                            <option {{ old('location') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        </select>
                    </fieldset>
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Par Total<span class="text-red-700">*</span></legend>
                        <input class="input rounded-xl w-full validator placeholder:text-gray-500 placeholder:italic" name="par" type="number" title="Must be between be 0 to 50" required placeholder="Hole" min="0" max="50" value="{{ old('par') }}" />
                    </fieldset>
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Hole Total<span class="text-red-700">*</span></legend>
                        <input class="input rounded-xl w-full validator placeholder:text-gray-500 placeholder:italic" name="total_holes" type="number" title="Must be between be 0 to 20" required placeholder="Hole" min="0" max="20" value="{{ old('total_holes') }}" />
                    </fieldset>
                </div>
                <div class="w-full my-6 pb-2 px-6 flex items-center justify-end">
                    <button class="bg-green-700 rounded-lg shadow text-white px-4 py-1 flex items-center cursor-pointer" type="button" @click="addHole">
                        Add Hole
                    </button>
                </div>
                <div class="w-full grid grid-cols-3 gap-12 px-6 pb-2">
                    <div class="rounded-2xl bg-white shadow-[0px_0px_4px_0px_rgba(0,0,0,0.2)] relative" v-for="(item, index) in holes" :key="index">
                        <span class="absolute right-3 top-3 rounded-full bg-red-700 text-white px-2 cursor-pointer" @click="removeHole(index)">x</span>
                        <div class="bg-neutral-100 rounded-t-2xl">
                            <p class="text-lg font-bold text-black p-4">Hole @{{ index + 1 }}</p>
                        </div>
                        <div class="flex items-center justify-start gap-1 flex-wrap p-4">
                            <fieldset class="fieldset w-full">
                                <legend class="fieldset-legend">Par <span class="text-red-700">*</span></legend>
                                <input class="input rounded-xl w-full validator placeholder:text-gray-500 placeholder:italic" type="number" title="Must be between be 0 to 10" :name="'holes[' + index + '][par]'" required placeholder="Hole" min="0" max="50" />
                            </fieldset>
                            <fieldset class="fieldset w-full">
                                <legend class="fieldset-legend">Allowed Time <span class="text-red-700">*</span></legend>
                                <input class="input rounded-xl w-full validator placeholder:text-gray-500 placeholder:italic" type="number" title="Must be between be 0 to 60" :name="'holes[' + index + '][allowed_time]'" required placeholder="Allowed Time" min="0" max="60" />
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full p-4 flex items-center justify-center mt-4">
                <button class="bg-white rounded-lg shadow px-4 py-2 me-3 border border-gray-300 cursor-pointer" type="button" href="{{ url()->previous() }}">Cancel</button>
                <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" type="submit">Save</button>
            </div>
        </form>
    </div>
@endsection
