<?php

// 測試執行器
echo "🧪 Bootstrap Icons 轉換器測試執行\n";
echo "====================================\n\n";

// 載入測試類別
require_once __DIR__ . '/tests/JsonParserTest.php';
require_once __DIR__ . '/tests/PhpConfigGeneratorTest.php';
require_once __DIR__ . '/tests/IconProcessorTest.php';

// 執行所有測試
$tests = [
    new JsonParserTest(),
    new PhpConfigGeneratorTest(),
    new IconProcessorTest()
];

$allSuccess = true;
foreach ($tests as $test) {
    if (!$test->runAllTests()) {
        $allSuccess = false;
    }
}

$success = $allSuccess;

if ($success) {
    echo "🎉 所有測試通過！\n";
} else {
    echo "❌ 有測試失敗\n";
    exit(1);
}