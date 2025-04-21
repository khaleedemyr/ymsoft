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
                                            '/customers' => 'customers.index',
                                            '/master-data/suppliers' => 'master-data.suppliers.index'
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

                    <!-- Warehouse Management Menu -->
                    @php
                        $warehouseMenus = auth()->user()->roles->first()->permissions()
                            ->whereIn('menu_id', function($query) {
                                $query->select('id')
                                    ->from('menus')
                                    ->where('parent_id', function($subquery) {
                                        $subquery->select('id')
                                            ->from('menus')
                                            ->where('slug', 'warehouse-management');
                                    });
                            })
                            ->where('can_view', 1)
                            ->get();

                        // Map untuk route warehouse yang benar
                        $warehouseRouteMap = [
                            '/warehouse/good-receives' => 'warehouse.good-receives.index',
                            '/warehouse/reports/inventory' => 'warehouse.reports.inventory.index',
                            '/warehouse/reports/stock-card' => 'warehouse.reports.stock-card.index',
                            '/warehouse/reports/stock-analysis' => 'warehouse.reports.stock-analysis.index'
                        ];
                    @endphp

                    @if($warehouseMenus->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('warehouse*') ? 'active' : '' }}" href="#sidebarWarehouse" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('warehouse*') ? 'true' : 'false' }}" aria-controls="sidebarWarehouse">
                            <i class="ri-store-2-line"></i> <span>@lang('translation.warehouse_management.title')</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->is('warehouse*') ? 'show' : '' }}" id="sidebarWarehouse">
                            <ul class="nav nav-sm flex-column">
                                @foreach($warehouseMenus as $permission)
                                    @php 
                                        $menu = \App\Models\Menu::find($permission->menu_id);
                                        $routeName = $warehouseRouteMap[$menu->route] ?? $menu->route;
                                    @endphp
                                    @if($menu && Route::has($routeName))
                                        <li class="nav-item">
                                            <a href="{{ route($routeName) }}" class="nav-link {{ request()->is($menu->route . '*') ? 'active' : '' }}">
                                                <i class="{{ $menu->icon }}"></i> {{ $menu->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- Purchasing Menu -->
                    @php
                        $purchasingMenus = auth()->user()->roles->first()->permissions()
                            ->whereIn('menu_id', function($query) {
                                $query->select('id')
                                    ->from('menus')
                                    ->where('parent_id', function($subquery) {
                                        $subquery->select('id')
                                            ->from('menus')
                                            ->where('slug', 'purchasing');
                                    });
                            })
                            ->where('can_view', 1)
                            ->get();
                    @endphp

                    @if($purchasingMenus->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('purchasing*') ? 'active' : '' }}" href="#purchasingMenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('purchasing*') ? 'true' : 'false' }}" aria-controls="purchasingMenu">
                            <i class="ri-shopping-cart-2-line"></i> <span>@lang('translation.purchasing.title')</span>
                        </a>
                        <div class="collapse {{ request()->is('purchasing*') ? 'show' : '' }}" id="purchasingMenu">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('purchasing.purchase-requisitions.index') }}" class="nav-link {{ request()->is('purchasing/purchase-requisitions*') ? 'active' : '' }}">
                                        <i class="ri-file-list-line"></i> @lang('translation.purchase_requisition.title')
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('purchasing.purchase-orders.index') }}" class="nav-link {{ request()->is('purchasing/purchase-orders*') ? 'active' : '' }}">
                                        <i class="ri-shopping-bag-line"></i> @lang('translation.purchase_order.title')
                                    </a>
                                </li>
                                <!-- Tambahkan submenu lain seperti Good Receive, Purchase Invoice, dll jika diperlukan -->
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- Finance Menu -->
                    @php
                        $financeMenus = auth()->user()->roles->first()->permissions()
                            ->whereIn('menu_id', function($query) {
                                $query->select('id')
                                    ->from('menus')
                                    ->where('parent_id', function($subquery) {
                                        $subquery->select('id')
                                            ->from('menus')
                                            ->where('slug', 'finance');
                                    });
                            })
                            ->where('can_view', 1)
                            ->get();
                    @endphp

                    @if($financeMenus->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('finance*') ? 'active' : '' }}" href="#financeMenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('finance*') ? 'true' : 'false' }}" aria-controls="financeMenu">
                            <i class="ri-money-dollar-circle-line"></i> <span>@lang('translation.finance.title')</span>
                        </a>
                        <div class="collapse {{ request()->is('finance*') ? 'show' : '' }}" id="financeMenu">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('finance.purchase-invoices.index') }}" class="nav-link {{ request()->routeIs('finance.purchase-invoices.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file-invoice"></i>
                                        <p>{{ trans('translation.purchase_invoice.title') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('finance.contra-bons.index') }}" class="nav-link {{ request()->routeIs('finance.contra-bons.*') ? 'active' : '' }}">
                                        <i class="nav-icon ri-bill-fill"></i>
                                        <p>{{ trans('translation.contra_bon.title') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('finance.payments.index') }}" class="nav-link {{ request()->routeIs('finance.payments.*') ? 'active' : '' }}">
                                        <i class="nav-icon ri-money-dollar-circle-line"></i>
                                        <p>{{ trans('translation.payment.title') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('finance.aging-report.index') }}" class="nav-link {{ request()->routeIs('finance.aging-report.*') ? 'active' : '' }}">
                                        <i class="nav-icon ri-file-chart-line"></i>
                                        <p>{{ trans('translation.aging_report.title') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('finance.payment-planning.index') }}" class="nav-link {{ request()->routeIs('finance.payment-planning.*') ? 'active' : '' }}">
                                        <i class="nav-icon ri-calendar-check-line"></i>
                                        <p>{{ trans('translation.payment_planning.title') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('finance.payment-history.supplier.index') }}" class="nav-link {{ request()->routeIs('finance.payment-history.supplier.*') ? 'active' : '' }}">
                                        <i class="nav-icon ri-history-line"></i>
                                        <p>{{ trans('translation.payment_history.supplier.title') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('finance.payment-history.summary.index') }}" class="nav-link {{ request()->routeIs('finance.payment-history.summary.*') ? 'active' : '' }}">
                                        <i class="nav-icon ri-file-chart-line"></i>
                                        <p>{{ trans('translation.payment_history.summary.title') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#outstandingInvoiceMenu" class="nav-link {{ request()->is('finance/outstanding-invoice*') ? 'active' : '' }}" data-bs-toggle="collapse">
                                        <i class="nav-icon ri-file-list-3-line"></i>
                                        <p>
                                            {{ trans('translation.outstanding_invoice.title') }}
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <div class="collapse {{ request()->is('finance/outstanding-invoice*') ? 'show' : '' }}" id="outstandingInvoiceMenu">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{ route('finance.outstanding-invoice.invoices') }}" 
                                                   class="nav-link {{ request()->routeIs('finance.outstanding-invoice.invoices') ? 'active' : '' }}">
                                                    <i class="nav-icon ri-file-list-line"></i>
                                                    <p>{{ trans('translation.outstanding_invoice.invoices.title') }}</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('finance.outstanding-invoice.contra-bon') }}" 
                                                   class="nav-link {{ request()->routeIs('finance.outstanding-invoice.contra-bon') ? 'active' : '' }}">
                                                    <i class="nav-icon ri-bill-line"></i>
                                                    <p>{{ trans('translation.outstanding_invoice.contra_bon.title') }}</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
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

                    <!-- Maintenance Menu -->
                    <li class="nav-item">
                        @php
                            $maintenanceMenus = auth()->user()->roles->first()->permissions()
                                ->whereIn('menu_id', function($query) {
                                    $query->select('id')
                                        ->from('menus')
                                        ->where('parent_id', function($subquery) {
                                            $subquery->select('id')
                                                ->from('menus')
                                                ->where('slug', 'maintenance');
                                        });
                                })
                                ->where('can_view', 1)
                                ->get();
                        @endphp

                        @if($maintenanceMenus->count() > 0)
                            <a class="nav-link menu-link" href="#sidebarMaintenance" data-bs-toggle="collapse" role="button" 
                                aria-expanded="false" aria-controls="sidebarMaintenance">
                                <i class="ri-tools-fill"></i> 
                                <span>Maintenance</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarMaintenance">
                                <ul class="nav nav-sm flex-column">
                                    @foreach($maintenanceMenus as $permission)
                                        @php
                                            $menu = \App\Models\Menu::find($permission->menu_id);
                                        @endphp
                                        @if($menu)
                                            <li class="nav-item">
                                                <a href="{{ route($menu->route) }}" class="nav-link">
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

                    <!-- Daily Check Menu -->
                    <li class="nav-item">
                        @php
                            $dailyCheckMenus = auth()->user()->roles->first()->permissions()
                                ->whereIn('menu_id', function($query) {
                                    $query->select('id')
                                        ->from('menus')
                                        ->where('parent_id', function($subquery) {
                                            $subquery->select('id')
                                                ->from('menus')
                                                ->where('slug', 'daily-check');
                                        });
                                })
                                ->where('can_view', 1)
                                ->get();
                        @endphp

                        @if($dailyCheckMenus->count() > 0)
                            <a class="nav-link menu-link" href="#sidebarDailyCheck" data-bs-toggle="collapse" role="button" 
                                aria-expanded="false" aria-controls="sidebarDailyCheck">
                                <i class="ri-clipboard-check-line"></i> 
                                <span>Daily Check</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarDailyCheck">
                                <ul class="nav nav-sm flex-column">
                                    @foreach($dailyCheckMenus as $permission)
                                        @php
                                            $menu = \App\Models\Menu::find($permission->menu_id);
                                        @endphp
                                        @if($menu)
                                            <li class="nav-item">
                                                <a href="{{ route($menu->route) }}" class="nav-link">
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

                    <!-- Operational Tools Menu -->
                    <li class="nav-item">
                        @php
                            $operationalToolsMenus = auth()->user()->roles->first()->permissions()
                                ->whereIn('menu_id', function($query) {
                                    $query->select('id')
                                        ->from('menus')
                                        ->where('parent_id', function($subquery) {
                                            $subquery->select('id')
                                                ->from('menus')
                                                ->where('slug', 'operational-tools');
                                        });
                                })
                                ->where('can_view', 1)
                                ->get();
                        @endphp

                        @if($operationalToolsMenus->count() > 0)
                            <a class="nav-link menu-link" href="#sidebarOperationalTools" data-bs-toggle="collapse" role="button" 
                                aria-expanded="false" aria-controls="sidebarOperationalTools">
                                <i class="ri-survey-line"></i> 
                                <span>Operational Tools</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarOperationalTools">
                                <ul class="nav nav-sm flex-column">
                                    @foreach($operationalToolsMenus as $permission)
                                        @php
                                            $menu = \App\Models\Menu::find($permission->menu_id);
                                        @endphp
                                        @if($menu)
                                            <li class="nav-item">
                                                <a href="{{ route($menu->route) }}" class="nav-link">
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