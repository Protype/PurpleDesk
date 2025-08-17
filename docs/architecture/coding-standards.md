# PurpleDesk 程式碼規範

## 概述

本文件定義 PurpleDesk 專案的程式碼標準與最佳實務，確保程式碼品質、可維護性和團隊協作效率。

**更新日期**: 2025-08-17  
**版本**: 1.0  
**適用範圍**: Laravel 後端 + Vue.js 前端

## 通用原則

### 核心原則
1. **可讀性優於簡潔** - 程式碼應該清楚表達意圖
2. **一致性** - 遵循既定的命名和結構模式
3. **漸進式改善** - 每次修改都讓程式碼更好
4. **測試覆蓋** - 重要功能必須有測試保護
5. **文件化** - 複雜邏輯需要適當註解

### 檔案組織
- **單一責任** - 每個檔案/類別/函式只負責一件事
- **模組化** - 使用 `features/` 目錄進行功能分組
- **命名清晰** - 檔案名稱反映其功能和用途

## Laravel 後端規範

### 檔案結構與命名

#### 控制器 (Controllers)
```php
// ✅ 好的例子
class EmojiController extends Controller
{
    public function index(): JsonResponse
    {
        // 單一責任：只處理 emoji 列表
    }
    
    public function show(string $id): JsonResponse
    {
        // RESTful 方法命名
    }
}

// ❌ 避免
class MainController extends Controller
{
    public function getEmojis() {} // 方法名稱不符合 RESTful
    public function handleIcons() {} // 職責過於廣泛
}
```

#### 服務類別 (Services)
```php
// ✅ 好的例子
class EmojiService
{
    /**
     * 取得所有 emoji 資料（一次性載入）
     * 
     * @return array 包含分類和統計的 emoji 資料
     */
    public function getAllEmojis(): array
    {
        return Cache::remember('all_emojis', 86400, function () {
            // 實作邏輯
        });
    }
    
    private function loadEmojiCategory(string $categoryPath): array
    {
        // 私有方法使用 camelCase
    }
}
```

#### 模型 (Models)
```php
// ✅ 好的例子
class User extends Authenticatable
{
    protected $fillable = [
        'full_name',
        'email', 
        'avatar_data',  // JSON 欄位明確命名
    ];
    
    protected $casts = [
        'avatar_data' => 'array',  // 明確的類型轉換
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * 取得使用者的顯示名稱
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->display_name ?? $this->full_name ?? '未命名使用者';
    }
}
```

### 資料庫設計

#### 遷移檔案
```php
// ✅ 好的遷移命名和結構
// 檔名: 2025_08_15_230821_modify_avatar_to_avatar_data_in_users_table.php

Schema::table('users', function (Blueprint $table) {
    $table->text('avatar_data')->nullable()->after('email');
    $table->dropColumn('avatar'); // 明確移除舊欄位
});
```

#### 資料庫命名
- **表格**: 複數形式，snake_case (`users`, `organizations`, `user_teams`)
- **欄位**: snake_case (`full_name`, `avatar_data`, `created_at`)
- **外鍵**: `{表格單數}_id` (`user_id`, `organization_id`)
- **索引**: `{表格}_{欄位}_{類型}_index` (`users_email_unique_index`)

### API 設計

#### RESTful API 規範
```php
// ✅ 標準 RESTful 路由
Route::apiResource('emojis', EmojiController::class);
// GET /api/emojis - 列表
// GET /api/emojis/{id} - 詳細
// POST /api/emojis - 建立
// PUT/PATCH /api/emojis/{id} - 更新
// DELETE /api/emojis/{id} - 刪除

// ✅ 嵌套資源
Route::get('organizations/{organization}/teams', [TeamController::class, 'index']);
```

#### API 回應格式
```php
// ✅ 統一的 API 回應格式
return response()->json([
    'data' => $emojis,
    'meta' => [
        'total' => count($emojis),
        'categories' => $categoryCount,
    ],
    'status' => 'success'
], 200);

// ❌ 避免不一致的格式
return $emojis; // 缺少統一包裝
```

### 錯誤處理
```php
// ✅ 使用專門的例外類別
class EmojiNotFoundException extends ModelNotFoundException
{
    protected $message = 'Emoji not found';
}

// ✅ 統一的錯誤回應
catch (EmojiNotFoundException $e) {
    return response()->json([
        'error' => $e->getMessage(),
        'code' => 'EMOJI_NOT_FOUND'
    ], 404);
}
```

## Vue.js 前端規範

### 元件架構

