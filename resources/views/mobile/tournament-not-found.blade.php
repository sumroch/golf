@extends('layouts.app')

@section('page-title', 'Tournament Not Found or Not Active')

@section('page-content')
    <div class="h-full w-full md:w-1/3 md:mx-auto flex items-start justify-center bg-white ">
        <div class="px-8 w-full">
            <div class="w-full bg-green-700 px-8 py-2 flex items-center justify-between">
                <div class="dropdown dropdown-right">
                    <img class="w-5 h-auto cursor-pointer" src="{{ asset('img/icon/bar.svg') }}" alt="bar menu" role="button" tabindex="0">
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-md ms-2" tabindex="-1">
                            <li>
                                <button class="w-full text-start" type="submit">Logout</button>
                            </li>
                        </ul>
                    </form>
                </div>
                <div>
                    <h2 class="text-xl text-center text-white m-0">{{ auth()->user()->name }}</h2>
                </div>
                <img class="w-7 h-auto cursor-pointer" src="{{ asset('img/icon/notification.svg') }}" alt="notification">
            </div>
            <img class="w-60 h-auto mx-auto mt-40" src="{{ asset('img/login-success.png') }}" alt="">
            <h2 class="text-3xl text-center font-bold my-6">Tournament <br> Not Found or Inactive</h2>
            <p class="text-lg text-center mb-12">Please check back later or contact support for assistance.</p>
        </div>
    </div>
@endsection
