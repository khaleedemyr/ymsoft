@extends('layouts.master-without-nav')
@section('title')
@lang('translation.signup')
@endsection
@section('content')
    <section class="auth-page-wrapper py-5 position-relative bg-light d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="row g-0 align-items-center">
                                <div class="col-xxl-6 mx-auto">
                                    <div class="card mb-0 border-0">
                                        <div class="card-body p-sm-5 m-lg-4">
                                            <div class="text-center mt-2">
                                                <h5 class="fs-3xl">Get Started</h5>
                                                <p class="text-muted">Get your free Vixon account now</p>
                                            </div>
                                            <div class="p-2 mt-5">
                                                <form class="needs-validation" novalidate action="index">
    
                                                    <div class="mb-3">
                                                        <div class="input-group">
                                                            <span class="input-group-text" id="basic-addon"><i class="ri-mail-line"></i></span>
                                                            <input type="email" class="form-control" id="useremail" placeholder="Enter email address" required>
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            Please enter email
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="input-group">
                                                            <span class="input-group-text" id="basic-addon"><i class="ri-user-3-line"></i></span>
                                                            <input type="text" class="form-control" id="username" placeholder="Enter username">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="position-relative auth-pass-inputgroup overflow-hidden">
                                                            <div class="input-group">
                                                                <span class="input-group-text" id="basic-addon1"><i class="ri-lock-2-line"></i></span>
                                                                <input type="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password-input">
                                                            </div>
                                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                        </div>
                                                    </div>
    
                                                    <div class="mb-4">
                                                        <p class="mb-0 fs-sm text-muted fst-italic">By registering you agree to the Vixon <a href="pages-term-conditions" class="text-primary text-decoration-underline fst-normal fw-medium">Terms of Use</a></p>
                                                    </div>
    
                                                    <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                        <h5 class="fs-md">Password must contain:</h5>
                                                        <p id="pass-length" class="invalid fs-sm mb-2">Minimum <b>8 characters</b></p>
                                                        <p id="pass-lower" class="invalid fs-sm mb-2">At <b>lowercase</b> letter (a-z)</p>
                                                        <p id="pass-upper" class="invalid fs-sm mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                        <p id="pass-number" class="invalid fs-sm mb-0">A least <b>number</b> (0-9)</p>
                                                    </div>
    
                                                    <div class="mt-4">
                                                        <button class="btn btn-primary w-100" type="submit">Sign Up</button>
                                                    </div>
    
                                                    <div class="mt-4 text-center">
                                                        <div class="signin-other-title position-relative">
                                                            <h5 class="fs-sm mb-4 title text-muted">Create account with</h5>
                                                        </div>
    
                                                        <div>
                                                            <button type="button" class="btn btn-subtle-primary btn-icon "><i class="ri-facebook-fill fs-lg"></i></button>
                                                            <button type="button" class="btn btn-subtle-danger btn-icon "><i class="ri-google-fill fs-lg"></i></button>
                                                            <button type="button" class="btn btn-subtle-dark btn-icon "><i class="ri-github-fill fs-lg"></i></button>
                                                            <button type="button" class="btn btn-subtle-info btn-icon "><i class="ri-twitter-fill fs-lg"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="mt-4 text-center">
                                                <p class="mb-1">Already have an account ? </p>
                                                <a href="auth-signin" class="text-secondary text-decoration-underline"> Sign In </a>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                                <div class="col-xxl-5">
                                    <div class="card auth-card bg-secondary h-100 border-0 shadow-none d-none d-sm-block mb-0">
                                        <div class="card-body py-5 d-flex justify-content-between flex-column h-100">
                                            <div class="text-center">
                                                <h5 class="text-white">Welcome to your Vixon.</h5>
                                                <p class="text-white opacity-75">It brings together your tasks, projects, timelines, files and more</p>
                                            </div>
    
                                            <div class="auth-effect-main my-5 position-relative rounded-circle d-flex align-items-center justify-content-center mx-auto">
                                                <div class="auth-user-list list-unstyled">
                                                    <img src="build/images/auth/signin.png" alt="" class="img-fluid">
                                                </div>
                                            </div>
    
                                            <div class="text-center">
                                                <p class="text-white opacity-75 mb-0 mt-3">
                                                    &copy;
                                                    <script>document.write(new Date().getFullYear())</script> Vixon. Crafted with <i class="ti ti-heart-filled text-danger"></i> by Themesbrand
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end container-->
    </section>

@endsection
@section('script')
<!-- password create init js-->
<script src="{{ URL::asset('build/js/pages/passowrd-create.init.js') }}"></script>
@endsection