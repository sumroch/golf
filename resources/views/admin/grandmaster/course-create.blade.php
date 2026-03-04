@extends('layouts.admin')

@section('page-title', 'Course & Holes')

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

                <div class="w-full grid grid-cols-2 gap-6 px-6 mb-6">
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Course Name <span class="text-red-700">*</span></legend>
                        <input class="input rounded-xl w-full placeholder:text-gray-500 placeholder:italic" name="name" type="text" value="{{ old('name') }}" required placeholder="Course Name" />
                    </fieldset>
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Location <span class="text-red-700">*</span></legend>
                        <select class="select rounded-xl w-full" name="location">
                            <option disabled selected>Pick Location</option>
                            <option value="bandung" {{ old('location') == 'bandung' ? 'selected' : '' }}>Bandung</option>
                            <option value="bogor" {{ old('location') == 'bogor' ? 'selected' : '' }}>Bogor</option>
                            <option value="jakarta" {{ old('location') == 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                        </select>
                    </fieldset>
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Par Total<span class="text-red-700">*</span></legend>
                        <div class="flex items-center gap-4">
                            <label class="label cursor-pointer">
                                <input class="radio" name="par" type="radio" value="70" {{ old('par') == 70 ? 'checked' : '' }} />
                                <span class="label-text text-black ms-2">70</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input class="radio" name="par" type="radio" value="71" {{ old('par') == 71 ? 'checked' : '' }} />
                                <span class="label-text text-black ms-2">71</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input class="radio" name="par" type="radio" value="72" {{ old('par') == 72 ? 'checked' : '' }} required />
                                <span class="label-text text-black ms-2">72</span>
                            </label>
                        </div>
                    </fieldset>
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Hole Total<span class="text-red-700">*</span></legend>
                        <input class="input rounded-xl w-full validator placeholder:text-gray-500 placeholder:italic" name="total_holes" type="number" value="{{ old('total_holes', 18) }}" title="Must be between be 0 to 18" required placeholder="Hole" min="0" max="20" disabled />
                    </fieldset>
                </div>

                <div class="w-full grid grid-cols-4 gap-6 px-6 pb-2">
                    @foreach (range(1, 18) as $item)
                        <div class="rounded-2xl bg-white shadow-[0px_0px_4px_0px_rgba(0,0,0,0.2)] relative">
                            <div class="bg-neutral-100 rounded-t-2xl">
                                <p class="text-lg font-bold text-black p-4">Hole {{ $item }}</p>
                            </div>
                            <div class="flex items-center justify-start gap-1 flex-wrap p-4">
                                <fieldset class="fieldset w-full">
                                    <legend class="fieldset-legend">Par <span class="text-red-700">*</span></legend>
                                    <div class="flex items-center gap-4">
                                        <label class="label cursor-pointer">
                                            <input class="radio" name="holes[{{ $item }}][par]" type="radio" value="3" {{ old('holes.' . $item . '.par') == 3 ? 'checked' : '' }} />
                                            <span class="label-text text-black ms-2">3</span>
                                        </label>
                                        <label class="label cursor-pointer">
                                            <input class="radio" name="holes[{{ $item }}][par]" type="radio" value="4" {{ old('holes.' . $item . '.par') == 4 ? 'checked' : '' }} />
                                            <span class="label-text text-black ms-2">4</span>
                                        </label>
                                        <label class="label cursor-pointer">
                                            <input class="radio" name="holes[{{ $item }}][par]" type="radio" value="5" {{ old('holes.' . $item . '.par') == 5 ? 'checked' : '' }} required />
                                            <span class="label-text text-black ms-2">5</span>
                                        </label>
                                    </div>
                                </fieldset>
                                <fieldset class="fieldset w-full">
                                    <legend class="fieldset-legend">Allowed Time <span class="text-red-700">*</span></legend>
                                    <input class="input rounded-xl w-full validator placeholder:text-gray-500 placeholder:italic" name="holes[{{ $item }}][allowed_time]" type="number" value="{{ old('holes.' . $item . '.allowed_time') }}" title="Must be between be 0 to 60" required placeholder="Allowed Time" min="0" max="60" />
                                </fieldset>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="w-full p-4 flex items-center justify-center mt-4">
                <a class="bg-white rounded-lg shadow px-4 py-2 me-3 border border-gray-300 cursor-pointer" type="button" href="{{ url()->previous() }}">Cancel</a>
                <button class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" type="submit">Save</button>
            </div>
        </form>
    </div>
@endsection
