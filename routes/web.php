<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubHeadOfAccController;
use App\Http\Controllers\COAController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\PurchaseVoucherController;
use App\Http\Controllers\GatepassController;
use App\Http\Controllers\PaymentVoucherController;

// Auth routes (login, register, forgot password)
Auth::routes();

Route::middleware(['auth', RoleMiddleware::class . ':admin|superadmin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('roles/{id}/permissions', [RoleController::class, 'showPermissionsForm'])->name('roles.permissions');
    Route::post('roles/{id}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
});

Route::middleware(['auth', RoleMiddleware::class . ':superadmin'])->group(function () {
    Route::resource('modules', ModuleController::class);
    Route::get('modules/{id}/json', [ModuleController::class, 'json']);
});

Route::middleware(['auth'])->group(function () {
    // Dashboard (home page)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Laravel UI default home route
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Accounts
    Route::resource('shoa', SubHeadOfAccController::class);
    Route::resource('coa', COAController::class);

    //Status
    Route::resource('status', StatusController::class);
    Route::get('status/{id}/json', [StatusController::class, 'showJson'])->name('status.show-json');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::get('project-costing/{id}', [ProjectController::class, 'costingForm'])->name('project.costing');
    Route::post('projects/{project}/costing', [ProjectController::class, 'storeCosting'])->name('projects.costing.store');

    Route::post('project-pcs-update/{id}', [ProjectController::class, 'pcsUpdate'])->name('project-pcs-update');
    Route::get('project-pcs-show/{id}', [ProjectController::class, 'getPcs'])->name('project-pcs-show');
    Route::delete('project-pcs-delete/{id}', [ProjectController::class, 'deletePcs'])->name('project-pcs-delete');
    Route::post('/projects/bulk-status-update', [ProjectController::class, 'bulkStatusUpdate'])->name('projects.bulk-status-update');
    Route::post('/projects/bulk-delete', [ProjectController::class, 'bulkDelete'])->name('projects.bulk-delete');
    Route::get('/projects/{project}/costing/create', [ProjectCostingController::class, 'create'])->name('projects.costing.create');
    Route::post('/projects/{project}/costing', [ProjectCostingController::class, 'store'])->name('projects.costing.store');

    // Task
    Route::resource('task-categories', TaskCategoryController::class);
    Route::get('task-categories/{id}/json', [TaskCategoryController::class, 'showJson'])->name('task-category.show-json');

    Route::get('/tasks/filter', [TaskController::class, 'filter'])->name('tasks.filter');
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{id}/complete', [TaskController::class, 'markComplete'])->name('tasks.complete');
    Route::post('/tasks/bulk-complete', [TaskController::class, 'bulkComplete'])->name('tasks.bulk-complete');
    Route::post('/tasks/bulk-delete', [TaskController::class, 'bulkDelete'])->name('tasks.bulk-delete');

    // Gatepass
    Route::resource('gatepass', GatepassController::class);
    Route::get('gatepass/{id}/print', [GatepassController::class, 'print'])->name('gatepass.print');

    // Purchase Vouchers
    Route::resource('purchase-vouchers', PurchaseVoucherController::class);
    Route::get('purchase-vouchers/{id}/print', [PurchaseVoucherController::class, 'print'])->name('pv.print');

    // Sale Vouchers
    Route::resource('sale-vouchers', \App\Http\Controllers\SaleVoucherController::class);

    // Services
    Route::resource('services', ServiceController::class);

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{id}/print', [QuotationController::class, 'print'])->name('quotations.print');

    // Payment Vouchers
    Route::resource('payment-vouchers', PaymentVoucherController::class);
});