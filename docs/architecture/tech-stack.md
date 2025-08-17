# PurpleDesk 技術堆疊文件

## 概述

PurpleDesk 是一個現代化的專案管理系統，採用 Laravel + Vue.js 全端技術堆疊。本文件詳細記錄了所有技術選型、版本資訊和架構決策。

**更新日期**: 2025-08-17  
**版本**: 1.0  
**專案類型**: Monorepo (Laravel API + Vue.js SPA)

## 核心技術堆疊

### 後端技術

#### 主要框架
| 技術 | 版本 | 用途 | 選擇理由 |
|------|------|------|----------|
| **PHP** | ^8.2 | 後端運行環境 | 現代 PHP 特性，強類型支援 |
| **Laravel** | ^11.31 | Web 框架 | 成熟的 MVC 架構，豐富的生態系統 |
| **Laravel Sanctum** | ^4.0 | API 認證 | 輕量級 SPA 認證解決方案 |

#### 資料庫
| 技術 | 版本 | 用途 | 備註 |
|------|------|------|------|
| **SQLite** | (內建) | 開發環境資料庫 | 輕量化，適合開發和測試 |
| **Eloquent ORM** | (Laravel 內建) | 資料庫操作 | Laravel 原生 ORM |

#### 開發工具
| 技術 | 版本 | 用途 |
|------|------|------|
| **Laravel Pint** | ^1.13 | PHP 程式碼格式化 |
| **PHPUnit** | ^11.0.1 | 後端單元測試 |
| **Laravel Tinker** | ^2.9 | 互動式 REPL |
| **Laravel Pail** | ^1.1 | 日誌監控 |

### 前端技術

#### 主要框架
| 技術 | 版本 | 用途 | 選擇理由 |
|------|------|------|----------|
| **Vue.js** | ^3.5.18 | 前端框架 | 現代響應式框架，組合式 API |
| **Vue Router** | ^4.5.1 | 路由管理 | Vue 3 官方路由解決方案 |
| **Pinia** | ^3.0.3 | 狀態管理 | Vue 3 官方狀態管理庫 |

#### 建置工具
| 技術 | 版本 | 用途 | 特色 |
|------|------|------|------|
| **Vite** | ^6.0.11 | 前端建置工具 | 快速的 HMR，ESM 支援 |
| **Laravel Vite Plugin** | ^1.2.0 | Laravel 整合 | 無縫整合 Laravel 資產管理 |
| **PostCSS** | ^8.5.6 | CSS 處理 | Tailwind CSS 編譯 |

#### UI 與樣式
| 技術 | 版本 | 用途 | 優勢 |
|------|------|------|------|
| **Tailwind CSS** | ^3.4.17 | CSS 框架 | 工具優先，高度客製化 |
| **@tailwindcss/forms** | ^0.5.10 | 表單樣式 | 一致的表單元素樣式 |
| **HeroIcons** | ^1.0.6 | 圖標庫 | 高品質 SVG 圖標 |
| **Bootstrap Icons** | ^1.13.1 | 圖標庫 | 豐富的圖標選擇 |

#### 測試工具
| 技術 | 版本 | 用途 |
|------|------|------|
| **Vitest** | ^3.2.4 | 前端測試框架 |
| **@vue/test-utils** | ^2.4.6 | Vue 元件測試工具 |
| **Happy DOM** | ^18.0.1 | DOM 模擬環境 |
| **@vitest/coverage-v8** | ^3.2.4 | 測試覆蓋率報告 |

### 開發工具

#### 套件管理
| 工具 | 版本 | 用途 |
|------|------|------|
| **Composer** | (最新) | PHP 依賴管理 |
| **npm** | (最新) | JavaScript 依賴管理 |

#### 版本控制與協作
| 工具 | 用途 |
|------|------|
| **Git** | 版本控制 |
| **GitHub CLI** | PR 和 Issue 管理 |

## 架構模式與設計原則

### 後端架構模式

#### MVC 架構
```text
├── app/Http/Controllers/     # 控制器層 (處理 HTTP 請求)
├── app/Models/              # 模型層 (資料邏輯)
├── app/Services/            # 服務層 (業務邏輯)
└── resources/views/         # 視圖層 (Blade 模板)
```

