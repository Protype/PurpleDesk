<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\EmojiController;
use App\Http\Controllers\Api\HeroIconController;
use App\Http\Controllers\Api\BootstrapIconController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認證相關路由
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/settings', [AuthController::class, 'updateSettings'])->middleware('auth:sanctum');

// 組織管理路由
Route::get('/organizations', [OrganizationController::class, 'index']);
Route::apiResource('organizations', OrganizationController::class)->except(['index'])->middleware('auth:sanctum');

// 團隊管理路由
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/organizations/{organization}/teams', [TeamController::class, 'index']);
    Route::post('/organizations/{organization}/teams', [TeamController::class, 'store']);
    Route::get('/organizations/{organization}/teams/{team}', [TeamController::class, 'show']);
    Route::put('/organizations/{organization}/teams/{team}', [TeamController::class, 'update']);
    Route::delete('/organizations/{organization}/teams/{team}', [TeamController::class, 'destroy']);
    
    // 團隊成員管理
    Route::post('/organizations/{organization}/teams/{team}/members', [TeamController::class, 'addMember']);
    Route::delete('/organizations/{organization}/teams/{team}/members/{user}', [TeamController::class, 'removeMember']);
    Route::put('/organizations/{organization}/teams/{team}/members/{user}/role', [TeamController::class, 'updateMemberRole']);
});

// 管理員路由
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::get('/users/check-account', [AdminController::class, 'checkAccountAvailable']);
    Route::put('/users/{user}', [AdminController::class, 'updateUser']);
    Route::get('/users/search', [AdminController::class, 'searchUsers']);
    Route::get('/organizations', [AdminController::class, 'organizations']);
    Route::get('/stats', [AdminController::class, 'systemStats']);
    
    // 組織成員管理
    Route::post('/organizations/{organization}/invites', [OrganizationController::class, 'inviteMember']);
    Route::put('/organizations/{organization}/members/{user}', [OrganizationController::class, 'updateMemberRole']);
    Route::delete('/organizations/{organization}/members/{user}', [OrganizationController::class, 'removeMember']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Icon 配置 API 路由（公開訪問）
Route::prefix('config/icon')->group(function () {
    Route::get('/emoji', [EmojiController::class, 'all']);
    Route::get('/heroicons', [HeroIconController::class, 'index']);
    Route::get('/heroicons/categories', [HeroIconController::class, 'categories']);
    Route::get('/heroicons/search', [HeroIconController::class, 'search']);
    Route::get('/heroicons/by-category', [HeroIconController::class, 'byCategory']);
    Route::get('/heroicons/variants', [HeroIconController::class, 'variants']);
    Route::get('/heroicons/style/{style}', [HeroIconController::class, 'byStyle']);
    Route::get('/heroicons/icon/{component}/variants', [HeroIconController::class, 'iconVariants']);
    Route::get('/heroicons/icon/{component}/variant/{style}', [HeroIconController::class, 'hasVariant']);
    
    // Bootstrap Icons API 路由
    Route::get('/bootstrap-icons', [BootstrapIconController::class, 'index']);
    Route::get('/bootstrap-icons/search', [BootstrapIconController::class, 'search']);
    Route::get('/bootstrap-icons/categories', [BootstrapIconController::class, 'categories']);
    Route::get('/bootstrap-icons/priority', [BootstrapIconController::class, 'priority']);
    Route::get('/bootstrap-icons/priority/{priority}', [BootstrapIconController::class, 'byPriority']);
    Route::get('/bootstrap-icons/stats', [BootstrapIconController::class, 'stats']);
    Route::get('/bootstrap-icons/variants', [BootstrapIconController::class, 'variants']);
    Route::get('/bootstrap-icons/style/{style}', [BootstrapIconController::class, 'byStyle']);
    Route::get('/bootstrap-icons/icon/{className}/variants', [BootstrapIconController::class, 'iconVariants']);
    Route::get('/bootstrap-icons/icon/{className}/variant/{style}', [BootstrapIconController::class, 'hasVariant']);
    Route::post('/bootstrap-icons/cache/clear', [BootstrapIconController::class, 'clearCache']);
});
