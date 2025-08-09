<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認證相關路由
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');

// 單位管理路由
Route::get('/organizations', [OrganizationController::class, 'index']);
Route::apiResource('organizations', OrganizationController::class)->except(['index'])->middleware('auth:sanctum');

// 管理員路由
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::put('/users/{user}', [AdminController::class, 'updateUser']);
    Route::get('/organizations', [AdminController::class, 'organizations']);
    Route::get('/stats', [AdminController::class, 'systemStats']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
