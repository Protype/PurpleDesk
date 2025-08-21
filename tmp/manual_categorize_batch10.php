<?php

/**
 * 第十批手動分類 - 60個圖標
 */

echo "📋 第十批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十批手動分類定義 - 主要是 Facebook 和 file-earmark 系列
$manualCategories = [
    // Facebook -> communications
    'facebook' => 'communications',
    
    // File-earmark 系列 -> files
    'file-earmark-arrow-up-fill' => 'files',
    'file-earmark-arrow-up' => 'files',
    'file-earmark-bar-graph-fill' => 'files',
    'file-earmark-bar-graph' => 'files',
    'file-earmark-binary-fill' => 'files',
    'file-earmark-binary' => 'files',
    'file-earmark-break-fill' => 'files',
    'file-earmark-break' => 'files',
    'file-earmark-check-fill' => 'files',
    'file-earmark-check' => 'files',
    'file-earmark-code-fill' => 'files',
    'file-earmark-code' => 'files',
    'file-earmark-diff-fill' => 'files',
    'file-earmark-diff' => 'files',
    'file-earmark-easel-fill' => 'files',
    'file-earmark-easel' => 'files',
    'file-earmark-excel-fill' => 'files',
    'file-earmark-excel' => 'files',
    'file-earmark-fill' => 'files',
    'file-earmark-font-fill' => 'files',
    'file-earmark-font' => 'files',
    'file-earmark-image-fill' => 'files',
    'file-earmark-image' => 'files',
    'file-earmark-lock-fill' => 'files',
    'file-earmark-lock' => 'files',
    'file-earmark-lock2-fill' => 'files',
    'file-earmark-lock2' => 'files',
    'file-earmark-medical-fill' => 'files',
    'file-earmark-medical' => 'files',
    'file-earmark-minus-fill' => 'files',
    'file-earmark-minus' => 'files',
    'file-earmark-music-fill' => 'files',
    'file-earmark-music' => 'files',
    'file-earmark-person-fill' => 'files',
    'file-earmark-person' => 'files',
    'file-earmark-play-fill' => 'files',
    'file-earmark-play' => 'files',
    'file-earmark-plus-fill' => 'files',
    'file-earmark-plus' => 'files',
    'file-earmark-post-fill' => 'files',
    'file-earmark-post' => 'files',
    'file-earmark-ppt-fill' => 'files',
    'file-earmark-ppt' => 'files',
    'file-earmark-richtext-fill' => 'files',
    'file-earmark-richtext' => 'files',
    'file-earmark-ruled-fill' => 'files',
    'file-earmark-ruled' => 'files',
    'file-earmark-slides-fill' => 'files',
    'file-earmark-slides' => 'files',
    'file-earmark-spreadsheet-fill' => 'files',
    'file-earmark-spreadsheet' => 'files',
    'file-earmark-text-fill' => 'files',
    'file-earmark-text' => 'files',
    'file-earmark-word-fill' => 'files',
    'file-earmark-word' => 'files',
    'file-earmark-x-fill' => 'files',
    'file-earmark-x' => 'files',
    'file-earmark-zip-fill' => 'files',
    'file-earmark-zip' => 'files',
    'file-earmark' => 'files',
    
    // 填充工具 -> others
    'fill' => 'others',
    
    // 膠卷 -> media
    'film' => 'media',
    
    // 過濾器 -> ui
    'filter-circle-fill' => 'ui',
    'filter-circle' => 'ui',
    'filter-left' => 'ui',
    'filter-right' => 'ui',
    'filter-square-fill' => 'ui',
    'filter-square' => 'ui',
    'filter' => 'ui',
    
    // 旗幟 -> others
    'flag-fill' => 'others',
    'flag' => 'others',
    
    // 軟碟 -> files
    'floppy-fill' => 'files',
    'floppy' => 'files',
    'floppy2-fill' => 'files',
    'floppy2' => 'files',
    
    // 花朵 -> others
    'flower1' => 'others',
    'flower2' => 'others',
    'flower3' => 'others',
    
    // 資料夾 -> files
    'folder-check' => 'files',
    'folder-fill' => 'files',
    'folder-minus' => 'files',
    'folder-plus' => 'files',
    'folder-symlink-fill' => 'files',
    'folder-symlink' => 'files',
    'folder-x' => 'files',
    'folder' => 'files',
    'folder2-open' => 'files',
    'folder2' => 'files',
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

echo "\n🎉 第十批手動分類完成！\n";

?>