# 圖標系統顏色規範

**文件版本**: 1.0  
**建立日期**: 2025-08-22  
**維護者**: 開發團隊

## 概述

定義 PurpleDesk 圖標系統的完整顏色規範，包含預設調色盤、淡色系配色、顏色選擇原則和自動計算邏輯。

## 設定檔案位置

### 後端配置檔案
- **主要配置**: `config/icon/colors.php`
  - 亮度閾值設定 (`luminance_threshold`)
  - 標準深色調色盤 (`standard_colors`)
  - 淡色系調色盤 (`light_colors`)
  - 淡色背景與深色前景配對 (`light_colors_with_foreground`)
  - 灰階色彩 (`gray_colors`)

### 前端配置檔案
- **顯示配置**: `resources/js/config/iconDisplayConfig.js`
  - 圖標尺寸配置
  - 字體大小設定
  - 邊距和定位參數

### 使用方式
```php
// 後端讀取顏色配置
$colors = config('icon.colors');
$standardColors = $colors['standard_colors'];
$lightColors = $colors['light_colors'];
```

```javascript
// 前端讀取配置
import { ICON_DISPLAY_CONFIG } from '@/config/iconDisplayConfig.js'
const textSize = ICON_DISPLAY_CONFIG.textCJKBySize['md']
```

## 預設調色盤（16 色）

| 色碼 | 名稱 | 用途 |
|------|------|------|
| #ef4444 | 紅色 Red | 警告、錯誤狀態 |
| #f97316 | 橙色 Orange | 提醒、注意 |
| #f59e0b | 黃色 Amber | 警告、等待 |
| #eab308 | 黃綠色 Yellow | 提示、高亮 |
| #84cc16 | 萊姆色 Lime | 新鮮、活力 |
| #22c55e | 綠色 Green | 成功、確認 |
| #10b981 | 翠綠色 Emerald | 自然、環保 |
| #14b8a6 | 青綠色 Teal | 平衡、專業 |
| #06b6d4 | 青色 Cyan | 清新、技術 |
| #0ea5e9 | 天空藍 Sky Blue | 開放、自由 |
| #3b82f6 | 藍色 Blue | 信任、穩定 |
| #6366f1 | 靛藍色 Indigo | 智慧、深度 |
| #9b6eff | 紫色 Primary | **系統主色** |
| #8b5cf6 | 紫羅蘭 Violet | 創意、想像 |
| #a855f7 | 紫色 Purple | 尊貴、神秘 |
| #d946ef | 紫紅色 Fuchsia | 時尚、活潑 |
| #ec4899 | 桃紅色 Pink | 溫暖、友善 |

## 淡色系調色盤（16 色）

用於大面積背景或需要較低視覺強度的場景：

| 背景色碼 | 背景名稱 | 對應深色前景 | 前景色碼 |
|----------|----------|--------------|----------|
| #fecaca | 淡紅色 Light Red | 深紅色 | #991b1b |
| #fed7aa | 淡橙色 Light Orange | 深橙色 | #9a3412 |
| #fde68a | 淡黃色 Light Amber | 深黃色 | #92400e |
| #fef08a | 淡黃綠色 Light Yellow | 深黃綠色 | #854d0e |
| #d9f99d | 淡萊姆色 Light Lime | 深萊姆色 | #3f6212 |
| #bbf7d0 | 淡綠色 Light Green | 深綠色 | #14532d |
| #a7f3d0 | 淡翠綠色 Light Emerald | 深翠綠色 | #064e3b |
| #99f6e4 | 淡青綠色 Light Teal | 深青綠色 | #134e4a |
| #a5f3fc | 淡青色 Light Cyan | 深青色 | #164e63 |
| #bae6fd | 淡天空藍 Light Sky | 深天空藍 | #0c4a6e |
| #dbeafe | 淡藍色 Light Blue | 深藍色 | #1e3a8a |
| #c7d2fe | 淡靛藍色 Light Indigo | 深靛藍色 | #312e81 |
| #ddd6fe | 淡紫羅蘭 Light Violet | 深紫羅蘭 | #4c1d95 |
| #e9d5ff | 淡紫色 Light Purple | 深紫色 | #581c87 |
| #f5d0fe | 淡紫紅色 Light Fuchsia | 深紫紅色 | #701a75 |
| #fbcfe8 | 淡桃紅色 Light Pink | 深桃紅色 | #831843 |

