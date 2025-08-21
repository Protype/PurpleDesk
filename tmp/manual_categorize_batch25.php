<?php

/**
 * 第二十五批手動分類 - 60個圖標
 */

echo "📋 第二十五批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十五批手動分類定義
$manualCategories = [
    // 門票系列 -> others
    'ticket-detailed' => 'others',
    'ticket-fill' => 'others',
    'ticket-perforated-fill' => 'others',
    'ticket-perforated' => 'others',
    'ticket' => 'others',
    
    // TikTok -> communications
    'tiktok' => 'communications',
    
    // 視窗系列 -> ui
    'window-dash' => 'ui',
    'window-desktop' => 'ui',
    'window-fullscreen' => 'ui',
    'window-plus' => 'ui',
    'window-split' => 'ui',
    'window-stack' => 'ui',
    'window-x' => 'ui',
    
    // Xbox -> communications
    'xbox' => 'communications',
    
    // 網路/連接介面 -> others
    'ethernet' => 'others',
    'hdmi-fill' => 'others',
    'hdmi' => 'others',
    'usb-c-fill' => 'others',
    'usb-c' => 'others',
    'usb-fill' => 'others',
    'usb-plug-fill' => 'others',
    'usb-plug' => 'others',
    'usb-symbol' => 'others',
    'usb' => 'others',
    'displayport' => 'others',
    'displayport-fill' => 'others',
    'thunderbolt-fill' => 'others',
    'thunderbolt' => 'others',
    'usb-drive-fill' => 'others',
    'usb-drive' => 'others',
    'usb-micro-fill' => 'others',
    'usb-micro' => 'others',
    'usb-mini-fill' => 'others',
    'usb-mini' => 'others',
    
    // 音響設備 -> media
    'boombox-fill' => 'media',
    'optical-audio-fill' => 'media',
    'optical-audio' => 'media',
    
    // 電腦硬體 -> others
    'gpu-card' => 'others',
    'memory' => 'others',
    'modem-fill' => 'others',
    'modem' => 'others',
    'motherboard-fill' => 'others',
    'motherboard' => 'others',
    'pci-card' => 'others',
    'router-fill' => 'others',
    'router' => 'others',
    'device-hdd-fill' => 'others',
    'device-hdd' => 'others',
    'device-ssd-fill' => 'others',
    'device-ssd' => 'others',
    
    // 雲霧 -> others
    'cloud-haze2' => 'others',
    
    // 學士帽 -> others
    'mortarboard-fill' => 'others',
    'mortarboard' => 'others',
    
    // 終端機 X -> others
    'terminal-x' => 'others',
    
    // 愛心箭頭 -> general
    'arrow-through-heart-fill' => 'general',
    'arrow-through-heart' => 'general',
    
    // SD 徽章 -> others
    'badge-sd-fill' => 'others',
    'badge-sd' => 'others',
    
    // 愛心包包 -> others
    'bag-heart-fill' => 'others',
    'bag-heart' => 'others',
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

echo "\n🎉 第二十五批手動分類完成！\n";

?>