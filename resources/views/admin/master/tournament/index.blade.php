@extends('layouts.admin')

@section('page-title', 'Tournament')

@section('page-content')
    <div class="w-full max-h-screen h-full overflow-auto bg-gray-100" id="app">
        <div class="w-full px-4 pb-4">
            <div class="bg-white rounded-2xl shadow-md mt-4">
                <div class="border-b-4 border-green-700 py-2 mb-2 px-4">
                    <p class="text-2xl text-green-700 font-bold">Tournament</p>
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
                    <a class="bg-green-700 rounded-lg shadow text-white px-4 py-2 cursor-pointer" href="{{ route('tournament.create') }}">Add Tournament</a>
                </div>

                <div class="w-full p-4">
                    <div class="rounded-box border border-green-700 bg-base-100 mb-4">
                        <table class="table text-center">
                            <!-- head -->
                            <thead>
                                <tr class="text-white">
                                    <th class="bg-green-700 text-white rounded-tl-lg">NO</th>
                                    <th class="bg-green-700">NAME</th>
                                    <th class="bg-green-700">START DATE</th>
                                    <th class="bg-green-700">ROUND</th>
                                    <th class="bg-green-700">COURSE</th>
                                    <th class="bg-green-700">STATUS</th>
                                    <th class="bg-green-700">ORGANIZER</th>
                                    <th class="bg-green-700 text-white rounded-tr-lg">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($tournaments->isEmpty())
                                    <tr>
                                        <td colspan="8" class="border border-green-700/50">No data available</td>
                                    </tr>
                                @endif

                                @foreach ($tournaments as $key => $item)
                                    <tr>
                                        <td class="border border-green-700/50 w-20">{{ $tournaments->firstItem() + $key }}</td>
                                        <td class="border border-green-700/50">{{ $item->name }}</td>
                                        <td class="border border-green-700/50">{{ $item->date_start }}</td>
                                        <td class="border border-green-700/50">{{ $item->round }}</td>
                                        <td class="border border-green-700/50">{{ $item->course->name ?? '' }}</td>
                                        <td class="border border-green-700/50">
                                            @if ($item->status == 'created')
                                                <span class="py-2 px-4 rounded-lg bg-yellow-500 text-white">{{ strtoupper($item->status ?? '') }}</span>
                                            @elseif ($item->status == 'active')
                                                <span class="py-2 px-4 rounded-lg bg-green-700 text-white">{{ strtoupper($item->status ?? '') }}</span>
                                            @elseif ($item->status == 'hold')
                                                <span class="py-2 px-4 rounded-lg bg-gray-700 text-white">{{ strtoupper($item->status ?? '') }}</span>
                                            @elseif ($item->status == 'finish')
                                                <span class="py-2 px-4 rounded-lg bg-red-700 text-white">{{ strtoupper($item->status ?? '') }}</span>
                                            @endif
                                        </td>
                                        <td class="border border-green-700/50">{{ ucwords($item->organizer ?? '') }}</td>
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
                                                    <li><a href="{{ route('tournament.edit', $item->id) }}">Edit</a></li>
                                                    <li><a href="{{ route('tournament.active', $item->id) }}">Activate</a></li>
                                                    <li>
                                                        <form class="w-full h-full inline-block relative" action="{{ route('tournament.destroy', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="inline-block w-full h-full text-left cursor-pointer" type="submit">Delete</button>
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
                    {{ $tournaments->links() }}
                </div>
            </div>

        </div>

    </div>
@endsection
