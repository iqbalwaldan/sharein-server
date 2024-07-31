@extends('client.layouts.main')

@section('container')
    <div class="flex flex-col md:flex-row">
        <div class="h-screen w-register-left mx-auto p-20 bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ asset('assets/images/register-bg.png') }}');">
            <div
                class="h-full bg-blue-500 bg-opacity-30 backdrop-blur-lg rounded-2xl border-[6px] border-[#5882C1] border-opacity-50">
                <div class="h-full flex flex-col items-center justify-center">
                    <div class="mx-auto w-[350px] h-[300px] 2xl:w-[502px] 2xl:h-[502px]">
                        {{-- {renderImages()} --}}
                        <img src="{{ asset('/assets/images/chat-image.png') }}" alt="Person Image" />
                    </div>
                    <p class="text-center font-extrabold text-3xl 2xl:text-5xl text-white px-14">
                        <span class="font-extrabold text-3xl 2xl:text-5xl text-[#F7B217]">
                            {{-- {slides[currentIndex].span}{" "} --}}
                            Simplify
                        </span>
                        {{-- {slides[currentIndex].text} --}}
                        Social Media Strategy with Sharein
                    </p>
                    <div class="flex mx-auto mt-16 2xl:mt-20 mb-6">
                        {{-- {renderDots()} --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="md:w-register-right h-screen">
            <div class="bg-white w-full h-full md:px-16 md:py-3 2xl:my-14">
                {{-- <x-logo-sharein /> --}}
                <div
                    class="w-10 h-10 2xl:w-16 2xl:h-16 bg-gradient-to-b from-primary-base to-secondary-base rounded-lg flex items-center justify-center mb-3 2xl:mb-6">
                    <div class="w-7 h-7 2xl:w-[50px] 2xl:h-[50px]">
                        <img src="/assets/images/logo-white.png" />
                    </div>
                </div>
                {{--  --}}
                <h1 class="font-bold text-2xl 2xl:text-4xl text-neutral-80 mb-1 2xl:mb-4">
                    Sign Up
                </h1>
                <p class="text-sm 2xl:text-base font-light text-neutral-70">
                    Welcome back! Please enter your details
                </p>
                <form method="POST" action="/register">
                    @csrf
                    <div class="w-full md:flex flex-row space-x-2 md:justify-between">
                        <div class="w-1/2 flex flex-col mt-3 2xl:mt-6">
                            <div class="flex mb-1">
                                <p class="font-normal text-xs 2xl:text-base text-neutral-70">
                                    First Name
                                </p>
                                <p class="font-normal text-xs 2xl:text-base text-error-base">
                                    *
                                </p>
                            </div>
                            <input
                                class="border border-[#CFCFCF] p-3 text-neutral-70 focus:outline-none w-full flex-grow h-11 rounded-md text-xs 2xl:text-base font-light"
                                type="text" name="first_name" placeholder="your first name" :value="old('first_name')"
                                required>
                            @error('first_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong class="text-red-500 text-sm font-normal">{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="w-1/2 flex flex-col mt-3 2xl:mt-6">
                            <div class="flex mb-1">
                                <p class="font-normal text-xs 2xl:text-base text-neutral-70">
                                    Last Name
                                </p>
                                <p class="font-normal text-xs 2xl:text-base text-error-base">
                                    *
                                </p>
                            </div>
                            <input
                                class="border border-[#CFCFCF] p-3 text-neutral-70 focus:outline-none w-full flex-grow h-11 rounded-md text-xs 2xl:text-base font-light"
                                type="text" name="last_name" placeholder="your last name" :value="old('last_name')"
                                required>
                            @error('last_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong class="text-red-500 text-sm font-normal">{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col mt-3 2xl:mt-6">
                        <div class="flex mb-1">
                            <p class="font-normal text-xs 2xl:text-base text-neutral-70">
                                Email
                            </p>
                            <p class="font-normal text-xs 2xl:text-base text-error-base">
                                *
                            </p>
                        </div>
                        <input
                            class="border border-[#CFCFCF] p-3 text-neutral-70 focus:outline-none w-full flex-grow h-11 rounded-md text-xs 2xl:text-base font-light"
                            type="email" name="email" placeholder="your email" :value="old('email')" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong class="text-red-500 text-sm font-normal">{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="flex flex-col mt-3 2xl:mt-6">
                        <div class="flex mb-1">
                            <p class="font-normal text-xs 2xl:text-base text-neutral-70">
                                Phone Number
                            </p>
                            <p class="font-normal text-xs 2xl:text-base text-error-base">
                                *
                            </p>
                        </div>
                        <div class="relative w-full flex">
                            <input
                                class="cursor-pointer border border-r-0 border-[#CFCFCF] p-3 text-neutral-70 focus:outline-none w-14 2xl:w-20 h-11 rounded-l-md text-xs 2xl:text-base font-light"
                                type="text" placeholder="+62" name="country_number"
                                value="{{ old('country_number') }}" />
                            <input
                                class="border border-[#CFCFCF] p-3 text-neutral-70 focus:outline-none w-full flex-grow h-11 rounded-r-md text-xs 2xl:text-base font-light"
                                type="text" name="phone_number" placeholder="your phone number"
                                value="{{ old('phone_number') }}" />
                        </div>
                        @error('phone_number')
                            <span class="invalid-feedback" role="alert">
                                <strong class="text-red-500 text-sm font-normal">{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="flex flex-col mt-3 2xl:mt-6">
                        <div class="flex mb-1">
                            <p class="font-normal text-xs 2xl:text-base text-neutral-70">
                                Password
                            </p>
                            <p class="font-normal text-xs 2xl:text-base text-error-base">
                                *
                            </p>
                        </div>
                        <div class="relative">
                            <input
                                class="border border-[#CFCFCF] p-3 text-neutral-70 focus:outline-none w-full flex-grow h-11 rounded-md text-xs 2xl:text-base font-light"
                                type="password" name="password" placeholder="your password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong class="text-red-500 text-sm font-normal">{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col mt-3 2xl:mt-6">
                        <div class="flex mb-1">
                            <p class="font-normal text-xs 2xl:text-base text-neutral-70">
                                Password Confirmation
                            </p>
                            <p class="font-normal text-xs 2xl:text-base text-error-base">
                                *
                            </p>
                        </div>
                        <div class="relative">
                            <input
                                class="border border-[#CFCFCF] p-3 text-neutral-70 focus:outline-none w-full flex-grow h-11 rounded-md text-xs 2xl:text-base font-light"
                                type="password" name="password_confirmation" placeholder="your password" required>
                        </div>
                    </div>

                    <button
                        class="bg-[#2652FF] w-full h-10 2xl:h-[51px] mt-5 2xl:mt-10 rounded-md py-2 px-4 text-base 2xl:text-xl font-semibold text-white"
                        type="submit">
                        Sign Up
                    </button>
                </form>
                <div class="flex mx-auto mt-2 2xl:mt-3 justify-center">
                    <p class="font-normal text-xs 2xl:text-base text-neutral-70">
                        Already have an account?&nbsp;
                    </p>
                    <a class="font-semibold text-xs 2xl:text-base text-primary-base" href="{{ route('user.login') }}">
                        Sign in
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
