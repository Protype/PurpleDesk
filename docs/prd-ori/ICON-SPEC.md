# PurpleDesk 圖標系統完整規範

建立日期：2025-08-16
最後更新：2025-08-16
文件版本：1.0

## 1. 圖標系統概述

PurpleDesk 圖標系統提供統一的視覺識別解決方案，支援五種圖標類型，適用於 Avatar（頭像）、Logo（標誌）、Icon（功能圖標）三大應用場景。

## 2. 圖標類型定義

### 2.1 文字圖標 (text)
- 支援 1-2 個字符（中文、英文、數字）
- 自動識別 CJK 字符與拉丁字符
- 可自訂背景色與文字顏色
- 適用場景：個人頭像、團隊簡稱

### 2.2 Emoji 圖標 (emoji)
- 支援 Unicode 標準 Emoji
- 內建相容性過濾系統（移除 57 個不相容 emoji）
- 支援膚色選擇器
- 適用場景：狀態標示、分類標籤

### 2.3 Hero Icons (hero_icon)
- 整合 Heroicons v1 圖標庫
- 支援 outline/solid 樣式切換
- 可自訂圖標顏色
- 適用場景：組織標誌、功能圖標

### 2.4 Bootstrap Icons (bootstrap_icon)
- 2,252 個圖標完整整合
- 8 個分類系統（通用、UI、通訊、檔案、媒體、人物、其他、數字字母）
- 支援 outline/fill 樣式
- 適用場景：團隊標誌、UI 元件圖標

### 2.5 上傳圖片 (image)
- 支援 JPG、PNG、GIF 格式
- 自動圓形裁切顯示
- 檔案大小限制 2MB
- 適用場景：自訂頭像、品牌標誌

## 3. 預設圖標標準

### 3.1 組織預設圖標
```javascript
{
  type: 'hero_icon',
  icon: 'office-building',
  style: 'outline',
  backgroundColor: '#faf5ff',  // purple-50 淡紫色
  iconColor: '#7c3aed'         // purple-600 深紫色
}
```

### 3.2 團隊預設圖標
```javascript
{
  type: 'bootstrap_icon',
  icon: 'bi-people',
  style: 'outline',
  backgroundColor: '#dbeafe',  // blue-100 淡藍色
  iconColor: '#2563eb'         // blue-600 深藍色
}
```

### 3.3 個人頭像預設
```javascript
{
  type: 'text',
  text: '姓名前2字',           // 自動擷取
  backgroundColor: '#6366f1',  // indigo-500 靛藍色
  textColor: '#ffffff'         // 白色
}
```

## 4. 資料結構規範

### 4.1 IconData 物件完整結構
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

## 5. 顏色系統

### 5.1 預設調色盤（16 色）
| 色碼 | 名稱 |
|------|------|
| #ef4444 | 紅色 Red |
| #f97316 | 橙色 Orange |
| #f59e0b | 黃色 Amber |
| #eab308 | 黃綠色 Yellow |
| #84cc16 | 萊姆色 Lime |
| #22c55e | 綠色 Green |
| #10b981 | 翠綠色 Emerald |
| #14b8a6 | 青綠色 Teal |
| #06b6d4 | 青色 Cyan |
| #0ea5e9 | 天空藍 Sky Blue |
| #3b82f6 | 藍色 Blue |
| #6366f1 | 靛藍色 Indigo |
| #9b6eff | 紫色 Primary（**系統主色**）|
| #8b5cf6 | 紫羅蘭 Violet |
| #a855f7 | 紫色 Purple |
| #d946ef | 紫紅色 Fuchsia |
| #ec4899 | 桃紅色 Pink |

### 5.2 淡色系調色盤與對應深色前景（16 色）

**注意**：ColorPicker 實際使用的是彩度更高的淡色版本（Tailwind 200 級別），以提升識別度。
淡色系背景統一搭配深色前景，確保可讀性。

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

### 5.3 基礎色彩（黑白灰）
| 色碼 | 名稱 |
|------|------|
| #000000 | 黑色 |
| #ffffff | 白色 |
| #f9fafb | gray-50 |
| #f3f4f6 | gray-100 |
| #e5e7eb | gray-200 |
| #d1d5db | gray-300 |
| #9ca3af | gray-400 |
| #6b7280 | gray-500 |
| #374151 | gray-700 |
| #1f2937 | gray-800 |
| #111827 | gray-900 |

### 5.4 顏色選擇策略

#### 背景顏色使用原則
1. **預設調色盤（16 色）**：適用於需要醒目識別的場景
2. **淡色系調色盤（16 色）**：適用於大面積背景或輕量提示
3. **自訂顏色**：支援 HTML 色彩選擇器，但建議使用預設調色盤

