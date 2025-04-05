<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubHeadOfAccController;
use App\Http\Controllers\COAController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;

// Home
Route::get('/', [DashboardController::class, 'index']);

// Accounts
Route::resource('shoa', SubHeadOfAccController::class);
Route::resource('coa', COAController::class);

// Projects
Route::resource('project-status', ProjectStatusController::class);
Route::get('project-status/{id}/json', [ProjectStatusController::class, 'showJson'])->name('project-status.show-json');
Route::resource('projects', ProjectController::class);