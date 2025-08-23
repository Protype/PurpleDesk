<template>
  <div class="icon-picker-test-page">
    <!-- Phase 進度面板 -->
    <div class="phase-progress">
      <h3 class="text-lg font-semibold text-gray-800 mb-3">📋 重構進度更新</h3>
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
          <span class="text-lg status-completed">✅</span>
          <span class="text-sm">ST-022: 建立新測試頁面</span>
        </div>
        <div class="progress-item">
          <span class="text-lg status-completed">✅</span>
          <span class="text-sm">EP-004: 面板元件拆分</span>
        </div>
        <div class="progress-item">
          <span class="text-lg status-current">🔄</span>
          <span class="text-sm">優化清理: 移除未使用檔案</span>
        </div>
      </div>
      
      <div class="mt-4 p-3 bg-green-50 rounded-lg">
        <h4 class="text-sm font-semibold text-green-800 mb-1">🎉 重構狀態更新：</h4>
        <p class="text-xs text-green-700">
          主要重構已完成，目前統一使用新版 IconPicker。原版已移除並建立 git tag 備份。
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
            統一使用新版 IconPicker
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

    <!-- 版本資訊 -->
    <div class="version-info mt-8 mb-8">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 版本資訊</h3>
      <div class="info-grid grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- 當前版本 -->
        <div class="info-item border border-gray-200 rounded-lg p-4 bg-blue-50">
          <h4 class="text-md font-semibold text-blue-800 mb-3 flex items-center">
            <span class="mr-2">🆕</span>
            當前版本 (新版 IconPicker)
          </h4>
          
          <!-- 預覽區域 -->
          <div class="icon-preview-small bg-white border-2 border-dashed border-blue-300 rounded-lg p-4 text-center mb-3 min-h-24 flex items-center justify-center">
            <div v-if="selectedIcon">
              <IconDisplay 
                :icon-data="selectedIcon" 
                size="lg"
                class="mx-auto mb-2"
              />
              <div class="text-xs text-gray-600">
                {{ selectedIcon.type || 'unknown' }}
              </div>
            </div>
            <div v-else class="text-gray-400 text-sm">
              <div class="text-xl mb-1">🎯</div>
              <div>未選擇</div>
            </div>
          </div>
          
          <!-- 操作按鈕 -->
          <button 
            @click="openIconPicker"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors"
          >
            選擇圖標
          </button>
          
          <!-- 資料顯示 -->
          <details v-if="selectedIcon" class="mt-3">
            <summary class="cursor-pointer text-xs text-blue-600 hover:text-blue-800">查看資料結構</summary>
            <pre class="text-xs bg-gray-100 p-2 mt-1 rounded overflow-auto max-h-32">{{ JSON.stringify(selectedIcon, null, 2) }}</pre>
          </details>
        </div>

        <!-- 備份資訊 -->
        <div class="info-item border border-gray-200 rounded-lg p-4 bg-orange-50">
          <h4 class="text-md font-semibold text-orange-800 mb-3 flex items-center">
            <span class="mr-2">💾</span>
            舊版備份
          </h4>
          
          <!-- 資訊區域 -->
          <div class="backup-info bg-white border-2 border-dashed border-orange-300 rounded-lg p-4 text-center mb-3 min-h-24 flex items-center justify-center">
            <div class="text-gray-600 text-sm">
              <div class="text-xl mb-2">🏷️</div>
              <div class="font-semibold">git tag backup/icon-picker-ori-v1.0</div>
              <div class="text-xs mt-1">IconPickerOri.vue (1,393 行)</div>
            </div>
          </div>
          
          <!-- 回溯說明 -->
          <div class="text-xs text-orange-700 bg-orange-100 p-3 rounded">
            <div class="font-semibold mb-1">如需回溯原版：</div>
            <div class="font-mono text-xs">
              git checkout backup/icon-picker-ori-v1.0 -- <br/>
              resources/js/components/common/IconPickerOri.vue
            </div>
          </div>
        </div>
      </div>
    </div>

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
    const dynamicIconComponent = ref(null)

    // 當前版本
    const currentVersion = computed(() => {
      return '新版 IconPicker'
    })

    // 打開 IconPicker
    const openIconPicker = () => {
      isPickerOpen.value = true
    }

    // 關閉 IconPicker
    const closeIconPicker = () => {
      isPickerOpen.value = false
    }

    // 處理圖標選擇
    const handleIconSelected = (iconData) => {
      console.log('handleIconSelected called with:', iconData)
      selectedIcon.value = iconData
      testResult.value = ''
    }

    // 測試功能
    const testEmojiSelection = () => {
      testingEmoji.value = !testingEmoji.value
      if (testingEmoji.value) {
        testResult.value = '請選擇一個 Emoji 進行測試'
        openIconPicker()
      }
    }

    const testIconLibrary = () => {
      testingIconLibrary.value = !testingIconLibrary.value
      if (testingIconLibrary.value) {
        testResult.value = '請從圖標庫中選擇一個圖標進行測試'
        openIconPicker()
      }
    }

    const testTextIcon = () => {
      testingTextIcon.value = !testingTextIcon.value
      if (testingTextIcon.value) {
        testResult.value = '請建立一個文字圖標進行測試'
        openIconPicker()
      }
    }

    const testHeroIcon = () => {
      testingHeroIcon.value = !testingHeroIcon.value
      if (testingHeroIcon.value) {
        testResult.value = '請選擇一個 Hero Icon 進行測試'
        openIconPicker()
      }
    }

    const testFormatConsistency = () => {
      testingFormat.value = !testingFormat.value
      if (testingFormat.value && selectedIcon.value) {
        const hasRequiredFields = selectedIcon.value.type && (
          selectedIcon.value.icon || 
          selectedIcon.value.emoji || 
          selectedIcon.value.text || 
          selectedIcon.value.url
        )
        if (hasRequiredFields) {
          testResult.value = `✅ 格式檢查通過 - 類型: ${selectedIcon.value.type}`
        } else {
          testResult.value = '❌ 格式檢查失敗 - 缺少必要欄位'
        }
      } else if (testingFormat.value) {
        testResult.value = '請先選擇一個圖標再進行格式檢查'
      }
    }

    const clearSelection = () => {
      selectedIcon.value = null
      testResult.value = '選擇已清除'
      
      // 重置所有測試狀態
      testingEmoji.value = false
      testingIconLibrary.value = false
      testingTextIcon.value = false
      testingHeroIcon.value = false
      testingFormat.value = false
    }

    // 動態載入 UserIcon
    const loadDynamicIcon = async () => {
      try {
        const { UserIcon } = await import('@heroicons/vue/outline')
        dynamicIconComponent.value = UserIcon
      } catch (error) {
        console.error('Failed to load UserIcon:', error)
      }
    }

    // 載入動態圖標
    loadDynamicIcon()

    return {
      selectedIcon,
      isPickerOpen,
      testingEmoji,
      testingIconLibrary,
      testingTextIcon,
      testingHeroIcon,
      testingFormat,
      testResult,
      dynamicIconComponent,
      currentVersion,
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
  @apply px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors;
}

.test-button.active {
  @apply bg-primary-50 border-primary-300 text-primary-700;
}

.test-button.clear-button {
  @apply bg-red-50 border-red-300 text-red-700 hover:bg-red-100;
}
</style>