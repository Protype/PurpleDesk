<?php

/**
 * 第二十四批手動分類 - 60個圖標
 */

echo "📋 第二十四批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十四批手動分類定義
$manualCategories = [
    // 網路攝影機 -> media
    'webcam' => 'media',
    
    // 陰陽符號 -> others
    'yin-yang' => 'others',
    
    // 繃帶 -> others
    'bandaid-fill' => 'others',
    'bandaid' => 'others',
    
    // 藍牙 -> communications
    'bluetooth' => 'communications',
    
    // 內文 -> alphanumeric
    'body-text' => 'alphanumeric',
    
    // 音響 -> media
    'boombox' => 'media',
    
    // 箱子 -> others
    'boxes' => 'others',
    
    // 耳朵 -> people
    'ear-fill' => 'people',
    'ear' => 'people',
    
    // 信封系列 -> communications
    'envelope-check-fill' => 'communications',
    'envelope-check' => 'communications',
    'envelope-dash-fill' => 'communications',
    'envelope-dash' => 'communications',
    'envelope-exclamation-fill' => 'communications',
    'envelope-exclamation' => 'communications',
    'envelope-plus-fill' => 'communications',
    'envelope-plus' => 'communications',
    'envelope-slash-fill' => 'communications',
    'envelope-slash' => 'communications',
    'envelope-x-fill' => 'communications',
    'envelope-x' => 'communications',
    
    // Git -> others
    'git' => 'others',
    
    // 無限符號 -> general
    'infinity' => 'general',
    
    // 清單欄位 -> ui
    'list-columns-reverse' => 'ui',
    'list-columns' => 'ui',
    
    // 科技公司/平台 -> communications
    'meta' => 'communications',
    'nintendo-switch' => 'communications',
    'playstation' => 'communications',
    'steam' => 'communications',
    'quora' => 'communications',
    
    // 電腦/顯示器 -> others
    'pc-display-horizontal' => 'others',
    'pc-display' => 'others',
    'pc-horizontal' => 'others',
    'pc' => 'others',
    
    // 加減斜線 -> general
    'plus-slash-minus' => 'general',
    
    // 投影機 -> media
    'projector-fill' => 'media',
    'projector' => 'media',
    
    // QR碼 -> others
    'qr-code-scan' => 'others',
    'qr-code' => 'others',
    
    // 引號 -> alphanumeric
    'quote' => 'alphanumeric',
    
    // 機器人 -> others
    'robot' => 'others',
    
    // 傳送系列 -> communications
    'send-check-fill' => 'communications',
    'send-check' => 'communications',
    'send-dash-fill' => 'communications',
    'send-dash' => 'communications',
    'send-exclamation-fill' => 'communications',
    'send-exclamation' => 'communications',
    'send-fill' => 'communications',
    'send-plus-fill' => 'communications',
    'send-plus' => 'communications',
    'send-slash-fill' => 'communications',
    'send-slash' => 'communications',
    'send-x-fill' => 'communications',
    'send-x' => 'communications',
    'send' => 'communications',
    
    // 終端機系列 -> others
    'terminal-dash' => 'others',
    'terminal-plus' => 'others',
    'terminal-split' => 'others',
    
    // 門票 -> others
    'ticket-detailed-fill' => 'others',
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

echo "\n🎉 第二十四批手動分類完成！\n";

?>