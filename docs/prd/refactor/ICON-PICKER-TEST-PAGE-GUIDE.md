# Icon Picker 測試頁面 - 實作指南

**Story ID**: ST-022  
**Epic**: EP-001 (建立安全網和基礎架構)  
**PRD 參考**: [ICON-PICKER-BROWNFIELD-PRD.md](./ICON-PICKER-BROWNFIELD-PRD.md)  
**建立日期**: 2025-08-17  
**狀態**: 待實作  

## 📋 實作概述

### 🎯 目標
建立一個專用測試頁面，支援 Icon Picker 重構過程中各 Phase/Story 的功能驗證，提供即時測試環境以確保開發品質。

### 📊 整合點
- **IconPickerProxy**: 版本切換機制
- **IconPickerDevTool**: 開發者工具
- **現有路由系統**: Laravel Blade + Vue 3
- **Phase 進度追蹤**: 顯示重構進度資訊

---

## 🔧 技術規格

### 路由設定
```php
// routes/web.php
Route::get('/test/icon-picker', function () {
    return view('test.icon-picker');
})->name('test.icon-picker');
```

### Blade 視圖結構
```php
<!-- resources/views/test/icon-picker.blade.php -->
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icon Picker 測試頁面</title>
    @vite(['resources/css/app.css', 'resources/js/test-icon-picker.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div id="test-icon-picker-app"></div>
</body>
</html>
```

### Vue 應用進入點
```javascript
// resources/js/test-icon-picker.js
import { createApp } from 'vue'
import TestIconPickerApp from './components/test/TestIconPickerApp.vue'

const app = createApp(TestIconPickerApp)
app.mount('#test-icon-picker-app')
```

---

## 🎨 主要元件架構

### 1. TestIconPickerApp.vue (主容器)
```vue
<template>
  <div class="test-app-container">
    <!-- 頁面標題 -->
    <TestPageHeader />
    
    <!-- 測試控制面板 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
      <!-- Phase 進度面板 -->
      <PhaseProgressPanel 
        :current-phase="currentPhase"
        :completed-stories="completedStories"
        :total-stories="totalStories"
      />
      
      <!-- 測試控制區 -->
      <TestControlPanel 
        :selected-icon="selectedIcon"
        @open-picker="handleOpenPicker"
        @reset="handleReset"
      />
      
      <!-- 圖標預覽區 -->
      <IconPreviewPanel 
        :selected-icon="selectedIcon"
        :icon-type="iconType"
        :background-color="backgroundColor"
      />
    </div>
    
    <!-- Icon Picker -->
    <IconPickerProxy
      v-if="showPicker"
      v-model="selectedIcon"
      v-model:icon-type="iconType"
      @close="showPicker = false"
      @background-color-change="backgroundColor = $event"
    />
    
    <!-- 開發者工具 -->
    <IconPickerDevTool />
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import IconPickerProxy from '../../features/icon-picker/components/IconPickerProxy.vue'
import IconPickerDevTool from '../../features/icon-picker/components/IconPickerDevTool.vue'
import TestPageHeader from './TestPageHeader.vue'
import PhaseProgressPanel from './PhaseProgressPanel.vue'
import TestControlPanel from './TestControlPanel.vue'
import IconPreviewPanel from './IconPreviewPanel.vue'

export default {
  name: 'TestIconPickerApp',
  components: {
    IconPickerProxy,
    IconPickerDevTool,
    TestPageHeader,
    PhaseProgressPanel,
    TestControlPanel,
    IconPreviewPanel
  },
  setup() {
    // 測試狀態
    const showPicker = ref(false)
    const selectedIcon = ref('')
    const iconType = ref('')
    const backgroundColor = ref('')
    
    // Phase 進度資料
    const currentPhase = ref('Phase 2')
    const completedStories = ref([
      'ST-001', 'ST-002', 'ST-003', 'ST-004', 
      'ST-005', 'ST-006', 'ST-007', 'ST-008',
      'ST-009', 'ST-010', 'ST-011'
    ])
    const totalStories = ref(22)
    
    const handleOpenPicker = () => {
      showPicker.value = true
    }
    
    const handleReset = () => {
      selectedIcon.value = ''
      iconType.value = ''
      backgroundColor.value = ''
    }
    
    return {
      showPicker,
      selectedIcon,
      iconType,
      backgroundColor,
      currentPhase,
      completedStories,
      totalStories,
      handleOpenPicker,
      handleReset
    }
  }
}
</script>
```

