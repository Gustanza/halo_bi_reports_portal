<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DepartmentReportsController;

Route::middleware('auth:sanctum')->group(function(){
    Route::resource('/users', UserController::class);
    Route::delete('/dropUsers/{user}', [UserController::class, 'destroy']);

    Route::resource('/departments', DepartmentController::class);

    Route::resource('/reports', ReportController::class);

    Route::get('/spreports/{department}/reports', [DepartmentReportsController::class, 'index']);
});

Route::post('/register', [UsorController::class, 'store']);
Route::post('/login', [UsorController::class, 'login']);
