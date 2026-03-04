@extends('layouts.admin')

@section('page-title', 'Create Tournament')

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date_start", {
            dateFormat: "Y-m-d",
        });
    </script>
@endsection

@section('page-content')
    <form class="w-full max-h-screen h-full overflow-auto bg-gray-100" method="POST" action="{{ route('tournament.store') }}">
        @csrf
        <div class="w-full px-4 pb-4">
            <div class="p-4 bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 pb-2 mb-2">
                    <p class="text-2xl text-green-700 font-bold">Create Tournament</p>
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

                @if (session('noError'))
                    <div class="grid grid-cols-2 gap-4 py-4 px-6 border border-red-300 bg-red-100 rounded-lg">
                        <ul class="list-disc">
                            <li class="text-red-700 text-xs">{{ session('noError') }}</li>
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-x-32 gap-y-2 pb-4">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Tournament Name<span class="text-red-700">*</span></legend>
                        <input class="input rounded-xl w-full" name="name" type="text" value="{{ old('name') }}" placeholder="Type here" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Organizer<span class="text-red-700">*</span></legend>
                        <input class="input rounded-xl w-full" name="organizer" type="text" value="{{ old('organizer') }}" placeholder="Type here" />
                    </fieldset>

                    <div class="grid grid-cols-2 gap-x-4">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Location <span class="text-red-700">*</span></legend>
                            <select class="select rounded-xl w-full" name="location">
                                <option disabled selected>Pick Location</option>
                                <option value="bandung" {{ old('location') == 'bandung' ? 'selected' : '' }}>Bandung</option>
                                <option value="bogor" {{ old('location') == 'bogor' ? 'selected' : '' }}>Bogor</option>
                                <option value="jakarta" {{ old('location') == 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                            </select>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Timezone <span class="text-red-700">*</span></legend>
                            <select class="select rounded-xl w-full" name="timezone">
                                <option disabled selected>Pick Timezone</option>
                                <option value="Asia/Jakarta" {{ old('timezone') == 'Asia/Jakarta' ? 'selected' : '' }}>WIB</option>
                                <option value="Asia/Makassar" {{ old('timezone') == 'Asia/Makassar' ? 'selected' : '' }}>WITA</option>
                                <option value="Asia/Jayapura" {{ old('timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>WIT</option>
                            </select>
                        </fieldset>
                    </div>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Select Course <span class="text-red-700">*</span></legend>
                        <select class="select rounded-xl w-full" name="course_id">
                            <option disabled selected>Pick Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Start Date <span class="text-red-700">*</span></legend>
                        <input class="input pika-single rounded-xl w-full" id="date_start" name="date_start" type="text" value="{{ old('date_start') }}" placeholder="Select Date" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Round <span class="text-red-700">*</span></legend>
                        <select class="select rounded-xl w-full" name="round">
                            <option class="italic" disabled selected>Pick Round</option>
                            <option value="1" {{ old('round') == 1 ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('round') == 2 ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('round') == 3 ? 'selected' : '' }}>3</option>
                            <option value="4" {{ old('round') == 4 ? 'selected' : '' }}>4</option>
                        </select>
                    </fieldset>

                </div>
            </div>

            <div class="w-full p-4 flex items-center justify-center mt-4">
                <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer">Create</button>
            </div>
        </div>
    </form>
@endsection
