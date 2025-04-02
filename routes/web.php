<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubHeadOfAccController;
use App\Http\Controllers\COAController;

Route::get('/', function () {
    return view('home');
});

// Accounts
Route::resource('shoa', SubHeadOfAccController::class);
Route::resource('coa', COAController::class);