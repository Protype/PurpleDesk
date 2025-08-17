# PurpleDesk Brownfield 架構文件

## 介紹

此文件記錄 PurpleDesk 專案管理系統的**實際現況**，包含技術債務、既有約束和實際運作模式。本文件特別聚焦於 IconPicker 元件重構所涉及的架構區域。

### 文件範圍

**聚焦於 IconPicker 重構相關區域**：包含 IconPicker 元件及其相關的圖標管理、前後端整合架構。

### 變更記錄

| 日期 | 版本 | 描述 | 作者 |
|------|------|------|------|
| 2025-08-17 | 1.0 | 初始 brownfield 分析 | Winston (Architect) |

## 快速參考 - 關鍵檔案和進入點

### IconPicker 重構關鍵檔案

#### 核心檔案
- **IconPicker 主檔案**: `resources/js/components/common/IconPicker.vue` (1,386 行) - 當前超大型檔案，需要重構
- **備份版本**: `resources/js/components/common/IconPickerOri.vue` - 原版備份
- **新架構基礎**: `features/icon-picker/` - 重構後的模組化架構

#### 服務層檔案
- **前端服務**: `resources/js/services/IconService.js` - 前端圖標服務
- **後端服務**: `app/Services/EmojiService.php` - 後端 Emoji 資料服務
- **新資料載入器**: `features/icon-picker/services/IconDataLoader.js` - 統一資料載入服務

#### 圖標類型處理
- **類型定義**: `app/Icon/IconTypeInterface.php` - 圖標類型介面
- **文字圖標**: `app/Icon/Types/TextIcon.php`
- **Emoji 圖標**: `app/Icon/Types/EmojiIcon.php`
- **HeroIcon 圖標**: `app/Icon/Types/HeroIcon.php`
- **Bootstrap 圖標**: `app/Icon/Types/BootstrapIcon.php`
- **圖片上傳**: `app/Icon/Types/ImageIcon.php`

#### 設定檔案
- **顏色配置**: `config/icon/colors.php`
- **Bootstrap Icons**: `config/icon/bootstrap_icons.php`
- **HeroIcons**: `config/icon/heroicons.php`
- **Emoji 設定**: `config/icon/emoji/index.php`

## 高階架構

### 技術摘要

**核心挑戰**: IconPicker 元件 1,386 行程式碼需要在零破壞性的前提下重構為模組化架構。

### 實際技術堆疊

| 類別 | 技術 | 版本 | 備註 |
|------|------|------|------|
| 後端框架 | Laravel | ^11.31 | PHP 8.2+ 要求 |
| 前端框架 | Vue.js | ^3.5.18 | 組合式 API |
| 狀態管理 | Pinia | ^3.0.3 | Vue 3 狀態管理 |
| 路由 | Vue Router | ^4.5.1 | SPA 路由 |
| 建置工具 | Vite | ^6.0.11 | 快速建置 |
| CSS 框架 | Tailwind CSS | ^3.4.17 | 工具優先 CSS |
| 圖標庫 | HeroIcons + Bootstrap Icons | ^1.0.6 + ^1.13.1 | 雙圖標系統 |
| 測試框架 | Vitest + PHPUnit | ^3.2.4 + ^11.0.1 | 前端 + 後端測試 |
| 程式碼品質 | Laravel Pint | ^1.13 | PHP 程式碼格式化 |

### 專案結構類型
- **類型**: Monorepo (Laravel + Vue.js SPA)
- **套件管理**: npm (前端) + Composer (後端)
- **特色**: Features 資料夾架構用於模組化重構

## 原始碼樹狀結構與模組組織

### 實際專案結構

```text
purple-desk/
├── app/                    # Laravel 後端核心
│   ├── Http/Controllers/   # API 控制器
│   │   └── Api/EmojiController.php  # Emoji API 端點
│   ├── Icon/              # 圖標系統核心
│   │   ├── IconTypeInterface.php    # 圖標類型介面
│   │   └── Types/         # 各類型圖標實作
│   ├── Models/            # Eloquent 模型
│   └── Services/          # 業務服務層
│       └── EmojiService.php         # Emoji 後端服務
├── resources/js/          # Vue.js 前端應用
│   ├── components/        # Vue 元件
│   │   ├── common/        # 共用元件
│   │   │   ├── IconPicker.vue       # 主要圖標選擇器 (1,386行)
│   │   │   └── IconPickerOri.vue    # 原版備份
│   │   └── admin/         # 管理介面元件
│   ├── services/          # 前端服務層
│   │   └── IconService.js           # 前端圖標服務
│   ├── utils/             # 工具函式
│   │   ├── heroicons/     # HeroIcons 資料
│   │   ├── icons/         # Bootstrap Icons 資料
│   │   └── emojis/        # Emoji 處理工具
│   └── stores/            # Pinia 狀態管理
├── features/              # 新模組化架構 (重構中)
│   └── icon-picker/       # IconPicker 重構模組
│       ├── components/    # 模組化元件
│       ├── services/      # 統一資料服務
│       │   └── IconDataLoader.js    # 新資料載入器
│       └── tests/         # 模組測試
├── config/icon/           # 圖標設定檔
│   ├── colors.php         # 顏色配置
│   ├── bootstrap_icons.php # Bootstrap Icons 設定
│   ├── heroicons.php      # HeroIcons 設定
│   └── emoji/             # Emoji 分類設定
├── tests/                 # 測試檔案
│   ├── vue/              # 前端測試
│   └── Feature/          # Laravel 功能測試
└── docs/                 # 專案文件
    └── prd/refactor/     # IconPicker 重構規格
```