#### 服務導向架構
- **EmojiService**: 處理 Emoji 資料管理和快取
- **IconService**: 前端圖標服務整合
- 清晰的責任分離和可測試性

#### RESTful API 設計
```php
// 標準 RESTful 路由結構
Route::apiResource('emojis', EmojiController::class);
Route::apiResource('organizations', OrganizationController::class);
Route::apiResource('teams', TeamController::class);
```

### 前端架構模式

#### 組合式 API 模式
```javascript
// 使用 Vue 3 Composition API
export default {
  setup() {
    const state = reactive({})
    const computedValue = computed(() => {})
    return { state, computedValue }
  }
}
```

#### 模組化 Features 架構
```text
features/
└── icon-picker/           # 功能模組
    ├── components/        # 模組專用元件
    ├── composables/       # 可重用邏輯
    ├── services/          # 資料服務
    └── tests/            # 模組測試
```

#### 狀態管理模式
- **Pinia Store**: 用於全域狀態 (認證、使用者資料)
- **Composables**: 用於功能級狀態管理
- **Local State**: 用於元件內部狀態

## 開發環境設定

### 系統需求

#### 後端需求
- **PHP**: ≥ 8.2
- **Composer**: 最新版本
- **SQLite**: 系統內建

#### 前端需求
- **Node.js**: ≥ 18.0
- **npm**: ≥ 9.0

### 開發伺服器設定

#### Laravel 開發伺服器
```bash
php artisan serve               # 啟動在 http://localhost:8000
```

#### Vite 開發伺服器
```bash
npm run dev                     # 啟動 HMR 開發伺服器
```

#### 整合開發環境
```bash
composer run dev                # 同時啟動所有服務
# 包含: Laravel server, Queue worker, Logs, Vite HMR
```

### 設定檔案

#### Vite 設定
```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
})
```

#### Tailwind 設定
```javascript
// tailwind.config.js
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    500: '#9b6eff', // 自訂主色調
                },
            },
        },
    },
}
```

## 測試策略

### 測試框架配置

#### 後端測試 (PHPUnit)
```xml
<!-- phpunit.xml -->
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

#### 前端測試 (Vitest)
```javascript
// vitest.config.js
export default defineConfig({
  test: {
    environment: 'happy-dom',
    coverage: {
      thresholds: {
        global: {
          branches: 80,
          functions: 80,
          lines: 80,
          statements: 80
        }
      }
    }
  }
})
```

### 測試類型

#### 單元測試
- **後端**: 模型、服務、工具類別
- **前端**: Composables、工具函式、服務類別

#### 整合測試
- **API 測試**: 端點功能和資料流
- **元件測試**: Vue 元件行為和事件

#### E2E 測試
- **使用者流程**: 完整的使用者操作路徑
- **跨瀏覽器**: 主要功能的相容性測試

## 建置與部署

### 開發建置

#### 前端資產建置
```bash
npm run build                   # 生產環境建置
npm run dev                     # 開發環境 (HMR)
```

#### 後端最佳化
```bash
php artisan config:cache        # 設定快取
php artisan route:cache         # 路由快取
php artisan view:cache          # 視圖快取
composer install --optimize-autoloader --no-dev
```

### 生產環境需求

#### Web 伺服器設定
- **Nginx** 或 **Apache** 支援
- **PHP-FPM** 或 **mod_php**
- **HTTPS** 支援

#### 環境變數
```bash
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql             # 生產環境建議使用 MySQL
CACHE_DRIVER=redis              # 建議使用 Redis 快取
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 效能最佳化

### 前端效能

#### 建置最佳化
- **Vite**: Tree-shaking 和程式碼分割
- **Tailwind CSS**: PurgeCSS 移除未使用樣式
- **Vue 3**: 優化的響應式系統

#### 執行時最佳化
- **虛擬滾動**: 處理大量圖標列表
- **懶載入**: 按需載入圖標資料
- **快取策略**: 本地快取 API 回應

### 後端效能

