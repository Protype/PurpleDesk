<template>
  <div class="icon-picker-test-page">
    <!-- Phase 進度面板 -->
    <div class="phase-progress">
      <h3 class="text-lg font-semibold text-gray-800 mb-3">📋 重構進度 (Phase 0-2 已完成)</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="progress-item">
          <span class="text-lg status-completed">✅</span>
          <span class="text-sm">EP-001: 建立安全網和基礎架構</span>
        </div>
        <div class="progress-item">
          <span class="text-lg status-completed">✅</span>
          <span class="text-sm">EP-002: 服務層重構</span>
        </div>
        <div class="progress-item">
          <span class="text-lg status-completed">✅</span>
          <span class="text-sm">EP-003: VirtualScrollGrid 共用元件</span>
        </div>
        <div class="progress-item">
          <span class="text-lg status-current">🔄</span>
          <span class="text-sm">ST-022: 建立新測試頁面</span>
        </div>
        <div class="progress-item">
          <span class="text-lg status-pending">⏳</span>
          <span class="text-sm">EP-004: 面板元件拆分</span>
        </div>
        <div class="progress-item">
          <span class="text-lg status-pending">⏳</span>
          <span class="text-sm">EP-005: 邏輯抽離和整合</span>
        </div>
      </div>
      
      <div class="mt-4 p-3 bg-blue-50 rounded-lg">
        <h4 class="text-sm font-semibold text-blue-800 mb-1">📖 下一步開發計劃：</h4>
        <p class="text-xs text-blue-700">
          完成測試頁面後，將開始 EP-004 面板元件拆分，實作 TextIconPanel、EmojiPanel、IconLibraryPanel 等獨立元件。
        </p>
      </div>
    </div>

    <!-- 直接測試 HomeIcon -->
    <div class="mb-4 p-4 bg-yellow-100 rounded">
      <div class="text-sm font-semibold mb-2">直接測試 HomeIcon (不透過 IconDisplay):</div>
      <div class="flex gap-4 items-center">
        <div class="flex items-center gap-1">
          <HomeIcon class="h-6 w-6 text-blue-500" />
          <span class="text-xs">outline</span>
        </div>
        <div class="flex items-center gap-1">
          <HomeIconSolid class="h-6 w-6 text-red-500" />
          <span class="text-xs">solid</span>
        </div>
        <div class="flex items-center gap-1">
          <component v-if="dynamicIconComponent" :is="dynamicIconComponent" class="h-6 w-6 text-green-500" />
          <span class="text-xs">動態載入 UserIcon</span>
        </div>
      </div>
    </div>

    <!-- 圖標預覽區域 -->
    <div class="icon-preview">
      <div v-if="selectedIcon">
        <div class="mb-2">
          <IconDisplay 
            :icon-data="selectedIcon" 
            size="2xl"
            class="mx-auto"
          />
        </div>
        
        <!-- 測試固定的 heroicon -->
        <div class="mt-4 p-3 bg-gray-100 rounded">
          <div class="text-xs text-gray-600 mb-2">測試固定 heroicons:</div>
          <div class="flex gap-2 items-center">
            <IconDisplay 
              :icon-data="{ type: 'heroicons', icon: 'HomeIcon', variant: 'outline' }" 
              size="md"
            />
            <span class="text-xs">HomeIcon outline</span>
            
            <IconDisplay 
              :icon-data="{ type: 'heroicons', icon: 'HomeIcon', variant: 'solid' }" 
              size="md"
            />
            <span class="text-xs">HomeIcon solid</span>
            
            <IconDisplay 
              :icon-data="{ type: 'heroicons', icon: 'UserIcon', variant: 'outline' }" 
              size="md"
            />
            <span class="text-xs">UserIcon outline</span>
          </div>
        </div>
        <div class="text-sm text-gray-600">
          <div><strong>類型：</strong>{{ selectedIcon.type || 'unknown' }}</div>
          
          <!-- Emoji 相關 -->
          <div v-if="selectedIcon.emoji"><strong>Emoji：</strong>{{ selectedIcon.emoji }}</div>
          <div v-if="selectedIcon.name && selectedIcon.type === 'emoji'"><strong>名稱：</strong>{{ selectedIcon.name }}</div>
          
          <!-- 文字相關 -->
          <div v-if="selectedIcon.text"><strong>文字：</strong>{{ selectedIcon.text }}</div>
          <div v-if="selectedIcon.backgroundColor"><strong>背景色：</strong>{{ selectedIcon.backgroundColor }}</div>
          
          <!-- 圖標相關 -->
          <div v-if="selectedIcon.icon"><strong>圖標：</strong>{{ selectedIcon.icon }}</div>
          <div v-if="selectedIcon.variant"><strong>變體：</strong>{{ selectedIcon.variant }}</div>
          
          <!-- 圖片相關 -->
          <div v-if="selectedIcon.url"><strong>圖片URL：</strong>{{ selectedIcon.url.substring(0, 50) }}...</div>
          
          <!-- 舊版相容性 -->
          <div v-if="selectedIcon.class"><strong>CSS Class：</strong>{{ selectedIcon.class }}</div>
          
          <!-- 完整資料結構 -->
          <details class="mt-2">
            <summary class="cursor-pointer text-xs text-blue-600">查看完整資料結構</summary>
            <pre class="text-xs bg-gray-100 p-2 mt-1 rounded overflow-auto">{{ JSON.stringify(selectedIcon, null, 2) }}</pre>
          </details>
        </div>
      </div>
      <div v-else class="text-gray-500">
        <div class="text-4xl mb-2">🎯</div>
        <div>點擊下方按鈕選擇圖標</div>
      </div>
    </div>

    <!-- 觸發按鈕 -->
    <div class="text-center mb-6">
      <button 
        @click="openIconPicker"
        class="trigger-button"
      >
        🎨 開啟 Icon Picker
      </button>
      
      <div class="mt-4 text-sm text-gray-600">
        <div>當前使用版本：<strong>{{ currentVersion }}</strong></div>
        <div class="mt-1">
          <span class="text-xs bg-gray-100 px-2 py-1 rounded">
            使用右下角開發工具可切換版本
          </span>
        </div>
      </div>
    </div>

    <!-- Icon Picker -->
    <IconPickerProxy 
      v-model="selectedIcon"
      :is-open="isPickerOpen"
      @close="closeIconPicker"
      @update:modelValue="handleIconSelected"
    />

    <!-- 測試功能區域 -->
    <div class="test-actions">
      <h3 class="text-lg font-semibold text-gray-800 mb-3">🧪 測試功能</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <button
          @click="testEmojiSelection"
          class="test-button"
          :class="{ 'active': testingEmoji }"
        >
          😀 測試 Emoji 選擇
        </button>
        
        <button
          @click="testIconLibrary"
          class="test-button"
          :class="{ 'active': testingIconLibrary }"
        >
          ⭐ 測試圖標庫
        </button>
        
        <button
          @click="testTextIcon"
          class="test-button"
          :class="{ 'active': testingTextIcon }"
        >
          🔤 測試文字圖標
        </button>
        
        <button
          @click="testHeroIcon"
          class="test-button"
          :class="{ 'active': testingHeroIcon }"
        >
          🦸 測試 Hero Icon
        </button>
        
        <button
          @click="testFormatConsistency"
          class="test-button"
          :class="{ 'active': testingFormat }"
        >
          📊 測試格式統一性
        </button>
        
        <button
          @click="clearSelection"
          class="test-button clear-button"
        >
          🗑️ 清除選擇
        </button>
      </div>
      
      <div v-if="testResult" class="mt-4 p-3 bg-green-50 rounded-lg">
        <div class="text-sm font-semibold text-green-800">✅ 測試結果：</div>
        <div class="text-xs text-green-700 mt-1">{{ testResult }}</div>
      </div>
    </div>

    <!-- 開發者工具 -->
    <IconPickerDevTool />
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import IconPickerProxy from '@/features/icon-picker/demo/IconPickerProxy.vue'
import IconPickerDevTool from '@/features/icon-picker/demo/IconPickerDevTool.vue'
import IconDisplay from '@/components/common/IconDisplay.vue'
import { HomeIcon } from '@heroicons/vue/outline'
import { HomeIcon as HomeIconSolid } from '@heroicons/vue/solid'

