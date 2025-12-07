@extends('layouts/blankLayout')

@section('title', 'Login Basic - Pages')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6 mx-4">
            <!-- Login -->
            <div class="card p-sm-7 p-2">
                <!-- Logo -->
                <div class="app-brand justify-content-center mt-5">
                    <a href="{{ url('/') }}" class="app-brand-link gap-3">
                        <img src="{{ asset('assets/img/logo/logo-light.png') }}" class="app-brand-logo w-25 mx-auto" /> 
                    </a>
                </div>
                <!-- /Logo -->

                <div class="card-body mt-1">
                    <h4 class="mb-1 text-center">sign-in to your account</h4>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="form-floating form-floating-outline mb-5 form-control-validation">
                            <input id="email" class="form-control block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="Enter your email" required autofocus autocomplete="username" />
                            <label for="email">Email or Username</label>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mb-5">
                            <div class="form-password-toggle form-control-validation">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password" class="form-control block w-full" name="password" placeholder="Enter your password" aria-describedby="password" required autocomplete="current-password" />
                                        <label for="password">Password</label>
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line icon-20px"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5 pb-2 d-flex justify-content-between pt-2 align-items-center">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="remember_me" />
                                <label class="form-check-label" for="remember_me"> Remember Me </label>
                            </div>

                            <a href="{{ url('/forgot-password') }}" class="float-end mb-1">
                                <span>Forgot Password?</span>
                            </a>
                        </div>
                        <div class="mb-5">
                            <button class="btn btn-primary d-grid w-100" type="submit">login</button>
                        </div>
                    </form>

                    <p class="text-center mb-5">
                        <a href="{{ url('/register') }}">
                            <span>Create an account</span>
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Login -->
            {{-- <img src="{{ asset('assets/img/illustrations/tree-3.png') }}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block" />
            <img src="{{ asset('assets/img/illustrations/auth-basic-mask-light.png') }}" class="authentication-image d-none d-lg-block scaleX-n1-rtl" height="172" alt="triangle-bg" />
            <img src="{{ asset('assets/img/illustrations/tree.png') }}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block" /> --}}
        </div>
    </div>
</div>
@endsection