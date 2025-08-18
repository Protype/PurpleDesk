# IconPicker 重構 - User Stories

**專案**：IconPicker 元件重構  
**Epic 參考**：[ICON-PICKER-EPICS.md](./ICON-PICKER-EPICS.md)  
**建立日期**：2025-08-17  
**狀態**：Phase 0-1 已完成，進行中 (Phase 2)  
**下一步**：執行 ST-022-A (清理舊測試頁面) → ST-022 (建立新測試頁面)  

## 📋 Stories 總覽

| Story ID | Epic | Story 名稱 | 優先級 | 預估工時 | 狀態 |
|----------|------|------------|--------|----------|------|
| **EP-001: 建立安全網和基礎架構** |
| ST-001 | EP-001 | 建立 IconPickerOri 備份 | P0 | 2小時 | ✅ 已完成 |
| ST-002 | EP-001 | 建立 features 目錄結構 | P0 | 1小時 | ✅ 已完成 |
| ST-003 | EP-001 | 配置測試框架 | P0 | 4小時 | ✅ 已完成 |
| ST-004 | EP-001 | 建立版本切換機制 | P1 | 3小時 | ✅ 已完成 |
| ST-022-A | EP-001 | 清理舊測試頁面 | P0 | 1小時 | ✅ 已完成 |
| ST-022 | EP-001 | Icon Picker 測試頁面 | P0 | 3小時 | ✅ 已完成 |
| **EP-002: 服務層重構** |
| ST-005 | EP-002 | 實作 IconDataLoader 基礎架構 | P0 | 4小時 | ✅ 已完成 |
| ST-006 | EP-002 | 實作 getEmojiData API 載入 | P0 | 6小時 | ✅ 已完成 |
| ST-007 | EP-002 | 實作 getIconLibraryData 合併載入 | P0 | 6小時 | ✅ 已完成 |
| ST-008 | EP-002 | 建立 IconPickerSearch 元件 | P0 | 4小時 | ✅ 已完成 |
| **EP-003: 共用元件開發** |
| ST-009 | EP-003 | 實作 VirtualScrollGrid 核心邏輯 | P1 | 8小時 | ✅ 已完成 |
| ST-010 | EP-003 | 實作 VirtualScrollGrid slots 機制 | P1 | 4小時 | ✅ 已完成 |
| ST-011 | EP-003 | VirtualScrollGrid 效能優化 | P1 | 4小時 | ✅ 已完成 |
| **EP-004: 面板元件拆分** |
| ST-012 | EP-004 | 實作 TextIconPanel | P1 | 6小時 | ✅ 已完成 |
| ST-012-I | EP-004 | TextIconPanel 測試頁面整合 | P0 | 1小時 | 待開始 |
| ST-013 | EP-004 | 實作 EmojiPanel（含完整解決方案） | P1 | 8小時 | 進行中 |
| ST-013-I | EP-004 | EmojiPanel 測試頁面整合 | P0 | 1小時 | 待開始 |
| ST-014 | EP-004 | 實作 IconLibraryPanel | P1 | 10小時 | 待開始 |
| ST-014-I | EP-004 | IconLibraryPanel 測試頁面整合 | P0 | 1小時 | 待開始 |
| ST-015 | EP-004 | 實作 ImageUploadPanel | P1 | 6小時 | 待開始 |
| ST-015-I | EP-004 | ImageUploadPanel 測試頁面整合 | P0 | 1小時 | 待開始 |
| ST-016 | EP-004 | 實作 ColorPickerPanel | P1 | 6小時 | 待開始 |
| ST-016-I | EP-004 | ColorPickerPanel 測試頁面整合 | P0 | 1小時 | 待開始 |
| **EP-005: 邏輯抽離和整合** |
| ST-017 | EP-005 | 建立 composables | P2 | 8小時 | 待開始 |
| ST-018 | EP-005 | 重構主 IconPicker 整合 | P2 | 8小時 | 待開始 |
| **EP-006: 測試和優化** |
| ST-019 | EP-006 | 建立完整測試套件 | P1 | 10小時 | 待開始 |
| ST-020 | EP-006 | 效能測試和優化 | P1 | 6小時 | 待開始 |
| ST-021 | EP-006 | A/B 測試和文件 | P1 | 4小時 | 待開始 |
| **修正和測試補強** |
| ST-FIX-001 | 修正 | 修正 TextIconPanel 文字上限為 2 字元 | P0 | 2小時 | 待開始 |
| ST-TEST-001 | 測試 | TextIconPanel 專項 E2E 測試 | P0 | 3小時 | 待開始 |
| ST-TEST-002 | 測試 | Playwright 測試環境配置文件 | P1 | 1小時 | ✅ 已完成 |

---

## 📝 Story 詳細定義

### EP-001: 建立安全網和基礎架構