### 2. PhaseProgressPanel.vue (進度追蹤)
```vue
<template>
  <div class="bg-white rounded-lg shadow-sm border p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
      🚀 重構進度追蹤
    </h3>
    
    <!-- 當前 Phase -->
    <div class="mb-4">
      <div class="text-sm text-gray-600">當前階段</div>
      <div class="text-xl font-bold text-blue-600" data-testid="current-phase">
        {{ currentPhase }}
      </div>
    </div>
    
    <!-- 進度條 -->
    <div class="mb-4">
      <div class="flex justify-between text-sm text-gray-600 mb-1">
        <span>總進度</span>
        <span>{{ completedStories.length }}/{{ totalStories }}</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div 
          class="bg-green-600 h-2 rounded-full transition-all duration-300"
          :style="{ width: progressPercentage + '%' }"
        ></div>
      </div>
      <div class="text-xs text-gray-500 mt-1">
        {{ Math.round(progressPercentage) }}% 完成
      </div>
    </div>
    
    <!-- 已完成 Stories -->
    <div class="mb-4">
      <div class="text-sm font-medium text-gray-700 mb-2">
        ✅ 已完成 Stories
      </div>
      <div class="flex flex-wrap gap-1" data-testid="completed-stories">
        <span 
          v-for="story in completedStories" 
          :key="story"
          class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-md"
        >
          {{ story }}
        </span>
      </div>
    </div>
    
    <!-- 進行中/待辦 Stories -->
    <div>
      <div class="text-sm font-medium text-gray-700 mb-2">
        ⏳ 進行中/待辦
      </div>
      <div class="flex flex-wrap gap-1" data-testid="pending-stories">
        <span 
          v-for="story in pendingStories" 
          :key="story"
          class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-md"
        >
          {{ story }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'PhaseProgressPanel',
  props: {
    currentPhase: String,
    completedStories: Array,
    totalStories: Number
  },
  setup(props) {
    const progressPercentage = computed(() => {
      return (props.completedStories.length / props.totalStories) * 100
    })
    
    const pendingStories = computed(() => {
      const allStories = []
      for (let i = 1; i <= props.totalStories; i++) {
        const storyId = `ST-${String(i).padStart(3, '0')}`
        if (!props.completedStories.includes(storyId)) {
          allStories.push(storyId)
        }
      }
      return allStories
    })
    
    return {
      progressPercentage,
      pendingStories
    }
  }
}
</script>
```

### 3. TestControlPanel.vue (測試控制)
```vue
<template>
  <div class="bg-white rounded-lg shadow-sm border p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
      🎛️ 測試控制
    </h3>
    
    <!-- 主要操作按鈕 -->
    <div class="space-y-3 mb-6">
      <button
        @click="$emit('open-picker')"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors"
        data-testid="open-icon-picker"
      >
        🎨 開啟 Icon Picker
      </button>
      
      <button
        @click="$emit('reset')"
        class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg font-medium transition-colors"
        data-testid="reset-selection"
      >
        🔄 重置選擇
      </button>
    </div>
    
    <!-- 測試快捷功能 -->
    <div class="border-t pt-4">
      <div class="text-sm font-medium text-gray-700 mb-3">快速測試</div>
      <div class="grid grid-cols-2 gap-2">
        <button
          @click="testEmojiSelection"
          class="px-3 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-xs rounded-md transition-colors"
          data-testid="test-emoji"
        >
          😀 測試 Emoji
        </button>
        <button
          @click="testIconLibrary"
          class="px-3 py-2 bg-purple-100 hover:bg-purple-200 text-purple-800 text-xs rounded-md transition-colors"
          data-testid="test-icons"
        >
          ⭐ 測試圖標庫
        </button>
        <button
          @click="testTextIcon"
          class="px-3 py-2 bg-green-100 hover:bg-green-200 text-green-800 text-xs rounded-md transition-colors"
          data-testid="test-text"
        >
          📝 測試文字
        </button>
        <button
          @click="testColorPicker"
          class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-800 text-xs rounded-md transition-colors"
          data-testid="test-color"
        >
          🎨 測試顏色
        </button>
      </div>
    </div>
    
    <!-- 當前選擇資訊 -->
    <div class="border-t pt-4 mt-4" v-if="selectedIcon">
      <div class="text-sm font-medium text-gray-700 mb-2">當前選擇</div>
      <div class="text-xs text-gray-600 space-y-1">
        <div>圖標: {{ selectedIcon || '未選擇' }}</div>
        <div>類型: {{ iconType || '未知' }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TestControlPanel',
  props: {
    selectedIcon: String,
    iconType: String
  },
  emits: ['open-picker', 'reset'],
  setup(props, { emit }) {
    const testEmojiSelection = () => {
      // 觸發 emoji 測試邏輯
      emit('open-picker')
      // 可以添加自動選擇 emoji 頁籤的邏輯
    }
    
    const testIconLibrary = () => {
      emit('open-picker')
      // 可以添加自動選擇 icons 頁籤的邏輯
    }
    
    const testTextIcon = () => {
      emit('open-picker')
      // 可以添加自動選擇 text 頁籤的邏輯
    }
    
    const testColorPicker = () => {
      emit('open-picker')
      // 可以添加自動選擇 color 頁籤的邏輯
    }
    
    return {
      testEmojiSelection,
      testIconLibrary,
      testTextIcon,
      testColorPicker
    }
  }
}
</script>
```

