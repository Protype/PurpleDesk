# IconPicker 重構 - 開發交接文件

**專案**：IconPicker 元件重構  
**交接人**：John (PM)  
**接收人**：開發團隊  
**交接日期**：2025-08-17  
**狀態**：準備開始開發  

## 📋 交接摘要

### 🎯 專案背景
IconPicker 元件重構，從 1,379 行單一檔案拆分成模組化架構，保持 UI/UX 完全不變，建立測試覆蓋，支援未來擴展。

### 📚 完整文件清單
```
docs/prd/refactor/
├── ICON-PICKER-BROWNFIELD-PRD.md    # 主要需求文件
├── ICON-PICKER-EPICS.md             # 6 個 Epic 定義  
├── ICON-PICKER-STORIES.md           # 21 個詳細 Stories
├── ICON-PICKER-TEST-PLAN.md         # 完整 TDD 測試計劃
└── ICON-PICKER-HANDOVER.md          # 本交接文件
```

### ⚠️ 絕對約束條件
1. **UI/UX 完全不變** - 這是最高優先級，不容妥協
2. **隨時可回滾** - IconPickerOri.vue 是安全網
3. **TDD 強制執行** - 每個 Story 都必須先寫測試

---

## 🔧 關鍵技術決策記錄

### 1. 架構決策

#### ✅ 最終確定的架構
```
features/icon-picker/
├── components/
│   ├── shared/
│   │   └── VirtualScrollGrid.vue     # 虛擬滾動網格（合併版）
│   ├── IconPickerSearch.vue          # 搜尋元件（只搜尋當前頁籤）
│   ├── TextIconPanel.vue             # 文字圖標面板
│   ├── EmojiPanel.vue                # Emoji 面板（使用 VirtualScrollGrid）
│   ├── IconLibraryPanel.vue          # 圖標庫面板（HeroIcon + Bootstrap Icon）
│   ├── ImageUploadPanel.vue          # 圖片上傳面板
│   └── ColorPickerPanel.vue          # 顏色選擇器面板
├── composables/
│   ├── useIconPickerState.js         # 狀態管理
│   ├── useIconPosition.js            # 定位計算
│   ├── useIconSelection.js           # 選擇邏輯
│   └── useColorManagement.js         # 顏色管理
├── services/
│   └── IconDataLoader.js             # 統一資料載入
└── tests/
    ├── components/
    ├── composables/
    └── services/
```

#### 🔑 關鍵決策說明

1. **VirtualScrollGrid 設計**
   - **決策**：合併虛擬滾動 + 項目顯示功能為單一元件
   - **原因**：避免過度抽象，簡化架構
   - **責任**：處理虛擬滾動邏輯、網格佈局、slots 渲染
   - **不負責**：業務邏輯、資料載入、項目選擇

2. **IconLibraryPanel 合併**
   - **決策**：HeroIcon + Bootstrap Icon 合併為單一面板
   - **原因**：它們在同一個 "Icons" 頁籤顯示，用戶視角是統一的
   - **資料載入**：通過 IconDataLoader.getIconLibraryData() 統一處理

3. **IconDataLoader API 設計**
   ```javascript
   class IconDataLoader {
     async getEmojiData()        // 從後端 API 載入
     async getIconLibraryData()  // 合併 HeroIcon + Bootstrap Icon（前端檔案）
     clearCache()
   }
   ```

4. **搜尋功能範圍**
   - **決策**：IconPickerSearch 只搜尋當前頁籤內容
   - **原因**：保持原有行為，避免跨頁籤搜尋的複雜性

### 2. 命名決策

#### ✅ 重要命名說明
- **IconLibraryPanel** (不是 IconPanel) - 更清晰表達是圖標庫
- **VirtualScrollGrid** (不是 VirtualItemGrid) - 強調滾動功能
- **getIconLibraryData()** (不是 getIconData()) - 明確表示圖標庫資料

---

## 🚀 開發流程指南

### 1. 開始前準備

#### 檢查開發環境
```bash
# 確認 Laravel 和 Vite 伺服器運行
ps aux | grep -E "(php.*artisan.*serve|npm.*run.*dev)"

# 如果有遺留進程，先清理
pkill -f "php.*artisan.*serve"
pkill -f "npm.*run.*dev"

# 重新啟動
php artisan serve --host=0.0.0.0 --port=8000 &
npm run dev &
```

#### 確認測試環境
```bash
# 檢查 Vitest 是否正確安裝
npm run test --version

# 執行範例測試
npm run test tests/vue/services/IconService.test.js
```

### 2. TDD 開發循環

#### 🔴 Red Phase - 寫失敗的測試
```bash
# 1. 選擇 Story (建議從 ST-001 開始)
# 2. 建立對應的測試檔案
# 3. 根據 Story 驗收條件寫測試
# 4. 執行測試確認失敗

npm run test -- YourComponent.test.js
# 應該看到紅色失敗訊息
```