---

#### ST-001: 建立 IconPickerOri 備份

**作為**：開發者  
**我想要**：建立 IconPicker 的完整備份版本  
**所以**：在重構過程中隨時可以安全回滾  

**驗收條件**：
- [ ] 複製 `IconPicker.vue` 為 `IconPickerOri.vue`
- [ ] IconPickerOri 在當前環境正常運作
- [ ] 所有相依性（組件、樣式）都正確複製
- [ ] 測試 IconPickerOri 與原版功能完全一致

**技術要求**：
- 保持檔案完整性
- 確保所有 import 路徑正確
- 驗證所有功能運作正常

**測試要求**：
- 手動測試所有 5 種圖標選擇功能
- 驗證搜尋、顏色選擇、上傳功能

**DoD (Definition of Done)**：
- 程式碼審查通過
- 功能測試通過
- 無 console 錯誤

---

#### ST-002: 建立 features 目錄結構

**作為**：開發者  
**我想要**：建立標準化的 features 目錄結構  
**所以**：所有重構程式碼都有組織化的存放位置  

**驗收條件**：
- [ ] 建立 `resources/js/features/icon-picker/` 根目錄
- [ ] 建立 `components/` 子目錄
- [ ] 建立 `shared/` 子目錄
- [ ] 建立 `composables/` 子目錄
- [ ] 建立 `services/` 子目錄
- [ ] 建立 `tests/` 子目錄及其子結構

**技術要求**：
```
resources/js/features/icon-picker/
├── components/
│   └── shared/
├── composables/
├── services/
└── tests/
    ├── components/
    ├── composables/
    └── services/
```

**測試要求**：
- 驗證目錄結構符合規劃
- 確認路徑可被 Vite 正確解析

---

#### ST-003: 配置測試框架

**作為**：開發者  
**我想要**：配置完整的測試環境  
**所以**：可以進行 TDD 開發  

**驗收條件**：
- [ ] Vitest 配置檔案正確設定
- [ ] 測試環境可以運行 Vue 元件測試
- [ ] 設定測試覆蓋率報告
- [ ] 建立第一個範例測試並通過

**技術要求**：
- 支援 Vue 3 組合式 API
- 支援 ES modules
- 支援 mock 功能
- 支援覆蓋率報告

**測試要求**：
- 測試指令可正常執行
- 覆蓋率報告可正常生成
- 範例測試通過

---

#### ST-004: 建立版本切換機制

**作為**：開發者  
**我想要**：在開發環境可以切換新舊版本  
**所以**：可以隨時比對功能差異  

**驗收條件**：
- [ ] 建立環境變數控制版本選擇
- [ ] 可在開發環境快速切換
- [ ] 切換無需重新編譯
- [ ] 兩個版本都能正常運作

**技術要求**：
- 使用環境變數或開發者工具
- 不影響生產環境
- 切換邏輯簡單可靠

---

#### ST-022-A: 清理舊測試頁面

**作為**：開發者  
**我想要**：清理過時的圖標系統測試頁面  
**所以**：避免與新的 Icon Picker 測試頁面產生混淆和路由衝突  

**驗收條件**：
- [ ] 移除 `/resources/views/test-icons.blade.php` 檔案
- [ ] 移除 `routes/web.php` 中的 `/test-icons` 路由
- [ ] 搜尋並確認無其他檔案引用此測試頁面
- [ ] 確認移除後不影響其他功能的正常運作

**技術要求**：
- 檢查檔案相依性
- 確保沒有硬編碼的 URL 引用
- 不影響其他路由和功能

**測試要求**：
- 驗證 `/test-icons` 路由移除後返回 404
- 確認主應用功能不受影響
- 檢查無殘留的 console 錯誤

**DoD (Definition of Done)**：
- 相關檔案完全移除
- 路由表乾淨
- 功能測試通過
- 無殘留引用或錯誤

---

#### ST-022: Icon Picker 測試頁面

**作為**：開發者和產品經理  
**我想要**：一個獨立的測試頁面來驗證 Icon Picker 功能  
**所以**：可以根據 phase/story 進度實時測試和驗證開發成果  

**驗收條件**：
- [ ] 建立測試路由 `/test/icon-picker`
- [ ] 頁面包含預覽區域顯示選中的圖標
- [ ] 頁面包含按鈕觸發 Icon Picker 彈窗
- [ ] 支援版本切換（整合 IconPickerProxy）
- [ ] 顯示當前 Phase/Story 進度資訊
- [ ] 整合開發者工具控制項

**技術要求**：
```php
// 路由
Route::get('/test/icon-picker', function () {
    return view('test.icon-picker');
});
```

