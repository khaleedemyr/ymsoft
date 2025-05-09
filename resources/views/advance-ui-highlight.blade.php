@extends('layouts.master')
@section('title') @lang('translation.highlight') @endsection
@section('content')

@component('components.breadcrumb')
@slot('li_1') Advanced UI @endslot
@slot('title') Highlight @endslot
@endcomponent


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">HTML Highlight</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted mb-0">HTML highlight is used to mark or highlight text that is of property, relevance, or special interest to an HTML document. here is the example shown below.</p>

            </div><!-- end card-body -->
            <div class="card-body bg-light border-bottom border-top bg-opacity-25">
                <h5 class="fs-xs text-muted mb-0">HTML Preview</h5>
            </div>
            <div class="card-body">
                <pre><code class="language-markup">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;title&gt;Vixon - Responsive Admin Dashboard Template&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        &lt;div&gt;
            &lt;h1&gt;This is a Heading 1&lt;/h1&gt;
            &lt;h2&gt;This is a Heading 2&lt;/h2&gt;
            &lt;h3&gt;This is a Heading 3&lt;/h3&gt;
            &lt;h4&gt;This is a Heading 4&lt;/h4&gt;
        &lt;/div&gt;
        &lt;!-- end div content --&gt;
    &lt;/body&gt;
&lt;/html&gt;</code></pre>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">CSS Highlight</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted mb-0">CSS highlight is used to mark or highlight text that is of property, relevance, or special interest to a CSS document. Here is the example shown below.</p>
            </div><!-- end card-body -->
            <div class="card-body bg-light border-bottom border-top bg-opacity-25 d-flex align-items-center">
                <h5 class="flex-grow-1 fs-xs text-muted mb-0">CSS Preview</h5>
                <div class="flex-shrink-0">
                    <a href="#!"><i class="bi bi-clipboard"></i></a>
                </div>
            </div>
            <div class="card-body">
                <pre><code class="language-css">body {
    color: #212529; 
    background-color: #f3f3f9;
    font-family: "Poppins",sans-serif;
}

.example {
    margin: 0;
    color: #74788d; 
}</code></pre>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Javascript Highlight</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted mb-0">Javascript highlight is used to mark or highlight text that is of property, relevance, or special interest to a Javascript document. Here is the example shown below.</p>
            </div><!-- end card-body -->
            <div class="card-body bg-light border-bottom border-top bg-opacity-25 d-flex align-items-center">
                <h5 class="flex-grow-1 fs-xs text-muted mb-0">Javascript Preview</h5>
                <div class="flex-shrink-0">
                    <a href="#!"><i class="bi bi-clipboard"></i></a>
                </div>
            </div>
            <div class="card-body">
                <pre><code class="language-js">function myFunction() {
    var divElement = document.getElementById("myDIV");
    if (divElement.style.display === "none") {
    divElement.style.display = "block";
    } else {
    divElement.style.display = "none";
    }
}</code></pre>
            </div>
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
