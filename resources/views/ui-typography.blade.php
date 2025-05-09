@extends('layouts.master')
@section('title')
    @lang('translation.typography')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Base UI
        @endslot
        @slot('title')
            Typography
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Font Family</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <p class="text-muted">Body Font Family</p>
                                <div>
                                    <p class="display-4  fw-normal">Aa</p>
                                </div>
                                <div>
                                    <p class="mb-2">Font Family</p>
                                    <h5 class="text-muted mb-0">"Inter", sans-serif</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mt-4 mt-sm-0">
                                <p class="text-muted">Heading Font Family</p>
                                <div>
                                    <h1 class="display-4 fw-semibold">Aa</h1>
                                </div>
                                <div>
                                    <p class="mb-2">Font Family</p>
                                    <h5 class="text-muted mb-0">"Inter", sans-serif</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Headings</h4>
                </div><!-- end card header -->

                <div class="card-body">

                    <p class="text-muted">All HTML headings, <code>&lt;h1&gt;</code> through
                        <code>&lt;h6&gt;</code>, are available.
                    </p>

                    <div>
                        <h1 class="mb-3">h1. Bootstrap heading <small class="text-muted">Semibold
                                2.03125rem (32.5px)</small></h1>
                        <h2 class="mb-3">h2. Bootstrap heading <small class="text-muted">Semibold
                                1.625rem (26px)</small></h2>
                        <h3 class="mb-3">h3. Bootstrap heading <small class="text-muted">Semibold
                                1.42188rem (22.8px)</small></h3>
                        <h4 class="mb-3">h4. Bootstrap heading <small class="text-muted">Semibold
                                1.21875rem (19.5px)</small></h4>
                        <h5 class="mb-3">h5. Bootstrap heading <small class="text-muted">Semibold
                                1.01563rem (16.25px)</small></h5>
                        <h6 class="mb-1">h6. Bootstrap heading <small class="text-muted">Semibold
                                0.8125rem (13px)</small></h6>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Display Headings</h4>
                </div><!-- end card header -->

                <div class="card-body">

                    <p class="text-muted">Traditional heading elements are designed to work best in the
                        meat of your page content.</p>

                    <div>
                        <h1 class="display-1 mb-4">Display 1</h1>
                        <h1 class="display-2 mb-4">Display 2</h1>
                        <h1 class="display-3 mb-4">Display 3</h1>
                        <h1 class="display-4 mb-4">Display 4</h1>
                        <h1 class="display-5 mb-4">Display 5</h1>
                        <h1 class="display-6 mb-0">Display 6</h1>
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
                    <h4 class="card-title mb-0">Blockquotes</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>&lt;blockquote class="blockquote"&gt;</code> class
                        for quoting blocks of content from another source within your document .</p>
                    <div class="row">
                        <div class="col-xxl-6">
                            <div>
                                <blockquote class="blockquote fs-md mb-0">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer
                                        posuere erat a ante.</p>
                                    <footer class="blockquote-footer mt-0">Someone famous in <cite
                                            title="Source Title">Source Title</cite></footer>
                                </blockquote>
                            </div>
                        </div><!-- end col -->
                        <div class="col-xxl-6">
                            <div class="mt-4 mt-lg-0">
                                <blockquote class="blockquote blockquote-reverse fs-md mb-0">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer
                                        posuere erat a ante.</p>
                                    <footer class="blockquote-footer mt-0">Someone famous in <cite
                                            title="Source Title">Source Title</cite></footer>
                                </blockquote>
                            </div>
                        </div><!-- end col -->
                    </div><!-- end row -->
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
                    <h4 class="card-title mb-0">Blockquote Background Color</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted text-muted">Use <code>blockquote-</code> class with the
                        below-mentioned color variation to set the blockquote background color.</p>
                    <div>
                        <div class="row gy-4">
                            <div class="col-xl-4 col-md-6">
                                <blockquote class="blockquote custom-blockquote blockquote-primary rounded mb-0">
                                    <p class="mb-2">Custom Blockquote Primary Example</p>
                                    <footer class="blockquote-footer mt-0">Angie Burt <cite
                                            title="Source Title">Designer</cite></footer>
                                </blockquote>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <blockquote class="blockquote custom-blockquote blockquote-secondary rounded mb-0">
                                    <p class="mb-2">Custom Blockquote Secondary Example</p>
                                    <footer class="blockquote-footer mt-0">Angie Burt <cite
                                            title="Source Title">Designer</cite></footer>
                                </blockquote>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <blockquote class="blockquote custom-blockquote blockquote-success rounded mb-0">
                                    <p class="mb-2">Custom Blockquote Success Example</p>
                                    <footer class="blockquote-footer mt-0">Angie Burt <cite
                                            title="Source Title">Designer</cite></footer>
                                </blockquote>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <blockquote class="blockquote custom-blockquote blockquote-info rounded mb-0">
                                    <p class="mb-2">Custom Blockquote Info Example</p>
                                    <footer class="blockquote-footer mt-0">Angie Burt <cite
                                            title="Source Title">Designer</cite></footer>
                                </blockquote>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                                    <p class="mb-2">Custom Blockquote Danger Example</p>
                                    <footer class="blockquote-footer mt-0">Angie Burt <cite
                                            title="Source Title">Designer</cite></footer>
                                </blockquote>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <blockquote class="blockquote custom-blockquote blockquote-dark text-body rounded mb-0">
                                    <p class="mb-2">Custom Blockquote Dark Example</p>
                                    <footer class="blockquote-footer mt-0 text-body">Angie Burt <cite
                                            title="Source Title">Designer</cite></footer>
                                </blockquote>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <blockquote class="blockquote custom-blockquote blockquote-dark rounded mb-0">
                                    <p class="mb-2">Custom Blockquote Dark Example</p>
                                    <footer class="blockquote-footer mt-0">Angie Burt <cite
                                            title="Source Title">Designer</cite></footer>
                                </blockquote>
                            </div>
                        </div><!-- end row -->
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
                    <h4 class="card-title mb-0">Blockquote Border Color</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <p class="text-muted text-muted">Use <code>blockquote-outline</code> class to set
                        blockquote border color.</p>
                    <div class="row gy-4">

                        <div class="col-xl-4 col-md-6">
                            <blockquote
                                class="blockquote custom-blockquote blockquote-outline blockquote-primary rounded mb-0">
                                <p class="mb-2">Custom Blockquote Outline Primary Example</p>
                                <footer class="blockquote-footer mt-0">Angie Burt <cite
                                        title="Source Title">Designer</cite></footer>
                            </blockquote>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <blockquote
                                class="blockquote custom-blockquote blockquote-outline blockquote-secondary rounded mb-0">
                                <p class="mb-2">Custom Blockquote Outline Secondary Example</p>
                                <footer class="blockquote-footer mt-0">Angie Burt <cite
                                        title="Source Title">Designer</cite></footer>
                            </blockquote>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <blockquote
                                class="blockquote custom-blockquote blockquote-outline blockquote-success rounded mb-0">
                                <p class="mb-2">Custom Blockquote Outline Success Example</p>
                                <footer class="blockquote-footer mt-0">Angie Burt <cite
                                        title="Source Title">Designer</cite></footer>
                            </blockquote>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <blockquote
                                class="blockquote custom-blockquote blockquote-outline blockquote-info rounded mb-0">
                                <p class="mb-2">Custom Blockquote Outline Info Example</p>
                                <footer class="blockquote-footer mt-0">Angie Burt <cite
                                        title="Source Title">Designer</cite></footer>
                            </blockquote>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <blockquote
                                class="blockquote custom-blockquote blockquote-outline blockquote-danger rounded mb-0">
                                <p class="mb-2">Custom Blockquote Outline Danger Example</p>
                                <footer class="blockquote-footer mt-0">Angie Burt <cite
                                        title="Source Title">Designer</cite></footer>
                            </blockquote>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <blockquote
                                class="blockquote custom-blockquote blockquote-outline blockquote-warning rounded mb-0">
                                <p class="mb-2">Custom Blockquote Outline Warning Example</p>
                                <footer class="blockquote-footer mt-0">Angie Burt <cite
                                        title="Source Title">Designer</cite></footer>
                            </blockquote>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <blockquote
                                class="blockquote custom-blockquote blockquote-outline blockquote-dark text-body rounded mb-0">
                                <p class="mb-2">Custom Blockquote Outline Dark Example</p>
                                <footer class="blockquote-footer text-body mt-0">Angie Burt <cite
                                        title="Source Title">Designer</cite></footer>
                            </blockquote>
                        </div>
                    </div><!-- end row -->
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Inline Text Elements</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Styling for inline HTML5 elements.</p>
                    <div>
                        <p class="lead">
                            Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                        </p>
                        <p>You can use the mark tag to <mark>highlight</mark> text.</p>
                        <p><del>This line of text is meant to be treated as deleted text.</del></p>
                        <p><s>This line of text is meant to be treated as no longer accurate.</s></p>
                        <p><ins>This line of text is meant to be treated as an addition to the
                                document.</ins></p>
                        <p><u>This line of text will render as underlined</u></p>
                        <p><small>This line of text is meant to be treated as fine print.</small></p>
                        <p><strong>This line rendered as bold text.</strong></p>
                        <p class="mb-0"><em>This line rendered as italicized text.</em></p>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Unstyled List</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>list-unstyled</code> class Remove the default
                        list-style and left margin on list
                        items (immediate children only). <strong>This only applies to immediate
                            children list items</strong>, meaning you will need to add the class
                        for any nested lists.</p>

                    <div>
                        <ul class="list-unstyled mb-0">
                            <li>Integer molestie lorem at massa</li>
                            <li>Nulla volutpat aliquam velit
                                <ul>
                                    <li>Phasellus iaculis neque</li>
                                    <li>Purus sodales ultricies</li>
                                    <li>Vestibulum laoreet porttitor sem</li>
                                </ul>
                            </li>
                            <li>Faucibus porta lacus fringilla vel</li>
                        </ul>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Inline List</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>list-inline</code> and
                        <code>list-inline-item</code> class combination to remove a list’s bullets and
                        apply some light margin.
                    </p>

                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">Lorem ipsum</li>
                        <li class="list-inline-item">Phasellus iaculis</li>
                        <li class="list-inline-item">Nulla volutpat</li>
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
                    <h4 class="card-title mb-0">Description List Alignment</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Align terms and descriptions
                        horizontally by using our grid system’s predefined classes (or semantic
                        mixins). For longer terms, you can optionally add a <code>.text-truncate</code>
                        class to
                        truncate the text with an ellipsis.</p>

                    <div>
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Description lists</dt>
                            <dd class="col-sm-9">A description list is perfect for defining terms.</dd>

                            <dt class="col-sm-3">Euismod</dt>
                            <dd class="col-sm-9">Vestibulum id ligula porta felis euismod semper eget
                                lacinia odio sem nec elit.</dd>
                            <dd class="col-sm-9 offset-sm-3">Donec id elit non mi porta gravida at eget
                                metus.</dd>

                            <dt class="col-sm-3">Malesuada porta</dt>
                            <dd class="col-sm-9">Etiam porta sem malesuada magna mollis euismod.</dd>

                            <dt class="col-sm-3 text-truncate">Truncated term is truncated</dt>
                            <dd class="col-sm-9">Fusce dapibus, tellus ac cursus commodo, tortor mauris
                                condimentum nibh, ut fermentum massa justo sit amet risus.</dd>

                            <dt class="col-sm-3">Nesting</dt>
                            <dd class="col-sm-9 mb-0">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Nested definition list</dt>
                                    <dd class="col-sm-8">Aenean posuere, tortor sed cursus feugiat, nunc
                                        augue blandit nunc.</dd>
                                </dl>
                            </dd>
                        </dl>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Text Wrapping and Overflow</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div>
                        <p class="text-muted">Use <code>text-wrap</code> class to wrap the text.</p>
                        <div class="badge bg-primary text-wrap" style="width: 6rem;">
                            This text should wrap.
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-muted">Use <code>text-nowrap</code> class to prevent text from
                            wrapping.</p>
                        <div class="text-nowrap border bg-light mt-3" style="width: 8rem;">
                            This text should overflow the parent.
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Word Break</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div>
                        <p class="text-muted">Use <code>text-break</code> class to prevent long strings
                            of text from breaking your components' layout.</p>
                        <p class="text-break mb-0">
                            mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm
                        </p>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Font size</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>fs-1</code>, <code>fs-2</code>, <code>fs-3</code>,
                        <code>fs-4</code>, <code>fs-5</code>, or <code>fs-6</code>, class to change the
                        font-size respectively.
                    </p>
                    <div>
                        <p class="fs-1">.fs-1 text</p>
                        <p class="fs-2">.fs-2 text</p>
                        <p class="fs-3">.fs-3 text</p>
                        <p class="fs-4">.fs-4 text</p>
                        <p class="fs-5">.fs-5 text</p>
                        <p class="fs-6">.fs-6 text</p>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Font weight and italics</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>fst-</code>with modifier class to change font-style
                        and Use <code>fw-</code>with modifier class to change font-weight.</p>

                    <div>
                        <p class="fw-bold">Bold text.</p>
                        <p class="fw-bolder">Bolder weight text (relative to the parent element).</p>
                        <p class="fw-semibold">Semibold weight text.</p>
                        <p class="fw-normal">Normal weight text.</p>
                        <p class="fw-light">Light weight text.</p>
                        <p class="fw-lighter">Lighter weight text (relative to the parent element).</p>
                        <p class="fst-italic">Italic text.</p>
                        <p class="fst-normal mb-0">Text with normal font style</p>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Line height</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>lh-</code> with modifier class to change the line
                        height of an element.</p>
                    <div>
                        <p class="lh-1">This is a long paragraph written to show how the line-height of
                            an element is affected by our utilities. Classes are applied to the element
                            itself or sometimes the parent element. These classes can be customized as
                            needed with our utility API.</p>
                        <p class="lh-sm">This is a long paragraph written to show how the line-height of
                            an element is affected by our utilities. Classes are applied to the element
                            itself or sometimes the parent element. These classes can be customized as
                            needed with our utility API.</p>
                        <p class="lh-base">This is a long paragraph written to show how the line-height
                            of an element is affected by our utilities. Classes are applied to the
                            element itself or sometimes the parent element.</p>
                        <p class="lh-lg mb-0">This is a long paragraph written to show how the
                            line-height of an element is affected by our utilities. Classes are applied
                            to the element itself or sometimes the parent element.</p>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Text Transform</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>text-lowercase</code>, <code>text-uppercase</code>,
                        or <code>text-capitalize</code> to transform the text.</p>
                    <div>
                        <p class="text-lowercase">Lowercased text.</p>
                        <p class="text-uppercase">Uppercased text.</p>
                        <p class="text-capitalize mb-0">CapiTaliZed text.</p>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Text Decoration</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>text-decoration-underline</code>,
                        <code>text-decoration-line-through</code>, or <code>text-decoration-none</code>
                        class to decorate text in components respectively.
                    </p>

                    <div>
                        <p class="text-decoration-underline">This text has a line underneath it.</p>
                        <p class="text-decoration-line-through">This text has a line going through it.
                        </p>
                        <a href="#" class="text-decoration-none">This link has its text decoration
                            removed</a>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Text Truncation</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>text-truncate</code> class to truncate the text with
                        an ellipsis. Requires <code>display: inline-block</code> or
                        <code>display: block</code>.
                    </p>
                    <div>
                        <!-- Block level -->
                        <div class="row">
                            <div class="col-2 text-truncate">
                                This text is quite long, and will be truncated once displayed.
                            </div>
                        </div>

                        <!-- Inline level -->
                        <span class="d-inline-block text-truncate" style="max-width: 150px;">
                            This text is quite long, and will be truncated once displayed.
                        </span>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Visibility</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>visible</code> or <code>invisible</code> class to
                        show or to hide elements respectively.</p>

                    <div>
                        <div class="visible">text visible Lorem ipsum dolor sit amet</div>
                        <div class="invisible">text invisible Lorem ipsum dolor sit amet</div>
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
                    <h4 class="card-title mb-0">Clearfix</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <p class="text-muted">Use <code>clearfix</code> class to clear/set floated content
                        within a container.</p>
                    <div class="bg-light clearfix p-3">
                        <button type="button" class="btn btn-secondary float-start">Example Button
                            floated left</button>
                        <button type="button" class="btn btn-success float-end">Example Button floated
                            right</button>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