```vue
// 測試頁面主要元件
<template>
  <div class="test-container">
    <!-- Phase 進度面板 -->
    <PhaseProgressPanel />
    
    <!-- 圖標預覽區域 -->
    <IconPreviewArea :selected-icon="selectedIcon" />
    
    <!-- 觸發按鈕 -->
    <TriggerButton @click="openIconPicker" />
    
    <!-- Icon Picker -->
    <IconPickerProxy v-model="selectedIcon" />
    
    <!-- 開發者工具 -->
    <IconPickerDevTool />
  </div>
</template>
```

**整合要求**：
- 使用現有的 IconPickerProxy 元件
- 整合現有的 IconPickerDevTool 元件
- 遵循 Laravel Blade + Vue 3 架構模式
- 不影響主應用路由

**測試要求**：
- 測試 Icon Picker 所有功能正常
- 測試版本切換機制
- 測試預覽顯示正確
- 驗證 Phase 進度資訊準確

**DoD (Definition of Done)**：
- 測試路由可正常訪問
- Icon Picker 功能完整可用
- 版本切換正常運作
- 無 console 錯誤
- Phase 進度顯示準確

---

#### ST-012-I: TextIconPanel 測試頁面整合

**作為**：開發者和產品經理  
**我想要**：TextIconPanel 立即整合到測試頁面的新版 IconPicker  
**所以**：可以即時驗證開發成果並確保功能正確性  

**驗收條件**：
- [ ] 修改新版 IconPicker.vue 引入 TextIconPanel 元件
- [ ] 替換 `initials` 標籤頁的內嵌邏輯使用 TextIconPanel
- [ ] 移除舊版內嵌邏輯，使用純新架構
- [ ] 未完成功能顯示「開發中」狀態或適當錯誤提示
- [ ] 測試頁面切換到新版後文字圖標功能正常
- [ ] TextIconPanel 功能與原版一致（其他功能可不完整）
- [ ] 新版架構獨立運作，不依賴舊版邏輯

**技術要求**：
```vue
<!-- 在 IconPicker.vue 中替換內嵌邏輯 -->
<TextIconPanel
  v-if="activeTab === 'initials'"
  v-model="customInitials"
  :background-color="backgroundColor"
  @text-selected="handleTextSelection"
/>
```

**整合流程**：
1. 在 `feat/text-icon-panel` 分支進行整合
2. 修改 resources/js/features/icon-picker/IconPicker.vue 的 initials 標籤頁
3. 調整事件處理邏輯
4. 本地測試驗證
5. 更新 PR #28 包含整合內容

**測試要求**：
- 測試頁面 `/test/icon-picker?iconpicker=new` 功能驗證
- **已實作功能（TextIconPanel）**：
  - 文字輸入限制（3字元）正常
  - 大寫轉換正常
  - 預覽顯示正確
  - 顏色計算準確
  - 應用按鈕邏輯正確
- **未實作功能**：
  - Emoji 標籤頁顯示開發中狀態
  - Icons 標籤頁顯示開發中狀態
  - Upload 標籤頁顯示開發中狀態
  - 確認不會意外觸發舊版邏輯

**DoD (Definition of Done)**：
- 新版 IconPicker 使用 TextIconPanel 元件
- 已實作功能在測試頁面正常運作
- TextIconPanel 功能與原版一致
- 未實作功能顯示適當的開發中狀態
- 新版架構獨立運作，無舊版依賴
- 程式碼結構清晰，遵循新架構原則
- PR 包含完整整合內容

---

### EP-002: 服務層重構

---

#### ST-005: 實作 IconDataLoader 基礎架構

**作為**：開發者  
**我想要**：建立統一的圖標資料載入服務  
**所以**：可以抽象化不同的資料來源  

**驗收條件**：
- [ ] 建立 `IconDataLoader` 類別
- [ ] 定義統一的資料格式介面
- [ ] 實作基礎的錯誤處理機制
- [ ] 實作快取機制
- [ ] 建立完整的單元測試

**技術要求**：
```javascript
class IconDataLoader {
  constructor()
  async getEmojiData()
  async getIconLibraryData()
  clearCache()
  _handleError()
}
```

**測試要求**：
- 測試類別初始化
- 測試錯誤處理
- 測試快取機制
- 測試覆蓋率 > 90%

---

#### ST-006: 實作 getEmojiData API 載入

**作為**：開發者  
**我想要**：從後端 API 載入 emoji 資料  
**所以**：可以統一資料來源管理  

**驗收條件**：
- [ ] 整合現有的 `/api/config/icon/emoji` API
- [ ] 實作資料格式轉換
- [ ] 實作錯誤處理和重試機制
- [ ] 保持與原版資料格式完全一致
- [ ] 建立 API 整合測試

**技術要求**：
- 使用現有的 IconService
- 處理網路錯誤
- 資料快取 24 小時
- 支援離線模式回退

