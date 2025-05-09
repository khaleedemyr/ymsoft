@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.password-reset')
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
                                    <div class="card mb-0 border-0 shadow-none mb-0">
                                        <div class="card-body p-sm-5 m-lg-4">
                                            <div class="text-center mt-2">
                                                <h5 class="fs-3xl">Forgot Password?</h5>
                                                <p class="text-muted mb-4">Reset password with Vixon</p>
                                                <div class="pb-4">
                                                    <img src="build/images/auth/email.png" alt="" class="img-fluid">
                                                </div>
                                            </div>

                                            <div class="alert border-0 alert-info text-center mb-2 mx-2" role="alert">
                                                Enter your email and instructions will be sent to you!
                                            </div>
                                            <div class="p-2">
                                                <form>
                                                    <div class="mb-4">
                                                        <div class="input-group">
                                                            <span class="input-group-text" id="basic-addon"><i class="ri-mail-line"></i></span>
                                                            <input type="email" class="form-control" id="useremail" placeholder="Enter email address" required>
                                                        </div>
                                                    </div>

                                                    <div class="text-center mt-4">
                                                        <button class="btn btn-primary w-100" type="submit">Send Reset Link</button>
                                                    </div>
                                                </form><!-- end form -->
                                            </div>
                                            <div class="mt-4 text-center">
                                                <p class="mb-1">Wait, I remember my password...</p>
                                                <a href="auth-signin" class="text-secondary text-decoration-underline"> Click here </a>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                                <div class="col-xxl-5">
                                    <div class="card auth-card bg-secondary h-100 border-0 shadow-none d-none d-sm-block mb-0">
                                        <div class="card-body py-5 d-flex justify-content-between flex-column h-100">
                                            <div class="text-center">
                                                <h5 class="text-white">Welcome to Vixon.</h5>
                                                <p class="text-white opacity-75 fs-base">It brings together your tasks, projects, timelines, files and more</p>
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