#### 資料庫最佳化
```php
// Eloquent 查詢最佳化
User::with(['organizations:id,name'])
    ->select(['id', 'full_name', 'email'])
    ->paginate(20);
```

#### 快取策略
```php
// Redis 快取
Cache::remember('all_emojis', 86400, function () {
    return $this->loadAllEmojiCategories();
});
```

## 安全性考量

### 認證與授權
- **Laravel Sanctum**: SPA Token 認證
- **CSRF 保護**: 表單請求保護
- **CORS 設定**: 適當的跨域請求設定

### 資料驗證
```php
// 後端驗證
$request->validate([
    'icon_data' => 'required|array',
    'icon_data.type' => 'required|in:emoji,heroicons,bootstrap,initials,upload',
]);
```

### 檔案上傳安全
- **檔案類型驗證**: 限制允許的檔案格式
- **檔案大小限制**: 防止過大檔案上傳
- **檔案名稱處理**: 防止路徑遍歷攻擊

## 監控與除錯

### 開發除錯工具

#### Laravel 除錯
```bash
php artisan pail                # 即時日誌監控
php artisan tinker              # 互動式控制台
```

#### Vue.js 除錯
- **Vue DevTools**: 瀏覽器擴充功能
- **Vite HMR**: 熱模組重載
- **Source Maps**: 生產環境除錯

### 記錄與監控

#### 應用程式記錄
```php
// Laravel 記錄
Log::info('Emoji data loaded', ['count' => count($emojis)]);
Log::error('Failed to load icons', ['error' => $e->getMessage()]);
```

#### 效能監控
- **Laravel Debugbar**: 開發環境效能分析
- **Vite Bundle Analyzer**: 前端資產分析

## 升級與維護

### 版本更新策略

#### 主要依賴更新
- **Laravel**: 遵循 LTS 版本更新路徑
- **Vue.js**: 關注主要版本變更
- **Node.js**: 使用 LTS 版本

#### 次要依賴更新
- 定期更新安全性修補
- 測試相容性後批次更新

### 維護檢查清單

#### 每週檢查
- [ ] 檢查安全性更新
- [ ] 執行完整測試套件
- [ ] 檢查效能指標

#### 每月檢查
- [ ] 更新次要版本依賴
- [ ] 檢查程式碼品質指標
- [ ] 備份和恢復測試

## 團隊工具與工作流程

### 程式碼品質工具

#### 自動格式化
```bash
php artisan pint               # PHP 程式碼格式化
npm run lint                   # JavaScript/Vue 檢查
```

#### Git Hooks
- **Pre-commit**: 自動格式化和基本檢查
- **Pre-push**: 執行測試套件

### 開發工作流程

#### 分支策略
```bash
git checkout -b feat/new-feature
git commit -m "feat(scope): description"
gh pr create --title "feat: New Feature" --body "Description..."
```

#### 程式碼審查
- **Pull Request**: 必要的程式碼審查
- **自動化測試**: CI/CD 整合
- **程式碼覆蓋率**: 維持品質標準

---

## 技術決策記錄

### 為什麼選擇 Laravel + Vue.js？

#### Laravel 優勢
- **成熟框架**: 豐富的功能和生態系統
- **快速開發**: Eloquent ORM 和 Artisan 指令
- **安全性**: 內建安全性防護機制

#### Vue.js 優勢
- **學習曲線**: 相對容易學習和使用
- **組合式 API**: 更好的程式碼組織和重用
- **生態系統**: 豐富的第三方套件

### 為什麼使用 Vite 而非 Webpack？

#### Vite 優勢
- **開發效能**: 極快的冷啟動和 HMR
- **現代標準**: 原生 ESM 支援
- **簡化配置**: 更少的設定檔案

### 為什麼選擇 Tailwind CSS？

#### Tailwind 優勢
- **一致性**: 設計系統的一致性
- **客製化**: 高度可客製化的設計標記
- **效能**: PurgeCSS 移除未使用樣式

---

**維護者**: Winston (系統架構師)  
**最後更新**: 2025-08-17  
**下次審查**: 每季度或主要技術升級時