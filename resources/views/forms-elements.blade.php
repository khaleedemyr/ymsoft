@extends('layouts.master')
@section('title')
@lang('translation.basic-elements')
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1')
Forms
@endslot
@slot('title')
Basic Elements
@endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Input Example</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="basiInput" class="form-label">Basic Input</label>
                            <input type="text" class="form-control" id="basiInput">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="labelInput" class="form-label">Input with Label</label>
                            <input type="text" class="form-control" id="labelInput">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="placeholderInput" class="form-label">Input with Placeholder</label>
                            <input type="text" class="form-control" id="placeholderInput" placeholder="Placeholder">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="valueInput" class="form-label">Input with Value</label>
                            <input type="text" class="form-control" id="valueInput" value="Input value">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="readonlyPlaintext" class="form-label">Readonly Plain Text Input</label>
                            <input type="text" class="form-control-plaintext" id="readonlyPlaintext" value="Readonly input" readonly>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="readonlyInput" class="form-label">Readonly Input</label>
                            <input type="text" class="form-control" id="readonlyInput" value="Readonly input" readonly>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="disabledInput" class="form-label">Disabled Input</label>
                            <input type="text" class="form-control" id="disabledInput" value="Disabled input" disabled>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="iconInput" class="form-label">Input with Icon</label>
                            <div class="form-icon">
                                <input type="email" class="form-control form-control-icon" id="iconInput" placeholder="example@gmail.com">
                                <i class="ri-mail-unread-line"></i>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="iconrightInput" class="form-label">Input with Icon Right</label>
                            <div class="form-icon right">
                                <input type="email" class="form-control form-control-icon" id="iconrightInput" placeholder="example@gmail.com">
                                <i class="ri-mail-unread-line"></i>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="exampleInputdate" class="form-label">Input Date</label>
                            <input type="date" class="form-control" id="exampleInputdate">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="exampleInputtime" class="form-label">Input Time</label>
                            <input type="time" class="form-control" id="exampleInputtime">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="exampleInputpassword" class="form-label">Input Password</label>
                            <input type="password" class="form-control" id="exampleInputpassword" value="44512465">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="exampleFormControlTextarea5" class="form-label">Example Textarea</label>
                            <textarea class="form-control" id="exampleFormControlTextarea5" rows="3"></textarea>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="formtextInput" class="form-label">Form Text</label>
                            <input type="password" class="form-control" id="formtextInput">
                            <div id="passwordHelpBlock" class="form-text">
                                Must be 8-20 characters long.
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="colorPicker" class="form-label">Color Picker</label>
                            <input type="color" class="form-control form-control-color w-100" id="colorPicker" value="#364574">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="borderInput" class="form-label">Input Border Style</label>
                            <input type="text" class="form-control border-dashed" id="borderInput" placeholder="Enter your name">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <label for="exampleDataList" class="form-label">Datalist example</label>
                        <input class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Search your country...">
                        <datalist id="datalistOptions">
                            <option value="Switzerland">
                            <option value="New York">
                            <option value="France">
                            <option value="Spain">
                            <option value="Chicago">
                            <option value="Bulgaria">
                            <option value="Hong Kong">
                        </datalist>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div>
                            <label for="exampleInputrounded" class="form-label">Rounded Input</label>
                            <input type="text" class="form-control rounded-pill" id="exampleInputrounded" placeholder="Enter your name">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xxl-3 col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="firstnamefloatingInput" placeholder="Enter your firstname">
                            <label for="firstnamefloatingInput">Floating Input</label>
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

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Input Sizing</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>form-control-lg</code> class to set large size input and Use <code>form-control-sm</code> class to set small size input. No class is needed for default size input.</p>
                <div class="row align-items-center g-3">
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm" type="text" placeholder=".form-control-sm">
                    </div>
                    <!--end col-->
                    <div class="col-lg-4">
                        <input class="form-control" type="text" placeholder=".form-control">
                    </div>
                    <!--end col-->
                    <div class="col-lg-4">
                        <input class="form-control form-control-lg" type="text" placeholder=".form-control-lg">
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

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">File Input</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-lg-4">
                        <div>
                            <label for="formFile" class="form-label">Default File Input Example</label>
                            <p class="text-muted">Use <code>input</code> attribute with <code>type="file"</code> tag for default file input</p>
                            <input class="form-control" type="file" id="formFile">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4">
                        <div>
                            <label for="formFileMultiple" class="form-label">Multiple Files Input Example</label>
                            <p class="text-muted">Use <code>multiple</code> attribute within the input attribute to select multiple files.</p>
                            <input class="form-control" type="file" id="formFileMultiple" multiple>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4">
                        <div>
                            <label for="formFileDisabled" class="form-label">Disabled File Input Example</label>
                            <p class="text-muted">Use <code>disabled</code> attribute within the input attribute to disable the file input.</p>
                            <input class="form-control" type="file" id="formFileDisabled" disabled>
                        </div>
                    </div>
                </div>
                <!--end row-->
                <div class="row mt-4 align-items-center g-3">
                    <h5 class="fs-md">File Input Sizing</h5>
                    <div class="col-lg-4">
                        <div>
                            <label for="formSizeSmall" class="form-label">Small Size File Input Example</label>
                            <p class="text-muted">Use <code>form-control-sm</code> class within the form-control class to set a small size file input.</p>
                            <input class="form-control form-control-sm" id="formSizeSmall" type="file">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div>
                            <label for="formSizeDefault" class="form-label">Default Size File Input Example</label>
                            <p class="text-muted">Use <code>form-control </code>class and <code>type="file"</code> attribute within the input attribute to set a default size file input.</p>
                            <input class="form-control" id="formSizeDefault" type="file">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div>
                            <label for="formSizeLarge" class="form-label">Large Size File Input Example</label>
                            <p class="text-muted">Use <code>form-control-lg</code> class within the form-control class to set a large size file input.</p>

                            <input class="form-control form-control-lg" id="formSizeLarge" type="file">
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


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Input Group</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div>
                    <h5 class="fs-base">Basic example</h5>
                    <p class="text-muted">Use <code>input-group</code> class to div element which contains input attribute to wrap a default input in the group.</p>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">@</span>
                                <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                <span class="input-group-text" id="basic-addon2">@example.com</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Username" aria-label="Username">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control" placeholder="Server" aria-label="Server">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text">With textarea</span>
                                <textarea class="form-control" aria-label="With textarea" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="basic-url" class="form-label">Your vanity URL</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">https://example.com/users/</span>
                                <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="fs-base">Wrapping</h5>
                    <p class="text-muted">Input groups wrap by default via <code>flex-wrap: wrap</code> in order to accommodate custom form field validation within an input group. You may disable this with <code>flex-nowrap</code> class.</p>
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping">@</span>
                        <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="addon-wrapping">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Input Group Sizing</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>input-group-sm</code> class to set a small size input group and <code>input-group-lg</code> class to input-group class to set a large size input group respectively. no such class is required for a default size input group.</p>
                <div class="row align-items-center g-3">
                    <div class="col-lg-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" id="inputGroup-sizing-sm">Small</span>
                            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroup-sizing-default">Default</span>
                            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text" id="inputGroup-sizing-lg">Large</span>
                            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Multiple Inputs</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">While multiple <code>&lt;input&gt;</code>s are supported visually, validation styles are only available for input groups with a single <code>&lt;input&gt;</code>.</p>
                <div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">First and last name</span>
                        <input type="text" aria-label="First name" class="form-control">
                        <input type="text" aria-label="Last name" class="form-control">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">$</span>
                        <span class="input-group-text">0.00</span>
                        <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">
                    </div>

                    <div class="input-group">
                        <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">
                        <span class="input-group-text">$</span>
                        <span class="input-group-text">0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons, Checkboxes and Radios Group</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div>
                    <p class="text-muted">Use any checkbox, radio, or button option within an input group’s addon instead of text. We recommend adding <code>mt-0</code> class to the <code>form-check-input</code> when there’s no visible text next to the input.</p>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
                                </div>
                                <input type="text" class="form-control" aria-label="Text input with checkbox">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" value="" aria-label="Radio button for following text input">
                                </div>
                                <input type="text" class="form-control" aria-label="Text input with radio button">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button class="btn btn-outline-primary" type="button" id="button-addon1">Button</button>
                                <input type="text" class="form-control" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" aria-label="Recipient's username" aria-describedby="button-addon2">
                                <button class="btn btn-outline-success" type="button" id="button-addon2">Button</button>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button class="btn btn-primary" type="button">Button</button>
                                <button class="btn btn-success" type="button">Button</button>
                                <input type="text" class="form-control" placeholder="" aria-label="Example text with two button addons">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" aria-label="Recipient's username with two button addons">
                                <button class="btn btn-primary" type="button">Button</button>
                                <button class="btn btn-success" type="button">Button</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons with dropdowns</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div>
                    <p class="text-muted">You can use a button with the dropdown toggle to set the file input group.</p>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Separated link</a></li>
                                </ul>
                                <input type="text" class="form-control" aria-label="Text input with dropdown button">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" aria-label="Text input with dropdown button">
                                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Separated link</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Action before</a></li>
                                    <li><a class="dropdown-item" href="#">Another action before</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Separated link</a></li>
                                </ul>
                                <input type="text" class="form-control" aria-label="Text input with 2 dropdown buttons">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Separated link</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Custom Forms</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Input groups include support for custom selects and custom file inputs. Browser default versions of these are not supported.</p>
                <div>
                    <h5 class="fs-base mb-3">Select</h5>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Options</label>
                                <select class="form-select" id="inputGroupSelect01">
                                    <option selected>Choose...</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <select class="form-select" id="inputGroupSelect02">
                                    <option selected>Choose...</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                                <label class="input-group-text" for="inputGroupSelect02">Options</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button class="btn btn-outline-primary" type="button">Button</button>
                                <select class="form-select" id="inputGroupSelect03" aria-label="Example select with button addon">
                                    <option selected>Choose...</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <select class="form-select" id="inputGroupSelect04" aria-label="Example select with button addon">
                                    <option selected>Choose...</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button">Button</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="fs-base mb-3">File Input</h5>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupFile01">Upload</label>
                                <input type="file" class="form-control" id="inputGroupFile01">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="file" class="form-control" id="inputGroupFile02">
                                <label class="input-group-text" for="inputGroupFile02">Upload</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button class="btn btn-outline-primary" type="button" id="inputGroupFileAddon03">Button</button>
                                <input type="file" class="form-control" id="inputGroupFile03" aria-describedby="inputGroupFileAddon03" aria-label="Upload">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                                <button class="btn btn-outline-success" type="button" id="inputGroupFileAddon04">Button</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
