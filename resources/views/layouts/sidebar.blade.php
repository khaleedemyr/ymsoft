<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="build/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="build/images/logo-dark.png" alt="" height="22">
            </span>
        </a>
        <a href="index" class="logo logo-light">
            <span class="logo-sm">
                <img src="build/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="build/images/logo-light.png" alt="" height="22">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover shadow-none" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <div class="navbar-navigation data-simplebar">
                <ul class="navbar-nav" id="navbar-nav">

                    <li class="menu-title"><span data-key="t-menu">@lang('translation.menu')</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link collapsed" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ti ti-brand-google-home"></i> <span data-key="t-dashboards">@lang('translation.dashboards')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarDashboards">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="index" class="nav-link" data-key="t-analytics">  @lang('translation.analytics')  </a>
                                </li>
                                <li class="nav-item">
                                    <a href="dashboard-ecommerce" class="nav-link" data-key="t-ecommerce"> @lang('translation.ecommerce') </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTransaction" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTransaction">
                            <i class="ri-exchange-dollar-line"></i> <span>Transaction</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTransaction">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('sales.index') }}" class="nav-link">@lang('translation.sales.title')</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarMasterData" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMasterData">
                            <i class="ri-database-2-line"></i> <span>@lang('translation.master_data')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarMasterData">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('categories.index') }}" class="nav-link">@lang('translation.category.title')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('sub-categories.index') }}" class="nav-link">@lang('translation.subcategory.title')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('units.index') }}" class="nav-link">@lang('translation.unit.title')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('regions.index') }}" class="nav-link">@lang('translation.region.title')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('items.index') }}" class="nav-link">@lang('translation.item.title')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('warehouses.index') }}" class="nav-link">@lang('translation.warehouse.title')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('customers.index') }}" class="nav-link">@lang('translation.customer.title')</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Reports Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarReports" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarReports">
                            <i class="ri-file-chart-line"></i> <span>@lang('translation.reports.title')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarReports">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('reports.fj.index') }}" class="nav-link">@lang('translation.reports.fj.title')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reports.rekap-fj.index') }}" class="nav-link">@lang('translation.reports.rekap_fj.title')</a>
                                </li>
                                <!-- Tambahkan menu report lain di sini -->
                            </ul>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>