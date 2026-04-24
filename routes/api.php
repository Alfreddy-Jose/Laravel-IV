<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\SedeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por middleware de Sanctum
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/user/create', [UserController::class, 'store']);
    Route::get('/user/{user}', [UserController::class, 'show']);

    Route::get('/roles', [RolesController::class, 'index']);
    Route::post('/roles', [RolesController::class, 'store']);

    Route::get('/sedes', [SedeController::class, 'index']);

});
