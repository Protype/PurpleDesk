# IconPicker 元件規範

**文件版本**: 1.0  
**建立日期**: 2025-08-22  
**維護者**: 開發團隊

## 概述

IconPicker 是 PurpleDesk 的核心 UI 元件，提供統一的圖標選擇介面。支援 5 種圖標類型，具備搜尋、過濾、預覽等完整功能。

## IconData 物件規範

### 完整結構定義

```javascript
{
  type: 'text' | 'emoji' | 'hero_icon' | 'bootstrap_icon' | 'image',
  
  // type: 'text' 時必要欄位
  text: string,           // 1-2 個字符
  textColor: string,      // 文字顏色（#ffffff 或 #1f2937）
  
  // type: 'emoji' 時必要欄位
  emoji: string,          // Unicode Emoji 字符
  
  // type: 'hero_icon' 時必要欄位
  icon: string,           // 圖標名稱（如 'office-building'）
  style: 'outline' | 'solid',
  iconColor: string,      // 圖標顏色
  
  // type: 'bootstrap_icon' 時必要欄位
  icon: string,           // 圖標類別名稱（如 'bi-person'）
  style: 'outline' | 'fill',
  iconColor: string,      // 圖標顏色
  
  // type: 'image' 時必要欄位
  path: string,           // 圖片 URL 或 base64 資料
  
  // 所有類型通用選填欄位
  backgroundColor: string // 背景顏色（必須來自預設調色盤）
}
```

## 元件使用規範

### IconPicker 元件

#### 基本使用
```vue
<IconPicker
  v-model="iconData"
  :size="'md'"
  :hidePreview="false"
/>
```

#### Props 定義
```javascript
const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({})
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['4', '5', '6', '8', '10', '12', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl'].includes(value)
  },
  hidePreview: {
    type: Boolean,
    default: false
  }
})
```

#### Events
```javascript
const emit = defineEmits([
  'update:modelValue',  // 雙向綁定
  'iconSelected',       // 圖標選擇完成
  'panelToggle',        // 面板開關狀態
  'colorChanged'        // 顏色變更
])
```

### IconDisplay 元件

#### 基本使用
```vue
<IconDisplay
  :iconData="iconData"
  :size="'md'"
  :title="'使用者頭像'"
  :backgroundColor="'#6366f1'"
/>
```

#### Props 定義
```javascript
const props = defineProps({
  iconData: {
    type: Object,
    required: true
  },
  size: {
    type: String,
    default: 'md'
  },
  title: {
    type: String,
    default: ''
  },
  backgroundColor: {
    type: String,
    default: null  // 使用 iconData 中的 backgroundColor
  }
})
```

## 前後端資料流

### 1. 資料載入流程

```text
前端請求 → IconDataLoader → API 端點 → Service 層 → 配置檔案
    ↓
快取檢查 → 資料處理 → 格式統一 → 回傳前端
    ↓
前端快取 → 元件渲染 → 使用者互動
```

### 2. 資料儲存流程

```text
IconPicker 選擇 → IconData 物件 → 前端驗證 → API 請求
    ↓
後端驗證 → 資料庫儲存 → 回應確認 → 前端更新
```

## 模組化架構

### 重構後結構
```text
resources/js/features/icon-picker/
├── components/
│   ├── shared/
│   │   └── VirtualScrollGrid.vue    # 虛擬滾動網格
│   ├── IconPickerSearch.vue         # 搜尋元件
│   ├── TextIconPanel.vue            # 文字圖標面板
│   ├── EmojiPanel.vue               # Emoji 面板
│   ├── IconLibraryPanel.vue         # 圖標庫面板
│   ├── ImageUploadPanel.vue         # 圖片上傳面板
│   └── ColorPickerPanel.vue         # 顏色選擇器面板
├── composables/
│   ├── useIconPickerState.js        # 狀態管理
│   ├── useIconPosition.js           # 定位計算
│   ├── useIconSelection.js          # 選擇邏輯
│   └── useColorManagement.js        # 顏色管理
├── services/
│   └── IconDataLoader.js            # 統一資料載入
└── tests/                           # 完整測試覆蓋
    ├── components/
    ├── composables/
    └── services/
```

## 使用場景規範

### Avatar（使用者頭像）
- **建議尺寸**: 8, 10, md
- **預設類型**: text（使用者姓名前 2 字）
- **應用位置**: 導航列、使用者清單、評論區域

