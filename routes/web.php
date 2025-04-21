<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportFJController;
use App\Http\Controllers\RekapFJController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FloorOrderController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodReceiveController;
use App\Http\Controllers\Warehouse\Report\InventoryReportController;
use App\Http\Controllers\Warehouse\Report\StockCardReportController;
use App\Http\Controllers\Warehouse\Report\StockAnalysisReportController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\ContraBonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AgingReportController;
use App\Http\Controllers\Finance\PaymentPlanningController;
use App\Http\Controllers\Finance\PaymentHistoryController;
use App\Http\Controllers\Finance\OutstandingInvoiceController;
use App\Http\Controllers\MaintenanceKanbanController;
use App\Http\Controllers\MaintenanceTaskController;
use App\Http\Controllers\MaintenanceCommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MaintenancePurchaseRequisitionController;
use App\Http\Controllers\MaintenanceUnitController;
use App\Http\Controllers\MaintenanceEvidenceController;
use App\Http\Controllers\Maintenance\DashboardController;
use App\Http\Controllers\Maintenance\ReportController;
use App\Http\Controllers\DailyCheckController;
use App\Http\Controllers\CalendarEventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Maintenance\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');
Route::get('index/{locale}',[App\Http\Controllers\HomeController::class, 'lang']);

Route::post('/signature/upload', [SignatureController::class, 'upload'])->name('signature.upload');

