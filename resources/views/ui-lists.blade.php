@extends('layouts.master')
@section('title') @lang('translation.lists') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Base UI @endslot
@slot('title') Lists @endslot
@endcomponent
<div class="row">
    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Default List</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>list-group</code> class in an unordered list and
                    <code>list-group-item</code> class to create a default list group.
                </p>
                <div>
                    <ul class="list-group">
                        <li class="list-group-item"><i class="ri-bill-line align-middle me-2"></i>
                            Send the billing agreement</li>
                        <li class="list-group-item"><i class="ri-file-copy-2-line align-middle me-2"></i>Send over all the
                            documentation.</li>
                        <li class="list-group-item"><i class="ri-question-answer-line align-middle me-2"></i>Meeting with
                            daron to review the intake form</li>
                        <li class="list-group-item"><i class="ri-secure-payment-line align-middle me-2"></i>Check uikings
                            theme and give customer support</li>
                    </ul>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Active Item</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>active</code> class to <code>list-group-item</code>
                    to indicate the current active selection.</p>
                <div>
                    <ul class="list-group">
                        <li class="list-group-item active" aria-current="true">Send the billing
                            agreement</li>
                        <li class="list-group-item">Send over all the documentation.</li>
                        <li class="list-group-item">Meeting with daron to review the intake form
                        </li>
                        <li class="list-group-item">Check uikings theme and give customer support
                        </li>
                        <li class="list-group-item">Start making a presentation</li>
                    </ul>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Disabled Items</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>disabled</code> class to
                    <code>list-group-item</code> to make it <em>appear</em> disabled.
                </p>
                <ul class="list-group">
                    <li class="list-group-item disabled" aria-disabled="true">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="build/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-2">
                                James Ballard
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="build/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-2">
                                Nancy Martino
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="build/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-2">
                                Henry Baird
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="build/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-2">
                                Erica Kernan
                            </div>
                        </div>
                    </li>
                </ul>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">List with Link</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>&lt;a&gt;</code> tag to create actionable list group
                    items with hover, disabled, and active states by adding list-group-item-action.
                </p>
                <div class="list-group list-group-fill-success">
                    <a href="#" class="list-group-item list-group-item-action active"><i class="ri-download-2-fill align-middle me-2"></i>Category Download</a>
                    <a href="#" class="list-group-item list-group-item-action"><i class="ri-shield-check-line align-middle me-2"></i>Security Access</a>
                    <a href="#" class="list-group-item list-group-item-action"><i class="ri-database-2-line align-middle me-2"></i>Storage folder</a>
                    <a href="#" class="list-group-item list-group-item-action"><i class="ri-notification-3-line align-middle me-2"></i>Push
                        Notification</a>
                    <a href="#" class="list-group-item list-group-item-action disabled" tabindex="-1"><i class="ri-moon-fill align-middle me-2"></i>Dark Mode</a>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">List with Button</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>&lt;button&gt;</code> tag to create actionable list
                    group items with hover, disabled, and active states by adding
                    list-group-item-action.</p>
                <div class="list-group">
                    <button type="button" class="list-group-item list-group-item-action active" aria-current="true"><i class="ri-reply-fill align-middle me-2"></i>Reply</button>
                    <button type="button" class="list-group-item list-group-item-action"><i class="ri-share-forward-fill align-middle me-2"></i>Forward
                        Message</button>
                    <button type="button" class="list-group-item list-group-item-action"><i class="ri-equalizer-line align-middle me-2"></i>Filter Message</button>
                    <button type="button" class="list-group-item list-group-item-action"><i class="ri-delete-bin-5-line align-middle me-2"></i>Delete
                        Message</button>
                    <button type="button" class="list-group-item list-group-item-action" disabled><i class="ri-forbid-line align-middle me-2"></i>Block "Mark
                        Spencer</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Flush List</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>list-group-flush</code> class to remove some borders
                    and rounded corners to render list group items.</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Send the billing agreement</li>
                    <li class="list-group-item">Send over all the documentation.</li>
                    <li class="list-group-item">Meeting with daron to review the intake form</li>
                    <li class="list-group-item">Check uikings theme and give customer support</li>
                    <li class="list-group-item">Start making a presentation</li>
                </ul>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Horizontal List</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>list-group-horizontal</code> class to change the
                    layout of list group items from vertical to horizontal across all breakpoints.
                </p>
                <div>
                    <ul class="list-group list-group-horizontal-md mb-3">
                        <li class="list-group-item">Inbox</li>
                        <li class="list-group-item">Work</li>
                        <li class="list-group-item">Shopping</li>
                    </ul>
                    <ul class="list-group list-group-horizontal-md justify-content-center mb-3">
                        <li class="list-group-item">Inbox</li>
                        <li class="list-group-item">Work</li>
                        <li class="list-group-item">Shopping</li>
                    </ul>
                    <ul class="list-group list-group-horizontal-md justify-content-end">
                        <li class="list-group-item">Inbox</li>
                        <li class="list-group-item">Work</li>
                        <li class="list-group-item">Shopping</li>
                    </ul>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Contextual Classes</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use contextual classes to style list items with a stateful
                    background and color.</p>
                <ul class="list-group">
                    <li class="list-group-item">Dapibus ac facilisis in</li>
                    <li class="list-group-item list-group-item-primary">A simple primary list group
                        item</li>
                    <li class="list-group-item list-group-item-secondary">A simple secondary list
                        group item</li>
                    <li class="list-group-item list-group-item-success">A simple success list group
                        item</li>
                    <li class="list-group-item list-group-item-danger">A simple danger list group
                        item</li>
                    <li class="list-group-item list-group-item-warning">A simple warning list group
                        item</li>
                    <li class="list-group-item list-group-item-info">A simple info list group item
                    </li>
                    <li class="list-group-item list-group-item-light text-body">A simple light list group item
                    </li>
                    <li class="list-group-item list-group-item-dark text-body">A simple dark list group item
                    </li>
                </ul>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Contextual Classes with Link</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Contextual classes also work with
                    <code>.list-group-item-action</code>. Note the addition of the hover styles here
                    not present in the previous example.
                </p>
                <div class="live-preview">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">A simple default
                            list group item</a>
                        <a href="#" class="list-group-item list-group-item-action list-group-item-primary">A
                            simple primary list group item</a>
                        <a href="#" class="list-group-item list-group-item-action list-group-item-secondary">A
                            simple secondary list group item</a>
                        <a href="#" class="list-group-item list-group-item-action list-group-item-success">A
                            simple success list group item</a>
                        <a href="#" class="list-group-item list-group-item-action list-group-item-danger">A
                            simple danger list group item</a>
                        <a href="#" class="list-group-item list-group-item-action list-group-item-warning">A
                            simple warning list group item</a>
                        <a href="#" class="list-group-item list-group-item-action list-group-item-info">A
                            simple info list group item</a>
                        <a href="#" class="list-group-item list-group-item-action list-group-item-light text-body">A
                            simple light list group item</a>
                        <a href="#" class="list-group-item list-group-item-action list-group-item-dark text-body">A
                            simple dark list group item</a>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Custom Content</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Add nearly any HTML within, even for linked list groups like
                    the one below, with the help of <a href="https://getbootstrap.com/docs/5.1/utilities/flex/">flexbox
                        utilities</a>.</p>
                <div class="list-group">
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action active">
                        <div class="float-end">
                            Pending
                        </div>
                        <div class="d-flex mb-2 align-items-center">
                            <div class="flex-shrink-0">
                                <img src="build/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="list-title fs-base mb-1">Charlie Pritchard</h5>
                                <p class="list-text mb-0 fs-xs">12 min Ago</p>
                            </div>
                        </div>
                        <p class="list-text mb-0">They all have something to say beyond the words on
                            the page. They can come across as casual or neutral, exotic or graphic.
                            That's why it's important to think about your message, then choose a
                            font that fits. Cosby sweater eu banh mi, qui irure terry richardson ex
                            squid.</p>
                    </a>
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                        <div class="float-end">
                            Pending
                        </div>
                        <div class="d-flex mb-2 align-items-center">
                            <div class="flex-shrink-0">
                                <img src="build/images/users/avatar-2.jpg" alt="" class="avatar-sm rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="list-title fs-base mb-1">Dominic Charlton</h5>
                                <p class="list-text mb-0 fs-xs">12 min Ago</p>
                            </div>
                        </div>
                        <p class="list-text mb-0">Trust fund seitan letterpress, keytar raw denim
                            keffiyeh etsy art party before they sold out master cleanse gluten-free
                            squid scenester freegan cosby sweater. Fanny pack portland seitan DIY,
                            art party locavore wolf cliche high life echo park Austin.</p>
                    </a>
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                        <div class="float-end">
                            Rejected
                        </div>
                        <div class="d-flex mb-2 align-items-center">
                            <div class="flex-shrink-0">
                                <img src="build/images/users/avatar-3.jpg" alt="" class="avatar-sm rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="list-title fs-base mb-1">Declan Long</h5>
                                <p class="list-text mb-0 fs-xs">12 min Ago</p>
                            </div>
                        </div>
                        <p class="list-text mb-0">Big July earthquakes confound zany experimental
                            vow. My girl wove six dozen plaid jackets before she quit. Six big
                            devils from Japan quickly forgot how to waltz. try again until it looks
                            right, and each assumenda labore aes Homo nostrud organic, assumenda
                            labore aesthetic magna elements, buttons, everything.</p>
                    </a>
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                        <div class="float-end">
                            Successful
                        </div>
                        <div class="d-flex mb-2 align-items-center">
                            <div class="flex-shrink-0">
                                <img src="build/images/users/avatar-4.jpg" alt="" class="avatar-sm rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="list-title fs-base mb-1">Angela Bernier</h5>
                                <p class="list-text mb-0 fs-xs">5 days Ago</p>
                            </div>
                        </div>
                        <p class="list-text mb-0">Just like in the image where we talked about using
                            multiple fonts, you can see that the background in this graphic design
                            is blurred. Whenever you put text on top of an image, it’s important
                            that your viewers can understand the text, and sometimes that means
                            applying a gaussian readable.</p>
                    </a>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!--end row-->