#### 內容顏色自動計算
系統會根據背景色自動計算最佳文字/圖標顏色：

```javascript
// 相對亮度計算（W3C 公式）
const getLuminance = (hexColor) => {
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

#### 內容顏色限制
- **文字顏色**：僅限白色 (#ffffff) 或深灰色 (#1f2937)
- **圖標顏色**：僅限白色 (#ffffff) 或深灰色 (#1f2937)
- **對比度要求**：確保背景與內容有足夠對比度（WCAG AA 標準）

### 5.5 隨機顏色功能

- **隨機淡色**：從淡色系調色盤中隨機選擇
- **用途**：快速生成獨特識別的背景色
- **實作**：優先選擇未被使用的顏色

## 6. 尺寸規範

### 6.1 支援的尺寸
- **數字尺寸**：4, 5, 6, 8, 10, 12（單位：Tailwind rem）
- **文字尺寸**：xs, sm, md, lg, xl, 2xl, 3xl

### 6.2 尺寸對應表
| 尺寸 | 容器大小 | 文字 (CJK) | 文字 (Latin) | Emoji | Icon | Hero Icon |
|------|----------|------------|--------------|--------|------|-----------|
| 4    | 16px     | 0.3rem     | 0.4rem       | 0.4rem | 0.5rem | 0.5rem |
| 5    | 20px     | 0.4rem     | 0.5rem       | 0.5rem | 0.6rem | 0.6rem |
| 6    | 24px     | 0.5rem     | 0.6rem       | 0.8rem | 0.8rem | 0.8rem |
| 8    | 32px     | 0.7rem     | 0.9rem       | 1.1rem | 1.0rem | 1.1rem |
| 10   | 40px     | 0.9rem     | 1.1rem       | 1.3rem | 1.3rem | 1.4rem |
| 12   | 48px     | 1.1rem     | 1.4rem       | 1.7rem | 1.8rem | 1.8rem |
| xs   | 24px     | 0.5rem     | 0.6rem       | 0.8rem | 0.8rem | 0.8rem |
| sm   | 32px     | 0.7rem     | 0.9rem       | 1.1rem | 1.0rem | 1.1rem |
| md   | 40px     | 0.9rem     | 1.1rem       | 1.3rem | 1.3rem | 1.4rem |
| lg   | 48px     | 1.1rem     | 1.4rem       | 1.7rem | 1.8rem | 1.8rem |
| xl   | 64px     | 1.5rem     | 2.0rem       | 2.4rem | 2.2rem | 2.3rem |
| 2xl  | 80px     | 1.9rem     | 2.4rem       | 2.9rem | 2.6rem | 2.8rem |
| 3xl  | 96px     | 2.2rem     | 3.0rem       | 3.6rem | 3.4rem | 3.6rem |

## 7. 使用場景規範

### 7.1 Avatar（使用者頭像）
- **預設類型**：文字（使用者姓名前 2 字）
- **預設背景**：#6366f1（靛藍色）
- **預設文字色**：#ffffff（白色）
- **建議尺寸**：8, 10, md
- **應用位置**：
  - 導航列右上角
  - 使用者清單
  - 評論區域
  - 聊天介面

### 7.2 Logo（組織/團隊標誌）
#### 組織 Logo
- **預設類型**：hero_icon（office-building）
- **預設背景**：#faf5ff（淡紫色）
- **預設圖標色**：#7c3aed（深紫色）
- **建議尺寸**：10, 12, lg
- **應用位置**：組織清單、組織詳情頁

#### 團隊 Logo
- **預設類型**：bootstrap_icon（bi-people）
- **預設背景**：#dbeafe（淡藍色）
- **預設圖標色**：#2563eb（深藍色）
- **建議尺寸**：10, 12, lg
- **應用位置**：團隊清單、團隊頁面

### 7.3 Icon（功能圖標）
- **預設類型**：bootstrap_icon
- **預設背景**：透明或淡色系
- **建議尺寸**：4, 5, 6, 8
- **應用位置**：
  - 按鈕圖標
  - 選單項目
  - 狀態指示器
  - 操作提示

## 8. 前端實作

### 8.1 IconPicker 組件使用
```vue
<IconPicker
  v-model="iconData"
  :hidePreview="false"
  :size="'md'"
/>
```

### 8.2 IconDisplay 組件使用
```vue
<IconDisplay
  :iconData="iconData"
  :size="'md'"
  :title="'使用者頭像'"
  :backgroundColor="'#6366f1'"