## 基礎色彩（黑白灰）

| 色碼 | 名稱 | 用途 |
|------|------|------|
| #000000 | 黑色 | 最深文字 |
| #ffffff | 白色 | 淺色背景文字 |
| #f9fafb | gray-50 | 極淺背景 |
| #f3f4f6 | gray-100 | 淺背景 |
| #e5e7eb | gray-200 | 分隔線 |
| #d1d5db | gray-300 | 邊框 |
| #9ca3af | gray-400 | 次要文字 |
| #6b7280 | gray-500 | 中性文字 |
| #374151 | gray-700 | 主要文字 |
| #1f2937 | gray-800 | 深色文字 |
| #111827 | gray-900 | 最深背景 |

## 顏色選擇原則

### 背景顏色使用原則
1. **預設調色盤（16 色）**：適用於需要醒目識別的場景
2. **淡色系調色盤（16 色）**：適用於大面積背景或輕量提示
3. **自訂顏色**：支援 HTML 色彩選擇器，但建議使用預設調色盤

### 文字/圖標顏色限制
- **僅限兩種顏色**：白色 (#ffffff) 或深灰色 (#1f2937)
- **自動計算邏輯**：根據背景亮度自動選擇最佳前景色
- **對比度要求**：必須符合 WCAG AA 標準

## textColor 和 iconColor 決定邏輯

### 三種決定方式

#### 1. 預設固定顏色（最高優先級）
特定使用場景有固定的顏色配置：

```javascript
// 個人頭像預設
{
  type: 'text',
  backgroundColor: '#6366f1', // indigo-500
  textColor: '#ffffff'        // 固定白色
}

// 組織 Logo 預設
{
  type: 'hero_icon',
  backgroundColor: '#faf5ff', // purple-50  
  iconColor: '#7c3aed'        // 固定深紫色 purple-600
}

// 團隊 Logo 預設
{
  type: 'bootstrap_icon', 
  backgroundColor: '#dbeafe', // blue-100
  iconColor: '#2563eb'        // 固定深藍色 blue-600
}
```

#### 2. 預定義配對（次優先級）
從 `config/icon/colors.php` 的 `light_colors_with_foreground` 配置：

```php
'light_colors_with_foreground' => [
    '#faf5ff' => '#7c3aed', // purple-50 -> purple-600
    '#dbeafe' => '#2563eb', // blue-100 -> blue-600 
    '#fef2f2' => '#b91c1c', // red-50 -> red-700
    '#fff7ed' => '#c2410c', // orange-50 -> orange-700
    '#fffbeb' => '#b45309', // amber-50 -> amber-700
    '#fefce8' => '#a16207', // yellow-50 -> yellow-700
    '#f7fee7' => '#4d7c0f', // lime-50 -> lime-700
    '#f0fdf4' => '#15803d', // green-50 -> green-700
    '#ecfdf5' => '#047857', // emerald-50 -> emerald-700
    '#f0fdfa' => '#0f766e', // teal-50 -> teal-700
    '#ecfeff' => '#0e7490', // cyan-50 -> cyan-700
    '#f0f9ff' => '#0369a1', // sky-50 -> sky-700
    '#eff6ff' => '#1d4ed8', // blue-50 -> blue-700
    '#eef2ff' => '#4338ca', // indigo-50 -> indigo-700
    '#f3e8ff' => '#6d28d9', // violet-50 -> violet-700
    '#e9d5ff' => '#581c87', // purple-50 -> purple-700 (注意：與預設不同)
    '#f5d0fe' => '#be185d', // fuchsia-50 -> fuchsia-700
    '#fbcfe8' => '#831843'  // pink-50 -> pink-700
],
```

#### 3. 自動亮度計算（最低優先級）
當背景色不在預定義配對中時，使用 W3C 相對亮度公式：

```javascript
// 相對亮度計算（W3C 公式）
const getLuminance = (hexColor) => {
  const hex = hexColor.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16) 
  const b = parseInt(hex.substr(4, 2), 16)
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255
}

// 自動選擇文字/圖標顏色
const getAutoColor = (backgroundColor) => {
  const luminance = getLuminance(backgroundColor)
  const threshold = 0.6 // 來自 config('icon.colors.luminance_threshold')
  
  return luminance > threshold 
    ? '#1f2937'  // 深灰色（亮背景）
    : '#ffffff'  // 白色（暗背景）
}
```

### 完整決定流程

```javascript
// 完整的顏色決定邏輯
export const getTextOrIconColor = async (backgroundColor, iconType = 'text') => {
  const colorConfig = await fetchColorConfig()
  
  // 1. 檢查預定義配對（最高優先級）
  if (colorConfig.light_colors_with_foreground[backgroundColor]) {
    return colorConfig.light_colors_with_foreground[backgroundColor]
  }
  
  // 2. 使用亮度計算（最低優先級）
  const luminance = calculateLuminance(backgroundColor)
  const threshold = colorConfig.luminance_threshold || 0.6
  
  return luminance > threshold 
    ? colorConfig.dark_foreground_color   // #1f2937
    : colorConfig.light_foreground_color  // #ffffff
}
```

### 特殊情況處理

#### A. 品牌一致性優先
- 組織和團隊的預設配色有特定的品牌考量
- 即使亮度計算結果不同，仍使用預設配色

#### B. 衝突解決
當 `light_colors_with_foreground` 配對與預設配色不同時：
- **組織 Logo**: `#faf5ff` + `#7c3aed` （使用預設，不使用配對中的 purple-700）
- **其他場景**: 優先使用 `light_colors_with_foreground` 配對

#### C. 錯誤處理
```javascript
// 當無法決定顏色時的回退機制
const fallbackColor = colorConfig.light_foreground_color || '#ffffff'
```

## 顏色自動計算

### 亮度計算公式
```javascript
// 相對亮度計算（W3C 公式）
const getLuminance = (hexColor) => {
  const hex = hexColor.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255
}

// 自動選擇文字顏色
const textColor = getLuminance(backgroundColor) > 0.5 
  ? '#1f2937'  // 深灰色（亮背景）
  : '#ffffff'  // 白色（暗背景）
```

### 後端顏色驗證
```php
// IconDataHelper 驗證方法（使用配置檔案）
public static function isAllowedBackgroundColor(string $color): bool
{
    $colorConfig = config('icon.colors');
    
    $allowedColors = array_merge(
        $colorConfig['standard_colors'] ?? [],
        $colorConfig['light_colors'] ?? [],
        array_keys($colorConfig['light_colors_with_foreground'] ?? []),
        $colorConfig['gray_colors'] ?? []
    );
    
    return in_array($color, $allowedColors);
}

// 取得前景色（基於配置檔案邏輯）
public static function getForegroundColor(string $backgroundColor): string
{
    $colorConfig = config('icon.colors');
    
    // 檢查是否有特定的前景色配對
    if (isset($colorConfig['light_colors_with_foreground'][$backgroundColor])) {
        return $colorConfig['light_colors_with_foreground'][$backgroundColor];
    }
    
    // 使用亮度計算自動選擇
    $luminance = self::calculateLuminance($backgroundColor);
    $threshold = $colorConfig['luminance_threshold'] ?? 0.6;
    
    return $luminance > $threshold 
        ? ($colorConfig['dark_foreground_color'] ?? '#1f2937')
        : ($colorConfig['light_foreground_color'] ?? '#ffffff');
}
```

## 隨機顏色功能

### 實作邏輯
- **隨機淡色**：從淡色系調色盤中隨機選擇
- **用途**：快速生成獨特識別的背景色
- **實作**：優先選擇未被使用的顏色

### 前端實作（基於後端配置）
```javascript
// 從 API 取得顏色配置
const fetchColorConfig = async () => {
  const response = await fetch('/api/icon/colors')
  return response.json()
}

// 隨機淡色選擇
export const getRandomLightColor = async (excludeColors = []) => {
  const colorConfig = await fetchColorConfig()
  const lightColors = colorConfig.light_colors || []
  
  const availableColors = lightColors.filter(color => !excludeColors.includes(color))
  return availableColors[Math.floor(Math.random() * availableColors.length)]
}

// 亮度計算工具函式
const calculateLuminance = (hexColor) => {
  const hex = hexColor.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255
}

// 統一的顏色決定邏輯
const getColorByPriority = async (backgroundColor) => {
  const colorConfig = await fetchColorConfig()
  
  // 1. 檢查預定義配對（最高優先級）
  if (colorConfig.light_colors_with_foreground[backgroundColor]) {
    return colorConfig.light_colors_with_foreground[backgroundColor]
  }
  
  // 2. 使用亮度計算（最低優先級）
  const luminance = calculateLuminance(backgroundColor)
  const threshold = colorConfig.luminance_threshold || 0.6
  
  return luminance > threshold 
    ? colorConfig.dark_foreground_color   // #1f2937
    : colorConfig.light_foreground_color  // #ffffff
}

// 取得文字顏色
export const getTextColor = async (backgroundColor) => {
  return getColorByPriority(backgroundColor)
}

// 取得圖標顏色
export const getIconColor = async (backgroundColor) => {
  return getColorByPriority(backgroundColor)
}

// 產生完整的 IconData（處理預設情況）
export const generateIconData = async (type, options = {}) => {
  const { backgroundColor, icon, text, emoji, path, style } = options
  
  const baseData = {
    type,
    backgroundColor: backgroundColor || await getRandomLightColor()
  }
  
  switch (type) {
    case 'text':
      return {
        ...baseData,
        text: text || 'A',
        textColor: await getTextColor(baseData.backgroundColor)
      }
      
    case 'hero_icon':
    case 'bootstrap_icon':
      return {
        ...baseData,
        icon: icon || 'default-icon',
        style: style || (type === 'hero_icon' ? 'outline' : 'outline'),
        iconColor: await getIconColor(baseData.backgroundColor)
      }
      
    case 'emoji':
      return {
        ...baseData,
        emoji: emoji || '😀'
      }
      
    case 'image':
      return {
        ...baseData,
        path: path || ''
      }
      
    default:
      throw new Error(`Unknown icon type: ${type}`)
  }
}
```

## 設定檔案對應關係

### 配置映射表
| 規範中的顏色 | config/icon/colors.php 中的鍵值 | 用途 |
|-------------|--------------------------------|------|
| 預設調色盤 16 色 | `standard_colors` | 醒目識別場景 |
| 淡色系調色盤 16 色 | `light_colors` | 大面積背景 |
| 淡色背景+深色前景 | `light_colors_with_foreground` | 品牌一致性配對 |
| 基礎灰階色 | `gray_colors` | 中性場景 |
| 深色前景 | `dark_foreground_color` | #1f2937 |
| 淺色前景 | `light_foreground_color` | #ffffff |
| 亮度閾值 | `luminance_threshold` | 0.6 |

## 使用場景配色

### Avatar（使用者頭像）
- **預設背景**：#6366f1（靛藍色）- 來自 `standard_colors[0]`
- **預設文字色**：#ffffff（白色）- 固定預設
- **隨機模式**：使用 `light_colors` 隨機背景 + 自動計算文字色
- **決定邏輯**：預設固定 > 自動計算

### Logo（組織標誌）
- **組織預設**：#faf5ff 背景 + #7c3aed 圖標 - 品牌固定配色
- **自訂背景**：使用 `light_colors_with_foreground` 配對或自動計算
- **決定邏輯**：預設固定 > 預定義配對 > 自動計算

### Logo（團隊標誌）
- **團隊預設**：#dbeafe 背景 + #2563eb 圖標 - 品牌固定配色
- **自訂背景**：使用 `light_colors_with_foreground` 配對或自動計算
- **決定邏輯**：預設固定 > 預定義配對 > 自動計算

### Icon（功能圖標）
- **預設背景**：透明或來自 `light_colors`
- **圖標顏色**：根據 `luminance_threshold` 自動計算
- **決定邏輯**：預定義配對 > 自動計算

## 顏色決定優先級總結

| 優先級 | 決定方式 | 適用場景 | 範例 |
|--------|----------|----------|------|
| **最高** | 預設固定顏色 | 品牌預設配色 | 個人頭像白色、組織深紫色 |
| **次高** | 預定義配對 | 淡色背景的品牌配對 | purple-50 → purple-600 |
| **最低** | 自動亮度計算 | 通用背景色 | 任意顏色的對比度計算 |

## 驗證規則

### 前端驗證
```javascript
const validateIconColor = (backgroundColor, iconColor) => {
  const allowedColors = ['#ffffff', '#1f2937']
  
  if (!allowedColors.includes(iconColor)) {
    throw new Error('Icon color must be white or dark gray')
  }
  
  // 檢查對比度
  const contrast = calculateContrast(backgroundColor, iconColor)
  if (contrast < 4.5) {
    throw new Error('Insufficient color contrast')
  }
}
```

### 後端驗證（基於配置檔案）
```php
// Laravel 驗證規則
$colorConfig = config('icon.colors');
$allowedTextColors = [
    $colorConfig['light_foreground_color'],
    $colorConfig['dark_foreground_color']
];

$rules = [
    'backgroundColor' => ['required', 'string', function ($attribute, $value, $fail) {
        if (!IconDataHelper::isAllowedBackgroundColor($value)) {
            $fail('背景顏色必須來自預設調色盤');
        }
    }],
    'textColor' => 'required|in:' . implode(',', $allowedTextColors),
    'iconColor' => 'required|in:' . implode(',', $allowedTextColors)
];
```

### 新增 API 端點建議
```php
// 提供顏色配置的 API 端點
Route::get('/api/icon/colors', function () {
    return response()->json(config('icon.colors'));
});

// 提供顏色決定邏輯的 API 端點
Route::post('/api/icon/colors/calculate', function (Request $request) {
    $backgroundColor = $request->input('backgroundColor');
    $iconType = $request->input('type', 'text');
    
    // 使用與前端相同的邏輯
    $colorConfig = config('icon.colors');
    
    // 1. 檢查預定義配對
    if (isset($colorConfig['light_colors_with_foreground'][$backgroundColor])) {
        $foregroundColor = $colorConfig['light_colors_with_foreground'][$backgroundColor];
    } else {
        // 2. 使用亮度計算
        $foregroundColor = IconDataHelper::getForegroundColor($backgroundColor);
    }
    
    return response()->json([
        'backgroundColor' => $backgroundColor,
        'foregroundColor' => $foregroundColor,
        'method' => isset($colorConfig['light_colors_with_foreground'][$backgroundColor]) 
            ? 'predefined_mapping' 
            : 'luminance_calculation'
    ]);
});
```

## 測試與驗證

### 顏色對比度測試
```javascript
// WCAG AA 對比度檢查（最小 4.5:1）
const checkContrast = (backgroundColor, foregroundColor) => {
  const bgLuminance = calculateLuminance(backgroundColor)
  const fgLuminance = calculateLuminance(foregroundColor)
  
  const lighter = Math.max(bgLuminance, fgLuminance)
  const darker = Math.min(bgLuminance, fgLuminance)
  
  const contrast = (lighter + 0.05) / (darker + 0.05)
  
  return {
    ratio: contrast,
    passAA: contrast >= 4.5,
    passAAA: contrast >= 7.0
  }
}

// 測試所有預定義配對的對比度
export const validateAllColorPairs = async () => {
  const colorConfig = await fetchColorConfig()
  const results = []
  
  for (const [bg, fg] of Object.entries(colorConfig.light_colors_with_foreground)) {
    const contrast = checkContrast(bg, fg)
    results.push({
      backgroundColor: bg,
      foregroundColor: fg,
      ...contrast
    })
  }
  
  return results
}
```

### 顏色決定一致性測試
```javascript
// 測試前後端顏色決定邏輯是否一致
export const testColorConsistency = async (testColors) => {
  const results = []
  
  for (const backgroundColor of testColors) {
    // 前端計算
    const frontendColor = await getTextColor(backgroundColor)
    
    // 後端計算
    const response = await fetch('/api/icon/colors/calculate', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ backgroundColor })
    })
    const { foregroundColor: backendColor } = await response.json()
    
    results.push({
      backgroundColor,
      frontendColor,
      backendColor,
      consistent: frontendColor === backendColor
    })
  }
  
  return results
}
```
```