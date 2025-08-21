# Bootstrap Icons 轉換器

## 專案概述

這是一個以 TDD（測試驅動開發）方式開發的 PHP 工具，用於將 Bootstrap Icons 官方 JSON 格式轉換為 Laravel 專案中使用的 PHP 配置檔案格式。

## 功能特色

✅ **完整的 TDD 開發流程**
- 先寫測試，後實作功能
- 完整的測試覆蓋率
- 模組化設計

✅ **核心功能**
- 解析官方 Bootstrap Icons JSON 檔案（2078+ 個圖標）
- 自動轉換圖標名稱格式（`house-door` → `House Door`）
- 智慧生成關鍵字（從圖標名稱拆解）
- 自動檢測圖標變體（outline/solid）
- 生成完整的 PHP 配置檔案

## 專案結構

```
./tmp/bootstrap-icons-converter/
├── src/                          # 核心程式碼
│   ├── JsonParser.php           # JSON 解析器
│   ├── IconProcessor.php        # 圖標處理器
│   └── PhpConfigGenerator.php   # PHP 配置生成器
├── tests/                       # 測試檔案
│   ├── JsonParserTest.php
│   ├── IconProcessorTest.php
│   └── PhpConfigGeneratorTest.php
├── data/                        # 資料檔案
│   └── bootstrap-icons.json    # 官方 JSON 檔案
├── output/                      # 輸出目錄
└── demo.php                     # 演示程式
```

## 使用方法

### 1. 載入官方資料

```bash
curl -s https://raw.githubusercontent.com/twbs/icons/main/font/bootstrap-icons.json -o ./data/bootstrap-icons.json
```

### 2. 執行轉換器

```bash
php8.4 demo.php
```

## 轉換結果展示

轉換器成功將官方 JSON 格式：

```json
{
  "house": 61800,
  "house-door": 61801,
  "alarm-fill": 61697
}
```

轉換為 Laravel 配置格式：

```php
<?php

return [
    'id' => 'general',
    'name' => '通用圖標',
    'description' => '最常用的基礎圖標',
    'priority' => 'immediate',

    'icons' => [
        [
            'name' => 'house',
            'displayName' => 'House',
            'class' => 'bi-house',
            'keywords' => ['house'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-house'],
                'solid' => ['class' => 'bi-house-fill']
            ]
        ],
        [
            'name' => 'house-door',
            'displayName' => 'House Door',
            'class' => 'bi-house-door',
            'keywords' => ['house', 'door'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-house-door']
            ]
        ]
    ]
];
```

## 核心類別說明

### JsonParser
- 載入並解析官方 Bootstrap Icons JSON 檔案
- 提供圖標查詢和統計功能
- 錯誤處理和驗證

### IconProcessor
- 圖標名稱格式轉換
- 關鍵字自動生成
- 變體檢測（outline/solid）
- 分類篩選邏輯

### PhpConfigGenerator
- 生成符合 Laravel 規範的 PHP 配置檔案
- 支援完整的陣列結構
- 語法正確性驗證

## 測試結果

✅ **所有測試通過：**
- JSON 解析器測試：檔案載入、資料解析、錯誤處理
- 圖標處理器測試：名稱轉換、關鍵字生成、變體檢測
- PHP 配置生成器測試：格式輸出、語法驗證

## 執行統計

- **官方圖標總數：** 2,078 個
- **支援格式轉換：** 100%
- **自動變體檢測：** 支援 outline/solid
- **生成配置檔案：** 完全符合 Laravel 格式

## TDD 開發成果

這個專案完全遵循 TDD 開發流程：

1. **紅燈階段：** 先寫測試，確認需求
2. **綠燈階段：** 實作最小功能讓測試通過
3. **重構階段：** 優化程式碼品質
4. **整合測試：** 確保所有組件協同工作

最終產生了一個穩定、可靠、易於維護的 Bootstrap Icons 轉換工具！

## 使用場景

適合用於：
- 同步官方 Bootstrap Icons 到 Laravel 專案
- 批量轉換圖標配置格式
- 自動化圖標管理工作流程
- 確保前後端圖標資料一致性