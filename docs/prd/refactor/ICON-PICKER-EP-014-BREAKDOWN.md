# ST-014 Epic 分解：IconLibraryPanel Backend Migration

**建立日期**：2025-08-18  
**狀態**：待開始  
**優先級**：P1  
**預估總工時**：24小時 (約 6 個工作天)

## 📋 Epic 概述

### Epic 目標

將 HeroIcons 和 Bootstrap Icons 從前端靜態資料遷移到後端 API 架構，創建統一的 IconLibraryPanel 元件，提升資料管理一致性並遵循專案的 API-first 設計模式。

### 問題背景

原 ST-014 範圍過於複雜，包含了：
1. HeroIcons 資料遷移到後端 API
2. Bootstrap Icons 資料遷移到後端 API  
3. 前端整合邏輯更新
4. 新元件開發

需要拆分為獨立的 Stories 來降低風險並提升開發效率。

## 🎯 Epic 分解

### ST-014-1: 建立 HeroIcons 後端 API 服務

**預估工時**：6小時  
**優先級**：P0 (基礎依賴)

**作為**：開發者  
**我想要**：建立 HeroIcons 的後端 API 服務  
**所以**：前端可以通過統一的 API 介面載入 HeroIcons 資料

**驗收條件**：
- [ ] 創建 `app/Services/HeroIconService.php`
- [ ] 創建 `app/Http/Controllers/Api/HeroIconController.php`
- [ ] 建立 API 路由 `GET /api/config/icon/heroicons`
- [ ] 將現有 230 個 HeroIcons 資料轉為後端設定檔
- [ ] API 回應格式與前端期望一致
- [ ] API 回應時間 < 300ms
- [ ] 包含完整的單元測試

**技術要求**：
```php
// API 端點
GET /api/config/icon/heroicons

// 回應格式
{
  "data": [
    {
      "name": "Academic Cap",
      "component": "AcademicCapIcon",
      "category": "general",
      "keywords": ["education", "graduation", "school"]
    }
  ],
  "meta": {
    "total": 230,
    "categories": ["general", "navigation", "media", ...]
  }
}
```

**測試要求**：
- API 端點功能測試
- HeroIconService 單元測試
- 資料格式驗證測試
- 效能基準測試

---

### ST-014-2: 建立 Bootstrap Icons 後端 API 服務

**預估工時**：8小時  
**優先級**：P0 (基礎依賴)  
**依賴**：ST-014-1 完成 (架構參考)

**作為**：開發者  
**我想要**：建立 Bootstrap Icons 的後端 API 服務  
**所以**：前端可以通過統一的 API 介面載入 Bootstrap Icons 資料

**驗收條件**：
- [ ] 創建 `app/Services/BootstrapIconService.php`
- [ ] 創建 `app/Http/Controllers/Api/BootstrapIconController.php`
- [ ] 建立 API 路由 `GET /api/config/icon/bootstrap-icons`
- [ ] 遷移現有分類載入邏輯 (8 個分類，1800+ 圖標)
- [ ] 支援分類篩選參數 `?categories=general,ui`
- [ ] API 回應時間 < 500ms (資料量較大)
- [ ] 包含完整的單元測試

**技術要求**：
```php
// API 端點
GET /api/config/icon/bootstrap-icons
GET /api/config/icon/bootstrap-icons?categories=general,ui

// 回應格式
{
  "data": {
    "general": [
      {
        "name": "House",
        "class": "bi-house", 
        "keywords": ["home", "building"],
        "category": "general"
      }
    ],
    "ui": [...]
  },
  "meta": {
    "total": 1800,
    "categories": {
      "general": 120,
      "ui": 200,
      ...
    }
  }
}
```

**測試要求**：
- API 端點和分類篩選測試
- BootstrapIconService 單元測試
- 大量資料載入效能測試
- 分類邏輯正確性測試

---

### ST-014-3: 更新 IconDataLoader 整合新 API

**預估工時**：4小時  
**優先級**：P0 (整合依賴)  
**依賴**：ST-014-1, ST-014-2 完成

**作為**：開發者  
**我想要**：更新 IconDataLoader 使用新的後端 API  
**所以**：前端圖標載入邏輯統一並提升維護性

**驗收條件**：
- [ ] 修改 `IconDataLoader._loadHeroIcons()` 使用新 API
- [ ] 修改 `IconDataLoader._loadBootstrapIcons()` 使用新 API  
- [ ] 實作 API/前端雙模式支援 (環境變數控制)
- [ ] 保留前端 fallback 機制預防 API 失敗
- [ ] 更新錯誤處理邏輯和重試機制
- [ ] 快取機制正常運作
- [ ] 前端介面完全向後相容

