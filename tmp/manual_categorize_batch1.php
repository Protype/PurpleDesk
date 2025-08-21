<?php

/**
 * 第一批手動分類 - 60個圖標
 */

echo "📋 第一批手動圖標分類\n";
echo "=====================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第一批手動分類定義（基於圖標功能和用途）
$manualCategories = [
    // 文字對齊相關 -> alphanumeric (文字排版)
    'align-bottom' => 'alphanumeric',
    'align-center' => 'alphanumeric', 
    'align-end' => 'alphanumeric',
    'align-middle' => 'alphanumeric',
    'align-start' => 'alphanumeric',
    'align-top' => 'alphanumeric',
    
    // 檔案/存檔相關 -> files
    'archive-fill' => 'files',
    'archive' => 'files',
    
    // UI/顯示相關 -> ui
    'aspect-ratio-fill' => 'ui',
    'aspect-ratio' => 'ui',
    
    // 特殊符號 -> others
    'asterisk' => 'others',
    
    // 獎勵/成就 -> others
    'award-fill' => 'others',
    'award' => 'others',
    
    // 鍵盤/輸入相關 -> others  
    'backspace-fill' => 'others',
    'backspace' => 'others',
    'backspace-reverse-fill' => 'others',
    'backspace-reverse' => 'others',
    
    // UI 徽章相關 -> ui
    'badge-3d-fill' => 'ui',
    'badge-3d' => 'ui',
    'badge-4k-fill' => 'ui', 
    'badge-4k' => 'ui',
    'badge-8k-fill' => 'ui',
    'badge-8k' => 'ui',
    'badge-ad-fill' => 'ui',
    'badge-ad' => 'ui',
    'badge-ar-fill' => 'ui',
    'badge-ar' => 'ui',
    'badge-cc-fill' => 'ui',
    'badge-cc' => 'ui',
    'badge-hd-fill' => 'ui',
    'badge-hd' => 'ui',
    'badge-tm-fill' => 'ui',
    'badge-tm' => 'ui',
    'badge-vo-fill' => 'ui',
    'badge-vo' => 'ui',
    'badge-vr-fill' => 'ui',
    'badge-vr' => 'ui',
    'badge-wc-fill' => 'ui',
    'badge-wc' => 'ui',
    
    // 購物袋/商業相關 -> others
    'bag-dash-fill' => 'others',
    'bag-dash' => 'others',
    'bag-fill' => 'others',
    'bag' => 'others',
    
    // 圖表相關 -> ui
    'bar-chart-fill' => 'ui',
    'bar-chart' => 'ui',
    'bar-chart-line-fill' => 'ui',
    'bar-chart-line' => 'ui',
    
    // 購物籃/商業相關 -> others
    'basket-fill' => 'others',
    'basket' => 'others',
    'basket2-fill' => 'others',
    'basket2' => 'others',
    'basket3-fill' => 'others', 
    'basket3' => 'others',
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

echo "\n🎉 第一批手動分類完成！\n";

?>