<div class="row">
    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">List with Badges</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use badges to any list group item to show unread counts,
                    activity, and more with the help of some <a href="https://getbootstrap.com/docs/5.1/utilities/">utilities</a>.</p>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Send the billing agreement <span class="badge bg-success">High</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Send over all the documentation
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Meeting with daron to review the intake form <span class="badge bg-danger">Low</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Check uikings theme and give customer support <span class="badge bg-secondary">Medium</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Start making a presentation <span class="badge bg-success">High</span>
                    </li>
                </ul>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">List with Checkboxs</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use Bootstrap’s checkboxes within list group items and
                    customize as needed. </p>
                <div class="list-group">
                    <label class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" value="">
                        Declined Payment
                    </label>
                    <label class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" value="" checked>
                        Delivery Error
                    </label>
                    <label class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" value="" checked>
                        Wrong Amount
                    </label>
                    <label class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" value="">
                        Wrong Address
                    </label>
                    <label class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" value="">
                        Wrong UX/UI Solution
                    </label>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">List with Radios</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use Bootstrap’s radios within list group items and customize
                    as needed. </p>
                <div class="list-group">
                    <label class="list-group-item">
                        <input class="form-check-input fs-md mt-0 align-middle me-1" name="exampleRadios" type="radio" value="">
                        Declined Payment
                    </label>
                    <label class="list-group-item">
                        <input class="form-check-input fs-md mt-0 align-middle me-1" name="exampleRadios" type="radio" value="" checked>
                        Delivery Error
                    </label>
                    <label class="list-group-item">
                        <input class="form-check-input fs-md mt-0 align-middle me-1" name="exampleRadios" type="radio" value="">
                        Wrong Amount
                    </label>
                    <label class="list-group-item">
                        <input class="form-check-input fs-md mt-0 align-middle me-1" name="exampleRadios" type="radio" value="">
                        Wrong Address
                    </label>
                    <label class="list-group-item">
                        <input class="form-check-input fs-md mt-0 align-middle me-1" name="exampleRadios" type="radio" value="">
                        Wrong UX/UI Solution
                    </label>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">List with Icon</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use icons to any list group item to show icons to list group
                    items.</p>
                <ul class="list-group">
                    <li class="list-group-item"><i class="ti ti-checkbox align-middle lh-1 me-2"></i> Send the billing
                        agreement</li>
                    <li class="list-group-item"><i class="ti ti-checkbox align-middle lh-1 me-2"></i> Send over all the
                        documentation.</li>
                    <li class="list-group-item"><i class="ti ti-checkbox align-middle lh-1 me-2"></i> Meeting with
                        daron to review the intake form</li>
                    <li class="list-group-item"><i class="ti ti-checkbox align-middle lh-1 me-2"></i> Check uikings
                        theme and give customer support</li>
                    <li class="list-group-item"><i class="ti ti-checkbox align-middle lh-1 me-2"></i> Start making a
                        presentation</li>
                </ul>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">List with Numbered</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>list-group-numbered</code> class (optionally use an
                    <code>&lt;ol&gt;</code> element) to show numbered list group items.
                </p>
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Send the billing agreement</li>
                    <li class="list-group-item">Send over all the documentation.</li>
                    <li class="list-group-item">Meeting with daron to review the intake form</li>
                    <li class="list-group-item">Check uikings theme and give customer support</li>
                    <li class="list-group-item">Start making a presentation</li>
                </ol>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Custom Content Lists</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p>Add nearly any HTML within, even for linked list groups like the one below, with
                    the help of <a href="https://getbootstrap.com/docs/5.1/utilities/flex/">flexbox
                        utilities</a>.</p>
                <div data-simplebar style="max-height: 215px;">
                    <ul class="list-group mb-1">
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xs">
                                            <div class="avatar-title bg-danger-subtle text-danger rounded">
                                                <i class="ri-netflix-fill"></i>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <h6 class="fs-md mb-0">Netfilx</h6>
                                            <small class="text-muted">2 min Ago</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="text-danger">-$25.50</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xs">
                                            <div class="avatar-title bg-success-subtle text-success rounded">
                                                <i class="ri-spotify-fill"></i>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <h6 class="fs-md mb-0">Spotify</h6>
                                            <small class="text-muted">5 days Ago</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="text-success">$48.25</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="build/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded">
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <h6 class="fs-md mb-0">Emily Slater</h6>
                                            <small class="text-muted">8 days Ago</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="text-success">$354.90</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xs">
                                            <div class="avatar-title bg-secondary-subtle text-secondary rounded">
                                                <i class="ri-paypal-fill"></i>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <h6 class="fs-md mb-0">Paypal</h6>
                                            <small class="text-muted">1 month Ago</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="text-danger">-$12.22</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="build/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded">
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <h6 class="fs-md mb-0">Harvey Wells</h6>
                                            <small class="text-muted">4 month Ago</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="text-success">-$459.78</span>
                                </div>
                            </div>
                        </li>
                    </ul>
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