### 關鍵模組說明

#### IconPicker 系統 (重構中)
- **當前狀態**: 單一檔案 1,386 行，包含 5 種圖標類型邏輯
- **重構目標**: 模組化為 `features/icon-picker/` 架構
- **關鍵約束**: 零破壞性變更，UI/UX 必須 100% 相容

#### 圖標資料流
- **Emoji**: 後端 API (`/api/emojis`) → EmojiService → 前端快取
- **HeroIcons**: 前端檔案 (`utils/heroicons/`) → 直接載入
- **Bootstrap Icons**: 前端檔案 (`utils/icons/`) → 動態載入

## 資料模型和 API

### 圖標資料模型

參考實際模型檔案：
- **圖標類型系統**: 見 `app/Icon/Types/` 各檔案
- **使用者模型**: 見 `app/Models/User.php` (包含 avatar_data 欄位)
- **組織模型**: 見 `app/Models/Organization.php` (包含 icon_data 欄位)
- **團隊模型**: 見 `app/Models/Team.php` (包含 icon_data 欄位)

### API 規格

#### Emoji API
- **端點**: `GET /api/emojis`
- **控制器**: `App\Http\Controllers\Api\EmojiController`
- **回應格式**: 分類化的 emoji 資料結構
- **快取**: 24 小時快取機制

#### 圖標上傳 API
- **儲存路徑**: `storage/app/public/avatars/`
- **支援格式**: PNG, JPG (透過檔案驗證)
- **處理方式**: Base64 編碼儲存到資料庫

## 技術債務與已知問題

### 關鍵技術債務

#### 1. IconPicker 元件過大
- **檔案**: `resources/js/components/common/IconPicker.vue` 
- **問題**: 1,386 行單一檔案，5 種圖標類型邏輯混雜
- **影響**: 極難維護、測試和擴展
- **緩解策略**: 進行中的模組化重構 (Phase 0-4)

#### 2. 資料載入不一致
- **問題**: Emoji 從 API 載入，HeroIcons/Bootstrap Icons 從前端檔案載入
- **影響**: 邏輯分散，難以統一管理
- **緩解策略**: 新的 IconDataLoader 服務統一處理

#### 3. 測試覆蓋不足
- **問題**: IconPicker 原始版本零測試覆蓋
- **影響**: 重構風險極高
- **緩解策略**: TDD 重構流程，目標測試覆蓋率 >80%

### 約束和限制

#### 重構約束
- **絕對約束**: UI/UX 介面必須 100% 相容，零視覺差異
- **功能約束**: 所有 5 種圖標類型功能必須保持一致
- **效能約束**: 載入效能不可低於現有版本
- **回滾約束**: 任何階段都必須可以安全回滾到原版

#### 技術約束
- **Laravel 版本**: 固定在 11.31+，不可降級
- **Vue 版本**: 固定在 3.x 組合式 API，不可使用 Options API
- **圖標庫版本**: HeroIcons 1.x 和 Bootstrap Icons 1.x 的相容性要求

## 整合點和外部相依性

### 外部服務

| 服務 | 用途 | 整合方式 | 關鍵檔案 |
|------|------|----------|----------|
| 內建 Emoji API | Emoji 資料 | Laravel API | `app/Http/Controllers/Api/EmojiController.php` |
| HeroIcons | 圖標庫 | NPM 套件 | `@heroicons/vue` |
| Bootstrap Icons | 圖標庫 | NPM 套件 | `bootstrap-icons` |

### 內部整合點

#### 前後端通訊
- **API 基礎**: Laravel Sanctum 認證
- **資料格式**: JSON API 回應
- **錯誤處理**: 統一錯誤格式 (HTTP 狀態碼 + 訊息)

