@extends('layouts.app')

@section('page-title', 'Login')

@section('page-content')
    <div class="md:h-full w-full md:flex items-start justify-center bg-white relative">
        <div class="p-8 xl:p-14 md:w-3/4 lg:w-1/2 xl:w-3/8 my-auto">
            <img class="w-full md:hidden h-auto" src="{{ asset('img/login-banner-sp.png') }}" alt="">
            <h2 class="text-3xl font-bold my-6 md:mt-35">Welcome 👋</h2>
            <p class="text-lg mb-10">Sign in to use the golf timer Access your personalized golf sessions and keep track.</p>
            <form class="space-y-4" action="{{ route('authenticate') }}" method="POST">
                @csrf
                <div class="flex items-center justify-center mb-6">
                    <label class="flex items-center justify-center cursor-pointer w-full">
                        <!-- Toggle Container -->
                        <div class="relative w-full">
                            <!-- Input Checkbox -->
                            <input class="sr-only" id="section-toggle" name="section" type="checkbox" value="referee" onchange="toggleLabel()" {{ old('section', 'referee') == 'referee' ? 'checked' : '' }}>
                            <!-- Background -->
                            <div class="bg-gray-200 w-full h-10 rounded-full shadow-inner"></div>
                            <!-- Dot (moving part) -->
                            <div class="dot absolute left-1 top-1 bg-green-700 w-1/2 h-8 rounded-full transition-transform duration-300 flex items-center justify-center shadow-sm">
                                <span class="text-sm font-semibold text-gray-700" id="toggle-text"></span>
                            </div>
                            <!-- Labels inside the track for visual cue -->
                            <div class="absolute inset-0 flex items-center justify-between pointer-events-none text-sm text-gray-500 tracking-wider">
                                <span class="w-1/2 text-center" id="referee-label">Referee Timer</span>
                                <span class="w-1/2 text-center" id="dashboard-label">Dashboard</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="username" bv>Username</label>
                    <input class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 placeholder:text-gray-400" id="username" name="username" type="text" value="{{ old('username') }}" required placeholder="Username">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
                    <div class="relative">
                        <input class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 placeholder:text-gray-400" id="password" name="password" type="password" required placeholder="Insert Your Password">
                        <svg class="absolute top-2.5 right-3 cursor-pointer lucide lucide-eye-icon lucide-eye" id="eyes-open" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="absolute top-2.5 right-3 cursor-pointer lucide lucide-eye-off-icon lucide-eye-off hidden" id="eyes-close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49" />
                            <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" />
                            <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143" />
                            <path d="m2 2 20 20" />
                        </svg>
                    </div>
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
                <div>
                    <button class="w-full bg-green-700 text-white py-2 px-4 rounded-xl hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" type="submit">
                        Sign In
                    </button>
                    <div class="text-center text-xs my-3 relative">
                        <span class="relative z-1 bg-white rounded-full p-1">OR</span>
                        <hr class="my-2 border-gray-300 absolute top-0 z-0 w-full">
                    </div>
                </div>
            </form>
            <form class="space-y-4" action="{{ route('authenticate-qr') }}" method="POST">
                @csrf
                <div>
                    <label class="w-full block text-center border border-green-700 text-green-700 py-2 px-4 rounded-xl hover:bg-green-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 cursor-pointer" type="button">
                        <input class="hidden" type="file" accept="image/*" capture="environment" onchange="this.form.submit()">
                        Scan / Upload QR Code
                    </label>
                </div>

                <footer class="bg-white mt-30 flex items-end">
                    <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <p class="text-center text-sm text-gray-400">
                            &copy; {{ date('Y') }} ALL RIGHTS RESERVED.
                        </p>
                    </div>
                </footer>
            </form>
        </div>
        <div class="hidden md:p-8 md:flex items-center justify-center p-2 overflow-hidden h-full">
            <img class="h-full object-contain w-full" src="{{ asset('img/login-banner-cp.png') }}" alt="">
        </div>
    </div>

    <script>
        function toggleLabel() {
            const checkbox = document.getElementById('section-toggle');
            const dashboardLabel = document.getElementById('dashboard-label');
            const refereeLabel = document.getElementById('referee-label');
            const dot = document.querySelector('.dot');

            if (checkbox.checked) {
                dashboardLabel.classList.add('text-gray-500');
                dashboardLabel.classList.remove('text-white');
                refereeLabel.classList.add('text-white');
                dot.style.transform = 'translateX(0)';
                dot.classList.remove('translate-x-full');
            } else {
                dashboardLabel.classList.add('text-white');
                dashboardLabel.classList.remove('text-gray-500');
                refereeLabel.classList.add('text-gray-500');
                dot.style.transform = 'translateX(95.5%)';
            }
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyesOpen = document.getElementById('eyes-open');
            const eyesClose = document.getElementById('eyes-close');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyesOpen.style.display = 'none';
                eyesClose.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyesOpen.style.display = 'block';
                eyesClose.style.display = 'none';
            }
        }

        document.getElementById('eyes-open').addEventListener('click', togglePasswordVisibility);
        document.getElementById('eyes-close').addEventListener('click', togglePasswordVisibility);

        toggleLabel();
    </script>
@endsection
