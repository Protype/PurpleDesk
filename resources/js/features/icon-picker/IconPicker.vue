<template>
  <div class="icon-picker" ref="iconPickerRef">
    <!-- 圖標預覽按鈕 -->
    <button
      v-if="!hidePreview"
      type="button"
      @click="togglePicker"
      class="w-8 h-8 rounded border-2 border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors bg-white flex items-center justify-center"
    >
      <!-- 顯示選中的圖標 -->
      <component 
        v-if="selectedIcon && iconType === 'heroicons'" 
        :is="getDisplayIcon(selectedIcon)" 
        class="w-5 h-5 text-gray-600" 
      />
      <i 
        v-else-if="selectedIcon && iconType === 'bootstrap'" 
        :class="['bi', selectedIcon]"
        class="text-gray-600 text-sm"
      />
      <span v-else-if="selectedIcon && iconType === 'emoji'" class="text-sm">
        {{ selectedIcon }}
      </span>
      <span v-else-if="selectedIcon && iconType === 'initials'" class="text-xs font-semibold text-gray-600">
        {{ selectedIcon }}
      </span>
      <img 
        v-else-if="selectedIcon && iconType === 'upload'" 
        :src="selectedIcon"
        class="w-full h-full object-cover rounded"
      />
      <span v-else class="text-gray-400 text-xs">圖標</span>
    </button>
    
    <!-- 圖標選擇面板容器 -->
    <Teleport to="body">
      <div 
        v-if="isOpen" 
        ref="iconPanel"
        class="fixed z-[9999]"
        :style="panelPosition"
      >
        <!-- IconPicker 面板 -->
        <div class="absolute top-0 left-0 bg-white border border-gray-200 rounded-lg shadow-xl px-4 py-2 w-96 max-h-[80vh] overflow-hidden flex flex-col">
          <!-- 頂部標籤切換 -->
          <div class="flex border-b border-gray-200 mb-4">
            <button
              @click.stop="activeTab = 'initials'"
              :class="activeTab === 'initials' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
              class="px-2 me-3 pt-1 pb-2 text-sm font-medium transition-colors"
            >
              文字
            </button>
            <button
              @click.stop="activeTab = 'emoji'"
              :class="activeTab === 'emoji' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
              class="px-2 me-3 pt-1 pb-2 text-sm font-medium transition-colors"
            >
              Emoji
            </button>
            <button
              @click.stop="activeTab = 'icons'"
              :class="activeTab === 'icons' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
              class="px-2 me-3 pt-1 pb-2 text-sm font-medium transition-colors"
            >
              Icons
            </button>
            <button
              @click.stop="activeTab = 'upload'"
              :class="activeTab === 'upload' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
              class="px-2 me-3 pt-1 pb-2 text-sm font-medium transition-colors"
            >
              Upload
            </button>
            <div class="ml-auto flex items-center">
              <!-- 背景顏色選擇器按鈕 -->
              <div class="me-3 pt-1 pb-2 relative">
                <button
                  ref="eyedropperButton"
                  @click.stop="openColorPicker"
                  class="p-0 text-base text-gray-500 hover:text-gray-700 transition-colors relative"
                  title="選擇背景顏色"
                >
                  <i class="bi bi-eyedropper"></i>
                  <!-- 右下角的 4x4 顏色指示器 -->
                  <div 
                    class="absolute bottom-0.5 -right-0.5 w-2 h-2 border border-white rounded-sm shadow-sm"
                    :style="{ backgroundColor: localBackgroundColor || '#6366f1' }"
                  ></div>
                </button>
              </div>
              <!-- Reset Icon 按鈕 -->
              <button
                @click.stop="clearIcon"
                :disabled="!selectedIcon"
                :class="selectedIcon ? 'text-gray-500 hover:text-gray-700' : 'text-gray-400 cursor-not-allowed'"
                class="p-0 me-3 pt-1 pb-2 text-base transition-colors"
                title="Reset Icon"
              >
                <i class="bi bi-arrow-clockwise"></i>
              </button>
            </div>
          </div>


          <!-- 內容區域 -->
          <div class="flex-1 overflow-y-auto min-h-0 mb-1">
            <!-- 文字圖標標籤頁 - 使用 TextIconPanel -->
            <div v-show="activeTab === 'initials'" class="space-y-4">
              <TextIconPanel
                v-model="customInitials"
                :background-color="localBackgroundColor"
                @text-selected="handleTextSelection"
              />
            </div>

            <!-- Emoji 標籤頁 - 使用 EmojiPanel -->
            <div v-show="activeTab === 'emoji'">
              <EmojiPanel
                :selected-emoji="iconType === 'emoji' ? selectedIcon?.emoji : ''"
                @emoji-selected="handleEmojiSelection"
              />
            </div>

            <!-- Icons 標籤頁 - 使用 IconLibraryPanel -->
            <div v-show="activeTab === 'icons'">
              <IconLibraryPanel
                :selected-icon="iconType === 'heroicons' || iconType === 'bootstrap' ? selectedIcon : null"
                :items-per-row="8"
                @icon-select="handleIconSelection"
              />
            </div>

            <!-- Upload 標籤頁 - 使用 ImageUploadPanel -->
            <div v-show="activeTab === 'upload'">
              <ImageUploadPanel
                :selected-icon="iconType === 'upload' ? selectedIcon : null"
                @icon-select="handleImageUpload"
                @file-selected="handleFileSelected"
              />
            </div>
          </div>

          <!-- 顏色選擇器面板 -->
          <div 
            v-if="isColorPickerOpen"
            class="absolute top-full left-0 mt-2 p-4 bg-white border border-gray-200 rounded-lg shadow-xl z-10 w-80"
          >
            <div class="text-center py-8">
              <div class="text-4xl mb-4">🎨</div>
              <div class="text-gray-600 text-sm">
                <div class="font-medium mb-2">Color Picker Panel 開發中</div>
                <div class="text-xs text-gray-500">將提供完整的顏色選擇功能</div>
              </div>
              <button
                @click="closeColorPicker"
                class="mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm"
              >
                關閉
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- 點擊外部關閉面板的遮罩 -->
    <div 
      v-if="isOpen"
      @click="closePicker"
      class="fixed inset-0 z-[9998]"
    ></div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onUnmounted, nextTick } from 'vue'
