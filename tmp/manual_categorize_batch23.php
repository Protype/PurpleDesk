<?php

/**
 * 第二十三批手動分類 - 60個圖標
 */

echo "📋 第二十三批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十三批手動分類定義
$manualCategories = [
    // PDF -> files
    'file-pdf' => 'files',
    
    // 性別相關 -> people
    'gender-ambiguous' => 'people',
    'gender-female' => 'people',
    'gender-male' => 'people',
    'gender-trans' => 'people',
    
    // VR 頭戴裝置 -> others
    'headset-vr' => 'others',
    
    // 大型資訊符號 -> general
    'info-lg' => 'general',
    
    // 社群媒體平台 -> communications
    'mastodon' => 'communications',
    'messenger' => 'communications',
    'reddit' => 'communications',
    'skype' => 'communications',
    'behance' => 'communications',
    'dribbble' => 'communications',
    'line' => 'communications',
    'medium' => 'communications',
    'paypal' => 'communications',
    'pinterest' => 'communications',
    'signal' => 'communications',
    'snapchat' => 'communications',
    'spotify' => 'communications',
    'stack-overflow' => 'communications',
    'strava' => 'communications',
    'wordpress' => 'communications',
    'vimeo' => 'communications',
    
    // 小豬存錢筒 -> others
    'piggy-bank-fill' => 'others',
    'piggy-bank' => 'others',
    
    // 地圖圖釘 -> general
    'pin-map-fill' => 'general',
    'pin-map' => 'general',
    
    // 大型符號 -> general
    'plus-lg' => 'general',
    'question-lg' => 'general',
    'slash-lg' => 'general',
    'x-lg' => 'general',
    
    // 回收 -> others
    'recycle' => 'others',
    
    // 保險箱/安全 -> others
    'safe-fill' => 'others',
    'safe2-fill' => 'others',
    'safe2' => 'others',
    'safe' => 'others',
    
    // SD卡 -> others
    'sd-card-fill' => 'others',
    'sd-card' => 'others',
    
    // 翻譯 -> general
    'translate' => 'general',
    
    // 大型科技公司 -> communications
    'apple' => 'communications',
    'microsoft' => 'communications',
    'windows' => 'communications',
    
    // 活動/活躍度 -> general
    'activity' => 'general',
    
    // 畫架系列 -> others
    'easel2-fill' => 'others',
    'easel2' => 'others',
    'easel3-fill' => 'others',
    'easel3' => 'others',
    
    // 指紋 -> general
    'fingerprint' => 'general',
    
    // 帶箭頭的圖表 -> ui
    'graph-down-arrow' => 'ui',
    'graph-up-arrow' => 'ui',
    
    // 催眠 -> others
    'hypnotize' => 'others',
    
    // 魔法 -> others
    'magic' => 'others',
    
    // 人物相關 -> people
    'person-rolodex' => 'people',
    'person-video' => 'people',
    'person-video2' => 'people',
    'person-video3' => 'people',
    'person-workspace' => 'people',
    
    // 輻射性 -> others
    'radioactive' => 'others',
    
    // 網路攝影機 -> media
    'webcam-fill' => 'media',
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

echo "\n🎉 第二十三批手動分類完成！\n";

?>