Route::middleware(['auth'])->group(function () {
    // Calendar Events Routes
    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/', [CalendarEventController::class, 'index'])->name('index');
        Route::get('/events', [CalendarEventController::class, 'getEvents'])->name('events');
        Route::get('/upcoming', [CalendarEventController::class, 'upcoming'])->name('upcoming');
        Route::get('/activities', [CalendarEventController::class, 'activities'])->name('activities');
        Route::post('/', [CalendarEventController::class, 'store'])->name('store');
        Route::get('/{event}', [CalendarEventController::class, 'show'])->name('show');
        Route::put('/{event}', [CalendarEventController::class, 'update'])->name('update');
        Route::delete('/{event}', [CalendarEventController::class, 'destroy'])->name('destroy');
    });

    Route::resource('categories', CategoryController::class);
    Route::resource('sub-categories', SubCategoryController::class);
    Route::put('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::put('sub-categories/{subCategory}/toggle-status', [SubCategoryController::class, 'toggleStatus'])
        ->name('sub-categories.toggle-status');
    Route::resource('units', UnitController::class);
    Route::put('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('units.toggle-status');
    Route::resource('regions', RegionController::class);
    Route::put('regions/{region}/toggle-status', [RegionController::class, 'toggleStatus'])
        ->name('regions.toggle-status');
    Route::get('items/export-excel', [ItemController::class, 'exportExcel'])->name('items.export-excel');
    Route::get('items/export-pdf', [ItemController::class, 'exportPdf'])->name('items.export-pdf');
    Route::get('items/get-sub-categories', [ItemController::class, 'getSubCategories'])
        ->name('items.get-sub-categories');
    Route::resource('items', ItemController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::put('items/{item}/toggle-status', [ItemController::class, 'toggleStatus'])
        ->name('items.toggle-status');
    Route::get('items/template', [ItemController::class, 'downloadTemplate'])->name('items.template');
    Route::post('items/preview', [ItemController::class, 'previewImport'])->name('items.preview');
    Route::post('items/import', [ItemController::class, 'import'])->name('items.import');
    Route::resource('warehouses', WarehouseController::class);
    Route::put('warehouses/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus']);
    Route::resource('customers', CustomerController::class);
    Route::put('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus']);
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/upload', [SalesController::class, 'uploadForm'])->name('upload');
        Route::get('/template', [SalesController::class, 'downloadTemplate'])->name('template');
        Route::post('/preview', [SalesController::class, 'preview'])->name('preview');
        Route::get('/preview', [SalesController::class, 'showPreview'])->name('show_preview');
        Route::get('/progress', [SalesController::class, 'getProgress'])->name('progress');
        Route::post('/', [SalesController::class, 'store'])->name('store');
        Route::get('/{id}/details', [SalesController::class, 'getDetails'])->name('details');
    });
    Route::post('/sales/store', [SalesController::class, 'store'])
        ->middleware(['auth', 'json.response']);

    // User Management Routes
    Route::prefix('users')->name('users.')->middleware(['auth'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/set-role/{userId}', [UserController::class, 'setRole'])->name('set-role');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:users,create');
        Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users,edit');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users,delete');
        Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:users,edit');
    });

    // Role Management Routes
    Route::group(['middleware' => ['permission:roles,view']], function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles,create');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles,edit');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles,delete');
        Route::put('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->middleware('permission:roles,edit');
    });

    Route::get('/categories/{category}/sub-categories', [CategoryController::class, 'getSubCategories'])->name('categories.sub-categories');

    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users/{userId}/set-role', [App\Http\Controllers\UserController::class, 'setRole'])->name('users.set-role');

    // Menus Routes
    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
        Route::put('/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
        Route::get('/parents', [MenuController::class, 'getParentMenus'])->name('parents');
    });

    Route::prefix('floor-orders')->group(function () {
        // Routes untuk create dan draft yang tidak perlu middleware status check
        Route::post('draft', [FloorOrderController::class, 'storeDraft'])->name('floor-orders.store-draft');
        Route::put('draft/{floorOrder}', [FloorOrderController::class, 'updateDraft'])->name('floor-orders.update-draft');
        
        // Route untuk save draft (mengubah status ke saved)
        Route::post('{floorOrder}/save-draft', [FloorOrderController::class, 'saveDraft'])
            ->name('floor-orders.save-draft')
            ->middleware('check.floor.order.status');

        // Route untuk delete
        Route::delete('{floorOrder}', [FloorOrderController::class, 'destroy'])
            ->name('floor-orders.destroy');

        // Route untuk finalize (mengubah status dari draft ke saved)
        Route::put('draft/{floorOrder}/finalize', [FloorOrderController::class, 'finalize'])
            ->name('floor-orders.finalize');

        // Route lainnya...
    });

    Route::get('/floor-orders', [App\Http\Controllers\FloorOrderController::class, 'index'])->name('floor-orders.index');

    Route::get('floor-orders/items/{warehouseCode}', [FloorOrderController::class, 'getItemsByWarehouse']);
    Route::get('floor-orders/items-edit/{warehouseCode}', [FloorOrderController::class, 'getItemsByWarehouseEdit']);
    Route::resource('floor-orders', FloorOrderController::class);

    Route::get('/items/{item}/availability', [ItemController::class, 'getAvailability'])->name('items.availability');

    Route::get('/items/{item}/images', [ItemController::class, 'getImages']);

    Route::get('/items/{item}/prices', [ItemController::class, 'getPrices'])->name('items.prices');

    Route::get('/items/{item}/units', [ItemController::class, 'getUnits'])->name('items.units');

    Route::resource('packing-lists', PackingListController::class);
    Route::get('packing-lists/{id}/details', [PackingListController::class, 'getDetails']);

    // Daily Check Routes
    Route::prefix('daily-check')->name('daily-check.')->group(function() {
        Route::get('/', [DailyCheckController::class, 'index'])->name('index');
        Route::get('/list', [DailyCheckController::class, 'list'])->name('list');
        Route::get('/create', [DailyCheckController::class, 'create'])->name('create');
        Route::post('/autosave', [DailyCheckController::class, 'autosave'])->name('autosave');
        Route::post('/store', [DailyCheckController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DailyCheckController::class, 'edit'])->name('edit');
        Route::get('/show/{id}', [DailyCheckController::class, 'show'])->name('show');
        Route::delete('/{id}', [DailyCheckController::class, 'destroy'])->name('destroy');
        Route::delete('/photos/{id}', [DailyCheckController::class, 'deletePhoto'])->name('photos.destroy');
        Route::post('/upload-photo', [DailyCheckController::class, 'uploadPhoto'])->name('upload-photo');
        Route::post('/delete-photo', [DailyCheckController::class, 'deletePhoto'])->name('delete-photo');
        
        // Debug route
        Route::get('/debug/{id?}', [DailyCheckController::class, 'debug'])->name('debug');
    });

    Route::get('/daily-check', [DailyCheckController::class, 'index'])->name('daily-check.index');
    Route::get('/daily-check/create', [DailyCheckController::class, 'create'])->name('daily-check.create');
    Route::post('/daily-check', [DailyCheckController::class, 'store'])->name('daily-check.store');
    Route::post('/daily-check/autosave', [DailyCheckController::class, 'autosave'])->name('daily-check.autosave');
    Route::get('/daily-check/list', [DailyCheckController::class, 'list'])->name('daily-check.list');
    Route::get('/daily-check/{dailyCheck}', [DailyCheckController::class, 'show'])->name('daily-check.show');
    Route::get('/daily-check/{dailyCheck}/edit', [DailyCheckController::class, 'edit'])->name('daily-check.edit');
    Route::put('/daily-check/{dailyCheck}', [DailyCheckController::class, 'update'])->name('daily-check.update');
    Route::delete('/daily-check/{dailyCheck}', [DailyCheckController::class, 'destroy'])->name('daily-check.destroy');

    Route::get('/{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');

    Route::delete('items/delete-image/{id}', [ItemController::class, 'deleteImage']);

    Route::put('/floor-orders/{floorOrder}', [FloorOrderController::class, 'update'])->name('floor-orders.update');

    Route::get('/items/{id}/detail', [ItemController::class, 'getItemDetail'])->name('items.detail');

    Route::get('/items/{id}/show', [ItemController::class, 'show'])->name('items.show');

    Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');

    // Good Receive routes
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::resource('good-receives', GoodReceiveController::class);
        Route::post('good-receives/scan-qr', [GoodReceiveController::class, 'scanQR'])->name('good-receives.scan-qr');
        Route::post('good-receives/search-po', [GoodReceiveController::class, 'searchPO'])->name('good-receives.search-po');
        Route::post('good-receives/{goodReceive}/approve', [GoodReceiveController::class, 'approve'])->name('good-receives.approve');
        Route::post('good-receives/{goodReceive}/reject', [GoodReceiveController::class, 'reject'])->name('good-receives.reject');
        Route::get('items/specs-and-images', [ItemController::class, 'getSpecsAndImages'])->name('items.specs-and-images');
        
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::controller(InventoryReportController::class)->group(function () {
                Route::get('inventory', 'index')->name('inventory.index');
                Route::post('inventory/data', 'getData')->name('inventory.data');
                Route::post('inventory/export', 'export')->name('inventory.export');
            });

            Route::controller(StockCardReportController::class)->group(function () {
                Route::get('stock-card', 'index')->name('stock-card.index');
                Route::post('stock-card/data', 'getData')->name('stock-card.data');
                Route::post('stock-card/export', 'export')->name('stock-card.export');
            });
        });
    });

    Route::prefix('reports')->name('warehouse.reports.')->group(function () {
        Route::get('stock-analysis', [StockAnalysisReportController::class, 'index'])->name('stock-analysis.index');
        Route::post('stock-analysis/data', [StockAnalysisReportController::class, 'getData'])->name('stock-analysis.data');
        Route::post('stock-analysis/export', [StockAnalysisReportController::class, 'export'])->name('stock-analysis.export');
    });

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::prefix('purchase-invoices')->name('purchase-invoices.')->group(function () {
            Route::get('/', [PurchaseInvoiceController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseInvoiceController::class, 'create'])->name('create');
            Route::post('/', [PurchaseInvoiceController::class, 'store'])->name('store');
            Route::get('/{purchaseInvoice}', [PurchaseInvoiceController::class, 'show'])->name('show');
            Route::get('/{purchaseInvoice}/edit', [PurchaseInvoiceController::class, 'edit'])->name('edit');
            Route::put('/{purchaseInvoice}', [PurchaseInvoiceController::class, 'update'])->name('update');
            Route::post('/approve/{id}', [PurchaseInvoiceController::class, 'approve'])->name('approve');
            Route::post('/reject/{id}', [PurchaseInvoiceController::class, 'reject'])->name('reject');
        });

        // Contra Bon routes
        Route::resource('contra-bons', ContraBonController::class);
        Route::get('contra-bons/supplier/{id}/invoices', [ContraBonController::class, 'getSupplierInvoices'])
            ->name('finance.contra-bons.get-supplier-invoices');
        Route::post('contra-bons/{id}/approve', [ContraBonController::class, 'approve'])->name('contra-bons.approve');
        Route::post('contra-bons/{id}/mark-as-paid', [ContraBonController::class, 'markAsPaid'])->name('contra-bons.mark-as-paid');

        // Payment routes
        Route::resource('payments', PaymentController::class);
        Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

        // Payment Planning routes
        Route::get('payment-planning', [PaymentPlanningController::class, 'index'])->name('payment-planning.index');
        Route::post('payment-planning/data', [PaymentPlanningController::class, 'data'])->name('payment-planning.data');
        Route::post('payment-planning/export', [PaymentPlanningController::class, 'export'])->name('payment-planning.export');

        // Payment History routes
        Route::prefix('payment-history')->name('payment-history.')->group(function () {
            // Supplier Payment History
            Route::get('/supplier', [PaymentHistoryController::class, 'supplierIndex'])->name('supplier.index');
            Route::post('/supplier/data', [PaymentHistoryController::class, 'supplierData'])->name('supplier.data');
            
            // Payment Summary
            Route::get('/summary', [PaymentHistoryController::class, 'summaryIndex'])->name('summary.index');
            Route::post('/summary/data', [PaymentHistoryController::class, 'summaryData'])->name('summary.data');
        });

        Route::group(['prefix' => 'outstanding-invoice', 'as' => 'outstanding-invoice.'], function () {
            Route::get('/invoices', [OutstandingInvoiceController::class, 'invoices'])->name('invoices');
            Route::get('/contra-bon', [OutstandingInvoiceController::class, 'contraBon'])->name('contra-bon');
            Route::post('/invoices/data', [OutstandingInvoiceController::class, 'invoicesData'])->name('invoices.data');
            Route::post('/contra-bon/data', [OutstandingInvoiceController::class, 'contraBonData'])->name('contra-bon.data');
        });
    });

    Route::get('finance/aging-report', [AgingReportController::class, 'index'])->name('finance.aging-report.index');
    Route::post('finance/aging-report/data', [AgingReportController::class, 'data'])->name('finance.aging-report.data');
    Route::post('finance/aging-report/export', [AgingReportController::class, 'export'])->name('finance.aging-report.export');

    // Route untuk maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/kanban', [MaintenanceKanbanController::class, 'index'])->name('kanban.index');
        Route::get('/get-members', [MaintenanceKanbanController::class, 'getMembers'])->name('getMembers');
        Route::get('/get-ruko/{outletId}', [MaintenanceKanbanController::class, 'getRuko'])->name('getRuko');
        Route::get('/task/{taskId}/pr/list', [MaintenancePurchaseRequisitionController::class, 'getTaskPrs']);
        Route::get('/pr/{prId}/detail', [MaintenancePurchaseRequisitionController::class, 'getPrDetail']);
        Route::post('/pr/store', [MaintenancePurchaseRequisitionController::class, 'store']);
        Route::get('/units', [MaintenanceUnitController::class, 'getUnits']);
        Route::get('/task/{taskId}/po/list', [MaintenanceKanbanController::class, 'getTaskPos'])->name('task.po.list');
        Route::get('/task/{id}/pr-items', [MaintenanceKanbanController::class, 'getTaskPrItems'])
            ->name('maintenance.task.pr-items');
        Route::post('/po/store', [MaintenanceKanbanController::class, 'storePo'])
            ->name('po.store');
        Route::get('/po/{id}/preview', [MaintenanceKanbanController::class, 'previewPo'])
            ->name('maintenance.po.preview');
        Route::get('/po/{id}/detail', [MaintenanceKanbanController::class, 'getPoDetail'])
            ->name('po.detail');
        Route::post('/po/{id}/approve', [MaintenanceKanbanController::class, 'approvePo'])
            ->name('po.approve');
        Route::post('/po/{id}/reject', [MaintenanceKanbanController::class, 'rejectPo'])
            ->name('po.reject');
        Route::post('/po/{id}/upload-invoice', [MaintenanceKanbanController::class, 'uploadInvoice'])
             ->name('maintenance.po.upload-invoice');
        Route::post('/po/{id}/good-receive', [MaintenanceKanbanController::class, 'saveGoodReceive'])
             ->name('maintenance.po.good-receive');
        Route::get('/task/{taskId}/po/stats', [MaintenanceKanbanController::class, 'getPoStats']);
        Route::get('/maintenance/kanban/po-stats/{taskId}', [MaintenanceKanbanController::class, 'getPoStats'])->name('maintenance.getPoStats');
    });

    // Notifikasi Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::post('/notifications/delete', [NotificationController::class, 'delete'])->name('notifications.delete');

    // Routes untuk notifikasi
    Route::get('/notifications/get', [NotificationController::class, 'index'])->name('notifications.get');
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Route tambahan untuk notifikasi
    Route::get('/notifications/check-new', [NotificationController::class, 'checkNew'])->name('notifications.check-new');
    Route::get('/notifications/last-id', [NotificationController::class, 'getLastId'])->name('notifications.last-id');

    // Route untuk preview task (ringkasan singkat)
    Route::get('/maintenance/tasks/{taskId}/preview', [MaintenanceKanbanController::class, 'getTaskPreview']);

    // Approval PR routes
    Route::post('/maintenance/pr/{id}/approve', [App\Http\Controllers\MaintenanceKanbanController::class, 'approvePr'])->name('maintenance.pr.approve');
    Route::post('/maintenance/pr/{id}/reject', [App\Http\Controllers\MaintenanceKanbanController::class, 'rejectPr'])->name('maintenance.pr.reject');
    Route::get('/maintenance/user-info', [App\Http\Controllers\MaintenancePurchaseRequisitionController::class, 'getUserInfo'])->name('user.info');

    Route::get('/maintenance/pr/{id}/preview', [MaintenancePurchaseRequisitionController::class, 'previewPr'])
        ->name('maintenance.pr.preview');

    Route::get('/maintenance/ba/{id}/preview', [MaintenanceKanbanController::class, 'previewBa'])->name('maintenance.ba.preview');

    Route::post('/maintenance/po/{id}/approve', [MaintenanceKanbanController::class, 'approvePo'])->name('maintenance.po.approve');

    // Maintenance Evidence Routes
    Route::post('/maintenance/task/check-evidence-access', [App\Http\Controllers\MaintenanceEvidenceController::class, 'checkAccess'])->name('maintenance.evidence.check-access');
    Route::post('/maintenance/task/save-evidence', [App\Http\Controllers\MaintenanceEvidenceController::class, 'store'])->name('maintenance.evidence.store');
    Route::get('/maintenance/task/{taskId}/evidence', [App\Http\Controllers\MaintenanceEvidenceController::class, 'show'])->name('maintenance.evidence.show');

    Route::post('/maintenance/kanban/upload-evidence', [MaintenanceKanbanController::class, 'uploadEvidence'])->name('maintenance.kanban.upload-evidence');
    Route::get('/maintenance/kanban/task/{taskId}/evidence', [MaintenanceKanbanController::class, 'getTaskEvidence'])->name('maintenance.kanban.task-evidence');

    // Routes untuk timeline dan delete task
    Route::get('/maintenance/tasks/{taskId}/timeline', [MaintenanceKanbanController::class, 'getTaskTimeline']);
    Route::delete('/maintenance/tasks/{taskId}', [MaintenanceKanbanController::class, 'destroy']);

    // Route untuk mendapatkan detail task
    Route::get('/maintenance/tasks/{taskId}', [MaintenanceKanbanController::class, 'getTask'])->name('maintenance.getTask');

    // Route untuk mendapatkan members dari sebuah task
    Route::get('/maintenance/tasks/{taskId}/members', [MaintenanceKanbanController::class, 'getTaskMembers']);

    // Route untuk update members sebuah task
    Route::post('/maintenance/tasks/{taskId}/update-members', [MaintenanceKanbanController::class, 'updateTaskMembers']);

    Route::get('/maintenance/task/{taskId}/pr/stats', [MaintenancePurchaseRequisitionController::class, 'getTaskPrStats']);

    Route::get('/maintenance/pr/{id}/download-pdf', [MaintenancePurchaseRequisitionController::class, 'downloadPdf'])
        ->name('maintenance.pr.download-pdf');

    Route::post('/maintenance/kanban/check-done-requirements', [MaintenanceKanbanController::class, 'checkDoneRequirements'])->name('maintenance.checkDoneRequirements');

    Route::get('/maintenance/dashboard', [DashboardController::class, 'index'])->name('maintenance.dashboard');

    Route::resource('maintenance/tasks', 'App\Http\Controllers\Maintenance\TaskController');

    // Route untuk task status report
    Route::get('/maintenance/reports/task-status', [App\Http\Controllers\Maintenance\ReportController::class, 'taskStatusReport'])
        ->name('maintenance.task-status-report');

    Route::get('/maintenance/dashboard/export-tasks-by-member', 'App\Http\Controllers\Maintenance\DashboardController@exportTasksByMember')
        ->name('maintenance.dashboard.export-tasks-by-member');

    Route::get('/maintenance/dashboard/export-tasks-by-priority', 'App\Http\Controllers\Maintenance\DashboardController@exportTasksByPriority')
        ->name('maintenance.dashboard.export-tasks-by-priority');

    Route::get('/maintenance/dashboard/activities', [DashboardController::class, 'getAllActivities'])
        ->name('maintenance.dashboard.activities');

    Route::get('/maintenance/dashboard/activities/export', 'App\Http\Controllers\Maintenance\DashboardController@exportActivities')
        ->name('maintenance.dashboard.activities.export');

    // Evidence gallery routes
    Route::prefix('maintenance/dashboard')->name('maintenance.dashboard.')->group(function () {
        Route::get('/get-evidence-outlets', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceOutlets'])->name('get-evidence-outlets');
        Route::get('/get-evidence-rukos', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceRukos'])->name('get-evidence-rukos');
        Route::get('/get-evidence-dates', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceDates'])->name('get-evidence-dates');
        Route::get('/get-evidence-tasks', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceTasks'])->name('get-evidence-tasks');
        Route::get('/get-evidence-files', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceFiles'])->name('get-evidence-files');
        Route::get('/get-all-evidence', [App\Http\Controllers\Maintenance\DashboardController::class, 'getAllEvidence'])->name('get-all-evidence');
    });
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/fj', [ReportFJController::class, 'index'])->name('fj.index');
    Route::post('/fj/data', [ReportFJController::class, 'getData'])->name('fj.data');
    Route::post('/fj/export', [ReportFJController::class, 'export'])->name('fj.export');
    Route::get('rekap-fj', [RekapFJController::class, 'index'])->name('rekap-fj.index');
    Route::post('rekap-fj/data', [RekapFJController::class, 'data'])->name('rekap-fj.data');
    Route::post('rekap-fj/export', [RekapFJController::class, 'export'])->name('rekap-fj.export');
});

// Purchasing Routes
Route::prefix('purchasing')->name('purchasing.')->group(function () {
    Route::get('purchase-requisitions', [PurchaseRequisitionController::class, 'index'])
        ->name('purchase-requisitions.index');
    Route::get('purchase-requisitions/create', [PurchaseRequisitionController::class, 'create'])
        ->name('purchase-requisitions.create');
    Route::post('purchase-requisitions', [PurchaseRequisitionController::class, 'store'])
        ->name('purchase-requisitions.store');
    Route::get('purchase-requisitions/{id}', [PurchaseRequisitionController::class, 'show'])
        ->name('purchase-requisitions.show');
    Route::get('purchase-requisitions/{id}/edit', [PurchaseRequisitionController::class, 'edit'])
        ->name('purchase-requisitions.edit');
    Route::put('purchase-requisitions/{id}', [PurchaseRequisitionController::class, 'update'])
        ->name('purchase-requisitions.update');
    
    // Tambahkan route untuk approval
    Route::post('purchase-requisitions/{id}/approve', [PurchaseRequisitionController::class, 'approve'])
        ->name('purchase-requisitions.approve');

    // Purchase Order Routes
    Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);
    Route::get('purchasing/purchase-requisitions/{id}/pr-items', [PurchaseRequisitionController::class, 'getPRItems'])
        ->name('purchasing.purchase-requisitions.pr-items');
    Route::post('purchase-orders/{id}/approve', [\App\Http\Controllers\PurchaseOrderController::class, 'approve'])
         ->name('purchase-orders.approve');
    Route::post('purchase-orders/{id}/cancel', [\App\Http\Controllers\PurchaseOrderController::class, 'cancel'])
         ->name('purchase-orders.cancel');
    Route::get('purchasing/purchase-orders/get-pr-items/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'getPurchaseRequisitionItems'])
        ->name('purchasing.purchase-orders.get-pr-items');
});

// Tambahkan route temporary untuk debug
Route::get('debug-pr/{id}', function($id) {
    $pr = \DB::table('purchase_requisitions')->where('id', $id)->first();
    dd([
        'requested_id' => $id,
        'pr_data' => $pr,
        'exists' => !is_null($pr)
    ]);
});

// Master Data Routes
Route::prefix('master-data')->name('master-data.')->group(function () {
    // Existing routes...
    
    // Supplier Routes
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
    Route::post('/master-data/suppliers', 'SupplierController@store')->name('suppliers.store');
    Route::put('/master-data/suppliers/{id}', 'SupplierController@update')->name('suppliers.update');
});

// Route untuk Suppliers
Route::prefix('master-data')->group(function () {
    // Index route untuk menampilkan halaman supplier
    Route::get('/suppliers', 'App\Http\Controllers\SupplierController@index')->name('suppliers.index');
    
    // Route untuk menyimpan supplier baru
    Route::post('/suppliers', 'App\Http\Controllers\SupplierController@store')->name('suppliers.store');
    
    // Route untuk mendapatkan data supplier
    Route::get('/suppliers/{id}', 'App\Http\Controllers\SupplierController@show')->name('suppliers.show');
    
    // Route untuk mendapatkan form edit supplier
    Route::get('/suppliers/{id}/edit', 'App\Http\Controllers\SupplierController@edit')->name('suppliers.edit');
    
    // Route untuk update supplier
    Route::put('/suppliers/{id}', 'App\Http\Controllers\SupplierController@update')->name('suppliers.update');
    
    // Route untuk mengubah status supplier
    Route::post('/suppliers/{id}/toggle-status', 'App\Http\Controllers\SupplierController@toggleStatus')->name('suppliers.toggle-status');
    
    // Route untuk menghapus supplier
    Route::delete('/suppliers/{id}', 'App\Http\Controllers\SupplierController@destroy')->name('suppliers.destroy');
});

// Tambahkan sebagai route terpisah, tidak di dalam grup mana pun
Route::get('get-pr-items/{id}', [PurchaseRequisitionController::class, 'getPRItems'])->name('get.pr.items');

// Finance Routes
Route::middleware(['auth'])->prefix('finance')->name('finance.')->group(function () {
    // Purchase Invoice Routes
    Route::resource('purchase-invoices', PurchaseInvoiceController::class);
    
    // Tambahkan route untuk approve dan reject
    Route::post('purchase-invoices/{id}/approve', [PurchaseInvoiceController::class, 'approve'])
        ->name('purchase-invoices.approve');
    Route::post('purchase-invoices/{id}/reject', [PurchaseInvoiceController::class, 'reject'])
        ->name('purchase-invoices.reject');

    // Contra Bon routes
    Route::resource('contra-bons', ContraBonController::class);
    Route::get('contra-bons/supplier/{id}/invoices', [ContraBonController::class, 'getSupplierInvoices'])
        ->name('finance.contra-bons.get-supplier-invoices');
    Route::post('contra-bons/{id}/approve', [ContraBonController::class, 'approve'])->name('contra-bons.approve');
    Route::post('contra-bons/{id}/mark-as-paid', [ContraBonController::class, 'markAsPaid'])->name('contra-bons.mark-as-paid');
});

Route::get('/finance/purchase-invoice/items/{id}', [PurchaseInvoiceController::class, 'getItems']);

Route::prefix('finance/contra-bons')->name('finance.contra-bons.')->group(function () {
    Route::post('{id}/approve', [ContraBonController::class, 'approve'])->name('approve');
});

Route::get('/items/{id}/conversions', [\App\Http\Controllers\ItemController::class, 'getConversions'])->name('items.conversions');

Route::get('/maintenance/kanban/getTasks', [MaintenanceKanbanController::class, 'getTasks'])->name('maintenance.kanban.getTasks');

Route::post('/maintenance/tasks/store', [MaintenanceTaskController::class, 'store'])->name('maintenance.tasks.store');

Route::post('/maintenance/kanban/store', [MaintenanceKanbanController::class, 'store'])->name('maintenance.kanban.store');

Route::get('/maintenance/comments/{taskId}', [MaintenanceCommentController::class, 'getComments']);
Route::post('/maintenance/comments', [MaintenanceCommentController::class, 'store']);
Route::delete('/maintenance/comments/{commentId}', [MaintenanceCommentController::class, 'destroy']);

Route::post('/maintenance/kanban/updateStatus', [MaintenanceKanbanController::class, 'updateTaskStatus'])->name('maintenance.kanban.updateStatus');

// Routes untuk timeline dan delete task
Route::get('/maintenance/tasks/{taskId}/timeline', [MaintenanceKanbanController::class, 'getTaskTimeline']);
Route::delete('/maintenance/tasks/{taskId}', [MaintenanceKanbanController::class, 'destroy']);

// Route untuk mendapatkan detail task
Route::get('/maintenance/tasks/{taskId}', [MaintenanceKanbanController::class, 'getTask'])->name('maintenance.getTask');

// Route untuk mendapatkan members dari sebuah task
Route::get('/maintenance/tasks/{taskId}/members', [MaintenanceKanbanController::class, 'getTaskMembers']);

// Route untuk update members sebuah task
Route::post('/maintenance/tasks/{taskId}/update-members', [MaintenanceKanbanController::class, 'updateTaskMembers']);

Route::get('/maintenance/task/{taskId}/pr/stats', [MaintenancePurchaseRequisitionController::class, 'getTaskPrStats']);

Route::get('/maintenance/pr/{id}/download-pdf', [MaintenancePurchaseRequisitionController::class, 'downloadPdf'])
    ->name('maintenance.pr.download-pdf');

Route::post('/maintenance/kanban/check-done-requirements', [MaintenanceKanbanController::class, 'checkDoneRequirements'])->name('maintenance.checkDoneRequirements');

Route::get('/maintenance/dashboard', [DashboardController::class, 'index'])->name('maintenance.dashboard');

Route::resource('maintenance/tasks', 'App\Http\Controllers\Maintenance\TaskController');

// Route untuk task status report
Route::get('/maintenance/reports/task-status', [App\Http\Controllers\Maintenance\ReportController::class, 'taskStatusReport'])
    ->name('maintenance.task-status-report');

Route::get('/maintenance/dashboard/export-tasks-by-member', 'App\Http\Controllers\Maintenance\DashboardController@exportTasksByMember')
    ->name('maintenance.dashboard.export-tasks-by-member');

Route::get('/maintenance/dashboard/export-tasks-by-priority', 'App\Http\Controllers\Maintenance\DashboardController@exportTasksByPriority')
    ->name('maintenance.dashboard.export-tasks-by-priority');

Route::get('/maintenance/dashboard/activities', [DashboardController::class, 'getAllActivities'])
    ->name('maintenance.dashboard.activities');

Route::get('/maintenance/dashboard/activities/export', 'App\Http\Controllers\Maintenance\DashboardController@exportActivities')
    ->name('maintenance.dashboard.activities.export');

// Evidence gallery routes
Route::prefix('maintenance/dashboard')->name('maintenance.dashboard.')->group(function () {
    Route::get('/get-evidence-outlets', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceOutlets'])->name('get-evidence-outlets');
    Route::get('/get-evidence-rukos', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceRukos'])->name('get-evidence-rukos');
    Route::get('/get-evidence-dates', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceDates'])->name('get-evidence-dates');
    Route::get('/get-evidence-tasks', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceTasks'])->name('get-evidence-tasks');
    Route::get('/get-evidence-files', [App\Http\Controllers\Maintenance\DashboardController::class, 'getEvidenceFiles'])->name('get-evidence-files');
    Route::get('/get-all-evidence', [App\Http\Controllers\Maintenance\DashboardController::class, 'getAllEvidence'])->name('get-all-evidence');
});

// Test route for debugging
Route::post('/test-request', [TestController::class, 'testRequest'])->name('test.request');