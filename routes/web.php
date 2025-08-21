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

// Bootstrap Icons 轉換器 dd 檢視
Route::get('/debug/bootstrap-icons/general', function () {
    $generalConfig = include config_path('icon/bootstrap-icons/general_test.php');
    dd($generalConfig);
});

// 測試 BootstrapIconService
Route::get('/debug/service-test', function () {
    try {
        // 測試 loadCategoriesFromFiles 方法
        $service = new \App\Services\BootstrapIconService();
        $reflector = new ReflectionClass($service);
        $method = $reflector->getMethod('loadCategoriesFromFiles');
        $method->setAccessible(true);
        $categoriesData = $method->invoke($service);
        
        dd([
            'categories_count' => count($categoriesData),
            'categories_sample' => count($categoriesData) > 0 ? [
                'first_keys' => array_keys($categoriesData[0]),
                'first_sample' => array_slice($categoriesData[0], 0, 4, true)
            ] : null
        ]);
    } catch (\Exception $e) {
        dd([
            'error' => true,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

Route::get('/debug/heroicons-config', function () {
    $config = config('icon.heroicons');
    dd($config);
});

// Vue SPA 路由 - 所有前端路由都指向同一個 Blade 模板
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
