@extends('layouts.master')
@section('title') @lang('translation.modals') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Base UI @endslot
@slot('title') Modals @endslot
@endcomponent


<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Default Modal</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted text-muted">Use <code>modal-dialog</code> class to show default
                    modal.</p>
                <div>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#myModal">Standard Modal</button>
                    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">Modal Heading</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fs-base">
                                        Overflowing text to show scroll behavior
                                    </h5>
                                    <p class="text-muted">One morning, when Gregor Samsa woke from
                                        troubled dreams, he found himself transformed in his bed
                                        into a horrible vermin. He lay on his armour-like back, and
                                        if he lifted his head a little he could see his brown belly,
                                        slightly domed and divided by arches into stiff sections.
                                    </p>
                                    <p class="text-muted">The bedding was hardly able to cover it
                                        and seemed ready to slide off any moment. His many legs,
                                        pitifully thin compared with the size of the rest of him,
                                        waved about helplessly as he looked. "What's happened to
                                        me?" he thought.</p>
                                    <p class="text-muted">It wasn't a dream. His room, a proper
                                        human room although a little too small, lay peacefully
                                        between its four familiar walls.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary ">Save
                                        Changes</button>
                                </div>

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Vertically Centered Modal</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>modal-dialog-centered</code> class to show
                    vertically center the modal.</p>
                <div>
                    <!-- center modal -->
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">Center Modal</button>
                    <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <i class="bi bi-exclamation-triangle text-warning display-5"></i>
                                    <div class="mt-4">
                                        <h4 class="mb-3">Oops something went wrong!</h4>
                                        <p class="text-muted mb-4"> The transfer was not
                                            successfully received by us. the email of the recipient
                                            wasn't correct.</p>
                                        <div class="hstack gap-2 justify-content-center">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <a href="javascript:void(0);" class="btn btn-danger">Try
                                                Again</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>