**測試要求**：
- Mock API 回應測試
- 錯誤情況測試
- 資料格式驗證測試
- 與原版資料對比測試

---

#### ST-007: 實作 getIconLibraryData 合併載入

**作為**：開發者  
**我想要**：合併 HeroIcon 和 Bootstrap Icon 資料  
**所以**：IconLibraryPanel 可以統一顯示  

**驗收條件**：
- [ ] 載入 HeroIcons 資料（前端檔案）
- [ ] 載入 Bootstrap Icons 資料（前端檔案）
- [ ] 合併兩種資料為統一格式
- [ ] 保持分類和排序邏輯
- [ ] 實作樣式過濾邏輯

**技術要求**：
- 保持現有的載入邏輯
- 統一資料結構格式
- 支援樣式切換（outline/solid/fill）
- 保持分類順序

**測試要求**：
- 測試資料載入
- 測試合併邏輯
- 測試樣式過濾
- 驗證資料完整性

---

#### ST-008: 建立 IconPickerSearch 元件

**作為**：使用者  
**我想要**：搜尋功能從原 IconPicker 獨立出來  
**所以**：搜尋邏輯更清晰且可重用  

**驗收條件**：
- [ ] 建立獨立的 IconPickerSearch 元件
- [ ] 實作搜尋輸入和清除功能
- [ ] 只搜尋當前頁籤的內容
- [ ] 保持原有的搜尋行為
- [ ] 建立元件測試

**技術要求**：
```vue
<IconPickerSearch 
  v-model="searchQuery"
  :placeholder="searchPlaceholder"
  @clear="handleClear"
/>
```

**測試要求**：
- 測試搜尋輸入
- 測試清除功能
- 測試 v-model 雙向綁定
- 測試搜尋結果過濾

---

### EP-003: 共用元件開發

---

#### ST-009: 實作 VirtualScrollGrid 核心邏輯

**作為**：開發者  
**我想要**：建立高效能的虛擬滾動網格元件  
**所以**：可以流暢顯示大量圖標項目  

**驗收條件**：
- [ ] 實作虛擬滾動核心演算法
- [ ] 支援動態行數計算
- [ ] 實作滾動位置計算
- [ ] 支援緩衝區機制
- [ ] 效能達到原版水準

**技術要求**：
```vue
<VirtualScrollGrid
  :items="items"
  :items-per-row="10"
  :row-height="36"
  :container-height="176"
  :buffer="2"
>
  <template #item="{ item, index }">
    <!-- 自訂項目渲染 -->
  </template>
</VirtualScrollGrid>
```

**測試要求**：
- 測試滾動計算邏輯
- 測試效能基準
- 測試大量資料處理
- 記憶體洩漏測試

---

#### ST-010: 實作 VirtualScrollGrid slots 機制

**作為**：開發者  
**我想要**：VirtualScrollGrid 支援自訂項目渲染  
**所以**：可以用於不同類型的圖標顯示  

**驗收條件**：
- [ ] 實作 #item slot
- [ ] 支援分類標題渲染
- [ ] 支援空白佔位符
- [ ] 傳遞正確的 slot props
- [ ] 保持原有的渲染邏輯

**技術要求**：
- 支援多種項目類型
- 正確的事件委派
- 保持網格對齊
- 支援響應式調整

**測試要求**：
- 測試 slot 渲染
- 測試事件傳遞
- 測試不同項目類型
- 視覺回歸測試

---

#### ST-011: VirtualScrollGrid 效能優化

**作為**：使用者  
**我想要**：虛擬滾動非常流暢  
**所以**：瀏覽大量圖標時體驗良好  

**驗收條件**：
- [ ] 滾動幀率穩定在 60fps
- [ ] 記憶體使用優化
- [ ] 支援滾動位置保持
- [ ] 減少不必要的重新渲染
- [ ] 建立效能基準測試

**技術要求**：
- 使用 requestAnimationFrame
- 實作防抖和節流
- 優化 DOM 操作
- 記憶體回收機制

**測試要求**：
- 效能基準測試
- 記憶體使用測試
- 長時間滾動測試
- 不同裝置相容性測試

---

### EP-004: 面板元件拆分

---

#### ST-012: 實作 TextIconPanel

**作為**：使用者  
**我想要**：文字圖標選擇功能獨立運作  
**所以**：可以輸入文字並預覽圖標效果  

**驗收條件**：
- [ ] 從原 IconPicker 抽取文字圖標邏輯
- [ ] 保持文字輸入限制（3字元）
- [ ] 保持預覽功能
- [ ] 保持顏色計算邏輯
- [ ] 保持應用按鈕行為

**技術要求**：
```vue
<TextIconPanel
  v-model="textIcon"
  :background-color="backgroundColor"
  @text-selected="handleTextSelection"
/>
```