#### 單一檔案元件結構
```vue
<template>
  <!-- HTML 結構 -->
  <div class="icon-picker">
    <!-- 清晰的 DOM 結構 -->
  </div>
</template>

<script setup>
// ✅ 使用 Composition API
import { ref, computed, onMounted } from 'vue'
import { useIconPickerState } from '../composables/useIconPickerState.js'

// Props 定義
const props = defineProps({
  selectedIcon: {
    type: String,
    default: ''
  },
  iconType: {
    type: String,
    default: 'emoji',
    validator: (value) => ['emoji', 'heroicons', 'bootstrap', 'initials', 'upload'].includes(value)
  }
})

// Emits 定義
const emit = defineEmits(['update:selectedIcon', 'iconSelected'])

// 狀態管理
const { isOpen, togglePicker } = useIconPickerState()

// 計算屬性
const displayIcon = computed(() => {
  // 清楚的計算邏輯
  return props.selectedIcon || 'default-icon'
})

// 生命週期
onMounted(() => {
  // 初始化邏輯
})
</script>

<style scoped>
/* 元件專用樣式 */
.icon-picker {
  @apply w-8 h-8 rounded border-2 border-gray-300;
}
</style>
```

#### 元件命名規範
```javascript
// ✅ 好的元件命名
IconPicker.vue          // PascalCase，清楚描述功能
IconPickerSearch.vue    // 具體的子元件
VirtualScrollGrid.vue   // 通用元件

// ❌ 避免
iconpicker.vue          // 小寫
Icon.vue               // 過於簡短
IPicker.vue            // 縮寫不清楚
```

### Composables 設計

```javascript
// ✅ 好的 composable 結構
// composables/useIconPickerState.js
import { ref, computed } from 'vue'

export function useIconPickerState() {
  // 狀態
  const isOpen = ref(false)
  const activeTab = ref('emoji')
  
  // 方法
  const togglePicker = () => {
    isOpen.value = !isOpen.value
  }
  
  const setActiveTab = (tab) => {
    if (['emoji', 'heroicons', 'bootstrap', 'initials', 'upload'].includes(tab)) {
      activeTab.value = tab
    }
  }
  
  // 計算屬性
  const currentTabLabel = computed(() => {
    const labels = {
      emoji: 'Emoji',
      heroicons: 'HeroIcons',
      bootstrap: 'Bootstrap',
      initials: '文字',
      upload: '上傳'
    }
    return labels[activeTab.value] || 'Unknown'
  })
  
  // 回傳公開的 API
  return {
    isOpen: readonly(isOpen),
    activeTab: readonly(activeTab),
    currentTabLabel,
    togglePicker,
    setActiveTab
  }
}
```

### 服務層設計

```javascript
// ✅ 好的服務類別設計
// services/IconDataLoader.js
export class IconDataLoader {
  constructor() {
    this.cache = new Map()
    this.cacheExpiry = new Map()
    this.defaultCacheDuration = 24 * 60 * 60 * 1000
  }
  
  /**
   * 取得 Emoji 資料
   * 
   * @returns {Promise<Array>} Emoji 資料陣列
   * @throws {Error} 載入失敗時拋出錯誤
   */
  async getEmojiData() {
    const cacheKey = 'emoji-data'
    
    if (this._hasValidCache(cacheKey)) {
      return this._getCache(cacheKey)
    }
    
    try {
      const rawData = await this.iconService.fetchEmojis()
      const processedData = this._processEmojiData(rawData)
      this._setCache(cacheKey, processedData)
      return processedData
    } catch (error) {
      throw this._handleError('Failed to load emoji data', error)
    }
  }
  
  // 私有方法使用 _ 前綴
  _hasValidCache(key) {
    // 實作快取檢查邏輯
  }
}
```

### 狀態管理 (Pinia)

```javascript
// ✅ 好的 store 結構
// stores/auth.js
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const isAuthenticated = ref(false)
  
  // Getters
  const userDisplayName = computed(() => {
    return user.value?.display_name || user.value?.full_name || '訪客'
  })
  
  // Actions
  const login = async (credentials) => {
    try {
      const response = await authService.login(credentials)
      user.value = response.data.user
      isAuthenticated.value = true
      return response
    } catch (error) {
      throw new Error(`登入失敗: ${error.message}`)
    }
  }
  
  const logout = async () => {
    try {
      await authService.logout()
      user.value = null
      isAuthenticated.value = false
    } catch (error) {
      console.error('登出錯誤:', error)
    }
  }
  
  return {
    // State
    user: readonly(user),
    isAuthenticated: readonly(isAuthenticated),
    
    // Getters
    userDisplayName,
    
    // Actions
    login,
    logout
  }
})
```

## CSS 與樣式規範

### Tailwind CSS 使用原則

