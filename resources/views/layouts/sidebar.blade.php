{{-- resources/views/layouts/sidebar.blade.php --}}
<aside class="h-screen w-72 bg-white text-base-content flex flex-col shadow-lg z-1 no-print">
    {{-- Brand / Logo --}}
    <div class="px-4 py-6 flex items-end gap-3">
        <img class="w-10 h-10" src="{{ asset('img/icon/golf.png') }}" alt="" />
        <h2 class="font-semibold text-lg text-green-700">Golf Tournament</h2>
    </div>

    {{-- Menu (scrollable) --}}
    <nav class="px-2 py-4 overflow-y-auto flex-1 space-y-2">
        <ul class="menu rounded-box max-w-xs w-full">
            @foreach ($rounds as $key => $item)
                <li class="mt-4">
                    <details {{ request()->route('round') == $item->id ? 'open' : '' }}>
                        <summary class="text-green-600 font-bold">
                            <img class="h-8 w-8 me-4" src="{{ asset('img/icon/gears.svg') }}" alt="">
                            <span>Round {{ $key + 1 }}</span>
                        </summary>
                        <ul>
                            <li class="py-2">
                                <a class="{{ request()->route('round') == $item->id && request()->is('admin/dashboard*') ? 'bg-green-600 text-white' : '' }}" href="{{ route('dashboard', ['round' => $item->id]) }}">
                                    <img class="h-8 w-8 me-4" src="{{ asset('img/icon/dashboard.svg') }}" alt="">
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="py-2">
                                <a class="{{ request()->route('round') == $item->id && request()->is('admin/setup*') ? 'bg-green-600 text-white' : '' }}" href="{{ route('round.setup', ['round' => $item->id]) }}">
                                    <img class="h-8 w-8 me-4" src="{{ asset('img/icon/setup.svg') }}" alt="">
                                    <span>Setup</span>
                                </a>
                            </li>
                            <li class="py-2">
                                <a class="{{ request()->route('round') == $item->id && request()->is('admin/group*') ? 'bg-green-600 text-white' : '' }}" href="{{ route('round.group', ['round' => $item->id]) }}">
                                    <img class="h-8 w-8 me-4" src="{{ asset('img/icon/group.svg') }}" alt="">
                                    <span>Group</span>
                                </a>
                            </li>
                            <li class="py-2">
                                <a class="{{ request()->route('round') == $item->id && request()->is('admin/pace*') ? 'bg-green-600 text-white' : '' }}" href="{{ route('round.pace', ['round' => $item->id]) }}">
                                    <img class="h-8 w-8 me-4" src="{{ asset('img/icon/pace.svg') }}" alt="">
                                    <span>Pace Of Play</span>
                                </a>
                            </li>
                            <li class="py-2">
                                <a class="{{ request()->route('round') == $item->id && request()->is('admin/referee*') ? 'bg-green-600 text-white' : '' }}" href="{{ route('round.referee', ['round' => $item->id]) }}">
                                    <img class="h-8 w-8 me-4" src="{{ asset('img/icon/referee.svg') }}" alt="">
                                    <span>Assign Referee</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                </li>
            @endforeach
            <li class="mt-4">
                <details {{ request()->is('admin/master*') ? 'open' : '' }}>
                    <summary class="text-green-600 font-bold">
                        <img class="h-8 w-8 me-4" src="{{ asset('img/icon/data.svg') }}" alt="">
                        <span>Master Data</span>
                    </summary>
                    <ul>
                        <li class="py-2">
                            <a class="{{ request()->is('admin/master/tournament*') ? 'bg-green-600 text-white' : '' }}" href="{{ route('tournament.index') }}">
                                <img class="h-8 w-8 me-4" src="{{ asset('img/icon/tournament.svg') }}" alt="">
                                <span>Tournament</span>
                            </a>
                        </li>
                        <li class="py-2">
                            <a class="{{ request()->is('admin/master/referee*') ? 'bg-green-600 text-white' : '' }}" href="{{ route('referee.index') }}">
                                <img class="h-8 w-8 me-4" src="{{ asset('img/icon/referee.svg') }}" alt="">
                                <span>Referee</span>
                            </a>
                        </li>
                    </ul>
                </details>

                <details class="mt-4" {{ request()->is('admin/grandmaster*') ? 'open' : '' }}>
                    <summary class="text-green-600 font-bold">
                        <img class="h-8 w-8 me-4" src="{{ asset('img/icon/user.svg') }}" alt="">
                        <span>Superadmin</span>
                    </summary>
                    <ul>
                        <li class="py-2">
                            <a class="{{ request()->is('admin/grandmaster/course*') ? 'bg-green-600 text-white' : '' }}" href="{{ route('course.index') }}">
                                <img class="h-8 w-8 me-4" src="{{ asset('img/icon/setup.svg') }}" alt="">
                                <span>Courses & Holes</span>
                            </a>
                        </li>
                    </ul>
                </details>
            </li>
        </ul>
    </nav>

    {{-- Account info (fixed at bottom) --}}
    <div class="py-4 bg-base-100">
        <div class="dropdown dropdown-right dropdown-end collapse-arrow w-full">
            <div class="font-bold flex items-center gap-3 px-2 cursor-pointer" role="button" tabindex="0">
                <div class="avatar">
                    <div class="w-12 rounded-full flex items-center justify-center bg-green-600 text-white text-xl">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div>{{ auth()->user()->name }}</div>
                    <div class="text-xs text-neutral overflow-hidden truncate">{{ ucfirst(auth()->user()->getRoleNames()[0]) }}</div>
                </div>
                <img src="{{ asset('img/icon/arrow.svg') }}" alt="" class="w-3 h-3 me-2 mt-2">
            </div>
            <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm" tabindex="-1">
                <li>
                    <form class="inline-block" action="{{ route('logout') }}" method="POST">@csrf
                        <button type="submit" class="w-full text-start h-full inline-block">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</aside>
