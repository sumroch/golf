@extends('layouts.admin')

@section('page-title', 'Course & Holes')

@section('page-content')
    <div class="w-full max-h-screen h-full overflow-auto bg-gray-100">
        <div class="w-full px-4 pb-4">
            <div class="bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 py-2 mb-2 px-4">
                    <p class="text-2xl text-green-700 font-bold">Setting</p>
                </div>

                <div class="w-full flex items-center justify-end px-4 pt-3">
                    <a class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" href="{{ route('setting.create') }}">Add Event</a>
                </div>

                <div class="w-full p-4">
                    <div class="rounded-box border border-green-700 bg-base-100 mb-4">
                        <table class="table text-center">
                            <!-- head -->
                            <thead>
                                <tr class="bg-green-700 text-white">
                                    <th class="">NO</th>
                                    <th class="">NAME</th>
                                    <th class="">PERIODE</th>
                                    <th class="">START</th>
                                    <th class="">END</th>
                                    <th class="">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($settings->isEmpty())
                                    <tr>
                                        <td colspan="6" class="border border-green-700/50">No data available</td>
                                    </tr>
                                @endif

                                @foreach ($settings as $index => $setting)
                                    <tr>
                                        <td class="w-20 border border-green-700/50">{{ $index + 1 }}</td>
                                        <td class="border border-green-700/50">{{ $setting->name }}</td>
                                        <td class="border border-green-700/50">{{ $setting->periode }}</td>
                                        <td class="border border-green-700/50">{{ $setting->date_start }}</td>
                                        <td class="border border-green-700/50">{{ $setting->date_end }}</td>
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
                                                    <li><a href="{{ route('setting.edit', $setting->id) }}">Edit</a></li>
                                                    <li>
                                                        <form action="{{ route('setting.destroy', $setting->id) }}" method="POST" class="w-full h-full block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="w-full h-full text-start" onclick="return confirm('Are you sure you want to delete this event ?')">Delete</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $settings->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
