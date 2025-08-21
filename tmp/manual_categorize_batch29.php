<?php

/**
 * 第二十九批手動分類 - 60個圖標
 */

echo "📋 第二十九批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十九批手動分類定義
$manualCategories = [
    // Android -> communications
    'android' => 'communications',
    'android2' => 'communications',
    
    // 盒子 -> others
    'box-fill' => 'others',
    'box-seam-fill' => 'others',
    
    // 瀏覽器系列 -> communications
    'browser-chrome' => 'communications',
    'browser-edge' => 'communications',
    'browser-firefox' => 'communications',
    'browser-safari' => 'communications',
    
    // 字母 C -> alphanumeric
    'c-circle-fill' => 'alphanumeric',
    'c-circle' => 'alphanumeric',
    'c-square-fill' => 'alphanumeric',
    'c-square' => 'alphanumeric',
    
    // 膠囊藥丸 -> others
    'capsule-pill' => 'others',
    'capsule' => 'others',
    
    // 汽車前面 -> others
    'car-front-fill' => 'others',
    'car-front' => 'others',
    
    // 錄音帶 -> media
    'cassette-fill' => 'media',
    'cassette' => 'media',
    
    // CC 版權 -> alphanumeric
    'cc-circle-fill' => 'alphanumeric',
    'cc-circle' => 'alphanumeric',
    'cc-square-fill' => 'alphanumeric',
    'cc-square' => 'alphanumeric',
    
    // 熱杯 -> others
    'cup-hot-fill' => 'others',
    'cup-hot' => 'others',
    
    // 印度盧比 -> others
    'currency-rupee' => 'others',
    
    // Dropbox -> communications
    'dropbox' => 'communications',
    
    // 逃脫鍵 -> alphanumeric
    'escape' => 'alphanumeric',
    
    // 快進系列 -> media
    'fast-forward-circle-fill' => 'media',
    'fast-forward-circle' => 'media',
    'fast-forward-fill' => 'media',
    'fast-forward' => 'media',
    
    // SQL 檔案 -> files
    'filetype-sql' => 'files',
    
    // 火焰 -> others
    'fire' => 'others',
    
    // Google Play -> communications
    'google-play' => 'communications',
    
    // 字母 H -> alphanumeric
    'h-circle-fill' => 'alphanumeric',
    'h-circle' => 'alphanumeric',
    'h-square-fill' => 'alphanumeric',
    'h-square' => 'alphanumeric',
    
    // 縮排 -> ui
    'indent' => 'ui',
    
    // 肺部 -> others
    'lungs-fill' => 'others',
    'lungs' => 'others',
    
    // Microsoft Teams -> communications
    'microsoft-teams' => 'communications',
    
    // 字母 P -> alphanumeric
    'p-circle-fill' => 'alphanumeric',
    'p-circle' => 'alphanumeric',
    'p-square-fill' => 'alphanumeric',
    'p-square' => 'alphanumeric',
    
    // 通行證 -> others
    'pass-fill' => 'others',
    'pass' => 'others',
    
    // 處方 -> others
    'prescription' => 'others',
    'prescription2' => 'others',
    
    // 字母 R -> alphanumeric
    'r-circle-fill' => 'alphanumeric',
    'r-circle' => 'alphanumeric',
    'r-square-fill' => 'alphanumeric',
    'r-square' => 'alphanumeric',
    
    // 重複 -> media
    'repeat-1' => 'media',
    'repeat' => 'media',
    
    // 倒帶 -> media
    'rewind-btn-fill' => 'media',
    'rewind-btn' => 'media',
    'rewind-circle-fill' => 'media',
    'rewind-circle' => 'media',
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
    exec('cd ' . dirname(__DIR__) . ' && php8.4 artisan cache:clear', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ 快取清除成功\n";
    } else {
        echo "⚠️  快取清除可能失敗\n";
    }
    
} else {
    echo "❌ 更新 all.php 失敗\n";
    exit(1);
}

echo "\n🎉 第二十九批手動分類完成！\n";

?>