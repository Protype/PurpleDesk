# PurpleDesk 原始碼樹狀結構文件

## 概述

本文件詳細說明 PurpleDesk 專案的檔案組織結構、模組劃分和檔案命名規範。此專案採用 Laravel + Vue.js 的 Monorepo 架構，特別針對 IconPicker 重構進行了模組化設計。

**更新日期**: 2025-08-17  
**版本**: 1.0  
**專案類型**: Monorepo (Laravel API + Vue.js SPA)

## 專案根目錄結構

```text
purple-desk/
├── 📁 app/                     # Laravel 後端應用核心
├── 📁 resources/               # 前端資源 (Vue.js, CSS, JS)
├── 📁 features/                # 🆕 模組化功能架構
├── 📁 config/                  # Laravel 設定檔
├── 📁 database/               # 資料庫遷移、模型工廠、種子檔案
├── 📁 tests/                  # 測試檔案 (PHPUnit + Vitest)
├── 📁 docs/                   # 專案文件
├── 📁 public/                 # Web 根目錄，靜態資源
├── 📁 storage/                # Laravel 儲存目錄
├── 📁 vendor/                 # Composer 依賴
├── 📁 node_modules/           # npm 依賴
├── 📄 composer.json           # PHP 依賴管理
├── 📄 package.json            # JavaScript 依賴管理
├── 📄 vite.config.js          # Vite 建置設定
├── 📄 tailwind.config.js      # Tailwind CSS 設定
├── 📄 vitest.config.js        # Vitest 測試設定
└── 📄 CLAUDE.md               # AI 開發指引
```

## Laravel 後端結構 (`app/`)

### 核心應用結構

```text
app/
├── 📁 Console/
│   └── 📁 Commands/           # Artisan 自訂指令
├── 📁 Helpers/                # 工具類別
│   ├── 📄 IconDataHelper.php  # 圖標資料處理工具
│   └── 📄 IconResetHelper.php # 圖標重設工具
├── 📁 Http/
│   └── 📁 Controllers/        # HTTP 控制器
│       ├── 📄 AdminController.php
│       ├── 📄 AuthController.php
│       ├── 📄 OrganizationController.php
│       ├── 📄 TeamController.php
│       └── 📁 Api/            # API 控制器
│           └── 📄 EmojiController.php  # Emoji API 端點
├── 📁 Icon/                   # 🎯 圖標系統核心模組
│   ├── 📄 Color.php           # 圖標顏色處理
│   ├── 📄 IconTypeInterface.php # 圖標類型介面
│   └── 📁 Types/              # 各類型圖標實作
│       ├── 📄 BootstrapIcon.php
│       ├── 📄 EmojiIcon.php
│       ├── 📄 HeroIcon.php
│       ├── 📄 ImageIcon.php
│       └── 📄 TextIcon.php
├── 📁 Models/                 # Eloquent 資料模型
│   ├── 📄 Organization.php
│   ├── 📄 Team.php
│   └── 📄 User.php
├── 📁 Providers/
│   └── 📄 AppServiceProvider.php
└── 📁 Services/               # 業務服務層
    └── 📄 EmojiService.php    # Emoji 後端服務
```

### 圖標系統架構 (`app/Icon/`)

這是 PurpleDesk 圖標系統的核心模組，負責定義圖標類型和處理邏輯：

```text
app/Icon/
├── 📄 IconTypeInterface.php   # 圖標類型統一介面
├── 📄 Color.php              # 圖標顏色計算和處理
└── 📁 Types/                 # 各類型圖標實作
    ├── 📄 TextIcon.php       # 文字圖標 (使用者姓名縮寫)
    ├── 📄 EmojiIcon.php      # Emoji 圖標
    ├── 📄 HeroIcon.php       # HeroIcons 圖標庫
    ├── 📄 BootstrapIcon.php  # Bootstrap Icons 圖標庫
    └── 📄 ImageIcon.php      # 上傳圖片圖標
```

#### 檔案功能說明

