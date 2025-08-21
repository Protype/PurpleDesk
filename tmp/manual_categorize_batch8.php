<?php

/**
 * 第八批手動分類 - 60個圖標
 */

echo "📋 第八批手動圖標分類\n";
echo "=====================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第八批手動分類定義
$manualCategories = [
    // 雲端相關（續） -> others
    'cloud-haze-fill' => 'others',
    'cloud-haze' => 'others',
    'cloud-haze2-fill' => 'others',
    'cloud-slash-fill' => 'others',
    'cloud-slash' => 'others',
    
    // 命令/鍵盤 -> others
    'command' => 'others',
    
    // CPU/硬體 -> others
    'cpu-fill' => 'others',
    'cpu' => 'others',
    
    // 信用卡/金融 -> others
    'credit-card-2-back-fill' => 'others',
    'credit-card-2-back' => 'others',
    'credit-card-2-front-fill' => 'others',
    'credit-card-2-front' => 'others',
    'credit-card-fill' => 'others',
    'credit-card' => 'others',
    
    // 裁切工具 -> others
    'crop' => 'others',
    
    // 杯子/飲料 -> others
    'cup-fill' => 'others',
    'cup-straw' => 'others',
    'cup' => 'others',
    
    // 游標/指標 -> ui
    'cursor-fill' => 'ui',
    'cursor-text' => 'ui',
    'cursor' => 'ui',
    
    // 破折號/符號 -> ui
    'dash-circle-dotted' => 'ui',
    'dash-circle-fill' => 'ui',
    'dash-circle' => 'ui',
    'dash-square-dotted' => 'ui',
    'dash-square-fill' => 'ui',
    'dash-square' => 'ui',
    'dash' => 'ui',
    
    // 圖表/結構圖 -> ui
    'diagram-2-fill' => 'ui',
    'diagram-2' => 'ui',
    'diagram-3-fill' => 'ui',
    'diagram-3' => 'ui',
    
    // 鑽石形狀 -> ui
    'diamond-fill' => 'ui',
    'diamond-half' => 'ui',
    'diamond' => 'ui',
    
    // 骰子 -> others
    'dice-1-fill' => 'others',
    'dice-1' => 'others',
    'dice-2-fill' => 'others',
    'dice-2' => 'others',
    'dice-3-fill' => 'others',
    'dice-3' => 'others',
    'dice-4-fill' => 'others',
    'dice-4' => 'others',
    'dice-5-fill' => 'others',
    'dice-5' => 'others',
    'dice-6-fill' => 'others',
    'dice-6' => 'others',
    
    // 顯示相關 -> ui
    'display-fill' => 'ui',
    'display' => 'ui',
    
    // 分佈 -> ui
    'distribute-horizontal' => 'ui',
    'distribute-vertical' => 'ui',
    
    // 門 -> others
    'door-closed-fill' => 'others',
    'door-closed' => 'others',
    'door-open-fill' => 'others',
    'door-open' => 'others',
    
    // 點/符號 -> ui
    'dot' => 'ui',
    
    // 下載 -> files
    'download' => 'files',
    
    // 方向鍵盤 -> others
    'dpad-fill' => 'others',
    'dpad' => 'others',
    
    // 水滴 -> others
    'droplet-fill' => 'others',
    'droplet-half' => 'others',
    'droplet' => 'others',
    
    // 耳機 -> media
    'earbuds' => 'media',
    
    // 蛋 -> others
    'egg-fill' => 'others',
    'egg-fried' => 'others',
    'egg' => 'others',
    
    // 彈出 -> ui
    'eject-fill' => 'ui',
    'eject' => 'ui',
];

// 應用分類
$updatedCount = 0;
foreach ($allConfig['icons'] as &$icon) {
    if (isset($manualCategories[$icon['name']])) {
        $oldCategory = $icon['category'];
        $newCategory = $manualCategories[$icon['name']];
        $icon['category'] = $newCategory;
        echo "✅ {$icon['name']}: {$oldCategory} -> {$newCategory}\n";
        $updatedCount++;
    }
}

echo "\n📊 更新了 {$updatedCount} 個圖標的分類\n\n";

// 統計每個分類的數量
$categoryStats = [];
foreach ($manualCategories as $iconName => $category) {
    $categoryStats[$category] = ($categoryStats[$category] ?? 0) + 1;
}

echo "📈 分類統計:\n";
foreach ($categoryStats as $category => $count) {
    echo "  {$category}: {$count} 個圖標\n";
}

// 寫入檔案
$phpContent = "<?php\n\nreturn " . var_export($allConfig, true) . ";\n";

if (file_put_contents($allConfigPath, $phpContent)) {
    echo "\n✅ 成功更新 all.php\n";
    
    // 清除 Laravel 快取
    echo "\n🔄 清除 Laravel 快取...\n";
    exec('cd ' . dirname(__DIR__) . ' && php artisan cache:clear', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ 快取清除成功\n";
    } else {
        echo "⚠️  快取清除可能失敗\n";
    }
    
} else {
    echo "❌ 更新 all.php 失敗\n";
    exit(1);
}

echo "\n🎉 第八批手動分類完成！\n";

?>