**測試要求**：
- 測試文字輸入限制
- 測試預覽顯示
- 測試顏色計算
- 測試事件發送

---

#### ST-013: 實作 EmojiPanel（含完整解決方案）

**作為**：使用者  
**我想要**：emoji 選擇功能使用標準化架構且無權宜處理  
**所以**：載入速度更快、程式碼更清晰且符合最佳實務  

**背景說明**：
此 Story 整合了 emoji 重構的所有必要改進，避免技術債累積，確保交付完整可用的功能。

**驗收條件**：
- [ ] 使用 VirtualScrollGrid 顯示 emoji
- [ ] 使用 IconDataLoader 載入資料
- [ ] **後端過濾實作**（解決權宜處理）
  - [ ] 建立 EmojiFilterService 進行相容性和膚色變體過濾
  - [ ] API 只回傳基礎 emoji，不含膚色變體
  - [ ] 根據客戶端平台進行相容性過濾
- [ ] **前端膚色處理**（標準化實作）
  - [ ] 使用 emojiSkinTone.js 的 Unicode 標準判斷膚色支援
  - [ ] 移除所有硬編碼的膚色支援列表
  - [ ] 動態生成膚色選項和變體
- [ ] **功能完整性**
  - [ ] 膚色切換時保持滾動位置
  - [ ] 分類顯示樣式一致性
  - [ ] 保持搜尋過濾功能

**技術要求**：
**後端改進**：
```php
// 新增 EmojiFilterService
class EmojiFilterService {
    public function filterForPlatform($emojis, $userAgent)
    public function removeSkintoneVariants($emojis)
    public function ensureBaseEmojisOnly($emojis)
}
```

**前端改進**：
```javascript
// 移除硬編碼，使用 Unicode 標準
import { supportsSkinTone, applySkinTone, removeSkinTone } from '@/utils/emojiSkinTone.js'

// 動態判斷膚色支援
const canApplySkinTone = (emoji) => supportsSkinTone(emoji)
```

**責任劃分**：
- **後端職責**：相容性過濾、提供基礎 emoji
- **前端職責**：膚色變體生成、UI 互動邏輯

**測試要求**：
**後端測試**：
- 測試 EmojiFilterService 單元測試
- 測試 API 回應格式和內容正確性
- 測試平台相容性過濾邏輯

**前端測試**：
- 測試 Unicode 膚色支援判斷
- 測試膚色變體動態生成
- 測試滾動位置保持
- 測試虛擬滾動效能
- 更新 mock 資料符合實際 API 格式

**整合測試**：
- 測試完整的 emoji 選擇流程
- 測試與其他 IconPicker 元件整合
- 效能基準測試（與原版對比）

**DoD 補充**：
- 無任何硬編碼的 emoji 列表或分類對應
- 所有 emoji 判斷基於 Unicode 標準
- API 回應結構清晰且符合前端期望
- 測試覆蓋率 > 90%
- 滾動和互動體驗與原版一致

---

#### ST-014: 實作 IconLibraryPanel

**作為**：使用者  
**我想要**：HeroIcon 和 Bootstrap Icon 整合顯示  
**所以**：可以在同一個頁籤選擇不同類型的圖標  

**驗收條件**：
- [ ] 使用 VirtualScrollGrid 顯示圖標
- [ ] 使用 IconDataLoader 載入合併資料
- [ ] 保持樣式切換功能（outline/solid/fill）
- [ ] 保持分類顯示邏輯
- [ ] 保持搜尋過濾功能

**技術要求**：
- 整合兩種圖標類型
- 支援不同的渲染邏輯
- 保持樣式切換
- 保持分類順序

**測試要求**：
- 測試資料載入和合併
- 測試兩種圖標類型渲染
- 測試樣式切換
- 測試搜尋功能
- 效能測試

---

#### ST-015: 實作 ImageUploadPanel

**作為**：使用者  
**我想要**：圖片上傳功能獨立運作  
**所以**：可以上傳自訂圖片作為圖標  

**驗收條件**：
- [ ] 從原 IconPicker 抽取上傳邏輯
- [ ] 保持拖放上傳功能
- [ ] 保持檔案驗證邏輯
- [ ] 保持預覽功能
- [ ] 保持錯誤處理

**技術要求**：
```vue
<ImageUploadPanel
  @file-selected="handleFileSelection"
  @upload-error="handleUploadError"
/>
```

**測試要求**：
- 測試檔案拖放
- 測試檔案驗證
- 測試預覽功能
- 測試錯誤處理

---

#### ST-016: 實作 ColorPickerPanel

**作為**：使用者  
**我想要**：顏色選擇功能獨立運作  
**所以**：可以自訂圖標背景顏色  

**驗收條件**：
- [ ] 從原 IconPicker 抽取顏色選擇邏輯
- [ ] 保持預設調色盤
- [ ] 保持淡色系調色盤
- [ ] 保持自訂顏色輸入
- [ ] 保持隨機顏色功能

