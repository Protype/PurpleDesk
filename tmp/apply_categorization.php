<?php

/**
 * 應用圖標分類變更到 all.php
 */

echo "🔧 應用圖標分類變更\n";
echo "====================\n\n";

// 載入分類結果
$resultPath = __DIR__ . '/categorization_result.json';
if (!file_exists($resultPath)) {
    echo "❌ 找不到分類結果檔案: $resultPath\n";
    echo "請先執行 categorize_icons.php\n";
    exit(1);
}

$categorizedIcons = json_decode(file_get_contents($resultPath), true);

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

$icons = $allConfig['icons'];
$updatedIcons = [];
$updatedCount = 0;

echo "📝 更新圖標分類...\n\n";

foreach ($icons as $icon) {
    $iconName = $icon['name'];
    $updated = false;
    
    // 檢查每個分類中是否有這個圖標
    foreach ($categorizedIcons as $category => $categoryIcons) {
        foreach ($categoryIcons as $categorizedIcon) {
            if ($categorizedIcon['name'] === $iconName) {
                // 更新分類
                $icon['category'] = $category;
                $updated = true;
                $updatedCount++;
                echo "✅ 更新 {$iconName}: all -> {$category}\n";
                break 2;
            }
        }
    }
    
    $updatedIcons[] = $icon;
}

// 更新配置陣列
$allConfig['icons'] = $updatedIcons;

// 生成 PHP 配置檔案內容
$phpContent = "<?php\n\nreturn " . var_export($allConfig, true) . ";\n";

// 寫入檔案
if (file_put_contents($allConfigPath, $phpContent)) {
    echo "\n✅ 成功更新 all.php，共更新 $updatedCount 個圖標\n";
    
    // 清除 Laravel 快取
    echo "\n🔄 清除 Laravel 快取...\n";
    exec('cd ' . dirname(__DIR__) . ' && php artisan cache:clear', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ 快取清除成功\n";
    } else {
        echo "⚠️  快取清除可能失敗，請手動執行: php artisan cache:clear\n";
    }
    
} else {
    echo "\n❌ 更新 all.php 失敗\n";
    exit(1);
}

echo "\n📊 分類統計:\n";
foreach ($categorizedIcons as $category => $categoryIcons) {
    if (!empty($categoryIcons)) {
        echo "  $category: " . count($categoryIcons) . " 個圖標\n";
    }
}

echo "\n🎉 分類更新完成！\n";

?>