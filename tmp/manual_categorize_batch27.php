<?php

/**
 * 第二十七批手動分類 - 60個圖標
 */

echo "📋 第二十七批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十七批手動分類定義
$manualCategories = [
    // 檔案類型系列 -> files
    'filetype-doc' => 'files',
    'filetype-docx' => 'files',
    'filetype-exe' => 'files',
    'filetype-gif' => 'files',
    'filetype-heic' => 'files',
    'filetype-html' => 'files',
    'filetype-java' => 'files',
    'filetype-jpg' => 'files',
    'filetype-js' => 'files',
    'filetype-jsx' => 'files',
    'filetype-key' => 'files',
    'filetype-m4p' => 'files',
    'filetype-md' => 'files',
    'filetype-mdx' => 'files',
    'filetype-mov' => 'files',
    'filetype-mp3' => 'files',
    'filetype-mp4' => 'files',
    'filetype-otf' => 'files',
    'filetype-pdf' => 'files',
    'filetype-php' => 'files',
    'filetype-png' => 'files',
    'filetype-ppt' => 'files',
    'filetype-psd' => 'files',
    'filetype-py' => 'files',
    'filetype-raw' => 'files',
    'filetype-rb' => 'files',
    'filetype-sass' => 'files',
    'filetype-scss' => 'files',
    'filetype-sh' => 'files',
    'filetype-svg' => 'files',
    'filetype-tiff' => 'files',
    'filetype-tsx' => 'files',
    'filetype-ttf' => 'files',
    'filetype-txt' => 'files',
    'filetype-wav' => 'files',
    'filetype-woff' => 'files',
    'filetype-xls' => 'files',
    'filetype-xml' => 'files',
    'filetype-yml' => 'files',
    
    // 愛心相關 -> general
    'heart-arrow' => 'general',
    'heart-pulse-fill' => 'general',
    'heart-pulse' => 'general',
    'heartbreak-fill' => 'general',
    'heartbreak' => 'general',
    'hearts' => 'general',
    
    // 醫院 -> others
    'hospital-fill' => 'others',
    'hospital' => 'others',
    
    // 愛心房屋 -> others
    'house-heart-fill' => 'others',
    'house-heart' => 'others',
    
    // 隱身 -> general
    'incognito' => 'general',
    
    // 磁鐵 -> others
    'magnet-fill' => 'others',
    'magnet' => 'others',
    
    // 愛心人物 -> people
    'person-heart' => 'people',
    'person-hearts' => 'people',
    
    // 翻轉手機 -> communications
    'phone-flip' => 'communications',
    
    // 外掛 -> others
    'plugin' => 'others',
    
    // 郵戳 -> communications
    'postage-fill' => 'communications',
    'postage-heart-fill' => 'communications',
    'postage-heart' => 'communications',
    'postage' => 'communications',
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

echo "\n🎉 第二十七批手動分類完成！\n";

?>