import TextIconPanel from './components/TextIconPanel.vue'
import EmojiPanel from './components/EmojiPanel.vue'
import IconLibraryPanel from './components/IconLibraryPanel.vue'
import ImageUploadPanel from './components/ImageUploadPanel.vue'
import IconPickerSearch from './components/IconPickerSearch.vue'
import SkinToneSelector from '@/components/common/SkinToneSelector.vue'
import { usePreloadedDataProvider } from './composables/usePreloadedData.js'

// Props 和 Emits
defineOptions({
  name: 'IconPicker'
})

const props = defineProps({
  modelValue: {
    type: [Object, null],
    default: null
  },
  iconType: {
    type: String,
    default: 'emoji'
  },
  backgroundColor: {
    type: String,
    default: '#6366f1'
  },
  hidePreview: {
    type: Boolean,
    default: false
  },
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits([
  'update:modelValue',
  'update:iconType',
  'update:isOpen',
  'background-color-change',
  'file-selected',
  'close'
])

// 預載入資料提供者
const preloadedData = usePreloadedDataProvider()

// 響應式狀態
const iconPickerRef = ref(null)
const iconPanel = ref(null)
const eyedropperButton = ref(null)
const isOpen = ref(props.isOpen)
const activeTab = ref('initials')
const isColorPickerOpen = ref(false)
const selectedIcon = ref(props.modelValue)
const iconType = ref(props.iconType)
const customInitials = ref('')
const localBackgroundColor = ref(props.backgroundColor)
const searchQuery = ref('')
const selectedSkinTone = ref(0)

// 面板位置計算
const panelPosition = reactive({
  top: '0px',
  left: '0px'
})

// 監聽 props 變化
watch(() => props.modelValue, (newValue) => {
  selectedIcon.value = newValue
})

watch(() => props.isOpen, (newValue) => {
  isOpen.value = newValue
  if (newValue) {
    nextTick(() => {
      calculatePanelPosition()
    })
  }
})

watch(() => props.backgroundColor, (newValue) => {
  localBackgroundColor.value = newValue
})

watch(() => props.iconType, (newValue) => {
  iconType.value = newValue
})

// 計算面板位置
const calculatePanelPosition = () => {
  if (!iconPickerRef.value || !iconPanel.value) return

  const buttonRect = iconPickerRef.value.getBoundingClientRect()
  const panelRect = iconPanel.value.getBoundingClientRect()
  const viewportWidth = window.innerWidth
  const viewportHeight = window.innerHeight

  let top = buttonRect.bottom + 8
  let left = buttonRect.left
  
  // 面板預設高度（如果無法取得實際高度）
  const panelHeight = panelRect.height || 400

  // 檢查面板是否會超出下方邊界
  const wouldOverflowBottom = top + panelHeight > viewportHeight - 16
  
  // 檢查上方是否有足夠空間
  const topSpace = buttonRect.top - 16
  const hasEnoughTopSpace = topSpace >= panelHeight + 8

  // 如果下方空間不足且上方有足夠空間，則顯示在上方
  if (wouldOverflowBottom && hasEnoughTopSpace) {
    top = buttonRect.top - panelHeight - 8
  } else if (wouldOverflowBottom) {
    // 如果上下都沒有足夠空間，則置中顯示並確保不超出邊界
    const centerY = (viewportHeight - panelHeight) / 2
    top = Math.max(16, Math.min(centerY, viewportHeight - panelHeight - 16))
  }

  // 如果面板超出右側邊界，向左調整
  if (left + panelRect.width > viewportWidth) {
    left = viewportWidth - panelRect.width - 16
  }

  // 確保不超出左側邊界
  left = Math.max(16, left)

  // 最終確保 top 不超出邊界
  top = Math.max(16, Math.min(top, viewportHeight - panelHeight - 16))

  panelPosition.top = `${top}px`
  panelPosition.left = `${left}px`
}

// 顯示圖標工具
const getDisplayIcon = (iconName) => {
  // 這裡需要實作圖標顯示邏輯
  return 'div'
}

// 面板控制
const togglePicker = () => {
  if (isOpen.value) {
    closePicker()
  } else {
    openPicker()
  }
}

const openPicker = () => {
  isOpen.value = true
  emit('update:isOpen', true)
  nextTick(() => {
    // 延遲一小段時間確保面板完全渲染
    setTimeout(() => {
      calculatePanelPosition()
    }, 10)
  })
}

const closePicker = () => {
  isOpen.value = false
  isColorPickerOpen.value = false
  emit('close')
}

// 顏色選擇器
const openColorPicker = () => {
  isColorPickerOpen.value = !isColorPickerOpen.value
}

const closeColorPicker = () => {
  isColorPickerOpen.value = false
}

// 清除圖標
const clearIcon = () => {
  selectedIcon.value = null
  emit('update:modelValue', null)
  emit('update:iconType', '')
}

// 文字圖標選擇處理
const handleTextSelection = (data) => {
  const textObject = {
    type: 'text',
    text: data.text,
    backgroundColor: data.backgroundColor
  }
  
  selectedIcon.value = textObject
  localBackgroundColor.value = data.backgroundColor
  
  emit('update:modelValue', textObject)
  emit('update:iconType', 'text')
  emit('background-color-change', data.backgroundColor)
  
  closePicker()
}

// Emoji 選擇處理
const handleEmojiSelection = (data) => {
  const emojiObject = {
    type: 'emoji',
    emoji: data.emoji,
    name: data.name || 'emoji'
  }
  
  selectedIcon.value = emojiObject
  iconType.value = 'emoji'
  
  emit('update:modelValue', emojiObject)
  emit('update:iconType', 'emoji')
  
  closePicker()
}

// 圖標選擇處理
const handleIconSelection = (icon) => {
  let iconObject
  
  if (icon.type === 'heroicons') {
    iconObject = {
      type: 'heroicons',
      icon: icon.value,
      variant: icon.variant_type || 'outline'
    }
  } else if (icon.type === 'bootstrap-icons') {
    iconObject = {
      type: 'bootstrap-icons',
      icon: icon.value
    }
  } else {
    // 預設處理
    iconObject = {
      type: icon.type || 'heroicons',
      icon: icon.value || icon.name
    }
  }
  
  selectedIcon.value = iconObject
  iconType.value = icon.type
  
  emit('update:modelValue', iconObject)
  emit('update:iconType', icon.type)
  
  closePicker()
}

// 處理圖片上傳選擇
const handleImageUpload = (iconData) => {
  selectedIcon.value = iconData
  iconType.value = iconData.iconType || 'upload'
  
  emit('update:modelValue', iconData)
  emit('update:iconType', iconData.iconType || 'upload')
  
  closePicker()
}

// 處理檔案選擇事件
const handleFileSelected = (file) => {
  emit('file-selected', file)
}

// 鍵盤事件處理
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && isOpen.value) {
    closePicker()
  }
}

// 窗口大小變化處理
const handleResize = () => {
  if (isOpen.value) {
    calculatePanelPosition()
  }
}

// 生命週期
onMounted(() => {
  if (typeof document !== 'undefined' && document.addEventListener) {
    document.addEventListener('keydown', handleKeyDown)
  }
  if (typeof window !== 'undefined' && window.addEventListener) {
    window.addEventListener('resize', handleResize)
  }
})

onUnmounted(() => {
  if (typeof document !== 'undefined' && document.removeEventListener) {
    document.removeEventListener('keydown', handleKeyDown)
  }
  if (typeof window !== 'undefined' && window.removeEventListener) {
    window.removeEventListener('resize', handleResize)
  }
})
</script>

<style scoped>
.icon-picker {
  position: relative;
  display: inline-block;
}

/* Utility classes for proper z-index layering */
.z-\[9998\] {
  z-index: 9998;
}

.z-\[9999\] {
  z-index: 9999;
}
</style>