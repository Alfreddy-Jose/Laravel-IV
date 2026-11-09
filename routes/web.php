<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Autenticación SPA con cookies HttpOnly
// Estas rutas necesitan el middleware 'web' (session + CSRF)
Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});
