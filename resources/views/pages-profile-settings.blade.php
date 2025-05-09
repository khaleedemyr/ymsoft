@extends('layouts.master')
@section('title')
@lang('translation.profile-settings')
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Pages @endslot
@slot('title') Profile Settings @endslot
@endcomponent

<div class="card">
    <div class="profile-foreground position-relative">
        <div class="profile-wid-bg position-static">
            <img src="build/images/small/img-2.jpg" alt="" class="profile-wid-img card-img-top">
            <div>
                <input id="profile-foreground-img-file-input" type="file" class="profile-foreground-img-file-input d-none">
                <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light btn-sm position-absolute end-0 top-0 m-3 z-1">
                    <i class="ri-image-edit-line align-bottom me-1"></i> Edit Cover Images
                </label>
            </div>
        </div>
        <div class="bg-overlay bg-primary bg-opacity-75 card-img-top"></div>
    </div>

    <div class="card-body mt-n5">
        <div class="position-relative mt-n3">
            <div class="avatar-lg position-relative">
                <img src="build/images/users/avatar-4.jpg" alt="user-img" class="img-thumbnail rounded-circle user-profile-image" style="z-index: 1;">
                <div class="avatar-xs p-0 rounded-circle profile-photo-edit position-absolute end-0 bottom-0">
                    <input id="profile-img-file-input" type="file" class="profile-img-file-input d-none">
                    <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                        <span class="avatar-title rounded-circle bg-light text-body">
                            <i class="bi bi-camera"></i>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
            <div class="mt-3">
                <h3 class="fs-xl mb-1">Alexandra Marshall</h3>
                <p class="fs-md text-muted mb-0">Owner & Founder</p>
            </div>

            <div class="">
                <a href="pages-profile-settings" class="btn btn-primary"><i class="ri-edit-box-line align-bottom"></i> Edit Profile</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3">
        <div class="card overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4 pb-2">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Complete Your Profile</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="javascript:void(0);" class="badge bg-light text-secondary"><i class="ri-edit-box-line align-bottom me-1"></i> Edit</a>
                    </div>
                </div>
                <div class="progress animated-progress custom-progress progress-label">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                        <div class="label">30%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Portfolio</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="javascript:void(0);" class="badge bg-info-subtle text-info fs-12"><i class="ri-add-fill align-bottom me-1"></i> Add</a>
                    </div>
                </div>
                <div class="mb-3 d-flex">
                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                        <span class="avatar-title rounded-circle bg-dark-subtle text-body">
                            <i class="bi bi-github"></i>
                        </span>
                    </div>
                    <input type="email" class="form-control" id="gitUsername" placeholder="Username" value="@alexandramarshall">
                </div>
                <div class="mb-3 d-flex">
                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                            <i class="bi bi-facebook"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="websiteInput" placeholder="www.example.com" value="www.vixon.com">
                </div>
                <div class="mb-3 d-flex">
                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                        <span class="avatar-title rounded-circle bg-success-subtle text-success">
                            <i class="bi bi-dribbble"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="dribbleName" placeholder="Username" value="@alexandra_marshall">
                </div>
                <div class="d-flex">
                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                        <span class="avatar-title rounded-circle bg-danger-subtle text-danger">
                            <i class="bi bi-instagram"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="pinterestName" placeholder="Username" value="Alexandra Marshall">
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class=" ">
                    <div class="d-flex align-items-center mb-3 pb-1">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Skills</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" class="badge bg-light text-secondary"><i class="ri-edit-box-line align-bottom me-1"></i> Edit</a>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 fs-15">
                        <a href="javascript:void(0);" class="badge text-primary bg-primary-subtle">Photoshop</a>
                        <a href="javascript:void(0);" class="badge text-primary bg-primary-subtle">illustrator</a>
                        <a href="javascript:void(0);" class="badge text-primary bg-primary-subtle">HTML</a>
                        <a href="javascript:void(0);" class="badge text-primary bg-primary-subtle">CSS</a>
                        <a href="javascript:void(0);" class="badge text-primary bg-primary-subtle">Javascript</a>
                        <a href="javascript:void(0);" class="badge text-primary bg-primary-subtle">Php</a>
                        <a href="javascript:void(0);" class="badge text-primary bg-primary-subtle">Python</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
    <div class="col-xl-9">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills nav-custom-outline nav-info gap-2 flex-grow-1 mb-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fs-md active" data-bs-toggle="tab" href="#personalDetails" role="tab" aria-selected="true">
                            Personal Details
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fs-md" data-bs-toggle="tab" href="#changePassword" role="tab" aria-selected="false" tabindex="-1">
                            Changes Password
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fs-md" data-bs-toggle="tab" href="#education" role="tab" aria-selected="false" tabindex="-1">
                            Education
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fs-md" data-bs-toggle="tab" href="#securityPrivacy" role="tab" aria-selected="false" tabindex="-1">
                            Security & Privacy
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card">
            <div class="tab-content">

                <div class="tab-pane active" id="personalDetails" role="tabpanel">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Personal Details</h6>
                    </div>
                    <div class="card-body">
                        <form action="javascript:void(0);">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="firstnameInput" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstnameInput" placeholder="Enter your firstname" value="Richard">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="lastnameInput" class="form-label fs-md">Last Name</label>
                                        <input type="text" class="form-control fs-md" id="lastnameInput" placeholder="Enter your last name" value="Marshall">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phonenumberInput" class="form-label fs-md">Phone Number</label>
                                        <input type="text" class="form-control fs-md" id="phonenumberInput" placeholder="Enter your phone number" value="617 219 6245">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="emailInput" class="form-label fs-md">Email Address</label>
                                        <input type="email" class="form-control fs-md" id="emailInput" placeholder="Enter your email" value="alexandramarshall@steex.com">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="birthDateInput" class="form-label fs-md">Birth of Date</label>
                                        <input type="text" class="form-control fs-md" data-provider="flatpickr" id="birthDateInput" data-date-format="d M, Y" data-default-date="24 June, 1998" placeholder="Select date">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="JoiningdatInput" class="form-label fs-md">Joining Date</label>
                                        <input type="text" class="form-control fs-md" data-provider="flatpickr" id="JoiningdatInput" data-date-format="d M, Y" data-default-date="30 Oct, 2023" placeholder="Select date">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="skillsInput" class="form-label fs-md">Skills</label>
                                        <select class="form-control fs-md" name="skillsInput" data-choices data-choices-text-unique-true multiple id="skillsInput">
                                            <option value="illustrator" class="fs-sm">Illustrator</option>
                                            <option value="photoshop" class="fs-sm">Photoshop</option>
                                            <option value="css" class="fs-sm">CSS</option>
                                            <option value="html" class="fs-sm">HTML</option>
                                            <option value="javascript" class="fs-sm" selected>Javascript</option>
                                            <option value="python" class="fs-sm">Python</option>
                                            <option value="php" class="fs-sm">PHP</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="designationInput" class="form-label fs-md">Designation</label>
                                        <input type="text" class="form-control fs-md" id="designationInput" placeholder="Designation" value="Web Developer">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="websiteInput1" class="form-label fs-md">Website</label>
                                        <input type="text" class="form-control fs-md" id="websiteInput1" placeholder="www.example.com" value="www.themesbrand.com">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="cityInput" class="form-label fs-md">City</label>
                                        <input type="text" class="form-control fs-md" id="cityInput" placeholder="City" value="Phoenix">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="countryInput" class="form-label fs-md">Country</label>
                                        <input type="text" class="form-control fs-md" id="countryInput" placeholder="Country" value="USA">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="zipcodeInput" class="form-label fs-md">Zip Code</label>
                                        <input type="text" class="form-control fs-md" minlength="5" maxlength="6" id="zipcodeInput" placeholder="Enter zipcode" value="00012">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="mb-3 pb-2">
                                        <label for="exampleFormControlTextarea" class="form-label fs-md">Description</label>
                                        <textarea class="form-control fs-md" id="exampleFormControlTextarea" placeholder="Enter your description" rows="5">A Web Developer creates and designs different websites for clients. They are responsible for their aesthetic as well as their function. Professionals in this field may also need to be able to ensure sites are compatible with multiple types of media. Web Developers need to have a firm understanding of programming and graphical design. Having a strong resume that emphasizes these attributes makes it significantly easier to get hired as a Web Developer. As a web designer, my objective is to make a positive impact on clients, co-workers, and the Internet using my skills and experience to design compelling and attractive websites. Solving code problems. Editing & Design with designing team in the company to build perfect web designs.</textarea>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">Updates</button>
                                        <button type="button" class="btn btn-subtle-danger">Cancel</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                </div>
                <!--end tab-pane-->
                <div class="tab-pane" id="changePassword" role="tabpanel">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Changes Password</h6>
                    </div>
                    <div class="card-body">
                        <form action="pages-profile-settings">
                            <div class="row g-2 justify-content-lg-between align-items-center">
                                <div class="col-lg-4">
                                    <div class="auth-pass-inputgroup">
                                        <label for="oldpasswordInput" class="form-label fs-md">Old Password*</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control fs-md password-input" id="oldpasswordInput" placeholder="Enter current password">
                                            <button class="btn btn-link shadow-none position-absolute top-0 end-0 text-decoration-none text-muted password-addon" type="button"><i class="ri-eye-fill align-middle"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="auth-pass-inputgroup">
                                        <label for="password-input" class="form-label fs-md">New Password*</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control password-input fs-md" id="password-input" onpaste="return false" placeholder="Enter new password" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                            <button class="btn btn-link shadow-none position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button"><i class="ri-eye-fill align-middle"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="auth-pass-inputgroup">
                                        <label for="confirm-password-input" class="form-label fs-md">Confirm Password*</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control password-input fs-md" onpaste="return false" id="confirm-password-input" placeholder="Confirm password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                            <button class="btn btn-link shadow-none position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button"><i class="ri-eye-fill align-middle"></i></button>
                                        </div>

                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);" class="link-primary text-decoration-underline fs-md">Forgot Password ?</a>
                                    <div class="">

                                        <button type="submit" class="btn btn-info">Change Password</button>
                                    </div>
                                </div>

                                <!--end col-->

                                <div class="col-lg-12">
                                    <div class="card bg-light shadow-none passwd-bg" id="password-contain">
                                        <div class="card-body">
                                            <div class="mb-4">
                                                <h5 class="fs-sm">Password must contain:</h5>
                                            </div>
                                            <div class="">
                                                <p id="pass-length" class="invalid fs-xs mb-2">Minimum <b>8 characters</b></p>
                                                <p id="pass-lower" class="invalid fs-xs mb-2">At <b>lowercase</b> letter (a-z)</p>
                                                <p id="pass-upper" class="invalid fs-xs mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                <p id="pass-number" class="invalid fs-xs mb-0">A least <b>number</b> (0-9)</p>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--end row-->
                        </form>
                        <div class="mt-4 mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title  mb-0">Login History</h5>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-secondary">All Logout</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">Mobile</th>
                                                <th scope="col">IP Address</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Address</th>
                                                <th scope="col"><i class="ri-logout-box-r-line"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><i class="bi bi-phone align-baseline me-1"></i> iPhone 12 Pro</td>
                                                <td>192.44.234.160</td>
                                                <td>18 Dec, 2023</td>
                                                <td>Los Angeles, United States</td>
                                                <td><a href="#" class="icon-link icon-link-hover">Logout <i class="bi bi-box-arrow-right"></i></a></td>
                                            </tr>

                                            <tr>
                                                <td><i class="bi bi-tablet align-baseline me-1"></i> Apple iPad Pro</td>
                                                <td>192.44.234.162</td>
                                                <td>03 Jan, 2023</td>
                                                <td>Phoenix, United States</td>
                                                <td><a href="#" class="icon-link icon-link-hover">Logout <i class="bi bi-box-arrow-right"></i></a></td>
                                            </tr>

                                            <tr>
                                                <td><i class="bi bi-phone align-baseline me-1"></i> Galaxy S21 Ultra 5G</td>
                                                <td>192.45.234.54</td>
                                                <td>25 Feb, 2023</td>
                                                <td>Washington, United States</td>
                                                <td><a href="#" class="icon-link icon-link-hover">Logout <i class="bi bi-box-arrow-right"></i></a></td>
                                            </tr>

                                            <tr>
                                                <td><i class="bi bi-laptop align-baseline me-1"></i> Dell Inspiron 14</td>
                                                <td>192.40.234.32</td>
                                                <td>16 Oct, 2022</td>
                                                <td>Phoenix, United States</td>
                                                <td><a href="#" class="icon-link icon-link-hover">Logout <i class="bi bi-box-arrow-right"></i></a></td>
                                            </tr>

                                            <tr>
                                                <td><i class="bi bi-phone align-baseline me-1"></i> iPhone 12 Pro</td>
                                                <td>192.44.326.42</td>
                                                <td>22 May, 2022</td>
                                                <td>Conneticut, United States</td>
                                                <td><a href="#" class="icon-link icon-link-hover">Logout <i class="bi bi-box-arrow-right"></i></a></td>

                                            </tr>

                                            <tr>
                                                <td><i class="bi bi-tablet align-baseline me-1"></i> Apple iPad Pro</td>
                                                <td>190.44.182.33</td>
                                                <td>19 Nov, 2023</td>
                                                <td>Los Angeles, United States</td>
                                                <td><a href="#" class="icon-link icon-link-hover">Logout <i class="bi bi-box-arrow-right"></i></a></td>

                                            </tr>

                                            <tr>
                                                <td><i class="bi bi-phone align-baseline me-1"></i> Galaxy S21 Ultra 5G</td>
                                                <td>194.44.235.87</td>
                                                <td>30 Aug, 2022</td>
                                                <td>Conneticut, United States</td>
                                                <td><a href="#" class="icon-link icon-link-hover">Logout <i class="bi bi-box-arrow-right"></i></a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end tab-pane-->
                <div class="tab-pane" id="education" role="tabpanel">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Education</h6>
                    </div>
                    <div class="card-body">
                        <form>
                            <div id="newlink">
                                <div id="1">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="degreeName" class="form-label fs-md">Degree Name</label>
                                                <input type="text" class="form-control fs-md" id="degreeName" placeholder="Degree name">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="universityName" class="form-label fs-md">University/school Name</label>
                                                <input type="text" class="form-control fs-md" id="universityName" placeholder="University/school name">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="passedYear" class="form-label fs-md">Passed Years</label>
                                                <div class="row g-2 justify-content-center">
                                                    <div class="col-lg-5">
                                                        <select class="form-control" data-choices data-choices-search-false name="passedYear" id="passedYear">
                                                            <option value="">Select years</option>
                                                            <option value="Choice 1">2001</option>
                                                            <option value="Choice 2">2002</option>
                                                            <option value="Choice 3">2003</option>
                                                            <option value="Choice 4">2004</option>
                                                            <option value="Choice 5">2005</option>
                                                            <option value="Choice 6">2006</option>
                                                            <option value="Choice 7">2007</option>
                                                            <option value="Choice 8">2008</option>
                                                            <option value="Choice 9">2009</option>
                                                            <option value="Choice 10">2010</option>
                                                            <option value="Choice 11">2011</option>
                                                            <option value="Choice 12">2012</option>
                                                            <option value="Choice 13">2013</option>
                                                            <option value="Choice 14">2014</option>
                                                            <option value="Choice 15">2015</option>
                                                            <option value="Choice 16">2016</option>
                                                            <option value="Choice 17" selected>2017</option>
                                                            <option value="Choice 18">2018</option>
                                                            <option value="Choice 19">2019</option>
                                                            <option value="Choice 20">2020</option>
                                                            <option value="Choice 21">2021</option>
                                                            <option value="Choice 22">2022</option>
                                                        </select>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-auto align-self-center">
                                                        to
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-5">
                                                        <select class="form-control fs-md" data-choices data-choices-search-false>
                                                            <option value="">Select years</option>
                                                            <option value="Choice 1">2001</option>
                                                            <option value="Choice 2">2002</option>
                                                            <option value="Choice 3">2003</option>
                                                            <option value="Choice 4">2004</option>
                                                            <option value="Choice 5">2005</option>
                                                            <option value="Choice 6">2006</option>
                                                            <option value="Choice 7">2007</option>
                                                            <option value="Choice 8">2008</option>
                                                            <option value="Choice 9">2009</option>
                                                            <option value="Choice 10">2010</option>
                                                            <option value="Choice 11">2011</option>
                                                            <option value="Choice 12">2012</option>
                                                            <option value="Choice 13">2013</option>
                                                            <option value="Choice 14">2014</option>
                                                            <option value="Choice 15">2015</option>
                                                            <option value="Choice 16">2016</option>
                                                            <option value="Choice 17">2017</option>
                                                            <option value="Choice 18">2018</option>
                                                            <option value="Choice 19">2019</option>
                                                            <option value="Choice 20" selected>2020</option>
                                                            <option value="Choice 21">2021</option>
                                                            <option value="Choice 22">2022</option>
                                                        </select>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="degreeDescription" class="form-label fs-md">Degree Description</label>
                                                <textarea class="form-control fs-md" id="degreeDescription" rows="3" placeholder="Enter description"></textarea>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="hstack gap-2 justify-content-end">
                                            <a class="btn btn-danger" href="javascript:deleteEl(1)">Delete</a>
                                        </div>
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>
                            <div id="newForm" style="display: none;">

                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2">
                                    <button type="submit" class="btn btn-secondary">Update</button>
                                    <a href="javascript:new_link()" class="btn btn-primary">Add New</a>
                                </div>
                            </div>
                            <!--end col-->
                        </form>
                    </div>
                </div>
                <!--end tab-pane-->
                <div class="tab-pane" id="securityPrivacy" role="tabpanel">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Security & Privacy</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 pb-2">
                            <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0">
                                <div class="flex-grow-1">
                                    <h6 class="fs-md mb-1">Two-factor Authentication</h6>
                                    <p class="text-muted fs-sm">Two-factor authentication is an enhanced security. Once enabled, you'll be required to give two types of identification when you log into Google Authentication and SMS are Supported.</p>
                                </div>
                                <div class="flex-shrink-0 ms-sm-3">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary">Enable Two-factor Authentication</a>
                                </div>
                            </div>
                            <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                                <div class="flex-grow-1">
                                    <h6 class="fs-md mb-1">Secondary Verification</h6>
                                    <p class="text-muted fs-sm">The first factor is a password and the second commonly includes a text with a code sent to your smartphone, or biometrics using your fingerprint, face, or retina.</p>
                                </div>
                                <div class="flex-shrink-0 ms-sm-3">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary">Set up secondary method</a>
                                </div>
                            </div>
                            <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                                <div class="flex-grow-1">
                                    <h6 class="fs-md mb-1">Backup Codes</h6>
                                    <p class="text-muted mb-sm-0">A backup code is automatically generated for you when you turn on two-factor authentication through your iOS or Android Twitter app. You can also generate a backup code on twitter.com.</p>
                                </div>
                                <div class="flex-shrink-0 ms-sm-3">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary">Generate backup codes</a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h5 class="card-title text-decoration-underline mb-3">Application Notifications:</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex">
                                    <div class="flex-grow-1">
                                        <label for="directMessage" class="form-check-label fs-md">Direct messages</label>
                                        <p class="text-muted fs-sm">Messages from people you follow</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="directMessage" checked>
                                        </div>
                                    </div>
                                </li>
                                <li class="d-flex mt-2">
                                    <div class="flex-grow-1">
                                        <label class="form-check-label fs-md mb-1" for="desktopNotification">
                                            Show desktop notifications
                                        </label>
                                        <p class="text-muted fs-sm">Choose the option you want as your default setting. Block a site: Next to "Not allowed to send notifications," click Add.</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="desktopNotification" checked>
                                        </div>
                                    </div>
                                </li>
                                <li class="d-flex mt-2">
                                    <div class="flex-grow-1">
                                        <label class="form-check-label fs-md mb-1" for="emailNotification">
                                            Show email notifications
                                        </label>
                                        <p class="text-muted fs-sm"> Under Settings, choose Notifications. Under Select an account, choose the account to enable notifications for. </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="emailNotification">
                                        </div>
                                    </div>
                                </li>
                                <li class="d-flex mt-2">
                                    <div class="flex-grow-1">
                                        <label class="form-check-label fs-md mb-1" for="chatNotification">
                                            Show chat notifications
                                        </label>
                                        <p class="text-muted fs-sm">To prevent duplicate mobile notifications from the Gmail and Chat apps, in settings, turn off Chat notifications.</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="chatNotification">
                                        </div>
                                    </div>
                                </li>
                                <li class="d-flex mt-2">
                                    <div class="flex-grow-1">
                                        <label class="form-check-label fs-md mb-1" for="purchaesNotification">
                                            Show purchase notifications
                                        </label>
                                        <p class="text-muted fs-sm">Get real-time purchase alerts to protect yourself from fraudulent charges.</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="purchaesNotification">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="card-title text-decoration-underline mb-3">Delete This Account:</h5>
                            <p class="text-muted fs-sm">Go to the Data & Privacy section of your profile Account. Scroll to "Your data & privacy options." Delete your Profile Account. Follow the instructions to delete your account :</p>
                            <div>
                                <input type="password" class="form-control" id="passwordInput" placeholder="Enter your password" value="richard@321654987" style="max-width: 265px;">
                            </div>
                            <div class="hstack gap-2 mt-3">
                                <a href="javascript:void(0);" class="btn btn-subtle-danger">Close & Delete This Account</a>
                                <a href="javascript:void(0);" class="btn btn-light">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end tab-pane-->
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->
@endsection
@section('script')
<script src="{{ URL::asset('build/js/pages/passowrd-create.init.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/profile-setting.init.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
