<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubHeadOfAccController;
use App\Http\Controllers\COAController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;

// Auth routes (login, register, forgot password)
Auth::routes();

// All routes below require login
Route::middleware(['auth'])->group(function () {
    // Dashboard (home page)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Laravel UI default home route
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Accounts
    // Route::resource('shoa', SubHeadOfAccController::class);
    // Route::resource('coa', COAController::class);

    // Projects
    Route::resource('project-status', ProjectStatusController::class);
    Route::get('project-status/{id}/json', [ProjectStatusController::class, 'showJson'])->name('project-status.show-json');

    Route::resource('projects', ProjectController::class);

    Route::post('project-pcs-update/{id}', [ProjectController::class, 'pcsUpdate'])->name('project-pcs-update');
    Route::get('project-pcs-show/{id}', [ProjectController::class, 'getPcs'])->name('project-pcs-show');
    Route::delete('project-pcs-delete/{id}', [ProjectController::class, 'deletePcs'])->name('project-pcs-delete');
    Route::post('/projects/bulk-status-update', [ProjectController::class, 'bulkStatusUpdate'])->name('projects.bulk-status-update');
    Route::post('/projects/bulk-delete', [ProjectController::class, 'bulkDelete'])->name('projects.bulk-delete');

    // Task
    Route::resource('task-categories', TaskCategoryController::class);
    Route::get('task-categories/{id}/json', [TaskCategoryController::class, 'showJson'])->name('task-category.show-json');

    Route::get('/tasks/filter', [TaskController::class, 'filter'])->name('tasks.filter');
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{id}/complete', [TaskController::class, 'markComplete'])->name('tasks.complete');
    Route::post('/tasks/bulk-complete', [TaskController::class, 'bulkComplete'])->name('tasks.bulk-complete');
    Route::post('/tasks/bulk-delete', [TaskController::class, 'bulkDelete'])->name('tasks.bulk-delete');
});