| 檔案 | 功能 | 關鍵方法 |
|------|------|----------|
| `IconTypeInterface.php` | 定義圖標類型統一介面 | `render()`, `validate()` |
| `Color.php` | 圖標顏色計算邏輯 | `generateColor()`, `getColorPalette()` |
| `TextIcon.php` | 文字圖標實作 | 姓名縮寫產生、顏色計算 |
| `EmojiIcon.php` | Emoji 圖標實作 | Emoji 驗證、膚色處理 |
| `HeroIcon.php` | HeroIcons 實作 | 圖標名稱驗證、SVG 渲染 |
| `BootstrapIcon.php` | Bootstrap Icons 實作 | 圖標類別生成、樣式處理 |
| `ImageIcon.php` | 圖片上傳實作 | 檔案驗證、儲存處理 |

## Vue.js 前端結構 (`resources/`)

### 前端資源組織

```text
resources/
├── 📁 css/
│   └── 📄 app.css             # 主要 CSS 檔案 (Tailwind)
├── 📁 js/
│   ├── 📄 app.js              # 前端應用入口點
│   ├── 📄 bootstrap.js        # 第三方套件初始化
│   ├── 📄 router.js           # Vue Router 設定
│   ├── 📁 components/         # Vue 元件庫
│   ├── 📁 pages/              # 頁面元件
│   ├── 📁 services/           # 前端服務層
│   ├── 📁 stores/             # Pinia 狀態管理
│   ├── 📁 utils/              # 工具函式和資料
│   ├── 📁 models/             # 前端資料模型
│   └── 📁 config/             # 前端設定檔
└── 📁 views/                  # Blade 模板
    ├── 📄 app.blade.php       # SPA 主模板
    ├── 📄 welcome.blade.php   # 歡迎頁面
    └── 📄 test-icons.blade.php # 圖標測試頁面
```

### Vue 元件架構 (`resources/js/components/`)

```text
components/
├── 📄 App.vue                 # 根元件
├── 📄 AppNavbar.vue           # 導航列元件
├── 📄 ProfilePage.vue         # 個人檔案頁面
├── 📄 RegisterPage.vue        # 註冊頁面
├── 📄 SettingsPage.vue        # 設定頁面
├── 📁 admin/                  # 管理介面元件
│   ├── 📄 AdminLayout.vue
│   ├── 📄 AdminOrganizations.vue
│   ├── 📄 AdminSystem.vue
│   ├── 📄 AdminUsers.vue
│   ├── 📄 OrganizationManage.vue
│   └── 📁 organization/       # 組織管理子元件
│       ├── 📄 OrganizationMembers.vue
│       ├── 📄 OrganizationSettings.vue
│       └── 📄 OrganizationTeams.vue
├── 📁 common/                 # 🎯 共用 UI 元件
│   ├── 📄 IconPicker.vue      # 主要圖標選擇器 (1,386行)
│   ├── 📄 IconPickerOri.vue   # 原版備份
│   ├── 📄 IconDisplay.vue     # 圖標顯示元件
│   ├── 📄 ColorPicker.vue     # 顏色選擇器
│   ├── 📄 FileUploader.vue    # 檔案上傳元件
│   ├── 📄 ImageSelector.vue   # 圖片選擇器
│   ├── 📄 LoadingSpinner.vue  # 載入動畫
│   ├── 📄 ConfirmDialog.vue   # 確認對話框
│   ├── 📄 VirtualScroll.vue   # 虛擬滾動元件
│   └── 📄 UserAvatarGroup.vue # 使用者頭像群組
└── 📁 dev-tool/               # 開發工具元件
    └── 📄 TestIconDisplay.vue
```

#### IconPicker 相關元件說明

| 元件 | 功能 | 狀態 |
|------|------|------|
| `IconPicker.vue` | 主要圖標選擇器，包含5種圖標類型 | 🔄 重構中 (1,386行) |
| `IconPickerOri.vue` | 原版備份，確保可回滾 | ✅ 穩定備份 |
| `IconDisplay.vue` | 圖標顯示邏輯 | ✅ 穩定 |
| `ColorPicker.vue` | 顏色選擇功能 | ✅ 穩定 |
| `VirtualScroll.vue` | 虛擬滾動處理大量項目 | ✅ 效能優化 |

### 前端服務層 (`resources/js/services/`)

```text
services/
├── 📄 IconService.js          # 前端圖標服務
└── 📄 UserService.js          # 使用者服務
```

### 前端工具函式 (`resources/js/utils/`)

