# IconPicker 重構專案

本專案旨在重構 PurpleDesk 系統中的 IconPicker 元件，從 1,386 行的單一檔案拆分為模組化架構。

## 📋 專案狀態

### ✅ Phase 0: 建立安全網（已完成）
- [x] **ST-001**: 建立 IconPickerOri.vue 備份
- [x] **ST-002**: 建立 features 目錄結構
- [x] **ST-003**: 配置 Vitest 測試框架
- [x] **ST-004**: 建立版本切換機制

### 🚧 待執行階段
- [ ] **Phase 1**: 服務層重構
- [ ] **Phase 2**: 共用元件開發
- [ ] **Phase 3**: 面板元件拆分
- [ ] **Phase 4**: 邏輯抽離和整合
- [ ] **Phase 5**: 測試和優化

## 🎛️ 開發環境版本切換

在開發過程中，可以隨時在原版和新版之間切換：

### 方法 1: 瀏覽器 Console
```javascript
// 切換到原版
switchIconPicker("original")

// 切換到新版  
switchIconPicker("new")
```

### 方法 2: URL 參數
```
?iconpicker=original  // 使用原版
?iconpicker=new       // 使用新版
```

### 方法 3: 環境變數
```env
VITE_ICONPICKER_VERSION=original
```

### 方法 4: 開發者工具面板
在開發環境中會自動顯示浮動的開發者工具面板，可以直接點擊切換版本。

## 📁 專案結構

```
features/icon-picker/
├── components/
│   ├── shared/                 # 共用元件
│   ├── IconPickerProxy.vue     # 版本代理元件
│   └── IconPickerDevTool.vue   # 開發者工具
├── composables/                # Vue 組合式函數
├── services/                   # 服務層
├── tests/                      # 測試檔案
│   ├── components/
│   ├── composables/
│   └── services/
└── README.md                   # 本文件
```

## 🧪 測試

```bash
# 執行所有測試
npm run test:run features/icon-picker/tests/

# 執行測試並產生覆蓋率報告
npm run test:coverage features/icon-picker/tests/

# 執行特定測試檔案
npm run test:run features/icon-picker/tests/components/IconPickerProxy.test.js
```

## 📖 開發原則

1. **TDD 測試驅動開發**：每個功能都必須先寫測試
2. **漸進式重構**：每次只重構一個功能模組
3. **100% UI 相容性**：保持介面和行為完全一致
4. **隨時可回滾**：確保原版備份隨時可用

## 🔄 重構策略

採用「絞殺者模式」(Strangler Pattern)：
1. 保留原版 IconPicker.vue → IconPickerOri.vue
2. 逐步建立新的模組化元件
3. 使用 IconPickerProxy.vue 在兩版本間切換
4. 完成重構後移除原版

## 📊 目標指標

- **測試覆蓋率**: > 80%
- **單一檔案行數**: < 300 行
- **載入時間**: 不超過原版
- **記憶體使用**: 不超過原版

## 🔗 相關文件

- [PRD 文件](../../../docs/prd/refactor/ICON-PICKER-BROWNFIELD-PRD.md)
- [Epic 定義](../../../docs/prd/refactor/ICON-PICKER-EPICS.md) 
- [User Stories](../../../docs/prd/refactor/ICON-PICKER-STORIES.md)