**技術要求**：
```vue
<ColorPickerPanel
  v-model="backgroundColor"
  @color-changed="handleColorChange"
/>
```

**測試要求**：
- 測試顏色選擇
- 測試自訂顏色輸入
- 測試隨機顏色
- 測試清除功能

---

### EP-005: 邏輯抽離和整合

---

#### ST-017: 建立 composables

**作為**：開發者  
**我想要**：將可重用邏輯抽離成 composables  
**所以**：邏輯可以重用且更容易測試  

**驗收條件**：
- [ ] 建立 useIconPickerState.js
- [ ] 建立 useIconPosition.js
- [ ] 建立 useIconSelection.js
- [ ] 建立 useColorManagement.js
- [ ] 每個 composable 都有完整測試

**技術要求**：
```javascript
// useIconPickerState.js
export function useIconPickerState() {
  const isOpen = ref(false)
  const activeTab = ref('emoji')
  const selectedIcon = ref('')
  // ...
  return { isOpen, activeTab, selectedIcon, ... }
}
```

**測試要求**：
- 測試每個 composable 的邏輯
- 測試狀態管理
- 測試反應性
- 單元測試覆蓋率 > 90%

---

#### ST-018: 重構主 IconPicker 整合

**作為**：開發者  
**我想要**：將所有子元件整合到新的 IconPicker  
**所以**：完成重構並保持功能完全一致  

**驗收條件**：
- [ ] 整合所有子元件
- [ ] 使用 composables 管理狀態
- [ ] 保持原有的事件處理
- [ ] 保持原有的介面
- [ ] 通過所有整合測試

**技術要求**：
- 使用組合式 API
- 保持 props 和 events 介面
- 保持樣式和行為
- 優化效能

**測試要求**：
- 整合測試
- E2E 測試
- 與原版 A/B 測試
- 效能基準測試

---

### EP-006: 測試和優化

---

#### ST-019: 建立完整測試套件

**作為**：開發者  
**我想要**：完整的測試覆蓋  
**所以**：確保重構品質和未來維護安全  

**驗收條件**：
- [ ] 所有元件都有單元測試
- [ ] 所有 composables 都有測試
- [ ] 所有服務都有測試
- [ ] 建立整合測試
- [ ] 測試覆蓋率達到 > 80%

**技術要求**：
- 使用 Vitest + Vue Test Utils
- Mock 外部相依性
- 測試使用者互動
- 測試錯誤情況

**測試要求**：
- 測試覆蓋率報告
- 測試執行時間 < 30秒
- 所有測試穩定通過
- CI/CD 整合

---

#### ST-020: 效能測試和優化

**作為**：使用者  
**我想要**：新版 IconPicker 效能不低於原版  
**所以**：使用體驗不會下降  

**驗收條件**：
- [ ] 建立效能基準測試
- [ ] 載入時間不超過原版
- [ ] 記憶體使用不超過原版
- [ ] 滾動效能達到 60fps
- [ ] 完成必要的效能優化

**技術要求**：
- 使用效能監控工具
- 建立自動化基準測試
- 優化關鍵路徑
- 減少不必要的重新渲染

**測試要求**：
- 載入時間基準測試
- 記憶體使用基準測試
- 滾動效能測試
- 長時間使用穩定性測試

---

#### ST-021: A/B 測試和文件

**作為**：開發者  
**我想要**：確保新版與原版完全一致並有完整文件  
**所以**：可以安全部署並方便未來維護  

**驗收條件**：
- [ ] 建立自動化 A/B 對比測試
- [ ] 視覺回歸測試通過
- [ ] 功能對比測試通過
- [ ] 撰寫完整的使用文件
- [ ] 撰寫維護文件

**技術要求**：
- 使用截圖對比工具
- 建立功能檢查清單
- 文件包含使用範例
- 文件包含架構說明

**測試要求**：
- 像素級視覺對比
- 功能完整性檢查
- 效能對比測試
- 文件準確性驗證

---

## 📊 Story 相依性和順序

### Phase 0 (第1週)
```
ST-001 → ST-002 → ST-003 → ST-004 → ST-022-A → ST-022
```

### Phase 1 (第2週)
```
ST-005 → ST-006
       → ST-007 → ST-008
```

### Phase 2 (第3-4週)
```
ST-009 → ST-010 → ST-011
              ↓
ST-012, ST-013, ST-014, ST-015, ST-016
(可並行開發)
```

### Phase 3 (第5週)
```
ST-017 → ST-018
```

### Phase 4 (第6週)
```
ST-019 → ST-020 → ST-021
```

## ⏱️ 預估工時總結