```vue
<template>
  <!-- ✅ 好的 Tailwind 使用 -->
  <div class="w-8 h-8 rounded border-2 border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors bg-white flex items-center justify-center">
    <!-- 清楚的功能分組 -->
  </div>
  
  <!-- ✅ 使用自定義元件類別避免重複 -->
  <div class="icon-button">
    <!-- 在 style 區段定義 icon-button -->
  </div>
</template>

<style scoped>
/* ✅ 自定義元件樣式 */
.icon-button {
  @apply w-8 h-8 rounded border-2 border-gray-300 hover:border-gray-400 
         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 
         transition-colors bg-white flex items-center justify-center;
}

/* ✅ 複雜動畫使用 CSS */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
```

### 響應式設計
```vue
<template>
  <!-- ✅ 行動優先的響應式設計 -->
  <div class="w-full sm:w-96 p-2 sm:p-4">
    <!-- 小螢幕全寬，大螢幕固定寬度 -->
  </div>
  
  <!-- ✅ 適當的斷點使用 -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <!-- 響應式網格系統 -->
  </div>
</template>
```

## 測試規範

### 前端測試 (Vitest)

```javascript
// ✅ 好的元件測試
// tests/components/IconPicker.test.js
import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import IconPicker from '@/components/common/IconPicker.vue'

describe('IconPicker', () => {
  let wrapper
  
  beforeEach(() => {
    wrapper = mount(IconPicker, {
      props: {
        selectedIcon: 'test-icon',
        iconType: 'emoji'
      }
    })
  })
  
  it('應該正確渲染選中的圖標', () => {
    expect(wrapper.find('.selected-icon').text()).toBe('test-icon')
  })
  
  it('點擊按鈕應該切換面板顯示狀態', async () => {
    const button = wrapper.find('button')
    
    expect(wrapper.find('.icon-panel').exists()).toBe(false)
    
    await button.trigger('click')
    
    expect(wrapper.find('.icon-panel').exists()).toBe(true)
  })
  
  it('選擇圖標應該發出正確的事件', async () => {
    await wrapper.vm.selectIcon('new-icon')
    
    expect(wrapper.emitted('iconSelected')).toBeTruthy()
    expect(wrapper.emitted('iconSelected')[0]).toEqual(['new-icon'])
  })
})
```

### 後端測試 (PHPUnit)

```php
// ✅ 好的功能測試
class EmojiControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_fetch_all_emojis(): void
    {
        // Given
        $this->artisan('db:seed', ['--class' => 'EmojiSeeder']);
        
        // When
        $response = $this->getJson('/api/emojis');
        
        // Then
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'categories',
                    'stats' => ['total_emojis', 'total_categories']
                ]
            ]);
    }
    
    public function test_emoji_service_caches_results(): void
    {
        // Given
        $service = new EmojiService();
        Cache::flush();
        
        // When
        $firstCall = $service->getAllEmojis();
        $secondCall = $service->getAllEmojis();
        
        // Then
        $this->assertEquals($firstCall, $secondCall);
        $this->assertTrue(Cache::has('all_emojis'));
    }
}
```

## 版本控制規範

### Git Commit 訊息

```bash
# ✅ 好的 commit 訊息格式
feat(icon-picker): 建立 IconDataLoader 統一資料載入服務

- 整合 EmojiService 和前端圖標載入
- 實作快取機制提升效能
- 新增錯誤處理和資料驗證
- 建立完整的單元測試覆蓋

Refs: #123

# ✅ 其他好例子
fix(auth): 修正登入後重導向問題
docs(api): 更新 emoji API 文件
test(icon-picker): 新增 VirtualScrollGrid 測試
refactor(ui): 重構 ColorPicker 元件結構
```

### 分支策略

```bash
# ✅ 功能分支命名
feat/icon-picker-refactor-phase1
fix/emoji-loading-performance  
refactor/user-profile-ui
docs/architecture-update

# ✅ 分支流程
git checkout -b feat/icon-picker-refactor-phase1
# 開發功能...
git add .
git commit -m "feat(icon-picker): 完成 Phase 1 服務層重構"
gh pr create --title "feat(icon-picker): Phase 1 服務層重構" --body "詳細說明..."
```

## 效能與最佳化

### 前端效能
```javascript
// ✅ 延遲載入大型資料
const loadEmojiData = async () => {
  if (!emojiData.value) {
    emojiData.value = await iconDataLoader.getEmojiData()
  }
}

// ✅ 虛擬滾動處理大量項目
const VirtualScrollGrid = defineComponent({
  props: {
    items: Array,
    itemHeight: Number,
    visibleCount: Number
  },
  setup(props) {
    const visibleItems = computed(() => {
      // 只渲染可見項目
    })
  }
})

// ✅ 防抖搜尋
import { debounce } from 'lodash-es'

const searchIcons = debounce((query) => {
  // 搜尋邏輯
}, 300)
```

