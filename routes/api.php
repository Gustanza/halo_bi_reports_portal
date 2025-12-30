<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;

Route::middleware('auth:sanctum')->group(function(){
    Route::resource('/users', UserController::class);
    Route::delete('/dropUsers/{user}', [UserController::class, 'destroy']);

    Route::resource('/departments', DepartmentController::class);
});

Route::post('/register', [UsorController::class, 'store']);
Route::post('/login', [UsorController::class, 'login']);
