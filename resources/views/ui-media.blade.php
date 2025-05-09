@extends('layouts.master')
@section('title') @lang('translation.media-object') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Pages @endslot
@slot('title') Media Object @endslot
@endcomponent

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Examples</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted mb-4">Use few flex utilities that allow even more flexibility
                    and customization than before.</p>
                <div>
                    <div class="d-flex align-items-start text-muted mb-4">
                        <div class="flex-shrink-0 me-3">
                            <img src="build/images/users/avatar-2.jpg" class="avatar-sm rounded" alt="...">
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="fs-md">Media heading</h5>
                            This is some content from a media component. You can replace this with
                            any content and adjust it as needed.
                        </div>
                    </div>
                    <div class="d-flex align-items-start text-muted mb-4">
                        <div class="flex-grow-1">
                            <h5 class="fs-md">Media heading</h5>
                            This is some content from a media component. You can replace this with
                            any content and adjust it as needed.
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <img src="build/images/users/avatar-3.jpg" class="avatar-sm rounded" alt="...">
                        </div>
                    </div>
                    <div class="d-flex align-items-start text-muted">
                        <div class="flex-shrink-0 me-3">
                            <img src="build/images/users/avatar-2.jpg" class="avatar-sm rounded" alt="...">
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-md">Media heading</h5>
                            This is some content from a media component. You can replace this with
                            any content and adjust it as needed.
                        </div>
                    </div>
                </div>

            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Nesting Example</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted mb-4">Media objects can be infinitely nested, though we suggest
                    you stop at some point. Place nested <code>d-flex align-items-start</code>
                    within the <code>flex-grow-1</code> of a parent media object.</p>
                <div>
                    <div class="d-flex align-items-start text-muted mb-4">
                        <div class="flex-shrink-0 me-3">
                            <img src="build/images/users/avatar-2.jpg" class="avatar-sm rounded" alt="...">
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-md">Media heading</h5>
                            This is some content from a media component. You can replace this with
                            any content and adjust it as needed.
                            <div class="d-flex align-items-start text-muted mt-3">
                                <div class="flex-shrink-0 me-3">
                                    <img src="build/images/users/avatar-3.jpg" class="avatar-sm rounded" alt="...">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-md">Media heading</h5>
                                    This is some content from a media component. You can replace
                                    this with any content and adjust it as needed.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-start text-muted">
                        <div class="flex-shrink-0 me-3">
                            <img src="build/images/users/avatar-4.jpg" class="avatar-sm rounded" alt="...">
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-md">Media heading</h5>
                            This is some content from a media component. You can replace this with
                            any content and adjust it as needed.
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Media Alignment</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted mb-4">The images or other media can be aligned top, middle, or
                    bottom. The default is top aligned.</p>
                <div>
                    <div class="d-flex align-items-start text-muted mb-4">
                        <div class="flex-shrink-0 me-3">
                            <img src="build/images/users/avatar-2.jpg" class="avatar-sm rounded" alt="...">
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="fs-md">Top Aligned media</h5>
                            <p class="mb-1">Cras sit amet nibh libero, in gravida nulla. Nulla vel
                                metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in
                                vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                                vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                            <p class="mb-0">Donec sed odio dui. Nullam quis risus eget urna mollis
                                ornare vel eu leo. Cum sociis natoque penatibus et magnis dis
                                parturient montes, nascetur ridiculus mus.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-muted mb-4">
                        <div class="flex-shrink-0 me-3">
                            <img src="build/images/users/avatar-6.jpg" class="avatar-sm rounded" alt="...">
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="fs-md">Center Aligned media</h5>
                            <p class="mb-1">Cras sit amet nibh libero, in gravida nulla. Nulla vel
                                metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in
                                vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                                vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                            <p class="mb-0">Donec sed odio dui. Nullam quis risus eget urna mollis
                                ornare vel eu leo. Cum sociis natoque penatibus et magnis dis
                                parturient montes, nascetur ridiculus mus.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end text-muted">
                        <div class="flex-shrink-0 me-3">
                            <img src="build/images/users/avatar-8.jpg" class="avatar-sm rounded" alt="...">
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-md">Bottom Aligned media</h5>
                            <p class="mb-1">Cras sit amet nibh libero, in gravida nulla. Nulla vel
                                metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in
                                vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                                vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                            <p class="mb-0">Donec sed odio dui. Nullam quis risus eget urna mollis
                                ornare vel eu leo. Cum sociis natoque penatibus et magnis dis
                                parturient montes, nascetur ridiculus mus.</p>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
