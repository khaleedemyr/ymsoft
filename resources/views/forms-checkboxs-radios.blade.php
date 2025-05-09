@extends('layouts.master')
@section('title') @lang('translation.checkboxs-radios') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Forms @endslot
@slot('title') Checkboxs & Radios @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Checkbox</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div>
                            <p class="text-muted fw-medium">Default Checkbox</p>
                            <p class="text-muted">Use <code>type="checkbox"</code> attribute to set a checkbox and add <code>checked</code>attribute to set a checkbox checked by default.</p>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="formCheck1">
                                <label class="form-check-label" for="formCheck1">
                                    Default checkbox
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="formCheck2" checked>
                                <label class="form-check-label" for="formCheck2">
                                    Checked checkbox
                                </label>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-4 col-md-6">
                        <div class="mt-4 mt-md-0">
                            <p class="text-muted fw-medium">Disabled Checkbox</p>
                            <p class="text-muted">Use <code>disabled</code> attribute to set a checkbox disabled and add <code>checked</code> attribute to set a checkbox checked by default.</p>
                            <div>
                                <div class="form-check form-check-right mb-2">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDisabled" disabled>
                                    <label class="form-check-label" for="flexCheckDisabled">
                                        Disabled checkbox
                                    </label>
                                </div>
                            </div>
                            <div>
                                <div class="form-check form-check-right">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckCheckedDisabled" checked disabled>
                                    <label class="form-check-label" for="flexCheckCheckedDisabled">
                                        Disabled checked checkbox
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-4 col-md-6">
                        <div class="mt-4 mt-md-0">
                            <p class="text-muted fw-medium">Checkbox Right</p>
                            <p class="text-muted">Use <code>form-check-right</code> class to form-check class to set a checkbox on the right side.</p>
                            <div class="form-check form-check-right mb-2">
                                <input class="form-check-input" type="checkbox" name="formCheckboxRight" id="formCheckboxRight1" checked="">
                                <label class="form-check-label" for="formCheckboxRight1">
                                    Form Radio Right
                                </label>
                            </div>
                            <div>
                                <div class="form-check form-check-right">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckCheckedRightDisabled" checked disabled>
                                    <label class="form-check-label" for="flexCheckCheckedRightDisabled">
                                        Disabled checked checkbox
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-6">
                        <div class="mt-3">
                            <p class="text-muted fw-medium">Indeterminate</p>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultIndeterminateCheck">
                                    <label class="form-check-label" for="defaultIndeterminateCheck">
                                        Indeterminate checkbox
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
        </div>
    </div> <!-- end col -->
