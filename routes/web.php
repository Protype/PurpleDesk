<?php

use Illuminate\Support\Facades\Route;

// Icon Picker 測試頁面
Route::get('/test/icon-picker', function () {
    return view('test.icon-picker');
});

// Vue SPA 路由 - 所有前端路由都指向同一個 Blade 模板
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
