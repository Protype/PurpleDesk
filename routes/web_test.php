<?php

use Illuminate\Support\Facades\Route;

/**
 * 臨時測試路由 - 用於查看轉換後的 Bootstrap Icons 配置
 */

Route::get('/debug/bootstrap-icons/general', function () {
    // 載入轉換後的 general 配置
    $generalConfig = include config_path('icon/bootstrap-icons/general_test.php');
    
    // 統計資訊
    $stats = [
        '配置 ID' => $generalConfig['id'],
        '配置名稱' => $generalConfig['name'], 
        '配置描述' => $generalConfig['description'],
        '優先級' => $generalConfig['priority'],
        '圖標總數' => count($generalConfig['icons']),
    ];
    
    // 範例圖標（前 10 個）
    $sampleIcons = array_slice($generalConfig['icons'], 0, 10);
    
    // 變體統計
    $variantStats = [
        'outline_only' => 0,
        'solid_only' => 0,
        'both_variants' => 0
    ];
    
    foreach ($generalConfig['icons'] as $icon) {
        $hasOutline = isset($icon['variants']['outline']);
        $hasSolid = isset($icon['variants']['solid']);
        
        if ($hasOutline && $hasSolid) {
            $variantStats['both_variants']++;
        } elseif ($hasOutline) {
            $variantStats['outline_only']++;
        } elseif ($hasSolid) {
            $variantStats['solid_only']++;
        }
    }
    
    return view('debug.bootstrap-icons', [
        'stats' => $stats,
        'sampleIcons' => $sampleIcons,
        'variantStats' => $variantStats,
        'allIcons' => $generalConfig['icons']
    ]);
});