### 後端效能
```php
// ✅ 適當的快取策略
public function getAllEmojis()
{
    return Cache::remember('all_emojis', 86400, function () {
        return $this->loadAllEmojiCategories();
    });
}

// ✅ 資料庫查詢最佳化
public function getUsersWithOrganizations()
{
    return User::with(['organizations:id,name'])
        ->select(['id', 'full_name', 'email'])
        ->paginate(20);
}

// ✅ API 回應最佳化
public function index()
{
    $emojis = $this->emojiService->getAllEmojis();
    
    return response()->json($emojis)
        ->header('Cache-Control', 'public, max-age=3600');
}
```

## 安全性規範

### 資料驗證
```php
// ✅ 後端驗證
public function store(Request $request)
{
    $validated = $request->validate([
        'icon_data' => 'required|array',
        'icon_data.type' => 'required|in:emoji,heroicons,bootstrap,initials,upload',
        'icon_data.value' => 'required|string|max:255',
    ]);
    
    // 處理驗證過的資料
}
```

```javascript
// ✅ 前端驗證
const validateIconData = (iconData) => {
  const allowedTypes = ['emoji', 'heroicons', 'bootstrap', 'initials', 'upload']
  
  if (!iconData || typeof iconData !== 'object') {
    throw new Error('Invalid icon data format')
  }
  
  if (!allowedTypes.includes(iconData.type)) {
    throw new Error('Invalid icon type')
  }
  
  return true
}
```

### 檔案上傳安全
```php
// ✅ 安全的圖片上傳
public function uploadAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ]);
    
    $file = $request->file('avatar');
    
    // 檢查檔案類型
    if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
        throw new InvalidFileTypeException();
    }
    
    // 安全的檔案名稱
    $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
    
    return $file->storeAs('avatars/users', $filename, 'public');
}
```

## 文件與註解

### 函式文件
```php
/**
 * 取得使用者的完整頭像資料
 * 
 * 包含頭像類型、值和相關設定。如果使用者沒有設定頭像，
 * 則回傳預設的文字頭像（使用使用者姓名的第一個字元）。
 * 
 * @param User $user 使用者實例
 * @param array $options 選項設定
 * @return array{type: string, value: string, color?: string} 頭像資料
 * 
 * @throws InvalidUserException 當使用者資料無效時
 * 
 * @example
 * $avatarData = $this->getUserAvatarData($user, ['includeColor' => true]);
 * // 回傳: ['type' => 'initials', 'value' => 'A', 'color' => '#FF5733']
 */
public function getUserAvatarData(User $user, array $options = []): array
```

```javascript
/**
 * 載入並快取圖標庫資料
 * 
 * 並行載入 HeroIcons 和 Bootstrap Icons，合併後回傳統一格式。
 * 載入的資料會被快取 24 小時以提升效能。
 * 
 * @returns {Promise<Array<IconItem>>} 合併的圖標資料陣列
 * @throws {Error} 當載入失敗時拋出錯誤
 * 
 * @example
 * const icons = await iconDataLoader.getIconLibraryData()
 * // icons: [{ name: 'home', type: 'heroicons', ... }, ...]
 */
async getIconLibraryData() {
  // 實作...
}
```

### README 檔案結構
```markdown
# IconPicker 元件

## 概述
IconPicker 是 PurpleDesk 的核心 UI 元件，支援 5 種圖標類型選擇。

## 使用方式
```vue
<IconPicker 
  v-model:selected-icon="userIcon"
  icon-type="emoji"
  @icon-selected="handleIconSelection"
/>
```

## API 文件
### Props
- `selectedIcon` (String) - 當前選中的圖標
- `iconType` (String) - 圖標類型，可選值：'emoji', 'heroicons', 'bootstrap', 'initials', 'upload'

### Events
- `iconSelected` - 當圖標被選中時發出

## 測試
```bash
npm run test:coverage
```
```

---

## 工具與檢查

### 自動化檢查
- **PHP**: Laravel Pint (`php artisan pint`)
- **JavaScript**: ESLint (透過 Vite 整合)
- **Vue**: Vue ESLint 規則
- **Tailwind**: 透過 PostCSS 檢查

### VS Code 設定
推薦的 `.vscode/settings.json`:
```json
{
  "editor.codeActionsOnSave": {
    "source.fixAll.eslint": true
  },
  "php.format.enable": true,
  "php.format.rules": {
    "psr12": true
  }
}
```

---

**維護責任**: 開發團隊全體  
**審查頻率**: 每月或重大重構後  
**執行工具**: Laravel Pint, ESLint, Vitest, PHPUnit