export default {
  name: 'IconPickerTestPage',
  components: {
    IconPickerProxy,
    IconPickerDevTool,
    IconDisplay,
    HomeIcon,
    HomeIconSolid
  },
  setup() {
    const selectedIcon = ref(null)
    const isPickerOpen = ref(false)
    const testingEmoji = ref(false)
    const testingIconLibrary = ref(false)
    const testingTextIcon = ref(false)
    const testingHeroIcon = ref(false)
    const testingFormat = ref(false)
    const testResult = ref('')
    
    // 動態載入測試
    const dynamicIconComponent = ref(null)
    
    const loadDynamicIcon = async () => {
      try {
        const iconName = 'UserIcon'
        const variant = 'outline'
        console.log(`使用新的 heroicons loader 載入: ${iconName}-${variant}`)
        
        // 使用新的 heroicons loader
        const { loadHeroicon } = await import('@/utils/heroicons/heroiconsLoader.js')
        const component = await loadHeroicon(iconName, variant)
        
        if (component) {
          console.log('載入成功:', component)
          dynamicIconComponent.value = component
        } else {
          console.warn('載入失敗: 圖標未找到')
          dynamicIconComponent.value = null
        }
      } catch (error) {
        console.error('動態載入失敗:', error)
        dynamicIconComponent.value = null
      }
    }
    
    // 頁面載入時執行
    loadDynamicIcon()
    
    const currentVersion = computed(() => {
      if (typeof window === 'undefined') return 'Unknown'
      
      const savedSetting = localStorage.getItem('iconpicker-use-original')
      if (savedSetting === 'true') return 'IconPickerOri (原版)'
      if (savedSetting === 'false') return 'IconPicker (新版)'
      
      const urlParams = new URLSearchParams(window.location.search)
      if (urlParams.get('iconpicker') === 'original') return 'IconPickerOri (原版)'
      if (urlParams.get('iconpicker') === 'new') return 'IconPicker (新版)'
      
      return 'IconPickerOri (原版，預設)'
    })
    
    const openIconPicker = () => {
      isPickerOpen.value = true
    }
    
    const closeIconPicker = () => {
      isPickerOpen.value = false
    }
    
    const handleIconSelected = (icon) => {
      selectedIcon.value = icon
      closeIconPicker()
      
      // 清除測試狀態
      testingEmoji.value = false
      testingIconLibrary.value = false
      testingTextIcon.value = false
      testingHeroIcon.value = false
      testingFormat.value = false
      
      // 設置測試結果
      if (icon) {
        testResult.value = `成功選擇 ${icon.type || 'unknown'} 類型圖標`
        setTimeout(() => {
          testResult.value = ''
        }, 3000)
      }
    }
    
    const testEmojiSelection = () => {
      testingEmoji.value = true
      testingIconLibrary.value = false
      testingTextIcon.value = false
      testingHeroIcon.value = false
      testingFormat.value = false
      
      // 清除當前選擇，直接打開選擇器測試 emoji
      selectedIcon.value = null
      openIconPicker()
    }
    
    const testIconLibrary = () => {
      testingIconLibrary.value = true
      testingEmoji.value = false
      testingTextIcon.value = false
      testingHeroIcon.value = false
      testingFormat.value = false
      
      // 清除當前選擇，直接打開選擇器測試圖標庫
      selectedIcon.value = null
      openIconPicker()
    }
    
    const testTextIcon = () => {
      testingTextIcon.value = true
      testingEmoji.value = false
      testingIconLibrary.value = false
      testingHeroIcon.value = false
      testingFormat.value = false
      
      // 清除當前選擇，直接打開選擇器測試文字圖標
      selectedIcon.value = null
      openIconPicker()
    }
    
    const testHeroIcon = () => {
      testingHeroIcon.value = true
      testingEmoji.value = false
      testingIconLibrary.value = false
      testingTextIcon.value = false
      testingFormat.value = false
      
      // 清除當前選擇，直接打開選擇器測試 Hero Icon
      selectedIcon.value = null
      openIconPicker()
    }
    
    const testFormatConsistency = () => {
      testingFormat.value = true
      testingEmoji.value = false
      testingIconLibrary.value = false
      testingTextIcon.value = false
      testingHeroIcon.value = false
      
      // 輪流展示不同格式的例子
      const examples = [
        { type: 'emoji', emoji: '🎯', name: 'direct hit' },
        { type: 'text', text: 'A', backgroundColor: '#ef4444' },
        { type: 'heroicons', icon: 'UserIcon', variant: 'solid' },
        { type: 'bootstrap-icons', icon: 'bi-heart-fill' }
      ]
      
      let currentIndex = 0
      selectedIcon.value = examples[currentIndex]
      
      const interval = setInterval(() => {
        currentIndex = (currentIndex + 1) % examples.length
        selectedIcon.value = examples[currentIndex]
        
        if (currentIndex === 0) {
          clearInterval(interval)
          testResult.value = '格式統一性測試完成 - 所有類型都使用統一的物件格式'
          setTimeout(() => {
            testResult.value = ''
            testingFormat.value = false
          }, 3000)
        }
      }, 1500)
    }
    
    const clearSelection = () => {
      selectedIcon.value = null
      testingEmoji.value = false
      testingIconLibrary.value = false
      testingTextIcon.value = false
      testingHeroIcon.value = false
      testingFormat.value = false
      testResult.value = '已清除選擇'
      setTimeout(() => {
        testResult.value = ''
      }, 2000)
    }
    
    return {
      selectedIcon,
      isPickerOpen,
      currentVersion,
      testingEmoji,
      testingIconLibrary,
      testingTextIcon,
      testingHeroIcon,
      testingFormat,
      testResult,
      dynamicIconComponent,
      openIconPicker,
      closeIconPicker,
      handleIconSelected,
      testEmojiSelection,
      testIconLibrary,
      testTextIcon,
      testHeroIcon,
      testFormatConsistency,
      clearSelection
    }
  }
}
</script>

<style scoped>
.test-button {
  @apply px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm rounded-md transition-colors border border-gray-300;
}

.test-button.active {
  @apply bg-blue-100 text-blue-800 border-blue-300;
}

.test-button:hover {
  @apply transform scale-105;
}

.test-button.clear-button {
  @apply bg-red-100 text-red-800 border-red-300;
}

.test-button.clear-button:hover {
  @apply bg-red-200;
}
</style>