**技術要求**：
```javascript
// 環境變數控制
const USE_BACKEND_ICONS = process.env.VITE_USE_BACKEND_ICONS === 'true'

// 雙模式支援
async _loadHeroIcons() {
  if (USE_BACKEND_ICONS) {
    return this._loadFromAPI('/api/config/icon/heroicons')
  } else {
    return this._loadFromFrontend()  // 原有邏輯保留
  }
}
```

**測試要求**：
- API 模式和前端模式功能測試
- 錯誤處理和 fallback 測試  
- 快取機制測試
- 效能對比測試

---

### ST-014-4: 實作 IconLibraryPanel 元件

**預估工時**：6小時  
**優先級**：P1 (功能實現)  
**依賴**：ST-014-3 完成

**作為**：使用者  
**我想要**：在獨立的 IconLibraryPanel 中選擇 HeroIcons 和 Bootstrap Icons  
**所以**：圖標選擇體驗更好且程式碼結構更清晰

**驗收條件**：
- [ ] 創建 `resources/js/features/icon-picker/components/IconLibraryPanel.vue`
- [ ] 整合 VirtualScrollGrid 顯示大量圖標
- [ ] 實作圖標搜尋功能 (名稱和關鍵字)
- [ ] 實作樣式切換 (outline/solid for HeroIcons)
- [ ] 保持現有的使用者互動體驗
- [ ] 整合到測試頁面並運作正常
- [ ] 建立完整的元件測試

**技術要求**：
```vue
<template>
  <div class="icon-library-panel">
    <!-- 搜尋和樣式切換 -->
    <IconPickerSearch v-model="searchQuery" />
    <IconStyleSelector v-model="selectedStyle" />
    
    <!-- 圖標網格 -->
    <VirtualScrollGrid
      :items="filteredIcons"
      :items-per-row="10"
      :row-height="36"
    >
      <template #item="{ item }">
        <!-- HeroIcon 或 Bootstrap Icon 渲染 -->
      </template>
    </VirtualScrollGrid>
  </div>
</template>
```

**測試要求**：
- 元件渲染和互動測試
- 搜尋功能測試
- 樣式切換測試  
- VirtualScrollGrid 整合測試
- E2E 使用者流程測試

---

## 📊 相依性圖

```
ST-014-1 (HeroIcons API)     ST-014-2 (Bootstrap API)
        ↓                            ↓
        └──────────→ ST-014-3 (IconDataLoader) ←──────────┘
                            ↓
                    ST-014-4 (IconLibraryPanel)
```

## ⏱️ 時程規劃

| Story | 工時 | 開始條件 | 完成標準 |
|-------|------|----------|----------|
| ST-014-1 | 6小時 | 立即開始 | API 測試通過，資料格式驗證 |
| ST-014-2 | 8小時 | ST-014-1 完成後 | API 效能測試通過 |
| ST-014-3 | 4小時 | ST-014-1,2 完成後 | 前端整合測試通過 |
| ST-014-4 | 6小時 | ST-014-3 完成後 | 元件測試和 E2E 通過 |

**總計**：24小時 (約 6 個工作天)

## 🔧 風險控制

### 主要風險
1. **API 效能風險**：新 API 載入速度可能影響使用者體驗
2. **資料一致性風險**：API 資料格式與前端期望不符
3. **整合複雜度風險**：前後端整合出現意外問題

### 緩解策略
1. **段階式遷移**：保留前端載入作為 fallback
2. **充分測試**：API 效能測試、資料格式驗證
3. **環境變數控制**：可快速切換回原有模式

### 回退計劃
- IconDataLoader 中保留環境變數 `VITE_USE_BACKEND_ICONS=false`
- 可立即切換回前端載入模式
- 原有前端資料檔案保留不刪除

## ✅ Definition of Done

### Epic 完成條件
- [ ] 所有 4 個 Stories 的驗收條件全部滿足
- [ ] 現有圖標選擇功能視覺和互動體驗零差異
- [ ] API 效能測試通過 (HeroIcons < 300ms, Bootstrap Icons < 500ms)
- [ ] IconLibraryPanel 整合到測試頁面正常運作
- [ ] 完整測試覆蓋率 > 90%
- [ ] 無任何現有功能回歸問題

### 交付物
1. **後端服務**：HeroIconService, BootstrapIconService
2. **API 端點**：2 個新的圖標 API 路由
3. **前端整合**：更新的 IconDataLoader 服務
4. **UI 元件**：新的 IconLibraryPanel.vue
5. **測試套件**：完整的前後端測試
6. **文件更新**：API 文件和使用說明

---

**負責人**：開發團隊  
**審查者**：產品經理 + 技術主管  
**完成目標**：2025-08-24 (6 個工作天後)