</div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Custom Checkboxes</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div>

                            <p class="text-muted">Use <code>form-check-</code> class with below-mentioned color variation to set a color checkbox.</p>
                            <!-- Bootstrap Custom Checkboxes color -->
                            <div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck6" checked>
                                    <label class="form-check-label" for="formCheck6">
                                        Checkbox Primary
                                    </label>
                                </div>
                                <div class="form-check form-check-secondary mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck7" checked>
                                    <label class="form-check-label" for="formCheck7">
                                        Checkbox Secondary
                                    </label>
                                </div>
                                <div class="form-check form-check-success mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck8" checked>
                                    <label class="form-check-label" for="formCheck8">
                                        Checkbox Success
                                    </label>
                                </div>
                                <div class="form-check form-check-warning mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck9" checked>
                                    <label class="form-check-label" for="formCheck9">
                                        Checkbox Warning
                                    </label>
                                </div>
                                <div class="form-check form-check-danger mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck10" checked>
                                    <label class="form-check-label" for="formCheck10">
                                        Checkbox Danger
                                    </label>
                                </div>
                                <div class="form-check form-check-info mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck11" checked>
                                    <label class="form-check-label" for="formCheck11">
                                        Checkbox Info
                                    </label>
                                </div>
                                <div class="form-check form-check-dark">
                                    <input class="form-check-input" type="checkbox" id="formCheck12" checked>
                                    <label class="form-check-label" for="formCheck12">
                                        Checkbox Dark
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-md-6">
                        <div class="mt-4 mt-md-0">
                            <p class="text-muted">Use <code>form-check-outline</code> class and <code>form-check-</code> class with below-mentioned color variation to set a color checkbox with outline.</p>
                            <!-- Bootstrap Custom Outline Checkboxes -->
                            <div>
                                <div class="form-check form-check-outline form-check-primary mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck13" checked>
                                    <label class="form-check-label" for="formCheck13">
                                        Checkbox Outline Primary
                                    </label>
                                </div>
                                <div class="form-check form-check-outline form-check-secondary mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck14" checked>
                                    <label class="form-check-label" for="formCheck14">
                                        Checkbox Outline Secondary
                                    </label>
                                </div>
                                <div class="form-check form-check-outline form-check-success mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck15" checked>
                                    <label class="form-check-label" for="formCheck15">
                                        Checkbox Outline Success
                                    </label>
                                </div>
                                <div class="form-check form-check-outline form-check-warning mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck16" checked>
                                    <label class="form-check-label" for="formCheck16">
                                        Checkbox Outline Warning
                                    </label>
                                </div>
                                <div class="form-check form-check-outline form-check-danger mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck17" checked>
                                    <label class="form-check-label" for="formCheck17">
                                        Checkbox Outline Danger
                                    </label>
                                </div>
                                <div class="form-check form-check-outline form-check-info mb-3">
                                    <input class="form-check-input" type="checkbox" id="formCheck18" checked>
                                    <label class="form-check-label" for="formCheck18">
                                        Checkbox Outline Info
                                    </label>
                                </div>
                                <div class="form-check form-check-outline form-check-dark">
                                    <input class="form-check-input" type="checkbox" id="formCheck19" checked>
                                    <label class="form-check-label" for="formCheck19">
                                        Checkbox Outline Dark
                                    </label>
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


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Radios</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div>
                            <p class="text-muted fw-medium">Default Radios</p>
                            <p class="text-muted">Use <code>type="radio"</code> attribute to set a radio button and add <code>checked</code> attribute to set a radio checked by default.</p>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    Default radio
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
                                <label class="form-check-label" for="flexRadioDefault2">
                                    Default checked radio
                                </label>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4 col-md-6">
                        <div class="mt-4 mt-md-0">
                            <p class="text-muted fw-medium">Disabled Radios</p>
                            <p class="text-muted">Use <code>disabled</code> attribute to set a radio button disabled and add <code>checked</code> attribute to set a radio checked by default.</p>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="flexRadioDisabled" id="flexRadioDisabled" disabled>
                                <label class="form-check-label" for="flexRadioDisabled">
                                    Disabled radio
                                </label>
                            </div>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="flexRadioDisabled" id="flexRadioCheckedDisabled" checked disabled>
                                    <label class="form-check-label" for="flexRadioCheckedDisabled">
                                        Disabled checked radio
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-4 col-md-6">
                        <div class="mt-4 mt-md-0">
                            <p class="text-muted fw-medium">Radio Right</p>
                            <p class="text-muted">Use <code>form-check-right</code> class to form-check class to set a radio button on the right side.</p>
                            <div class="form-check form-check-right mb-2">
                                <input class="form-check-input" type="radio" name="formradioRight" id="formradioRight1">
                                <label class="form-check-label" for="formradioRight1">
                                    Form Radio Right
                                </label>
                            </div>
                            <div>
                                <div class="form-check form-check-right">
                                    <input class="form-check-input" type="radio" value="" name="formradioRight" id="flexCheckCheckedDisabled2" checked disabled>
                                    <label class="form-check-label" for="flexCheckCheckedDisabled2">
                                        Disabled checked radio
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
        </div>
    </div> <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Custom Radio</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <p class="text-muted">Use <code>form-check-</code> class with below-mentioned color variation to set a color radio.</p>

                            <div class="form-check form-radio-primary mb-3">
                                <input class="form-check-input" type="radio" name="formradiocolor1" id="formradioRight5" checked>
                                <label class="form-check-label" for="formradioRight5">
                                    Radio Primary
                                </label>
                            </div>

                            <div class="form-check form-radio-secondary mb-3">
                                <input class="form-check-input" type="radio" name="formradiocolor2" id="formradioRight6" checked>
                                <label class="form-check-label" for="formradioRight6">
                                    Radio Secondary
                                </label>
                            </div>

                            <div class="form-check form-radio-success mb-3">
                                <input class="form-check-input" type="radio" name="formradiocolor3" id="formradioRight7" checked>
                                <label class="form-check-label" for="formradioRight7">
                                    Radio Success
                                </label>
                            </div>

                            <div class="form-check form-radio-warning mb-3">
                                <input class="form-check-input" type="radio" name="formradiocolor4" id="formradioRight8" checked>
                                <label class="form-check-label" for="formradioRight8">
                                    Radio Warning
                                </label>
                            </div>

                            <div class="form-check form-radio-danger mb-3">
                                <input class="form-check-input" type="radio" name="formradiocolor5" id="formradioRight9" checked>
                                <label class="form-check-label" for="formradioRight9">
                                    Radio Danger
                                </label>
                            </div>

                            <div class="form-check form-radio-info mb-3">
                                <input class="form-check-input" type="radio" name="formradiocolor6" id="formradioRight10" checked>
                                <label class="form-check-label" for="formradioRight10">
                                    Radio Info
                                </label>
                            </div>

                            <div class="form-check form-radio-dark">
                                <input class="form-check-input" type="radio" name="formradiocolor7" id="formradioRight11" checked>
                                <label class="form-check-label" for="formradioRight11">
                                    Radio Dark
                                </label>
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Switches</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div>
                            <p class="text-muted fw-medium">Deafult Switchs</p>
                            <p class="text-muted">Use <code>form-switch</code> class to form-check class to set a switch and add <code>checked</code> attribute to set a switch checked by default.</p>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Default switch input</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
                                <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch input</label>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-4 col-md-6">
                        <div class="mt-4 mt-md-0">
                            <p class="text-muted fw-medium">Disabled Switchs</p>
                            <p class="text-muted">Use <code>disabled</code> attribute to set a radio button disabled and add <code>checked</code> attribute to set a switch checked by default.</p>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDisabled" disabled>
                                <label class="form-check-label" for="flexSwitchCheckDisabled">Switch input</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCheckedDisabled1" checked disabled>
                                <label class="form-check-label" for="flexSwitchCheckCheckedDisabled1">Disabled checked switch input</label>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-4 col-md-6">
                        <div class="mt-4 mt-md-0">
                            <p class="text-muted fw-medium">Switch Right</p>
                            <p class="text-muted">Use <code>form-check-right</code> class to form-check class to set a switch button on the right side.</p>
                            <div>
                                <div class="form-check form-switch form-check-right mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckRightDisabled" checked>
                                    <label class="form-check-label" for="flexSwitchCheckRightDisabled">Switch Right input</label>
                                </div>
                            </div>

                            <div>
                                <div class="form-check form-switch form-check-right">
                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCheckedDisabled2" disabled>
                                    <label class="form-check-label" for="flexSwitchCheckCheckedDisabled2">Disabled checked switch input</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-4 col-md-6">
                        <div class="mt-3">
                            <p class="text-muted fw-medium">Switch sizes</p>
                            <p class="text-muted">Use <code>form-switch-md</code> class to set a medium size switch button and <code>form-switch-lg</code> class to form-check class to set a large size switch button respectively. No such class is required for small size switch button.</p>

                            <div class="form-check form-switch mb-3" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="customSwitchsizesm" checked="">
                                <label class="form-check-label" for="customSwitchsizesm">Small Size Switch</label>
                            </div>

                            <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="customSwitchsizemd">
                                <label class="form-check-label" for="customSwitchsizemd">Medium Size Switch</label>
                            </div>

                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="customSwitchsizelg" checked="">
                                <label class="form-check-label" for="customSwitchsizelg">Large Size Switch</label>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                </div>
                <!--end row-->
            </div>
        </div>
    </div> <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Switches Color</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use <code>form-check-</code> class with below-mentioned color variation to set a color switch.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" checked>
                                <label class="form-check-label" for="SwitchCheck1">Switch Default</label>
                            </div>

                            <div class="form-check form-switch form-switch-secondary mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck2" checked>
                                <label class="form-check-label" for="SwitchCheck2">Switch Secondary</label>
                            </div>

                            <div class="form-check form-switch form-switch-success mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck3" checked>
                                <label class="form-check-label" for="SwitchCheck3">Switch Success</label>
                            </div>

                            <div class="form-check form-switch form-switch-warning mb-2 mb-md-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck4" checked>
                                <label class="form-check-label" for="SwitchCheck4">Switch Warning</label>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-md-6">
                        <div>
                            <div class="form-check form-switch form-switch-danger mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck5" checked>
                                <label class="form-check-label" for="SwitchCheck5">Switch Danger</label>
                            </div>

                            <div class="form-check form-switch form-switch-info mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck6" checked>
                                <label class="form-check-label" for="SwitchCheck6">Switch Info</label>
                            </div>

                            <div class="form-check form-switch form-switch-dark mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck7" checked>
                                <label class="form-check-label" for="SwitchCheck7">Switch Dark</label>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div> <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Inline Checkbox & Radios</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use <code>form-check-inline</code> class to form-check class to set horizontally align checkboxes, radios, or switches.</p>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox6" value="option1">
                                <label class="form-check-label" for="inlineCheckbox6">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox7" value="option2">
                                <label class="form-check-label" for="inlineCheckbox7">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox8" value="option3" disabled>
                                <label class="form-check-label" for="inlineCheckbox8">3 (disabled)</label>
                            </div>
                        </div>
                    </div><!-- end col -->

                    <div class="col-lg-4">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions1" id="inlineRadio1" value="option1">
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="inlineRadio2" value="option2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions3" id="inlineRadio3" value="option3" disabled>
                                <label class="form-check-label" for="inlineRadio3">3 (disabled)</label>
                            </div>
                        </div>
                    </div><!-- end col -->

                    <div class="col-lg-4">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="inlineswitch5">
                                <label class="form-check-label" for="inlineswitch5">1</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="inlineswitch6">
                                <label class="form-check-label" for="inlineswitch6">2</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="inlineswitchdisabled2" disabled>
                                <label class="form-check-label" for="inlineswitchdisabled2">2</label>
                            </div>
                        </div>
                    </div><!-- end col -->
                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div><!-- end col -->