#### 狀態管理
- **工具**: Pinia store
- **範圍**: 主要用於認證狀態 (`stores/auth.js`)
- **圖標狀態**: 目前在元件內部管理 (重構後將抽離)

## 開發與部署

### 本地開發設定

#### 實際運作步驟
1. **後端啟動**: `php artisan serve` (Port 8000)
2. **前端開發**: `npm run dev` (Vite HMR)
3. **資料庫**: SQLite (`database/database.sqlite`)
4. **並行啟動**: `composer run dev` (同時啟動多個服務)

#### 必要環境變數
```bash
# 基本設定
APP_ENV=local
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# 圖標相關
VITE_API_URL=http://localhost:8000
```

### 建置與部署

#### 建置指令
```bash
# 前端建置
npm run build                   # Vite 生產建置

# 後端最佳化
php artisan config:cache        # 設定快取
php artisan route:cache         # 路由快取
php artisan view:cache          # 視圖快取
```

#### 部署約束
- **靜態資源**: 必須透過 `php artisan storage:link` 連結
- **權限要求**: `storage/` 和 `bootstrap/cache/` 目錄寫入權限
- **Web 伺服器**: 需要支援 SPA 路由重寫規則

## 測試現況

### 當前測試覆蓋率

#### 前端測試 (Vitest)
- **IconPicker 原版**: 0% 覆蓋率 (無測試)
- **新模組**: 建立中的測試基礎設施
- **目標覆蓋率**: >80% (在 `vitest.config.js` 中設定)

#### 後端測試 (PHPUnit)
- **EmojiService**: 基本測試覆蓋
- **圖標類型**: 單元測試存在 (`tests/Unit/Icon/`)

### 測試執行
```bash
# 前端測試
npm run test                    # Vitest 互動模式
npm run test:run               # CI 模式執行
npm run test:coverage          # 產生覆蓋率報告

# 後端測試
php artisan test               # PHPUnit 測試套件
```

## IconPicker 重構影響分析

### 需要修改的檔案

基於重構需求，以下檔案將受到影響：

#### 核心重構檔案
- `resources/js/components/common/IconPicker.vue` - 主要重構目標
- `features/icon-picker/` - 新模組化架構的所有檔案

#### 整合影響檔案
- `resources/js/components/ProfilePage.vue` - 使用 IconPicker 的主要頁面
- `resources/js/components/admin/OrganizationSettings.vue` - 組織圖標設定
- 任何其他使用 IconPicker 的元件

### 新增檔案/模組

重構將建立以下新檔案結構：
```text
features/icon-picker/
├── components/
│   ├── shared/VirtualScrollGrid.vue
│   ├── IconPickerSearch.vue
│   ├── TextIconPanel.vue
│   ├── EmojiPanel.vue
│   ├── IconLibraryPanel.vue
│   ├── ImageUploadPanel.vue
│   └── ColorPickerPanel.vue
├── composables/
│   ├── useIconPickerState.js
│   ├── useIconPosition.js
│   ├── useIconSelection.js
│   └── useColorManagement.js
├── services/
│   └── IconDataLoader.js (已建立)
└── tests/ (完整測試套件)
```

### 整合考量

#### 與現有系統整合
- **事件系統**: 必須保持與父元件的事件接口一致
- **樣式系統**: 維持 Tailwind CSS 類別的一致性
- **資料格式**: 輸出格式必須與現有系統相容

#### 效能影響
- **虛擬滾動**: 新的 VirtualScrollGrid 將改善大量圖標的載入效能
- **快取機制**: IconDataLoader 提供統一的快取管理
- **動態載入**: 按需載入圖標資料，減少初始載入時間

## 附錄 - 常用指令和腳本

### 開發常用指令

```bash
# 專案啟動
php artisan serve              # 啟動 Laravel 開發伺服器
npm run dev                    # 啟動 Vite 開發伺服器
composer run dev               # 同時啟動所有服務

# 測試執行
npm run test:coverage          # 前端測試覆蓋率
php artisan test              # 後端測試

# 程式碼品質
php artisan pint              # PHP 程式碼格式化
npm run build                 # 前端生產建置
```

### 除錯和故障排除

#### 常見問題
- **Vite HMR 失效**: 檢查 `vite.config.js` 設定和 port 衝突
- **圖標載入失敗**: 檢查 `storage:link` 是否正確執行
- **測試失敗**: 確認 `tests/vue/setup.js` 設定正確

#### 記錄檔位置
- **Laravel 記錄**: `storage/logs/laravel.log`
- **Vite 開發記錄**: 控制台輸出
- **測試覆蓋率報告**: `coverage/` 目錄

---

**最後更新**: 2025-08-17  
**下次審查**: Phase 1 重構完成後  
**負責人**: Winston (系統架構師)  
**狀態**: 進行中的重構文件化