<!---end row-->

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Grids in Modals</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>container-fluid</code> class within the modal-body
                    to utilize the Bootstrap grid system within a modal by nesting.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalgrid">
                    Launch Demo Modal
                </button>
                <div class="modal fade" id="exampleModalgrid" tabindex="-1" aria-labelledby="exampleModalgridLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalgridLabel">Grid Modals</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:void(0);">
                                    <div class="row g-3">
                                        <div class="col-xxl-6">
                                            <div>
                                                <label for="firstName" class="form-label">First
                                                    Name</label>
                                                <input type="text" class="form-control" id="firstName" placeholder="Enter firstname">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-xxl-6">
                                            <div>
                                                <label for="lastName" class="form-label">Last
                                                    Name</label>
                                                <input type="text" class="form-control" id="lastName" placeholder="Enter lastname">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-12">
                                            <label class="form-label">Gender</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                                    <label class="form-check-label" for="inlineRadio1">Male</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                                    <label class="form-check-label" for="inlineRadio2">Female</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
                                                    <label class="form-check-label" for="inlineRadio3">Others</label>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-xxl-6">
                                            <label for="emailInput" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="emailInput" placeholder="Enter your email">
                                        </div>
                                        <!--end col-->
                                        <div class="col-xxl-6">
                                            <label for="passwordInput" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="passwordInput" value="451326546">
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Static Backdrop Modal</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>data-bs-backdrop="static"</code> to modal not to
                    close when clicking outside the modal.</p>
                <div>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        Static Backdrop Modal
                    </button>
                    <!-- staticBackdrop Modal -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <i class="ph ph-confetti display-5 text-success"></i>
                                    <div class="mt-4">
                                        <h4 class="mb-3">You've made it!</h4>
                                        <p class="text-muted mb-4"> The transfer was not
                                            successfully received by us. the email of the recipient
                                            wasn't correct.</p>
                                        <div class="hstack gap-2 justify-content-center">
                                            <a href="javascript:void(0);" class="btn btn-link link-danger fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>
                                                Close</a>
                                            <a href="javascript:void(0);" class="btn btn-success">Completed</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Toggle Between Modal</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Toggle between multiple modals with some clever placement of
                    the <code>data-bs-target</code> and <code>data-bs-toggle</code> attributes.
                    Please note multiple modals cannot be open at the same time. this method simply
                    toggles between two separate modals.</p>
                <div>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal">Open First Modal</button>
                    <!-- First modal dialog -->
                    <div class="modal fade" id="firstmodal" aria-hidden="true" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <i class="bi bi-exclamation-triangle text-warning display-5"></i>
                                    <div class="mt-4 pt-4">
                                        <h4>Uh oh, something went wrong!</h4>
                                        <p class="text-muted"> The transfer was not successfully
                                            received by us. the email of the recipient wasn't
                                            correct.</p>
                                        <!-- Toogle to second dialog -->
                                        <button class="btn btn-warning" data-bs-target="#secondmodal" data-bs-toggle="modal" data-bs-dismiss="modal">
                                            Continue
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Second modal dialog -->
                    <div class="modal fade" id="secondmodal" aria-hidden="true" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <i class="bi bi-people text-primary display-5"></i>
                                    <div class="mt-4 pt-3">
                                        <h4 class="mb-3">Follow-Up Email</h4>
                                        <p class="text-muted mb-4">Hide this modal and show the
                                            first with the button below Automatically Send your
                                            invitees a follow -Up email.</p>
                                        <div class="hstack gap-2 justify-content-center">
                                            <!-- Toogle to first dialog, `data-bs-dismiss` attribute can be omitted - clicking on link will close dialog anyway -->
                                            <button class="btn btn-subtle-danger" data-bs-target="#firstmodal" data-bs-toggle="modal" data-bs-dismiss="modal">Back to
                                                First
                                            </button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Tooltips and Popovers </h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted"><a href="https://getbootstrap.com/docs/5.1/components/tooltips/">Tooltips</a>
                    and <a href="https://getbootstrap.com/docs/5.1/components/popovers/">Popovers</a>
                    can be placed within modals as needed.When modals are closed, any tooltips and
                    popovers within are also automatically dismissed.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalPopovers">
                    Launch Demo Modal
                </button>
                <!-- tooltips and popovers modal -->
                <div class="modal fade" id="exampleModalPopovers" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Tooltips and Popovers
                                    Modal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h5 class="fs-lg">Popover in a Modal</h5>
                                <p class="text-muted">You only need to know a little to make a big
                                    <a href="#" class="popover-test fw-medium text-decoration-underline link-success" data-bs-toggle="popover" title="Common Types of Fonts" data-bs-content="They're a good choice for more traditional projects." data-bs-container="body" data-bs-placement="bottom" data-bs-original-title="Popover Title">Popover on Click </a>
                                    you do every day. So let's get started. First, some common types
                                    of fonts and what you need to know about them triggers a popover
                                    on click.
                                </p>
                                <h5 class="fs-lg">Tooltips in a Modal</h5>
                                <p class="text-muted">Opposites attract, and that’s a fact. It’s in
                                    our <a href="#" class="tooltip-test text-decoration-underline fw-medium" title="Morton Bayer" data-bs-container="#exampleModalPopovers" data-bs-toggle="tooltip" data-bs-original-title="Tooltip title">graphic design</a> to
                                    be interested in the unusual, and that’s why using <a href="#" class="tooltip-test text-decoration-underline" title="Web Developers" data-bs-toggle="tooltip" data-bs-container="#exampleModalPopovers" data-bs-original-title="Tooltip title">design</a>
                                    contrasting colors in Graphic Design is a must.</p>
                            </div>
                            <div class="modal-footer">
                                <div class="mx-auto">
                                    <a href="javascript:void(0);" class="btn btn-link fw-medium">Read More <i class="ri-arrow-right-line ms-1 align-middle"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Scrollable Modal</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <p class="text-muted">Use<code> modal-dialog-scrollable</code> class to create a
                    modal scrollable.</p>

                <div>
                    <!-- Scrollable modal -->
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#exampleModalScrollable">Scrollable Modal</button>

                    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">
                                        Scrollable
                                        Modal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">You've probably heard that
                                                opposites attract. The same is true for fonts. Don't
                                                be afraid to combine font styles that are different
                                                but complementary, like sans serif with serif, short
                                                with tall, or decorative with simple. Qui photo
                                                booth letterpress, commodo enim craft beer mlkshk
                                                aliquip jean shorts ullamco ad vinyl cillum PBR.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <h6 class="fs-lg my-3">Graphic Design</h6>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Opposites attract, and that’s
                                                a fact. It’s in our nature to be interested in the
                                                unusual, and that’s why using contrasting colors in
                                                Graphic Design is a must. It’s eye-catching, it
                                                makes a statement, it’s impressive graphic design.
                                                Increase or decrease the letter spacing depending.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Trust fund seitan
                                                letterpress, keytar raw denim keffiyeh etsy art
                                                party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Just like in the image where
                                                we talked about using multiple fonts, you can see
                                                that the background in this graphic design is
                                                blurred. Whenever you put text on top of an image,
                                                it’s important that your viewers can understand.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Keytar raw denim keffiyeh
                                                etsy art party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save
                                        changes</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Varying Modal Content</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <p class="text-muted">Use <code>event.relatedTarget</code> and HTML
                    <code>data-bs-*</code> attributes to vary the contents of the modal depending on
                    which button was clicked.
                </p>

                <div>
                    <div class="hstack gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#varyingcontentModal" data-bs-whatever="Mary">Open Modal
                            for Mary</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#varyingcontentModal" data-bs-whatever="Jennifer">Open
                            Modal for Jennifer</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#varyingcontentModal" data-bs-whatever="Roderick">Open
                            Modal for Roderick</button>
                    </div>

                    <!-- Varying modal content -->
                    <div class="modal fade" id="varyingcontentModal" tabindex="-1" aria-labelledby="varyingcontentModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="varyingcontentModalLabel">New
                                        message</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="mb-3">
                                            <label for="customer-name" class="col-form-label">Customer Name:</label>
                                            <input type="text" class="form-control" id="customer-name">
                                        </div>
                                        <div class="mb-3">
                                            <label for="message-text" class="col-form-label">Message:</label>
                                            <textarea class="form-control" id="message-text" rows="4"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Back</button>
                                    <button type="button" class="btn btn-primary">Send
                                        message</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->