</div><!-- end row -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Without Labels</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Omit the wrapping, <code>form-check</code> class for checkboxes, radios, or switches that have no label text. Remember to still provide some form of accessible name for assistive technologies (for instance, using aria-label).</p>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3" disabled>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-lg-4">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="WithoutinlineRadio1" value="option1">
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="WithoutinlineRadio2" value="option2">
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="WithoutinlineRadio3" value="option3" disabled>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-lg-4">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="inlineswitch">
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="inlineswitch1">
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="inlineswitchdisabled" disabled>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div> <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Radio Toggle Buttons</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Create button-like checkboxes and radio buttons by using <code>btn</code> styles rather than form-check-label class on the &lt;label&gt; elements. These toggle buttons can further be grouped in a <a href="https://getbootstrap.com/docs/5.1/components/button-group/">button group</a> if needed.</p>
                <div class="hstack gap-2 flex-wrap">
                    <input type="radio" class="btn-check" name="options" id="option1" checked>
                    <label class="btn btn-secondary" for="option1">Checked</label>

                    <input type="radio" class="btn-check" name="options" id="option2">
                    <label class="btn btn-secondary" for="option2">Radio</label>

                    <input type="radio" class="btn-check" name="options" id="option3" disabled>
                    <label class="btn btn-secondary" for="option3">Disabled</label>

                    <input type="radio" class="btn-check" name="options" id="option4">
                    <label class="btn btn-secondary" for="option4">Radio</label>
                </div>
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div> <!-- end col -->

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Outlined Styles</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Different variants of <code>btn</code> attribute, such as the various outlined styles, are supported.</p>
                <div class="hstack gap-2 flex-wrap">
                    <input type="checkbox" class="btn-check" id="btn-check-outlined">
                    <label class="btn btn-outline-primary" for="btn-check-outlined">Single toggle</label>

                    <input type="checkbox" class="btn-check" id="btn-check-2-outlined" checked>
                    <label class="btn btn-outline-secondary" for="btn-check-2-outlined">Checked</label>

                    <input type="radio" class="btn-check" name="options-outlined" id="success-outlined" checked>
                    <label class="btn btn-outline-success" for="success-outlined">Checked success radio</label>

                    <input type="radio" class="btn-check" name="options-outlined" id="danger-outlined">
                    <label class="btn btn-outline-danger" for="danger-outlined">Danger radio</label>
                </div>
            </div>
        </div>
    </div> <!-- end col -->

</div>
<!-- end row -->
@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
