<?php

/**
 * 第十二批手動分類 - 60個圖標
 */

echo "📋 第十二批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十二批手動分類定義 - File 系列和其他檔案相關圖標
$manualCategories = [
    // File 系列 -> files (非 earmark 版本)
    'file-easel-fill' => 'files',
    'file-easel' => 'files',
    'file-excel-fill' => 'files',
    'file-excel' => 'files',
    'file-fill' => 'files',
    'file-font-fill' => 'files',
    'file-font' => 'files',
    'file-image-fill' => 'files',
    'file-image' => 'files',
    'file-lock-fill' => 'files',
    'file-lock' => 'files',
    'file-lock2-fill' => 'files',
    'file-lock2' => 'files',
    'file-medical-fill' => 'files',
    'file-medical' => 'files',
    'file-minus-fill' => 'files',
    'file-minus' => 'files',
    'file-music-fill' => 'files',
    'file-music' => 'files',
    'file-person-fill' => 'files',
    'file-person' => 'files',
    'file-play-fill' => 'files',
    'file-play' => 'files',
    'file-plus-fill' => 'files',
    'file-plus' => 'files',
    'file-post-fill' => 'files',
    'file-post' => 'files',
    'file-ppt-fill' => 'files',
    'file-ppt' => 'files',
    'file-richtext-fill' => 'files',
    'file-richtext' => 'files',
    'file-ruled-fill' => 'files',
    'file-ruled' => 'files',
    'file-slides-fill' => 'files',
    'file-slides' => 'files',
    'file-spreadsheet-fill' => 'files',
    'file-spreadsheet' => 'files',
    'file-text-fill' => 'files',
    'file-text' => 'files',
    'file-word-fill' => 'files',
    'file-word' => 'files',
    'file-x-fill' => 'files',
    'file-x' => 'files',
    'file-zip-fill' => 'files',
    'file-zip' => 'files',
    'file' => 'files',
    'files-alt' => 'files',
    'files' => 'files',
    
    // 字型相關 -> alphanumeric
    'fonts' => 'alphanumeric',
    
    // 前進/後退 -> ui
    'forward-fill' => 'ui',
    'forward' => 'ui',
    
    // 前面 -> others
    'front' => 'others',
    
    // 全螢幕相關 -> ui
    'fullscreen-exit' => 'ui',
    'fullscreen' => 'ui',
    
    // 漏斗/過濾 -> ui
    'funnel-fill' => 'ui',
    'funnel' => 'ui',
    
    // 齒輪/設定 -> ui
    'gear-fill' => 'ui',
    'gear-wide-connected' => 'ui',
    'gear-wide' => 'ui',
    'gear' => 'ui',
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

echo "\n🎉 第十二批手動分類完成！\n";

?>