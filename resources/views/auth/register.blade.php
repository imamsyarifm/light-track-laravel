@extends('layouts/blankLayout')

@section('title', 'Register Basic - Pages')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6 mx-4">
            <!-- Register Card -->
            <div class="card p-sm-7 p-2">
                <!-- Logo -->
                <div class="app-brand justify-content-center mt-5">
                    <a href="{{ url('/') }}" class="app-brand-link gap-3">
                        <img src="{{ asset('assets/img/logo/logo-light.png') }}" class="app-brand-logo w-25 mx-auto" /> 
                    </a>
                </div>
                <!-- /Logo -->
                <div class="card-body mt-1">
                    <h4 class="mb-1 app-brand-text demo text-heading fw-semibold text-center">Register</h4>

                    <form class="mb-5" method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="form-floating-outline mb-5 form-control-validation">
                            <label for="name">Username</label>
                            <input id="name" class="form-control block mt-1 w-full" placeholder="Enter your username" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="form-floating-outline mb-5 form-control-validation">
                            <label for="email">Email</label>
                            <input id="email" class="form-control block mt-1 w-full" placeholder="Enter your email" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-5 form-password-toggle form-control-validation">
                            <label for="password">Password</label>
                            <input id="password" class="form-control block mt-1 w-full"
                                            type="password"
                                            name="password"
                                            required autocomplete="new-password"
                                            placeholder="Enter your password" />

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-5 form-password-toggle form-control-validation">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" class="form-control block mt-1 w-full"
                                            type="password"
                                            name="password_confirmation" required autocomplete="new-password" 
                                            placeholder="Enter your confirm password"  />

                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button class="btn btn-primary d-grid w-100 mb-5">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>
                    <p class="text-center mb-5">
                        <span>Already have an account?</span>
                        <a href="{{ url('/login') }}">
                            <span>Sign in</span>
                        </a>
                    </p>

                </div>
            </div>
            <!-- Register Card -->
            {{-- <img src="{{ asset('assets/img/illustrations/tree-3.png') }}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block" />
            <img src="{{ asset('assets/img/illustrations/auth-basic-mask-light.png') }}" class="authentication-image d-none d-lg-block scaleX-n1-rtl" height="172" alt="triangle-bg" />
            <img src="{{ asset('assets/img/illustrations/tree.png') }}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block" /> --}}
        </div>
    </div>
</div>
@endsection