```text
utils/
├── 📁 emojis/                 # Emoji 處理工具
│   ├── 📄 api-manager.js      # Emoji API 管理
│   └── 📄 index.js            # Emoji 工具主檔案
├── 📁 heroicons/              # HeroIcons 資料
│   └── 📄 allHeroicons.js     # 所有 HeroIcons 定義
├── 📁 icons/                  # Bootstrap Icons 資料
│   ├── 📄 index.js            # Bootstrap Icons 主檔案
│   ├── 📄 bs-alphanumeric.js  # 字母數字圖標
│   ├── 📄 bs-communications.js # 通訊圖標
│   ├── 📄 bs-files.js         # 檔案圖標
│   ├── 📄 bs-general.js       # 一般圖標
│   ├── 📄 bs-media.js         # 媒體圖標
│   ├── 📄 bs-others.js        # 其他圖標
│   ├── 📄 bs-people.js        # 人物圖標
│   └── 📄 bs-ui.js            # UI 圖標
├── 📄 allBootstrapIcons.js    # 所有 Bootstrap Icons
├── 📄 allEmojis.js            # 所有 Emoji 資料
├── 📄 emojiFilter.js          # Emoji 過濾器
├── 📄 emojiSkinTone.js        # Emoji 膚色處理
├── 📄 iconManager.js          # 圖標管理器
├── 📄 iconMap.js              # 圖標映射表
└── 📄 iconSets.js             # 圖標集合定義
```

## 新模組化架構 (`resources/js/features/`)

### Features 目錄結構

這是 PurpleDesk 引入的新模組化架構，專門用於 IconPicker 重構：

```text
resources/js/features/
└── 📁 icon-picker/            # 🆕 IconPicker 重構模組
    ├── 📄 README.md           # 模組說明文件
    ├── 📁 components/         # 模組專用元件
    │   ├── 📄 IconPickerDevTool.vue    # 開發工具
    │   ├── 📄 IconPickerProxy.vue      # 代理元件
    │   ├── 📄 IconPickerSearch.vue     # 搜尋元件
    │   └── 📁 shared/         # 共用子元件
    ├── 📁 composables/        # 可重用邏輯
    ├── 📁 services/           # 模組服務層
    │   └── 📄 IconDataLoader.js # 統一資料載入服務
    └── 📁 tests/              # 模組測試
        ├── 📁 components/     # 元件測試
        │   ├── 📄 IconPickerProxy.test.js
        │   ├── 📄 IconPickerSearch.test.js
        │   └── 📄 vue-component.test.js
        ├── 📁 composables/    # Composables 測試
        ├── 📁 services/       # 服務測試
        │   ├── 📄 IconDataLoader.test.js
        │   ├── 📄 EmojiDataIntegration.test.js
        │   └── 📄 IconLibraryDataIntegration.test.js
        └── 📄 example.test.js
```

### 重構後目標架構

根據 IconPicker 重構 PRD，最終架構將會是：

```text
resources/js/features/icon-picker/
├── 📁 components/
│   ├── 📁 shared/
│   │   └── 📄 VirtualScrollGrid.vue    # 虛擬滾動網格
│   ├── 📄 IconPickerSearch.vue         # 搜尋元件
│   ├── 📄 TextIconPanel.vue            # 文字圖標面板
│   ├── 📄 EmojiPanel.vue               # Emoji 面板
│   ├── 📄 IconLibraryPanel.vue         # 圖標庫面板
│   ├── 📄 ImageUploadPanel.vue         # 圖片上傳面板
│   └── 📄 ColorPickerPanel.vue         # 顏色選擇器面板
├── 📁 composables/
│   ├── 📄 useIconPickerState.js        # 狀態管理
│   ├── 📄 useIconPosition.js           # 定位計算
│   ├── 📄 useIconSelection.js          # 選擇邏輯
│   └── 📄 useColorManagement.js        # 顏色管理
├── 📁 services/
│   └── 📄 IconDataLoader.js            # 統一資料載入
└── 📁 tests/                           # 完整測試覆蓋
    ├── 📁 components/
    ├── 📁 composables/
    └── 📁 services/
```

## 設定檔案結構

### Laravel 設定 (`config/`)

