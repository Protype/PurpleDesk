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

    <!-- 圖標預覽區域 -->
    <div class="icon-preview">
      <div v-if="selectedIcon">
        <div class="mb-2">
          <IconDisplay 
            :icon-data="selectedIcon" 
            :size="60"
            class="mx-auto"
          />
        </div>
        <div class="text-sm text-gray-600">
          <div><strong>類型：</strong>{{ selectedIcon.type || 'unknown' }}</div>
          <div v-if="selectedIcon.name"><strong>名稱：</strong>{{ selectedIcon.name }}</div>
          <div v-if="selectedIcon.emoji"><strong>Emoji：</strong>{{ selectedIcon.emoji }}</div>
          <div v-if="selectedIcon.class"><strong>CSS Class：</strong>{{ selectedIcon.class }}</div>
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
import IconPickerProxy from '@/features/icon-picker/components/IconPickerProxy.vue'
import IconPickerDevTool from '@/features/icon-picker/components/IconPickerDevTool.vue'
import IconDisplay from '@/components/common/IconDisplay.vue'

export default {
  name: 'IconPickerTestPage',
  components: {
    IconPickerProxy,
    IconPickerDevTool,
    IconDisplay
  },
  setup() {
    const selectedIcon = ref(null)
    const isPickerOpen = ref(false)
    const testingEmoji = ref(false)
    const testingIconLibrary = ref(false)
    const testingTextIcon = ref(false)
    const testResult = ref('')
    
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
      
      // 預設選擇一個 emoji 並打開選擇器
      selectedIcon.value = {
        type: 'emoji',
        emoji: '😀',
        name: 'grinning face'
      }
      openIconPicker()
    }
    
    const testIconLibrary = () => {
      testingIconLibrary.value = true
      testingEmoji.value = false
      testingTextIcon.value = false
      
      // 預設選擇一個圖標並打開選擇器
      selectedIcon.value = {
        type: 'bootstrap',
        class: 'bi-star',
        name: 'star'
      }
      openIconPicker()
    }
    
    const testTextIcon = () => {
      testingTextIcon.value = true
      testingEmoji.value = false
      testingIconLibrary.value = false
      
      // 預設選擇一個文字圖標並打開選擇器
      selectedIcon.value = {
        type: 'text',
        text: 'ABC',
        backgroundColor: '#3b82f6'
      }
      openIconPicker()
    }
    
    return {
      selectedIcon,
      isPickerOpen,
      currentVersion,
      testingEmoji,
      testingIconLibrary,
      testingTextIcon,
      testResult,
      openIconPicker,
      closeIconPicker,
      handleIconSelected,
      testEmojiSelection,
      testIconLibrary,
      testTextIcon
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
</style>