| Phase | 原工時 | 整合工時 | 總工時 | 說明 |
|-------|--------|----------|--------|------|
| Phase 0 | 13小時 | +0小時 | 13小時 | 基礎建設 + 測試頁面 |
| Phase 1 | 20小時 | +0小時 | 20小時 | 服務層重構 |
| Phase 2 | 52小時 | +5小時 | 57小時 | 共用元件 + 面板拆分 + 測試頁面整合 |
| Phase 3 | 16小時 | +2小時 | 18小時 | 邏輯整合 + 最終整合測試 |
| Phase 4 | 20小時 | +0小時 | 20小時 | 測試優化 |
| 修正和測試 | 0小時 | +6小時 | 6小時 | 文字上限修正 + 專項測試 |
| **總計** | **121小時** | **+13小時** | **134小時** | **約 3.4 個月（兼職）** |

### 新增整合任務明細

| 整合任務 | 工時 | 說明 |
|----------|------|------|
| ST-012-I | 1小時 | TextIconPanel 測試頁面整合 |
| ST-013-I | 1小時 | EmojiPanel 測試頁面整合 |
| ST-014-I | 1小時 | IconLibraryPanel 測試頁面整合 |
| ST-015-I | 1小時 | ImageUploadPanel 測試頁面整合 |
| ST-016-I | 1小時 | ColorPickerPanel 測試頁面整合 |
| ST-018-I | 2小時 | 完整整合測試 |
| **小計** | **7小時** | **測試頁面整合總工時** |

### 修正和測試補強任務明細

| 補強任務 | 工時 | 說明 |
|----------|------|------|
| ST-FIX-001 | 2小時 | 修正 TextIconPanel 文字上限為 2 字元 |
| ST-TEST-001 | 3小時 | TextIconPanel 專項 E2E 測試 |
| ST-TEST-002 | 1小時 | Playwright 測試環境配置文件 (已完成) |
| **小計** | **6小時** | **修正和測試補強總工時** |

## 🎯 下一步行動

### 立即執行項目（新增修正和測試）

1. **ST-FIX-001: 修正 TextIconPanel 文字上限為 2 字元** (優先級 P0)
   - 目前發現文字上限設定為 3 字元，需修正為 2 字元
   - 預估工時：2小時
   - 執行分支：`fix/text-icon-limit-correction`

2. **ST-TEST-001: TextIconPanel 專項 E2E 測試** (優先級 P0)
   - 為 TextIconPanel 建立完整的 E2E 測試覆蓋
   - 預估工時：3小時
   - 執行分支：`test/text-icon-panel-e2e`

3. **ST-013: 實作 EmojiPanel** (優先級 P1)
   - 下一個面板元件開發
   - 預估工時：8小時 + 1小時整合

### 後續執行順序

4. **ST-014: 實作 IconLibraryPanel** → **ST-014-I: 整合**
5. **ST-015: 實作 ImageUploadPanel** → **ST-015-I: 整合**
6. **ST-016: 實作 ColorPickerPanel** → **ST-016-I: 整合**
7. **ST-017: 建立 composables**
8. **ST-018: 重構主 IconPicker 整合** → **ST-018-I: 完整整合測試**

### 建議工作流程

每個面板元件的開發流程：
1. **開發階段**：實作元件功能和測試
2. **整合階段**：立即整合到測試頁面
3. **驗證階段**：測試頁面功能驗證
4. **提交階段**：PR 包含開發+整合內容

---

## 🔧 修正和測試補強項目

### ST-FIX-001: 修正 TextIconPanel 文字上限為 2 字元

**作為**：產品使用者  
**我想要**：文字圖標限制為 2 個字元  
**所以**：符合設計規範且保持視覺美觀  

**問題發現**：
- 目前 TextIconPanel 設定為 `maxlength="3"`
- 預設文字和提示都是 3 字元 (如: "AB")
- 應該修正為 2 字元上限

**驗收條件**：
- [ ] TextIconPanel.vue 修正 maxlength 為 2
- [ ] 更新 placeholder 文字為 "最多2個字元 (如: AB)"
- [ ] 檢查其他相關檔案的一致性修正
- [ ] 更新相關測試的期望值
- [ ] 驗證功能在測試頁面正常運作

**技術要求**：
```vue
<!-- 修正前 -->
<input maxlength="3" placeholder="最多3個字元 (如: AB)" />

<!-- 修正後 -->  
<input maxlength="2" placeholder="最多2個字元 (如: AB)" />
```

**影響範圍檢查**：
- [ ] resources/js/features/icon-picker/components/TextIconPanel.vue
- [ ] 相關測試檔案的字元限制測試
- [ ] 其他 IconPicker 相關檔案一致性檢查

**測試要求**：
- 手動測試輸入超過 2 字元被限制
- 驗證預設範例仍為 "AB" (2字元)
- 確認 UI 顯示和功能邏輯正確

