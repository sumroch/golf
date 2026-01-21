@extends('layouts.app')

@section('page-title', 'Tournament Not Found or Not Active')

@section('page-content')
    <div class="h-full w-full md:w-1/3 md:mx-auto flex items-start justify-center bg-white ">
        <div class="px-8 mt-40 w-full">
            <img class="w-60 h-auto mx-auto" src="{{ asset('img/login-success.png') }}" alt="">
            <h2 class="text-3xl text-center font-bold my-6">Tournament <br> Not Found or Inactive</h2>
            <p class="text-lg text-center mb-12">Please check back later or contact support for assistance.</p>
        </div>
    </div>
@endsection
