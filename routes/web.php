<?php

use Illuminate\Support\Facades\Route;
use App\Services\BootstrapIconService;
use App\Services\HeroIconService;
use App\Services\EmojiService;

// Icon Picker 測試頁面
Route::get('/test/icon-picker', function () {
    return view('test.icon-picker');
});

// 除錯路由 - 檢視圖標資料
Route::get('/debug/bootstrap-icons', function (BootstrapIconService $service) {
    $data = $service->getAllBootstrapIcons();
    dd($data); // Laravel 的 dump and die - 美化顯示陣列
});

Route::get('/debug/heroicons', function (HeroIconService $service) {
    $data = $service->getAllHeroIcons();
    dd($data);
});

Route::get('/debug/emojis', function (EmojiService $service) {
    $data = $service->getAllEmojis();
    dd($data);
});

// 檢視原始設定檔
Route::get('/debug/bootstrap-icons-config', function () {
    $config = config('icon.bootstrap-icons');
    dd($config);
});

Route::get('/debug/heroicons-config', function () {
    $config = config('icon.heroicons');
    dd($config);
});

// Vue SPA 路由 - 所有前端路由都指向同一個 Blade 模板
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
