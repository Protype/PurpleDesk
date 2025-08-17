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
        <div class="absolute top-0 left-0 bg-white border border-gray-200 rounded-lg shadow-xl px-4 py-2 w-96">
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
          <div>
            <!-- 文字圖標標籤頁 - 使用 TextIconPanel -->
            <div v-if="activeTab === 'initials'" class="space-y-4">
              <TextIconPanel
                v-model="customInitials"
                :background-color="localBackgroundColor"
                @text-selected="handleTextSelection"
              />
            </div>

            <!-- Emoji 標籤頁 - 開發中狀態 -->
            <div v-else-if="activeTab === 'emoji'" class="text-center py-8">
              <div class="text-4xl mb-4">🚧</div>
              <div class="text-gray-600 text-sm">
                <div class="font-medium mb-2">Emoji Panel 開發中</div>
                <div class="text-xs text-gray-500">將使用新的 EmojiPanel 元件和 VirtualScrollGrid</div>
              </div>
            </div>

            <!-- Icons 標籤頁 - 開發中狀態 -->
            <div v-else-if="activeTab === 'icons'" class="text-center py-8">
              <div class="text-4xl mb-4">🚧</div>
              <div class="text-gray-600 text-sm">
                <div class="font-medium mb-2">Icon Library Panel 開發中</div>
                <div class="text-xs text-gray-500">將整合 HeroIcons 和 Bootstrap Icons</div>
              </div>
            </div>

            <!-- Upload 標籤頁 - 開發中狀態 -->
            <div v-else-if="activeTab === 'upload'" class="text-center py-8">
              <div class="text-4xl mb-4">🚧</div>
              <div class="text-gray-600 text-sm">
                <div class="font-medium mb-2">Image Upload Panel 開發中</div>
                <div class="text-xs text-gray-500">將實作圖片上傳和預覽功能</div>
              </div>
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

<script>
import { ref, reactive, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import TextIconPanel from './TextIconPanel.vue'

export default {
  name: 'IconPicker',
  components: {
    TextIconPanel
  },
  props: {
    modelValue: {
      type: String,
      default: ''
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
  },
  emits: [
    'update:modelValue',
    'update:iconType',
    'background-color-change',
    'file-selected',
    'close'
  ],
  setup(props, { emit }) {
    // 響應式狀態
    const iconPickerRef = ref(null)
    const iconPanel = ref(null)
    const eyedropperButton = ref(null)
    const isOpen = ref(props.isOpen)
    const activeTab = ref('initials')
    const isColorPickerOpen = ref(false)
    const selectedIcon = ref(props.modelValue)
    const customInitials = ref('')
    const localBackgroundColor = ref(props.backgroundColor)

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

    // 計算面板位置
    const calculatePanelPosition = () => {
      if (!iconPickerRef.value || !iconPanel.value) return

      const buttonRect = iconPickerRef.value.getBoundingClientRect()
      const panelRect = iconPanel.value.getBoundingClientRect()
      const viewportWidth = window.innerWidth
      const viewportHeight = window.innerHeight

      let top = buttonRect.bottom + 8
      let left = buttonRect.left

      // 如果面板超出右側邊界，向左調整
      if (left + panelRect.width > viewportWidth) {
        left = viewportWidth - panelRect.width - 16
      }

      // 如果面板超出下方邊界，顯示在按鈕上方
      if (top + panelRect.height > viewportHeight) {
        top = buttonRect.top - panelRect.height - 8
      }

      // 確保不超出邊界
      left = Math.max(16, left)
      top = Math.max(16, top)

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
        calculatePanelPosition()
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
      selectedIcon.value = ''
      emit('update:modelValue', '')
      emit('update:iconType', '')
    }

    // 文字圖標選擇處理
    const handleTextSelection = (data) => {
      selectedIcon.value = data.text
      localBackgroundColor.value = data.backgroundColor
      
      emit('update:modelValue', data.text)
      emit('update:iconType', 'initials')
      emit('background-color-change', data.backgroundColor)
      
      closePicker()
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

    return {
      // Refs
      iconPickerRef,
      iconPanel,
      eyedropperButton,
      
      // State
      isOpen,
      activeTab,
      isColorPickerOpen,
      selectedIcon,
      customInitials,
      localBackgroundColor,
      panelPosition,
      
      // Methods
      togglePicker,
      openPicker,
      closePicker,
      openColorPicker,
      closeColorPicker,
      clearIcon,
      handleTextSelection,
      getDisplayIcon
    }
  }
}
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