</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Optional Sizes</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use <code>modal-fullscreen</code>, <code>modal-xl</code>,
                    <code>modal-lg</code>, or <code>modal-sm</code> class to modal-dialog class to
                    set different size modal respectively.
                </p>
                <div>
                    <div class="hstack flex-wrap gap-2">
                        <!-- Fullscreen modal -->
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target=".exampleModalFullscreen">Fullscreen Modal</button>

                        <!-- Extra Large modal -->
                        <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl">Extra large Modal</button>

                        <!-- Large modal -->
                        <button type="button" class="btn btn-success " data-bs-toggle="modal" data-bs-target=".bs-example-modal-lg">Large Modal</button>

                        <!-- Small modal -->
                        <button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target=".bs-example-modal-sm">Small Modal</button>
                    </div>

                    <!-- Full screen modal content -->
                    <div class="modal fade exampleModalFullscreen" tabindex="-1" aria-labelledby="exampleModalFullscreenLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenLabel">
                                        Fullscreen
                                        Modal Heading</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">You've probably heard that
                                                opposites attract. The same is true for fonts. Don't
                                                be afraid to combine font styles that are different
                                                but complementary, like sans serif with serif, short
                                                with tall, or decorative with simple. Qui photo
                                                booth letterpress, commodo enim craft beer mlkshk
                                                aliquip jean shorts ullamco ad vinyl cillum PBR.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <h6 class="fs-lg my-3">Graphic Design</h6>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Opposites attract, and that’s
                                                a fact. It’s in our nature to be interested in the
                                                unusual, and that’s why using contrasting colors in
                                                Graphic Design is a must. It’s eye-catching, it
                                                makes a statement, it’s impressive graphic design.
                                                Increase or decrease the letter spacing depending.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Trust fund seitan
                                                letterpress, keytar raw denim keffiyeh etsy art
                                                party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Just like in the image where
                                                we talked about using multiple fonts, you can see
                                                that the background in this graphic design is
                                                blurred. Whenever you put text on top of an image,
                                                it’s important that your viewers can understand.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Keytar raw denim keffiyeh
                                                etsy art party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!--  Extra Large modal example -->
                    <div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myExtraLargeModalLabel">Extra large
                                        modal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">You've probably heard that
                                                opposites attract. The same is true for fonts. Don't
                                                be afraid to combine font styles that are different
                                                but complementary, like sans serif with serif, short
                                                with tall, or decorative with simple. Qui photo
                                                booth letterpress, commodo enim craft beer mlkshk
                                                aliquip jean shorts ullamco ad vinyl cillum PBR.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <h6 class="fs-lg my-3">Graphic Design</h6>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Opposites attract, and that’s
                                                a fact. It’s in our nature to be interested in the
                                                unusual, and that’s why using contrasting colors in
                                                Graphic Design is a must. It’s eye-catching, it
                                                makes a statement, it’s impressive graphic design.
                                                Increase or decrease the letter spacing depending.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Trust fund seitan
                                                letterpress, keytar raw denim keffiyeh etsy art
                                                party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Just like in the image where
                                                we talked about using multiple fonts, you can see
                                                that the background in this graphic design is
                                                blurred. Whenever you put text on top of an image,
                                                it’s important that your viewers can understand.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Keytar raw denim keffiyeh
                                                etsy art party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!--  Large modal example -->
                    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myLargeModalLabel">Large modal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">You've probably heard that
                                                opposites attract. The same is true for fonts. Don't
                                                be afraid to combine font styles that are different
                                                but complementary, like sans serif with serif, short
                                                with tall, or decorative with simple. Qui photo
                                                booth letterpress, commodo enim craft beer mlkshk
                                                aliquip jean shorts ullamco ad vinyl cillum PBR.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <h6 class="fs-lg my-3">Graphic Design</h6>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Opposites attract, and that’s
                                                a fact. It’s in our nature to be interested in the
                                                unusual, and that’s why using contrasting colors in
                                                Graphic Design is a must. It’s eye-catching, it
                                                makes a statement, it’s impressive graphic design.
                                                Increase or decrease the letter spacing depending.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Trust fund seitan
                                                letterpress, keytar raw denim keffiyeh etsy art
                                                party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Just like in the image where
                                                we talked about using multiple fonts, you can see
                                                that the background in this graphic design is
                                                blurred. Whenever you put text on top of an image,
                                                it’s important that your viewers can understand.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Keytar raw denim keffiyeh
                                                etsy art party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!--  Small modal example -->
                    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="mySmallModalLabel">Small modal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Fullscreen Responsive Modals</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted text-muted">Below mentioned modifier classes are used to show
                    fullscreen modal as per minimum screen requirement.</p>
                <div>
                    <div class="hstack gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fullscreeexampleModal">
                            Fullscreen modal
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenSm">Full Screen Below sm</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenMd">Full Screen Below md</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenLg">Full Screen Below lg</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenXl">Full Screen Below xl</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenXxl">Full Screen Below
                            xxl</button>
                    </div>
                    <div class="modal fade" id="fullscreeexampleModal" tabindex="-1" aria-labelledby="fullscreeexampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="fullscreeexampleModalLabel">Full
                                        screen modal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">For that very reason, I went
                                                on a quest and spoke to many different professional
                                                graphic designers and asked them what graphic design
                                                tips they live.</p>
                                        </div>
                                    </div>
                                    <h6 class="fs-lg my-3">Graphic Design</h6>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Opposites attract, and that’s
                                                a fact. It’s in our nature to be interested in the
                                                unusual, and that’s why using contrasting colors in
                                                Graphic Design is a must. It’s eye-catching, it
                                                makes a statement, it’s impressive graphic design.
                                                Increase or decrease the letter spacing depending.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Trust fund seitan
                                                letterpress, keytar raw denim keffiyeh etsy art
                                                party before they sold out master cleanse
                                                gluten-free squid scenester freegan cosby sweater.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalFullscreenSm" tabindex="-1" aria-labelledby="exampleModalFullscreenSmLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen-sm-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenSmLabel">Full
                                        screen below sm</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalFullscreenMd" tabindex="-1" aria-labelledby="exampleModalFullscreenMdLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenMdLabel">Full
                                        screen below md</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalFullscreenLg" tabindex="-1" aria-labelledby="exampleModalFullscreenLgLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen-lg-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenLgLabel">Full
                                        screen below lg</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalFullscreenXl" tabindex="-1" aria-labelledby="exampleModalFullscreenXlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen-sm-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenXlLabel">Full
                                        screen below xl</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalFullscreenXxl" tabindex="-1" aria-labelledby="exampleModalFullscreenXxlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen-xxl-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenXxlLabel">Full
                                        screen below xxl</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fs-base">Give your text a good structure</h6>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">Raw denim you probably
                                                haven't heard of them jean shorts Austin.
                                                Nesciunt tofu stumptown aliqua, retro synth master
                                                cleanse.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">Too much or too little
                                                spacing, as in the example below, can make things
                                                unpleasant for the reader. The goal is to make your
                                                text as comfortable to read as possible. </p>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 ">
                                            <p class="text-muted mb-0">In some designs, you might
                                                adjust your tracking to create a certain artistic
                                                effect. It can also help you fix fonts that are
                                                poorly spaced to begin with.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="button" class="btn btn-primary ">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Animation Modals</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>fadeInRight</code>, <code>fadeInLeft</code>,
                    <code>fadeInUp</code>, <code>flip</code>, or <code>zoomIn</code> class to modal
                    class to set different modal with animation effect respectively.
                </p>
                <div>
                    <div class="hstack gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fadeInRightModal">Fade In Right Modal</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fadeInleftModal">Fade In Left Modal</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fadeInUpModal">Fade In Up Modal</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#flipModal">Flip Modal</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#zoomInModal">Zoom In Modal</button>
                    </div>
                    <div id="fadeInRightModal" class="modal fade fadeInRight" tabindex="-1" aria-labelledby="fadeInRightModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="fadeInRightModalLabel">Modal Heading
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fs-lg">
                                        Overflowing text to show scroll behavior
                                    </h5>
                                    <p class="text-muted">One morning, when Gregor Samsa woke from
                                        troubled dreams, he found himself transformed in his bed
                                        into a horrible vermin. He lay on his armour-like back, and
                                        if he lifted his head a little he could see his brown belly,
                                        slightly domed and divided by arches into stiff sections.
                                    </p>
                                    <p class="text-muted">The bedding was hardly able to cover it
                                        and seemed ready to slide off any moment. His many legs,
                                        pitifully thin compared with the size of the rest of him,
                                        waved about helplessly as he looked. "What's happened to
                                        me?" he thought.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary ">Save
                                        Changes</button>
                                </div>

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <div id="fadeInleftModal" class="modal fade fadeInLeft" tabindex="-1" aria-labelledby="fadeInleftModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="fadeInleftModalLabel">Modal Heading
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fs-lg">
                                        Overflowing text to show scroll behavior
                                    </h5>
                                    <p class="text-muted">One morning, when Gregor Samsa woke from
                                        troubled dreams, he found himself transformed in his bed
                                        into a horrible vermin. He lay on his armour-like back, and
                                        if he lifted his head a little he could see his brown belly,
                                        slightly domed and divided by arches into stiff sections.
                                    </p>
                                    <p class="text-muted">The bedding was hardly able to cover it
                                        and seemed ready to slide off any moment. His many legs,
                                        pitifully thin compared with the size of the rest of him,
                                        waved about helplessly as he looked. "What's happened to
                                        me?" he thought.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary ">Save
                                        Changes</button>
                                </div>

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <!-- Modal fadeInUp -->
                    <div id="fadeInUpModal" class="modal fade fadeInUp" tabindex="-1" aria-labelledby="fadeInUpModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="fadeInUpModalLabel">Modal Heading
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fs-lg">
                                        Overflowing text to show scroll behavior
                                    </h5>
                                    <p class="text-muted">One morning, when Gregor Samsa woke from
                                        troubled dreams, he found himself transformed in his bed
                                        into a horrible vermin. He lay on his armour-like back, and
                                        if he lifted his head a little he could see his brown belly,
                                        slightly domed and divided by arches into stiff sections.
                                    </p>
                                    <p class="text-muted">The bedding was hardly able to cover it
                                        and seemed ready to slide off any moment. His many legs,
                                        pitifully thin compared with the size of the rest of him,
                                        waved about helplessly as he looked. "What's happened to
                                        me?" he thought.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary ">Save
                                        Changes</button>
                                </div>

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- Modal flip -->
                    <div id="flipModal" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="flipModalLabel">Modal Heading</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fs-lg">
                                        Overflowing text to show scroll behavior
                                    </h5>
                                    <p class="text-muted">One morning, when Gregor Samsa woke from
                                        troubled dreams, he found himself transformed in his bed
                                        into a horrible vermin. He lay on his armour-like back, and
                                        if he lifted his head a little he could see his brown belly,
                                        slightly domed and divided by arches into stiff sections.
                                    </p>
                                    <p class="text-muted">The bedding was hardly able to cover it
                                        and seemed ready to slide off any moment. His many legs,
                                        pitifully thin compared with the size of the rest of him,
                                        waved about helplessly as he looked. "What's happened to
                                        me?" he thought.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary ">Save
                                        Changes</button>
                                </div>

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- Modal Blur -->
                    <div id="zoomInModal" class="modal fade zoomIn" tabindex="-1" aria-labelledby="zoomInModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="zoomInModalLabel">Modal Heading</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fs-lg">
                                        Overflowing text to show scroll behavior
                                    </h5>
                                    <p class="text-muted">One morning, when Gregor Samsa woke from
                                        troubled dreams, he found himself transformed in his bed
                                        into a horrible vermin. He lay on his armour-like back, and
                                        if he lifted his head a little he could see his brown belly,
                                        slightly domed and divided by arches into stiff sections.
                                    </p>
                                    <p class="text-muted">The bedding was hardly able to cover it
                                        and seemed ready to slide off any moment. His many legs,
                                        pitifully thin compared with the size of the rest of him,
                                        waved about helplessly as he looked. "What's happened to
                                        me?" he thought.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary ">Save
                                        Changes</button>
                                </div>

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>
<!---end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Modal Positions</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted text-muted">Use <code>modal-dialog-right</code>,
                    <code>modal-dialog-bottom</code>, or <code>modal-dialog-bottom-right</code>
                    class to modal-dialog class to set modal at different positions respectively.
                </p>
                <div>
                    <div class="hstack gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#topmodal">Top Modal</button>
                        <button type="button" class="btn btn-secondary " data-bs-toggle="modal" data-bs-target="#top-rightmodal">Top Right Modal</button>
                        <button type="button" class="btn btn-success " data-bs-toggle="modal" data-bs-target="#bottomModal">Bottom Modal</button>
                        <button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#bottom-rightModal">Bottom Right Modal</button>
                    </div>
                    <div id="topmodal" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <i class="bi bi-emoji-smile display-5 text-success"></i>
                                    <div class="mt-4">
                                        <h4 class="mb-3">Your event has been created.</h4>
                                        <p class="text-muted mb-4"> The transfer was not
                                            successfully received by us. the email of the recipient
                                            wasn't correct.</p>
                                        <div class="hstack gap-2 justify-content-center">
                                            <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>
                                                Close</a>
                                            <a href="javascript:void(0);" class="btn btn-success">Completed</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <div id="top-rightmodal" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-right">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-5">
                                        <i class="bi bi-emoji-smile display-5 text-success"></i>
                                        <div class="mt-4">
                                            <h4 class="mb-3">Your event has been created.</h4>
                                            <p class="text-muted mb-4"> The transfer was not
                                                successfully received by us. the email of the
                                                recipient wasn't correct.</p>
                                            <div class="hstack gap-2 justify-content-center">
                                                <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>
                                                    Close</a>
                                                <a href="javascript:void(0);" class="btn btn-success">Completed</a>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <!-- Modal fadeInUp -->
                    <div id="bottomModal" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-bottom">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <i class="bi bi-emoji-smile display-5 text-success"></i>
                                    <div class="mt-4">
                                        <h4 class="mb-3">Your event has been created.</h4>
                                        <p class="text-muted mb-4">The transfer was not successfully
                                            received by us. the email of the recipient wasn't
                                            correct.</p>
                                        <div class="hstack gap-2 justify-content-center">
                                            <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>
                                                Close</a>
                                            <a href="javascript:void(0);" class="btn btn-success">Completed</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- Modal flip -->
                    <div id="bottom-rightModal" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-bottom-right">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <i class="bi bi-emoji-smile display-5 text-success"></i>
                                    <div class="mt-4">
                                        <h4 class="mb-3">Your event has been created.</h4>
                                        <p class="text-muted mb-4"> The transfer was not
                                            successfully received by us. the email of the recipient
                                            wasn't correct.</p>
                                        <div class="hstack gap-2 justify-content-center">
                                            <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>
                                                Close</a>
                                            <a href="javascript:void(0);" class="btn btn-success">Completed</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>
<!---end row-->

@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
<script src="https://cdn.lordicon.com/libs/mssddfmo/lord-icon-2.1.0.js"></script>
<script src="{{ URL::asset('build/js/pages/modal.init.js') }}"></script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