/>
```

### 8.3 組件屬性說明
#### IconPicker Props
- `v-model`: IconData 物件（雙向綁定）
- `hidePreview`: 是否隱藏預覽按鈕
- `size`: 圖標大小

#### IconDisplay Props
- `iconData`: IconData 物件
- `size`: 顯示尺寸
- `title`: hover 提示文字
- `backgroundColor`: 強制背景色（可選）

## 9. 後端實作

### 9.1 資料儲存
- 使用 JSON 格式儲存於資料庫
- 透過 Eloquent JSON casting 自動轉換
- 欄位命名規範：
  - 使用者：`avatar_data`
  - 組織：`logo_data`
  - 團隊：`icon_data`

### 9.2 資料驗證
```php
// 使用 IconDataHelper 進行驗證
IconDataHelper::validateAvatarData($avatarData);

// 自動生成預設值
IconDataHelper::generateUserIconDefault($fullName);
IconDataHelper::generateOrganizationIconDefault();
IconDataHelper::generateTeamIconDefault();
```

### 9.3 Helper 方法
- `isAllowedBackgroundColor()`: 檢查背景色是否在允許清單
- `parseIconData()`: 解析圖標資料
- `isJsonString()`: 檢查是否為有效 JSON

## 10. 重置行為規範

### 10.1 重置規則
當用戶選擇「重置」時，系統應恢復到對應類型的預設設定：

| 類型 | 重置後狀態 |
|------|------------|
| 組織 | Hero Icon office-building + 淡紫背景 + 深紫圖標 |
| 團隊 | Bootstrap Icon bi-people + 淡藍背景 + 深藍圖標 |
| 個人 | 姓名首字母文字 + primary-500 背景 + 白色文字 |

### 10.2 重置實作
```javascript
// 前端重置
function resetIcon(type) {
  switch(type) {
    case 'organization':
      return generateOrganizationDefault();
    case 'team':
      return generateTeamDefault();
    case 'user':
      return generateUserDefault(userName);
  }
}
```

## 11. 效能優化

### 11.1 圖標載入策略
- **漸進式載入**：4 級優先級（immediate, high, medium, low）
- **程式碼分割**：17 個獨立 chunks
- **快取機制**：記憶體約 1MB
- **虛擬滾動**：大量圖標列表使用 VirtualScroll

### 11.2 圖片處理
- 自動壓縮上傳圖片
- 小圖片（< 10KB）使用 base64 編碼
- 大圖片使用本地儲存或 CDN

### 11.3 載入優化
- 初始載入時間減少 70-80%
- 建置時間優化至 8.85 秒
- Gzip 壓縮：CSS 82%、JS 66%

## 12. 無障礙設計

### 12.1 基本要求
- 所有圖標包含 `title` 屬性
- 支援鍵盤導航（Tab, Enter, Escape）
- ARIA 標籤支援
- 高對比度模式相容

### 12.2 ARIA 實作
```html
<div 
  role="img"
  :aria-label="title"
  :aria-describedby="description"
  tabindex="0"
>
  <!-- 圖標內容 -->
</div>
```

## 13. 測試規範

### 13.1 測試資料要求
- 所有假資料必須符合顏色規範
- 非預設頭像的背景色必須來自允許的顏色清單
- 文字/圖標顏色必須是白色或深色
- 包含各種類型的範例組合

### 13.2 測試檢查清單
- [ ] 組織預設圖像正確顯示
- [ ] 團隊預設圖像正確顯示
- [ ] 個人頭像預設正確生成
- [ ] 顏色驗證機制運作正常
- [ ] 重置功能正確恢復預設值
- [ ] ColorPicker 顏色限制生效
- [ ] Emoji 相容性過濾正常
- [ ] 圖片上傳大小限制
- [ ] 響應式尺寸調整
- [ ] 無障礙功能完整

## 14. 版本歷史

| 版本 | 日期 | 變更內容 |
|------|------|----------|
| 1.0 | 2025-08-16 | 初始版本，整合 ICON-AVATAR-STANDARDS 與 PRD 圖標規範 |

## 15. 附錄

### 15.1 相關文件
- `/docs/ICON-AVATAR-STANDARDS.md` - 原始圖標頭像標準
- `/docs/PRD.md` - 產品需求文件（第 6 章）
- `/resources/js/config/iconDisplayConfig.js` - 前端配置檔
- `/app/Helpers/IconDataHelper.php` - 後端輔助類別

### 15.2 技術指標
- **圖標總數**：6,033 個（BS Icons: 2,252 + Emojis: 3,781）
- **Emoji 相容性**：100% 準確度，過濾 57 個不相容 emoji
- **記憶體使用**：圖標系統約 1MB
- **響應式設計**：113 個響應式 CSS 類別跨 15 個組件

---

本規範為 PurpleDesk 圖標系統的權威文件，所有開發與設計決策應以此為準。