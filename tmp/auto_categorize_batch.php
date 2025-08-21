<?php

/**
 * 自動批次圖標分類工具
 * 持續處理直到 all-filtered 清空
 */

echo "🚀 自動批次圖標分類工具\n";
echo "========================\n\n";

$batchCount = 1;
$maxBatches = 50; // 最大批次數量，防止無限循環

while ($batchCount <= $maxBatches) {
    echo "📦 開始處理第 {$batchCount} 批...\n";
    
    // 檢查剩餘圖標數量
    $allConfig = include __DIR__ . '/../config/icon/bootstrap-icons/all.php';
    $remainingCount = 0;
    foreach ($allConfig['icons'] as $icon) {
        if (isset($icon['category']) && $icon['category'] === 'all') {
            $remainingCount++;
        }
    }
    
    echo "📊 剩餘 'all' 分類圖標: {$remainingCount} 個\n";
    
    if ($remainingCount === 0) {
        echo "🎉 所有圖標分類完成！\n";
        break;
    }
    
    // 執行分類
    echo "\n🔄 執行圖標分類...\n";
    $categorizeOutput = [];
    $categorizeReturn = 0;
    exec('cd ' . dirname(__DIR__) . ' && php8.4 tmp/categorize_icons.php 2>&1', $categorizeOutput, $categorizeReturn);
    
    if ($categorizeReturn !== 0) {
        echo "❌ 分類步驟失敗\n";
        echo implode("\n", $categorizeOutput) . "\n";
        break;
    }
    
    // 應用分類變更
    echo "\n🔧 應用分類變更...\n";
    $applyOutput = [];
    $applyReturn = 0;
    exec('cd ' . dirname(__DIR__) . ' && php8.4 tmp/apply_categorization.php 2>&1', $applyOutput, $applyReturn);
    
    if ($applyReturn !== 0) {
        echo "❌ 應用變更失敗\n";
        echo implode("\n", $applyOutput) . "\n";
        break;
    }
    
    // 清除快取
    echo "\n🗑️  清除快取...\n";
    exec('cd ' . dirname(__DIR__) . ' && php8.4 artisan cache:clear 2>&1');
    
    // Git commit
    echo "\n📝 提交變更...\n";
    $commitMessage = "feat(bootstrap-icons): 第" . $batchCount . "批圖標分類調整 (自動處理)

🤖 Generated with [Claude Code](https://claude.ai/code)

Co-Authored-By: Claude <noreply@anthropic.com>";
    
    exec('cd ' . dirname(__DIR__) . ' && git add config/icon/bootstrap-icons/all.php');
    exec('cd ' . dirname(__DIR__) . ' && git commit -m "' . addslashes($commitMessage) . '"', $commitOutput, $commitReturn);
    
    if ($commitReturn === 0) {
        echo "✅ 第 {$batchCount} 批變更已提交\n";
    } else {
        echo "⚠️  Git commit 可能失敗\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
    
    $batchCount++;
    
    // 短暫休息，避免系統負載過重
    sleep(1);
}

if ($batchCount > $maxBatches) {
    echo "⚠️  已達到最大批次數量限制 ({$maxBatches})，停止處理\n";
}

echo "🏁 批次處理完成！\n";

?>