```text
config/
├── 📄 app.php                 # 應用程式基本設定
├── 📄 auth.php                # 認證設定
├── 📄 database.php            # 資料庫設定
├── 📄 filesystems.php         # 檔案系統設定
└── 📁 icon/                   # 🎯 圖標系統設定
    ├── 📄 colors.php          # 圖標顏色配置
    ├── 📄 bootstrap_icons.php # Bootstrap Icons 設定
    ├── 📄 heroicons.php       # HeroIcons 設定
    └── 📁 emoji/              # Emoji 分類設定
        ├── 📄 index.php       # Emoji 索引
        ├── 📄 smileys_emotion.php
        ├── 📄 people_body.php
        ├── 📄 animals_nature.php
        ├── 📄 food_drink.php
        ├── 📄 activities.php
        ├── 📄 travel_places.php
        ├── 📄 objects.php
        ├── 📄 symbols.php
        └── 📄 flags.php
```

### 圖標設定檔說明

| 設定檔 | 功能 | 內容 |
|--------|------|------|
| `colors.php` | 圖標顏色配置 | 預定義顏色清單、顏色計算邏輯 |
| `bootstrap_icons.php` | Bootstrap Icons | 圖標名稱、分類、搜尋關鍵字 |
| `heroicons.php` | HeroIcons | 圖標名稱、樣式變體、SVG 路徑 |
| `emoji/index.php` | Emoji 索引 | 分類結構、載入順序 |
| `emoji/*.php` | 各分類 Emoji | 分類內的 Emoji 清單和資料 |

## 資料庫結構 (`database/`)

```text
database/
├── 📄 database.sqlite         # SQLite 資料庫檔案
├── 📁 factories/              # 模型工廠
│   ├── 📄 OrganizationFactory.php
│   ├── 📄 TeamFactory.php
│   └── 📄 UserFactory.php
├── 📁 migrations/             # 資料庫遷移檔案
│   ├── 📄 0001_01_01_000000_create_users_table.php
│   ├── 📄 2025_08_10_034625_create_organizations_table.php
│   ├── 📄 2025_08_15_230821_modify_avatar_to_avatar_data_in_users_table.php
│   └── ... (其他遷移檔案)
└── 📁 seeders/                # 資料種子檔案
    ├── 📄 DatabaseSeeder.php
    ├── 📄 UserSeeder.php
    ├── 📄 OrganizationSeeder.php
    └── 📄 AvatarTestSeeder.php
```

### 關鍵遷移檔案

與 IconPicker 相關的重要遷移檔案：

| 遷移檔案 | 功能 | 關鍵變更 |
|----------|------|----------|
| `modify_avatar_to_avatar_data_in_users_table.php` | 使用者頭像資料結構 | `avatar` → `avatar_data` (JSON) |
| `add_logo_data_to_organizations_table.php` | 組織 Logo 資料 | 新增 `logo_data` 欄位 |
| `add_avatar_data_to_teams_table.php` | 團隊圖標資料 | 新增 `icon_data` 欄位 |

## 測試結構 (`tests/`)

### 測試目錄組織

```text
tests/
├── 📄 TestCase.php            # 測試基底類別
├── 📁 Feature/                # Laravel 功能測試
│   ├── 📁 Api/
│   │   └── 📄 EmojiControllerTest.php
│   ├── 📄 AuthTest.php
│   └── 📄 ExampleTest.php
├── 📁 Unit/                   # Laravel 單元測試
│   ├── 📄 ExampleTest.php
│   ├── 📁 Icon/               # 圖標系統測試
│   │   ├── 📄 ColorTest.php
│   │   └── 📄 IconTypeTest.php
│   └── 📄 UserAccountFieldTest.php
└── 📁 vue/                    # Vue.js 前端測試
    ├── 📄 setup.js            # Vitest 設定
    ├── 📁 components/         # 元件測試
    │   ├── 📄 AppNavbar.test.js
    │   ├── 📄 ExampleComponent.test.js
    │   └── 📄 ProfilePage.test.js
    ├── 📁 e2e/                # E2E 測試
    │   └── 📄 auth-flow.test.js
    ├── 📁 integration/        # 整合測試
    ├── 📁 models/             # 模型測試
    │   └── 📄 User.test.js
    ├── 📁 pages/              # 頁面測試
    │   ├── 📄 Dashboard.test.js
    │   └── 📄 LoginPage.test.js
    ├── 📁 services/           # 服務測試
    │   ├── 📄 IconService.test.js
    │   ├── 📄 IconService.integration.test.js
    │   └── 📄 UserService.test.js
    ├── 📁 stores/             # 狀態管理測試
    │   └── 📄 auth.test.js
    └── 📁 utils/              # 工具函式測試
        └── 📄 testHelpers.js
```