### 4. IconPreviewPanel.vue (預覽顯示)
```vue
<template>
  <div class="bg-white rounded-lg shadow-sm border p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
      👁️ 圖標預覽
    </h3>
    
    <!-- 預覽區域 -->
    <div class="mb-6">
      <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
        <div 
          v-if="selectedIcon"
          class="inline-flex items-center justify-center w-16 h-16 rounded-lg text-2xl"
          :style="previewStyle"
          data-testid="icon-preview"
        >
          <component 
            v-if="iconType === 'icon'" 
            :is="selectedIcon"
            class="w-8 h-8"
          />
          <span v-else>{{ selectedIcon }}</span>
        </div>
        <div v-else class="text-gray-400">
          <div class="w-16 h-16 bg-gray-100 rounded-lg mx-auto mb-2"></div>
          <div class="text-sm">未選擇圖標</div>
        </div>
      </div>
    </div>
    
    <!-- 詳細資訊 -->
    <div class="space-y-3 text-sm">
      <div class="flex justify-between">
        <span class="text-gray-600">圖標內容:</span>
        <span class="font-mono text-gray-900" data-testid="icon-content">
          {{ selectedIcon || '無' }}
        </span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600">圖標類型:</span>
        <span class="font-mono text-gray-900" data-testid="icon-type">
          {{ iconType || '無' }}
        </span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600">背景顏色:</span>
        <span class="font-mono text-gray-900" data-testid="background-color">
          {{ backgroundColor || '無' }}
        </span>
      </div>
    </div>
    
    <!-- 測試資訊 -->
    <div class="border-t pt-4 mt-4">
      <div class="text-sm font-medium text-gray-700 mb-2">測試資訊</div>
      <div class="text-xs text-gray-600 space-y-1">
        <div>最後更新: {{ lastUpdated }}</div>
        <div>當前版本: {{ currentVersion }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref, watch } from 'vue'

export default {
  name: 'IconPreviewPanel',
  props: {
    selectedIcon: String,
    iconType: String,
    backgroundColor: String
  },
  setup(props) {
    const lastUpdated = ref(new Date().toLocaleTimeString())
    
    const previewStyle = computed(() => {
      return {
        backgroundColor: props.backgroundColor || '#f3f4f6',
        color: props.backgroundColor ? getContrastColor(props.backgroundColor) : '#374151'
      }
    })
    
    const currentVersion = computed(() => {
      if (typeof window !== 'undefined') {
        const savedSetting = localStorage.getItem('iconpicker-use-original')
        return savedSetting === 'false' ? 'IconPicker (新版)' : 'IconPickerOri (原版)'
      }
      return '未知'
    })
    
    // 監聽選擇變化，更新時間戳
    watch([() => props.selectedIcon, () => props.iconType, () => props.backgroundColor], () => {
      lastUpdated.value = new Date().toLocaleTimeString()
    })
    
    const getContrastColor = (hexColor) => {
      // 簡單的對比色計算
      const rgb = parseInt(hexColor.slice(1), 16)
      const r = (rgb >> 16) & 0xff
      const g = (rgb >> 8) & 0xff
      const b = (rgb >> 0) & 0xff
      const luma = 0.2126 * r + 0.7152 * g + 0.0722 * b
      return luma < 128 ? '#ffffff' : '#000000'
    }
    
    return {
      lastUpdated,
      previewStyle,
      currentVersion
    }
  }
}
</script>
```