#### 🟢 Green Phase - 最小實作
```bash
# 1. 實作最簡單的通過版本
# 2. 執行測試確認通過
# 3. 不要過度實作

npm run test -- YourComponent.test.js
# 應該看到綠色通過訊息
```

#### 🔵 Refactor Phase - 重構優化
```bash
# 1. 改善程式碼品質
# 2. 執行所有測試確認不會破壞功能
# 3. 提交到 git

npm run test
# 所有測試都應該通過
```

### 3. Git 工作流程

#### 建立開發分支
```bash
# 使用 git worktree 建立獨立開發環境
git worktree add _worktree/ST-001-iconpicker-ori-backup origin/main

# 切換到開發目錄
cd _worktree/ST-001-iconpicker-ori-backup

# 建立並切換到功能分支
git checkout -b feat/ST-001-iconpicker-ori-backup

# 確認環境正常（如需要安裝相依性）
npm install
composer install
```

#### 提交和 PR 流程
```bash
# 頻繁提交，使用語意化格式
git add .
git commit -m "feat(icon-picker): 建立 IconPickerOri 備份

- 複製 IconPicker.vue 為 IconPickerOri.vue
- 確保所有功能正常運作
- 建立版本切換機制

🤖 Generated with [Claude Code](https://claude.ai/code)

Co-Authored-By: Claude <noreply@anthropic.com>"

# 推送到 remote
git push -u origin feat/ST-001-iconpicker-ori-backup

# 建立 PR
gh pr create --title "feat(icon-picker): 建立 IconPickerOri 備份 (ST-001)" --body "$(cat <<'EOF'
## Summary
建立 IconPicker 的完整備份版本，確保重構過程中隨時可以安全回滾。

## 實作內容
- [x] 複製 IconPicker.vue 為 IconPickerOri.vue
- [x] 驗證所有功能正常運作
- [x] 建立版本切換機制（開發環境）
- [x] 測試 5 種圖標選擇功能

## 測試內容
- [x] 手動測試所有圖標類型選擇
- [x] 測試搜尋功能
- [x] 測試顏色選擇
- [x] 測試圖片上傳
- [x] 確認無 console 錯誤

## 驗收條件檢查
- [x] IconPickerOri 在當前環境正常運作
- [x] 所有相依性正確複製
- [x] 與原版功能完全一致
- [x] 可在開發環境切換版本

🤖 Generated with [Claude Code](https://claude.ai/code)
EOF
)"
```

---

## 📝 每個 Story 開發指南

### ST-001: 建立 IconPickerOri 備份

#### 🎯 目標
建立完整的 IconPicker 備份版本，確保重構過程中隨時可以安全回滾。

#### 📋 詳細步驟
1. **複製檔案**
   ```bash
   cp resources/js/components/common/IconPicker.vue resources/js/components/common/IconPickerOri.vue
   ```

2. **驗證功能**
   - 在 Profile 頁面測試 IconPickerOri
   - 測試所有 5 種圖標類型
   - 確認搜尋、顏色選擇、上傳功能

3. **建立切換機制**
   ```javascript
   // 在開發環境可選擇使用哪個版本
   const IconPickerComponent = process.env.NODE_ENV === 'development' && 
     import.meta.env.VITE_USE_ORI_ICON_PICKER 
     ? IconPickerOri 
     : IconPicker
   ```

#### ✅ 驗收檢查
- [ ] IconPickerOri 正常顯示和運作
- [ ] 所有 5 種圖標類型都能選擇
- [ ] 搜尋功能正常
- [ ] 顏色選擇正常
- [ ] 圖片上傳正常
- [ ] 無 console 錯誤

### ST-002: 建立 features 目錄結構

#### 🎯 目標
建立標準化的 features 目錄結構，為重構程式碼提供組織化存放位置。

#### 📋 詳細步驟
```bash
mkdir -p resources/js/features/icon-picker/{components/{shared},composables,services,tests/{components/{shared},composables,services}}
```

#### ✅ 驗收檢查
- [ ] 目錄結構符合規劃
- [ ] Vite 可以正確解析路徑
- [ ] 建立 index.js 檔案進行路徑測試

### ST-005: 實作 IconDataLoader 基礎架構

#### 🎯 目標
建立統一的圖標資料載入服務，抽象化不同的資料來源。

#### 📋 TDD 開發步驟

1. **Red Phase - 寫失敗的測試**
   ```javascript
   // tests/services/IconDataLoader.test.js
   describe('IconDataLoader', () => {
     it('should create instance with correct initial state', () => {
       const loader = new IconDataLoader()
       expect(loader).toBeDefined()
       expect(loader.cache).toBeInstanceOf(Map)
     })
   })
   ```

2. **Green Phase - 最小實作**
   ```javascript
   // services/IconDataLoader.js
   export class IconDataLoader {
     constructor() {
       this.cache = new Map()
     }
   }
   ```