## 文件結構 (`docs/`)

```text
docs/
├── 📄 TDD.md                  # TDD 開發規範
├── 📁 architecture/           # 🆕 架構文件
│   ├── 📄 brownfield-architecture.md  # 專案架構現況
│   ├── 📄 coding-standards.md         # 程式碼規範
│   ├── 📄 tech-stack.md              # 技術堆疊文件
│   └── 📄 source-tree.md             # 本文件
├── 📁 prd-ori/               # 原始產品需求
│   ├── 📄 ICON-SPEC.md       # 圖標規格文件
│   └── 📄 MAIN.md            # 主要產品文件
└── 📁 prd/refactor/          # IconPicker 重構文件
    ├── 📄 ICON-PICKER-BROWNFIELD-PRD.md  # 重構產品需求
    ├── 📄 ICON-PICKER-EPICS.md           # Epic 文件
    ├── 📄 ICON-PICKER-HANDOVER.md        # 交接文件
    ├── 📄 ICON-PICKER-STORIES.md         # User Stories
    └── 📄 ICON-PICKER-TEST-PLAN.md       # 測試計劃
```

## 靜態資源結構 (`public/`)

```text
public/
├── 📄 index.php               # PHP 應用程式入口
├── 📄 favicon.ico             # 網站圖標
├── 📄 robots.txt              # 搜尋引擎爬蟲設定
├── 📄 hot                     # Vite HMR 狀態檔案
├── 📁 build/                  # Vite 建置輸出
└── 📁 storage/                # 符號連結到 storage/app/public
    └── 📁 avatars/            # 上傳的頭像檔案
        ├── 📁 users/          # 使用者頭像
        └── 📁 organizations/  # 組織 Logo
```

## 儲存結構 (`storage/`)

```text
storage/
├── 📁 app/
│   ├── 📁 private/            # 私有檔案
│   └── 📁 public/             # 公開檔案
│       └── 📁 avatars/        # 上傳的圖標檔案
│           ├── 📁 users/      # 使用者頭像檔案
│           └── 📁 organizations/ # 組織 Logo 檔案
├── 📁 framework/              # Laravel 框架快取
│   ├── 📁 cache/
│   ├── 📁 sessions/
│   └── 📁 views/
└── 📁 logs/                   # 應用程式日誌
    └── 📄 laravel.log
```

## 建置設定檔案

### 關鍵設定檔案位置

| 檔案 | 功能 | 路徑 |
|------|------|------|
| `vite.config.js` | Vite 建置設定 | `/vite.config.js` |
| `tailwind.config.js` | Tailwind CSS 設定 | `/tailwind.config.js` |
| `vitest.config.js` | Vitest 測試設定 | `/vitest.config.js` |
| `postcss.config.js` | PostCSS 處理設定 | `/postcss.config.js` |
| `phpunit.xml` | PHPUnit 測試設定 | `/phpunit.xml` |
| `composer.json` | PHP 依賴管理 | `/composer.json` |
| `package.json` | JS 依賴管理 | `/package.json` |

## 檔案命名規範

### 後端檔案命名

#### PHP 類別檔案
- **控制器**: `{功能}Controller.php` (例: `EmojiController.php`)
- **模型**: `{實體名稱}.php` (例: `User.php`, `Organization.php`)
- **服務**: `{功能}Service.php` (例: `EmojiService.php`)
- **介面**: `{功能}Interface.php` (例: `IconTypeInterface.php`)

#### 資料庫相關
- **遷移**: `{日期}_{時間}_{描述}.php`
- **工廠**: `{模型名稱}Factory.php`
- **種子**: `{功能}Seeder.php`

### 前端檔案命名

#### Vue 元件
- **元件檔案**: `PascalCase.vue` (例: `IconPicker.vue`)
- **頁面元件**: `{頁面名稱}Page.vue` (例: `ProfilePage.vue`)
- **佈局元件**: `{佈局名稱}Layout.vue` (例: `AdminLayout.vue`)

