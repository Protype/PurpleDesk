<?php

/**
 * Bootstrap Icons 差異比較器
 * 比較前端 JS utils 檔案與 PHP all.php 配置檔案中的圖標差異
 */

echo "🔍 Bootstrap Icons 差異比較器\n";
echo "================================\n\n";

try {
    // 1. 載入 PHP all.php 配置檔案
    echo "1. 載入 PHP all.php 配置檔案...\n";
    $allPhpPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
    
    if (!file_exists($allPhpPath)) {
        throw new Exception("PHP 配置檔案不存在: $allPhpPath");
    }
    
    $phpConfig = include $allPhpPath;
    $phpIcons = [];
    
    if (isset($phpConfig['icons']) && is_array($phpConfig['icons'])) {
        foreach ($phpConfig['icons'] as $icon) {
            if (isset($icon['name'])) {
                $phpIcons[] = $icon['name'];
            }
        }
    }
    
    echo "   ✅ PHP 配置載入成功: " . count($phpIcons) . " 個圖標\n\n";
    
    // 2. 掃描並載入所有前端 JS 檔案
    echo "2. 掃描前端 JS utils 檔案...\n";
    $jsUtilsPath = __DIR__ . '/../resources/js/utils/icons';
    $jsIcons = [];
    
    if (!is_dir($jsUtilsPath)) {
        throw new Exception("JS utils 目錄不存在: $jsUtilsPath");
    }
    
    $jsFiles = glob($jsUtilsPath . '/bs-*.js');
    
    foreach ($jsFiles as $jsFile) {
        $filename = basename($jsFile);
        echo "   📄 處理檔案: $filename\n";
        
        $content = file_get_contents($jsFile);
        if ($content === false) {
            echo "   ❌ 無法讀取檔案: $filename\n";
            continue;
        }
        
        // 解析 JS 檔案中的圖標定義
        // 尋找類似 { name: 'House', class: 'bi-house' } 的模式
        $pattern = '/\{\s*name:\s*[\'"]([^\'\"]+)[\'"]\s*,\s*class:\s*[\'"]bi-([^\'\"]+)[\'"]/';
        preg_match_all($pattern, $content, $matches);
        
        if (!empty($matches[2])) {
            foreach ($matches[2] as $iconName) {
                $jsIcons[] = $iconName;
            }
            echo "     └─ 找到 " . count($matches[2]) . " 個圖標\n";
        } else {
            echo "     └─ 未找到圖標定義\n";
        }
    }
    
    // 移除重複項目
    $jsIcons = array_unique($jsIcons);
    sort($jsIcons);
    sort($phpIcons);
    
    echo "\n   ✅ JS 檔案掃描完成: " . count($jsIcons) . " 個唯一圖標\n\n";
    
    // 3. 進行差異比較
    echo "3. 進行差異分析...\n";
    
    // 只在 JS 中存在的圖標
    $onlyInJs = array_diff($jsIcons, $phpIcons);
    
    // 只在 PHP 中存在的圖標
    $onlyInPhp = array_diff($phpIcons, $jsIcons);
    
    // 共同存在的圖標
    $inBoth = array_intersect($jsIcons, $phpIcons);
    
    echo "   ✅ 差異分析完成\n\n";
    
    // 4. 顯示統計結果
    echo "📊 統計摘要:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "JS 檔案圖標總數:     " . str_pad(number_format(count($jsIcons)), 10, ' ', STR_PAD_LEFT) . "\n";
    echo "PHP 配置圖標總數:    " . str_pad(number_format(count($phpIcons)), 10, ' ', STR_PAD_LEFT) . "\n";
    echo "共同存在圖標:        " . str_pad(number_format(count($inBoth)), 10, ' ', STR_PAD_LEFT) . "\n";
    echo "只在 JS 中存在:      " . str_pad(number_format(count($onlyInJs)), 10, ' ', STR_PAD_LEFT) . "\n";
    echo "只在 PHP 中存在:     " . str_pad(number_format(count($onlyInPhp)), 10, ' ', STR_PAD_LEFT) . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // 5. 顯示詳細差異
    if (!empty($onlyInJs)) {
        echo "🔍 只在 JS 中存在的圖標 (" . count($onlyInJs) . " 個):\n";
        echo "─────────────────────────────────────────\n";
        $count = 0;
        foreach ($onlyInJs as $icon) {
            echo "  • $icon\n";
            $count++;
            if ($count >= 20) {
                $remaining = count($onlyInJs) - 20;
                echo "  ... 還有 $remaining 個圖標\n";
                break;
            }
        }
        echo "\n";
    }
    
    if (!empty($onlyInPhp)) {
        echo "🔍 只在 PHP 中存在的圖標 (" . count($onlyInPhp) . " 個):\n";
        echo "─────────────────────────────────────────\n";
        $count = 0;
        foreach ($onlyInPhp as $icon) {
            echo "  • $icon\n";
            $count++;
            if ($count >= 20) {
                $remaining = count($onlyInPhp) - 20;
                echo "  ... 還有 $remaining 個圖標\n";
                break;
            }
        }
        echo "\n";
    }
    
    // 6. 生成詳細報告檔案
    echo "6. 生成詳細報告檔案...\n";
    
    $reportContent = "# Bootstrap Icons 差異比較報告\n\n";
    $reportContent .= "生成時間: " . date('Y-m-d H:i:s') . "\n\n";
    
    $reportContent .= "## 統計摘要\n\n";
    $reportContent .= "| 項目 | 數量 |\n";
    $reportContent .= "|------|------|\n";
    $reportContent .= "| JS 檔案圖標總數 | " . number_format(count($jsIcons)) . " |\n";
    $reportContent .= "| PHP 配置圖標總數 | " . number_format(count($phpIcons)) . " |\n";
    $reportContent .= "| 共同存在圖標 | " . number_format(count($inBoth)) . " |\n";
    $reportContent .= "| 只在 JS 中存在 | " . number_format(count($onlyInJs)) . " |\n";
    $reportContent .= "| 只在 PHP 中存在 | " . number_format(count($onlyInPhp)) . " |\n\n";
    
    if (!empty($onlyInJs)) {
        $reportContent .= "## 只在 JS 中存在的圖標\n\n";
        foreach ($onlyInJs as $icon) {
            $reportContent .= "- `$icon`\n";
        }
        $reportContent .= "\n";
    }
    
    if (!empty($onlyInPhp)) {
        $reportContent .= "## 只在 PHP 中存在的圖標\n\n";
        foreach ($onlyInPhp as $icon) {
            $reportContent .= "- `$icon`\n";
        }
        $reportContent .= "\n";
    }
    
    $reportContent .= "## 共同存在的圖標 (前 50 個)\n\n";
    $shownCommon = array_slice($inBoth, 0, 50);
    foreach ($shownCommon as $icon) {
        $reportContent .= "- `$icon`\n";
    }
    if (count($inBoth) > 50) {
        $reportContent .= "\n... 還有 " . (count($inBoth) - 50) . " 個共同圖標\n";
    }
    
    $reportPath = __DIR__ . '/icons_comparison_report.md';
    file_put_contents($reportPath, $reportContent);
    
    echo "   ✅ 詳細報告已生成: $reportPath\n\n";
    
    // 7. 計算覆蓋率
    $coveragePhpToJs = count($jsIcons) > 0 ? (count($inBoth) / count($jsIcons)) * 100 : 0;
    $coverageJsToPhp = count($phpIcons) > 0 ? (count($inBoth) / count($phpIcons)) * 100 : 0;
    
    echo "📈 覆蓋率分析:\n";
    echo "─────────────────────────────────────────\n";
    echo "PHP 對 JS 覆蓋率:    " . number_format($coveragePhpToJs, 1) . "%\n";
    echo "JS 對 PHP 覆蓋率:    " . number_format($coverageJsToPhp, 1) . "%\n\n";
    
    echo "🎉 比較完成！\n";
    
} catch (Exception $e) {
    echo "❌ 比較失敗: " . $e->getMessage() . "\n";
    exit(1);
}

?>