@extends('layouts.app')

@section('page-title', 'Login')

@section('page-content')
    <div class="h-full w-full md:w-1/3 md:mx-auto flex items-start justify-center bg-white ">
        <div class="px-8 mt-40 w-full">
            <img class="w-60 h-auto mx-auto" src="{{ asset('img/login-success.png') }}" alt="">
            <h2 class="text-3xl text-center font-bold my-6">Sign In Success</h2>
            <p class="text-lg text-center mb-12">Sign in has been successful, now click continue to enter the golf timer.</p>

            <a href="{{ route('referee') }}" class="text-center w-full block mt-12 bg-green-700 text-white py-2 px-4 rounded-xl hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Continue
            </a>
        </div>
    </div>
@endsection