#### JavaScript 檔案
- **服務**: `{功能}Service.js` (例: `IconService.js`)
- **工具**: `camelCase.js` (例: `iconManager.js`)
- **設定**: `{功能}Config.js` (例: `iconDisplayConfig.js`)

#### 測試檔案
- **測試檔案**: `{被測試檔案}.test.js` (例: `IconPicker.test.js`)
- **整合測試**: `{功能}.integration.test.js`

## 模組依賴關係

### IconPicker 模組依賴圖

```text
IconPicker 模組依賴關係:

resources/js/features/icon-picker/
├── services/IconDataLoader.js
│   ├── → resources/js/services/IconService.js
│   ├── → resources/js/utils/heroicons/allHeroicons.js
│   └── → resources/js/utils/icons/index.js
│
├── components/IconPickerSearch.vue
│   └── → composables/useIconPickerState.js
│
└── components/{各種Panel}.vue
    ├── → composables/useIconSelection.js
    ├── → composables/useColorManagement.js
    └── → shared/VirtualScrollGrid.vue

後端支援:
app/Services/EmojiService.php
├── → app/Http/Controllers/Api/EmojiController.php
├── → config/icon/emoji/*.php
└── → app/Icon/Types/*.php
```

### 資料流向

```text
圖標資料流向:

1. Emoji 資料流:
   config/icon/emoji/*.php → EmojiService → EmojiController → API
   ↓
   IconService (前端) → IconDataLoader → IconPicker 元件

2. HeroIcons 資料流:
   utils/heroicons/allHeroicons.js → IconDataLoader → IconPicker 元件

3. Bootstrap Icons 資料流:
   utils/icons/*.js → IconDataLoader → IconPicker 元件

4. 圖片上傳流:
   ImageUploadPanel → FileUploader → storage/app/public/avatars/
```

## 開發工作流程

### 新功能開發流程

1. **建立功能分支**
   ```bash
   git checkout -b feat/icon-picker-new-feature
   ```

2. **模組化開發**
   - 在 `resources/js/features/` 下建立新模組
   - 或在現有模組下新增功能

3. **測試驅動開發**
   - 先寫測試 (`tests/` 或 `resources/js/features/{module}/tests/`)
   - 再實作功能

4. **程式碼審查**
   - 提交 PR 進行審查
   - 確保測試通過

### 檔案修改影響分析

當修改特定檔案時的影響範圍：

| 修改檔案 | 影響範圍 | 需要測試 |
|----------|----------|----------|
| `IconPicker.vue` | 所有使用圖標選擇的頁面 | UI 測試、功能測試 |
| `IconDataLoader.js` | 圖標資料載入邏輯 | 資料載入測試 |
| `EmojiService.php` | Emoji API 端點 | API 測試、整合測試 |
| `config/icon/*.php` | 圖標設定和資料 | 設定載入測試 |

## 最佳實務

### 檔案組織最佳實務

1. **單一責任原則**
   - 每個檔案只負責一個功能
   - 避免過大的檔案 (如當前的 IconPicker.vue)

2. **模組化設計**
   - 使用 `resources/js/features/` 目錄進行功能分組
   - 相關檔案放在同一模組下

3. **測試覆蓋**
   - 每個模組都有對應的測試目錄
   - 重要功能必須有測試保護

4. **文件化**
   - 複雜模組提供 README.md
   - API 和元件提供使用範例

### 檔案命名最佳實務

1. **一致性**
   - 同類型檔案使用相同命名規範
   - 避免縮寫和不清楚的命名

2. **描述性**
   - 檔案名稱要能清楚表達功能
   - 避免過於簡短的名稱

3. **層級清晰**
   - 使用目錄結構表達功能層級
   - 相關檔案分組放置

---

## 維護與更新

### 文件維護責任

- **架構師**: 負責整體架構文件更新
- **開發團隊**: 負責模組內部文件維護
- **QA 團隊**: 負責測試相關文件

### 定期審查

- **每月**: 檢查檔案組織是否合理
- **每季**: 評估模組化效果
- **重構後**: 更新相關架構文件

---

**維護者**: Winston (系統架構師)  
**最後更新**: 2025-08-17  
**下次審查**: IconPicker 重構完成後