### 5. TestPageHeader.vue (頁面標題)
```vue
<template>
  <header class="bg-white border-b border-gray-200">
    <div class="px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            🧪 Icon Picker 測試頁面
          </h1>
          <p class="text-sm text-gray-600 mt-1">
            Icon Picker 重構專案的功能驗證測試環境
          </p>
        </div>
        
        <div class="flex items-center space-x-4">
          <!-- 返回連結 -->
          <a 
            href="/" 
            class="text-gray-600 hover:text-gray-900 text-sm font-medium"
          >
            ← 返回主頁
          </a>
          
          <!-- 文件連結 -->
          <a 
            href="/docs/prd/refactor/ICON-PICKER-STORIES.md" 
            target="_blank"
            class="text-blue-600 hover:text-blue-700 text-sm font-medium"
          >
            📚 查看 Stories
          </a>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'TestPageHeader'
}
</script>
```

---

## 📁 檔案結構

```
resources/
├── js/
│   ├── test-icon-picker.js              # 測試頁面進入點
│   └── components/
│       └── test/
│           ├── TestIconPickerApp.vue    # 主應用容器
│           ├── TestPageHeader.vue       # 頁面標題
│           ├── PhaseProgressPanel.vue   # 進度追蹤面板
│           ├── TestControlPanel.vue     # 測試控制面板
│           └── IconPreviewPanel.vue     # 圖標預覽面板
└── views/
    └── test/
        └── icon-picker.blade.php        # Blade 模板

routes/
└── web.php                              # 路由定義

tests/
└── e2e/
    └── test-page.e2e.test.js           # E2E 測試
```

---

## 🚀 實作步驟

### Phase 1: 基礎設置
1. **建立路由**: 在 `routes/web.php` 新增測試路由
2. **建立 Blade 視圖**: 建立基礎的 HTML 模板
3. **建立 Vue 進入點**: 設置 Vue 應用掛載

### Phase 2: 核心元件
1. **主應用容器**: 實作 `TestIconPickerApp.vue`
2. **進度追蹤**: 實作 `PhaseProgressPanel.vue`
3. **測試控制**: 實作 `TestControlPanel.vue`

### Phase 3: 功能整合
1. **圖標預覽**: 實作 `IconPreviewPanel.vue`
2. **頁面標題**: 實作 `TestPageHeader.vue`
3. **整合現有元件**: 連接 IconPickerProxy 和 IconPickerDevTool

### Phase 4: 測試驗證
1. **功能測試**: 驗證所有基礎功能
2. **版本切換測試**: 確保新舊版本切換正常
3. **E2E 測試**: 建立自動化測試

---

## 🎯 驗收標準

### 功能驗收
- [ ] 測試頁面可正常載入，無 console 錯誤
- [ ] Icon Picker 可正常開啟和關閉
- [ ] 圖標選擇後預覽區域正確顯示
- [ ] 版本切換功能正常運作
- [ ] Phase 進度資訊準確顯示

### 品質驗收
- [ ] 頁面載入速度 < 2 秒
- [ ] 響應式設計支援手機和桌面
- [ ] 無障礙性基本要求達標
- [ ] 程式碼品質符合專案標準

### 整合驗收
- [ ] 與現有架構無衝突
- [ ] IconPickerProxy 整合無問題
- [ ] IconPickerDevTool 整合無問題
- [ ] 不影響主應用效能

---

## 📝 注意事項

### 開發注意
1. **保持簡潔**: 測試頁面重點在功能驗證，避免過度複雜化
2. **版本相容**: 確保新舊版本 Icon Picker 都能正常運作
3. **錯誤處理**: 添加適當的錯誤提示和處理
4. **效能考量**: 避免影響主應用的效能

### 維護注意
1. **及時更新**: 隨著重構進度更新進度資訊
2. **測試資料**: 保持測試資料的準確性
3. **文件同步**: 與其他重構文件保持同步
4. **清理程式碼**: 定期清理不必要的測試程式碼

---

**相關文件**:
- [ICON-PICKER-STORIES.md](./ICON-PICKER-STORIES.md) - ST-022 詳細定義
- [ICON-PICKER-TEST-PLAN.md](./ICON-PICKER-TEST-PLAN.md) - 測試頁面驗證計劃
- [ICON-PICKER-BROWNFIELD-PRD.md](./ICON-PICKER-BROWNFIELD-PRD.md) - 主要需求文件