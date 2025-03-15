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

                    <!-- Dashboard Menu -->
                    @php
                        $dashboardMenus = auth()->user()->roles->first()->permissions()
                            ->whereIn('menu_id', function($query) {
                                $query->select('id')
                                    ->from('menus')
                                    ->where('parent_id', function($subquery) {
                                        $subquery->select('id')
                                            ->from('menus')
                                            ->where('slug', 'dashboards');
                                    });
                            })
                            ->where('can_view', 1)
                            ->get();
                    @endphp

                    @if($dashboardMenus->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link menu-link collapsed" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ti ti-brand-google-home"></i> <span data-key="t-dashboards">@lang('translation.dashboards')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarDashboards">
                            <ul class="nav nav-sm flex-column">
                                @foreach($dashboardMenus as $permission)
                                    @php $menu = \App\Models\Menu::find($permission->menu_id); @endphp
                                    @if($menu)
                                        <li class="nav-item">
                                            <a href="{{ url($menu->route) }}" class="nav-link" data-key="t-{{ $menu->slug }}">
                                                @lang('translation.' . $menu->slug)
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- Transaction Menu -->
                    @php
                        $transactionMenus = auth()->user()->roles->first()->permissions()
                            ->whereIn('menu_id', function($query) {
                                $query->select('id')
                                    ->from('menus')
                                    ->where('parent_id', function($subquery) {
                                        $subquery->select('id')
                                            ->from('menus')
                                            ->where('slug', 'transaction');
                                    });
                            })
                            ->where('can_view', 1)
                            ->get();
                    @endphp

                    @if($transactionMenus->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTransaction" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTransaction">
                            <i class="ri-exchange-dollar-line"></i> <span>{{ __('translation.sidebar.transaction') }}</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTransaction">
                            <ul class="nav nav-sm flex-column">
                              
                                @foreach($transactionMenus as $permission)
                                    @php $menu = \App\Models\Menu::find($permission->menu_id); @endphp
                                    @if($menu)
                                        <li class="nav-item">
                                            <a href="{{ url($menu->route) }}" class="nav-link">
                                                @lang('translation.' . $menu->slug . '.title')
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- Master Data Menu -->
                    @php
                        $masterDataMenus = auth()->user()->roles->first()->permissions()
                            ->whereIn('menu_id', function($query) {
                                $query->select('id')
                                    ->from('menus')
                                    ->where('parent_id', function($subquery) {
                                        $subquery->select('id')
                                            ->from('menus')
                                            ->where('slug', 'master-data');
                                    });
                            })
                            ->where('can_view', 1)
                            ->get();
                    @endphp

                    @if($masterDataMenus->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarMasterData" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMasterData">
                            <i class="ri-database-2-line"></i> <span>@lang('translation.master_data')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarMasterData">
                            <ul class="nav nav-sm flex-column">
                                @foreach($masterDataMenus as $permission)
                                    @php 
                                        $menu = \App\Models\Menu::find($permission->menu_id);
                                        // Map route names correctly based on web.php
                                        $routeMap = [
                                            '/sub-categories' => 'sub-categories.index',
                                            '/categories' => 'categories.index',
                                            '/items' => 'items.index',
                                            '/units' => 'units.index',
                                            '/regions' => 'regions.index',
                                            '/warehouses' => 'warehouses.index',
                                            '/customers' => 'customers.index'
                                        ];
                                        $routeName = $routeMap[$menu->route] ?? $menu->route;
                                    @endphp
                                    @if($menu && Route::has($routeName))
                                        <li class="nav-item">
                                            <a href="{{ route($routeName) }}" class="nav-link">
                                                {{ $menu->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- Reports Menu -->
                    @php
                        $reportMenus = auth()->user()->roles->first()->permissions()
                            ->whereIn('menu_id', function($query) {
                                $query->select('id')
                                    ->from('menus')
                                    ->where('parent_id', function($subquery) {
                                        $subquery->select('id')
                                            ->from('menus')
                                            ->where('slug', 'reports');
                                    });
                            })
                            ->where('can_view', 1)
                            ->get();

                        // Map untuk route reports yang benar
                        $reportRouteMap = [
                            '/reports/fj' => 'reports.fj.index',
                            '/reports/rekap-fj' => 'reports.rekap-fj.index'
                        ];
                    @endphp

                    @if($reportMenus->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarReports" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarReports">
                            <i class="ri-file-chart-line"></i> <span>@lang('translation.reports.title')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarReports">
                            <ul class="nav nav-sm flex-column">
                                @foreach($reportMenus as $permission)
                                    @php 
                                        $menu = \App\Models\Menu::find($permission->menu_id);
                                        $routeName = $reportRouteMap[$menu->route] ?? $menu->route;
                                    @endphp
                                    @if($menu && Route::has($routeName))
                                        <li class="nav-item">
                                            <a href="{{ route($routeName) }}" class="nav-link">
                                                {{ $menu->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- User Management Menu -->
                    <li class="nav-item">
                        @php
                            $userManagementMenus = auth()->user()->roles->first()->permissions()
                                ->whereIn('menu_id', function($query) {
                                    $query->select('id')
                                        ->from('menus')
                                        ->where('parent_id', function($subquery) {
                                            $subquery->select('id')
                                                ->from('menus')
                                                ->where('slug', 'user-management');
                                        });
                                })
                                ->where('can_view', 1)
                                ->get();
                        @endphp

                        @if($userManagementMenus->count() > 0)
                            <a class="nav-link menu-link" href="#sidebarUserManagement" data-bs-toggle="collapse" role="button" 
                                aria-expanded="false" aria-controls="sidebarUserManagement">
                                <i class="ri-user-settings-line"></i> 
                                <span>{{ trans('translation.user_management.title') }}</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarUserManagement">
                                <ul class="nav nav-sm flex-column">
                                    @foreach($userManagementMenus as $permission)
                                        @php
                                            $menu = \App\Models\Menu::find($permission->menu_id);
                                        @endphp
                                        @if($menu)
                                            <li class="nav-item">
                                                <a href="{{ url($menu->route) }}" class="nav-link">
                                                    <i class="{{ $menu->icon }}"></i> 
                                                    <span>{{ $menu->name }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
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