### Logo（組織/團隊標誌）
- **建議尺寸**: 10, 12, lg
- **組織預設**: hero_icon (office-building)
- **團隊預設**: bootstrap_icon (bi-people)
- **應用位置**: 組織/團隊清單、詳情頁

### Icon（功能圖標）
- **建議尺寸**: 4, 5, 6, 8
- **預設類型**: bootstrap_icon
- **應用位置**: 按鈕、選單、狀態指示器

## 驗證規則

### 前端驗證
```javascript
const validateIconData = (iconData) => {
  const allowedTypes = ['text', 'emoji', 'hero_icon', 'bootstrap_icon', 'image']
  
  if (!iconData || typeof iconData !== 'object') {
    throw new Error('Invalid icon data format')
  }
  
  if (!allowedTypes.includes(iconData.type)) {
    throw new Error('Invalid icon type')
  }
  
  // 類型特定驗證
  switch (iconData.type) {
    case 'text':
      if (!iconData.text || iconData.text.length > 2) {
        throw new Error('Text must be 1-2 characters')
      }
      break
    case 'emoji':
      if (!iconData.emoji) {
        throw new Error('Emoji value is required')
      }
      break
    // ... 其他類型驗證
  }
  
  return true
}
```

### 後端驗證
```php
// Laravel 驗證規則
$rules = [
    'type' => 'required|in:text,emoji,hero_icon,bootstrap_icon,image',
    'text' => 'required_if:type,text|string|max:2',
    'emoji' => 'required_if:type,emoji|string',
    'icon' => 'required_if:type,hero_icon,bootstrap_icon|string',
    'style' => 'required_if:type,hero_icon,bootstrap_icon|string',
    'path' => 'required_if:type,image|string',
    'backgroundColor' => ['string', function ($attribute, $value, $fail) {
        if ($value && !IconDataHelper::isAllowedBackgroundColor($value)) {
            $fail('背景顏色必須來自預設調色盤');
        }
    }],
    'textColor' => 'nullable|in:#ffffff,#1f2937',
    'iconColor' => 'nullable|in:#ffffff,#1f2937'
];
```

## 效能優化

### 虛擬滾動
- **大量項目處理**: 使用 VirtualScrollGrid 元件
- **可見項目限制**: 僅渲染可見區域的圖標
- **記憶體管理**: 自動回收不可見項目

### 搜尋優化
```javascript
// 防抖搜尋
import { debounce } from 'lodash-es'

const searchIcons = debounce((query) => {
  // 搜尋邏輯
}, 300)
```

### 資料載入策略
- **漸進式載入**: 4 級優先級（immediate, high, medium, low）
- **程式碼分割**: 按圖標類型分割載入
- **快取策略**: 前端記憶體快取 + 後端 Redis 快取

## 無障礙設計

### ARIA 支援
```vue
<template>
  <div 
    role="img"
    :aria-label="iconData.name || title"
    :aria-describedby="description"
    tabindex="0"
    @keydown.enter="selectIcon"
    @keydown.space="selectIcon"
  >
    <!-- 圖標內容 -->
  </div>
</template>
```

### 鍵盤導航
- **Tab**: 焦點移動
- **Enter/Space**: 選擇圖標
- **Escape**: 關閉面板
- **Arrow Keys**: 網格導航

## 測試要求

### 單元測試
- **元件測試**: 每個面板元件獨立測試
- **Composables 測試**: 邏輯函式測試
- **服務測試**: 資料載入和處理測試

### 整合測試
- **API 整合**: 測試與後端 API 的完整流程
- **元件整合**: 測試元件間的互動
- **狀態管理**: 測試 Pinia store 整合

### E2E 測試
```javascript
// 範例 E2E 測試
describe('IconPicker E2E', () => {
  it('應該能完成完整的圖標選擇流程', async () => {
    // 1. 開啟 IconPicker
    await page.click('[data-testid="icon-picker-trigger"]')
    
    // 2. 切換到 emoji 頁籤
    await page.click('[data-testid="emoji-tab"]')
    
    // 3. 搜尋特定 emoji
    await page.fill('[data-testid="search-input"]', 'smile')
    
    // 4. 選擇 emoji
    await page.click('[data-testid="emoji-1f600"]')
    
    // 5. 驗證結果
    expect(await page.textContent('[data-testid="selected-icon"]')).toBe('😀')
  })
})
```