3. **Refactor Phase - 擴展功能**
   - 加入 getEmojiData() 方法
   - 加入錯誤處理
   - 加入快取機制

#### ✅ 驗收檢查
- [ ] 類別正確初始化
- [ ] 快取機制運作
- [ ] 錯誤處理完善
- [ ] 測試覆蓋率 > 90%

### ST-009: 實作 VirtualScrollGrid 核心邏輯

#### 🎯 目標
建立高效能的虛擬滾動網格元件，支援大量項目的流暢顯示。

#### 📋 關鍵實作要點

1. **Props 設計**
   ```javascript
   props: {
     items: Array,           // 所有資料項目
     itemsPerRow: Number,    // 每行項目數
     rowHeight: Number,      // 每行高度
     containerHeight: Number, // 容器高度
     buffer: Number          // 緩衝行數
   }
   ```

2. **虛擬滾動計算**
   ```javascript
   const totalRows = computed(() => Math.ceil(items.length / itemsPerRow))
   const visibleRows = computed(() => Math.ceil(containerHeight / rowHeight))
   const visibleStartIndex = computed(() => Math.floor(scrollTop.value / rowHeight))
   ```

3. **Slots 機制**
   ```vue
   <template #item="{ item, index }">
     <!-- 由父元件自訂渲染 -->
   </template>
   ```

#### ✅ 驗收檢查
- [ ] 虛擬滾動計算正確
- [ ] 大量資料渲染流暢
- [ ] Slots 機制正常
- [ ] 效能達到 60fps

---

## ⚠️ 重要注意事項

### 1. 絕對不能做的事
- ❌ **修改原 IconPicker.vue** - 必須保持不動
- ❌ **改變 UI/UX** - 任何視覺或行為改變都不被允許
- ❌ **跳過測試** - 每個功能都必須有測試
- ❌ **在 main branch 開發** - 必須使用功能分支

### 2. 必須做的事
- ✅ **每次都先寫測試** - 嚴格執行 TDD
- ✅ **頻繁提交** - 小步快跑，易於回滾
- ✅ **詳細的 PR 描述** - 包含實作細節和測試結果
- ✅ **保持 IconPickerOri 可用** - 隨時可切換回原版

### 3. 遇到問題時
- 🔄 **優先回滾到上一個穩定狀態**
- 📞 **及時討論架構問題** - 不要自行做重大變更
- 📋 **更新文件** - 如有決策變更，更新相關文件

### 4. 品質檢查
- 🧪 **每次提交前執行測試** - `npm run test`
- 🔍 **檢查 console 錯誤** - 開發者工具不能有錯誤
- 📊 **檢查覆蓋率** - `npm run test:coverage`

---

## 📞 支援和溝通

### 聯繫方式
- **架構決策問題**：重新啟動 PM agent 討論
- **需求澄清**：參考 PRD 和 Stories 文件
- **技術問題**：根據專案 CLAUDE.md 流程處理

### 文件更新
如有重要決策變更，請更新相應文件：
- 架構變更 → 更新 PRD
- 新增需求 → 更新 Stories
- 測試策略變更 → 更新 Test Plan

---

## 🎯 成功指標提醒

### 必要指標（不可妥協）
- ✅ **UI/UX 100% 相容** - 像素級一致
- ✅ **功能 100% 相容** - 所有現有功能正常
- ✅ **隨時可回滾** - IconPickerOri 隨時可用
- ✅ **零新增錯誤** - 無 console 錯誤

### 目標指標
- 📊 **測試覆蓋率 > 80%**
- 📊 **單一檔案 < 300 行**
- 📊 **載入時間不增加**
- 📊 **記憶體使用不增加**

---

## 🚀 開始建議

### 推薦開發順序
1. **ST-001** - 建立 IconPickerOri 備份（最重要的安全網）
2. **ST-002** - 建立目錄結構
3. **ST-003** - 配置測試框架
4. **ST-005** - IconDataLoader 基礎架構
5. **後續依 Stories 順序進行**

### 第一天行動清單
- [ ] 閱讀完所有交接文件
- [ ] 建立開發環境和 git worktree
- [ ] 完成 ST-001 (IconPickerOri 備份)
- [ ] 驗證測試環境正常運作
- [ ] 提交第一個 PR

---

**祝開發順利！記住：安全第一，測試先行，保持 UI 不變！** 🎉

---

**相關文件**：
- [Brownfield PRD](./ICON-PICKER-BROWNFIELD-PRD.md) - 完整需求和架構
- [Epic 定義](./ICON-PICKER-EPICS.md) - 6 個功能大塊
- [User Stories](./ICON-PICKER-STORIES.md) - 21 個開發任務  
- [TDD 測試計劃](./ICON-PICKER-TEST-PLAN.md) - 完整測試策略