@extends('layouts.admin')

@section('page-title', 'Setting')

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date_start", {
            dateFormat: "Y-m-d",
        });

        flatpickr("#date_end", {
            dateFormat: "Y-m-d",
        });
    </script>
@endsection

@section('page-content')
    <div class="w-full max-h-screen h-full overflow-auto bg-gray-100" id="app">
        <form class="w-full px-4 pb-4" action="{{ route('setting.store') }}" method="POST">
            @csrf
            <div class="p-4 bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 pb-2 mb-2">
                    <p class="text-2xl text-green-700 font-bold">Setting</p>
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

                <div class="w-full grid grid-cols-1 gap-2">
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Event Name <span class="text-red-700">*</span></legend>
                        <input class="input rounded-xl w-full placeholder:text-gray-500 placeholder:italic" name="name" type="text" value="{{ old('name') }}" required placeholder="Event Name" />
                    </fieldset>
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Periode <span class="text-red-700">*</span></legend>
                        <div class="grid grid-cols-2 gap-2">
                            <input class="input rounded-xl shadow w-full placeholder:text-gray-400" id="date_start" name="date_start" type="text" placeholder="Start Date" required value="{{ old('date_start') }}" />
                            <input class="input rounded-xl shadow w-full placeholder:text-gray-400" id="date_end" name="date_end" type="text" placeholder="End Date" required value="{{ old('date_end') }}" />
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="w-full p-4 flex items-center justify-center mt-4">
                <a class="bg-white rounded-lg shadow px-4 py-2 me-3 border border-gray-300 cursor-pointer" type="button" href="{{ url()->previous() }}">Cancel</a>
                <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" type="submit">Save</button>
            </div>
        </form>
    </div>
@endsection
