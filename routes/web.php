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

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/signature/upload', [SignatureController::class, 'upload'])->name('signature.upload');

Route::middleware(['auth'])->group(function () {
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
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/fj', [ReportFJController::class, 'index'])->name('fj.index');
    Route::post('/fj/data', [ReportFJController::class, 'getData'])->name('fj.data');
    Route::post('/fj/export', [ReportFJController::class, 'export'])->name('fj.export');
    Route::get('rekap-fj', [RekapFJController::class, 'index'])->name('rekap-fj.index');
    Route::post('rekap-fj/data', [RekapFJController::class, 'data'])->name('rekap-fj.data');
    Route::post('rekap-fj/export', [RekapFJController::class, 'export'])->name('rekap-fj.export');
});

Route::get('/items/{item}/availability', [ItemController::class, 'getAvailability'])->name('items.availability');

Route::get('/items/{item}/images', [ItemController::class, 'getImages']);

Route::get('/items/{item}/prices', [ItemController::class, 'getPrices'])->name('items.prices');

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');

Route::get('/categories/{category}/sub-categories', [CategoryController::class, 'getSubCategories']);