**DoD (Definition of Done)**：
- 程式碼修正完成且通過測試
- 功能在測試頁面驗證正確
- 相關檔案一致性檢查完成
- 無相關錯誤或警告

---

### ST-TEST-001: TextIconPanel 專項 E2E 測試

**作為**：開發者  
**我想要**：為 TextIconPanel 建立完整的 E2E 測試  
**所以**：確保文字圖標功能的穩定性和正確性  

**驗收條件**：
- [ ] 建立 `text-icon-panel.spec.js` 專項測試檔案
- [ ] 測試文字輸入限制（2字元上限）
- [ ] 測試大寫轉換功能
- [ ] 測試預覽顯示正確性
- [ ] 測試顏色計算邏輯
- [ ] 測試應用按鈕狀態管理
- [ ] 測試錯誤處理和邊界情況

**技術要求**：
```javascript
// 測試檔案結構
tests/e2e/text-icon-panel.spec.js
- 文字輸入和長度限制測試
- 預覽功能測試  
- 顏色處理測試
- 按鈕狀態測試
- 邊界情況和錯誤處理測試
```

**測試案例清單**：
1. **基本功能測試**
   - 輸入 1 字元顯示正確
   - 輸入 2 字元顯示正確
   - 嘗試輸入 3 字元被限制為 2 字元
   - 大寫轉換功能正確

2. **預覽功能測試**
   - 預覽區域顯示輸入文字
   - 背景顏色正確套用
   - 文字顏色對比度計算正確
   - 無輸入時顯示預設 "AB"

3. **互動測試**
   - 輸入空白時應用按鈕應被禁用
   - 有效輸入時應用按鈕應啟用
   - 點擊應用按鈕觸發正確事件
   - 清空輸入重置狀態

4. **整合測試**
   - 在 Icon Picker 彈窗內功能正常
   - 與其他標籤頁切換正常
   - 顏色選擇器整合正常

**測試覆蓋率目標**：> 90%

**DoD (Definition of Done)**：
- 所有測試案例實作完成
- 測試穩定通過（多次執行）
- 測試覆蓋關鍵功能路徑
- 測試執行時間合理（< 30秒）
- 與現有測試套件整合良好

---

### ST-TEST-002: Playwright 測試環境配置文件

**作為**：開發團隊  
**我想要**：完善的 Playwright 測試環境配置  
**所以**：支援高效率的 E2E 測試開發  

**已完成項目**：
- ✅ 安裝 Playwright 依賴和瀏覽器
- ✅ 配置 playwright.config.js
- ✅ 建立基礎測試檔案結構
- ✅ 配置 .gitignore 排除測試產生檔案
- ✅ 整合 Laravel 開發伺服器自動啟動

**配置完成項目**：
- ✅ 多瀏覽器支援 (Chrome, Firefox, WebKit)
- ✅ 行動裝置測試支援 (Mobile Chrome, Mobile Safari)
- ✅ HTML 報告和截圖配置
- ✅ 影片錄製和追蹤配置
- ✅ 基礎 URL 和超時時間設定

**測試環境優化**：
- ✅ 測試結果檔案不被 git 追蹤
- ✅ 測試報告和快取檔案排除
- ✅ 保留重要靜態資源的 git 追蹤

**狀態**：✅ 已完成

---

## 📝 重要架構決策記錄 (ADR)

### ADR-001: Emoji Panel 完整解決方案整合 (2025-08-17)

**背景**：
在 ST-013 (實作 EmojiPanel) 開發過程中，發現了多個權宜處理問題：
- 硬編碼的分類名稱對應
- 硬編碼的膚色支援列表
- 測試資料與實際 API 格式不符
- 前後端責任劃分不清

**決策**：
將所有 emoji 相關改進整合到 ST-013 中，而非拆分為獨立的 PRD/Epic。

**理由**：
1. **TDD 原則**：這些問題是 ST-013 完成的必要條件，不是額外功能
2. **敏捷原則**：一個 Story 應該交付完整可用的功能
3. **技術債避免**：避免累積權宜處理，確保架構品質
4. **責任清晰**：明確定義前後端職責劃分

**影響**：
- ST-013 工時維持 8 小時（這些是必要的完成工作）
- 交付品質更高，無技術債
- 符合「完成的定義」(Definition of Done)

**後果**：
- ✅ 交付真正可用且高品質的 EmojiPanel
- ✅ 建立清晰的前後端架構模式
- ✅ 為後續面板開發提供標準範本

---

**相關文件**：
- [PRD 文件](./ICON-PICKER-BROWNFIELD-PRD.md)
- [Epic 定義](./ICON-PICKER-EPICS.md)
- [測試頁面整合工作流程](./ICON-PICKER-INTEGRATION-WORKFLOW.md)