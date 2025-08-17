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

        <!-- 搜尋與選擇器區域 -->
        <div v-if="activeTab !== 'upload' && activeTab !== 'initials'" class="mb-4">
          <div class="flex space-x-2">
            <!-- 搜尋欄位 -->
            <div class="relative flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Filter..."
                class="icon-filter w-full text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
              <button
                v-if="searchQuery"
                @click="clearSearch"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 hover:text-gray-600"
              >
                ×
              </button>
            </div>
            <!-- 功能按鈕組 -->
            <div class="flex space-x-1">
              <!-- 圖標樣式選擇器 -->
              <IconStyleSelector
                v-if="activeTab === 'icons'"
                v-model="selectedIconStyle"
                @update:modelValue="handleIconStyleChange"
              />
              <!-- 膚色選擇器 -->
              <SkinToneSelector
                v-if="activeTab === 'emoji'"
                v-model="selectedSkinTone"
                @update:modelValue="handleSkinToneChange"
              />
            </div>
          </div>
        </div>

        <!-- 內容區域 -->
        <div>
          <!-- 字母標籤頁 -->
          <div 
            v-if="activeTab === 'initials'"
            class="space-y-4"
          >
            <!-- 字母輸入區 -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">輸入文字或字母</label>
              <input
                v-model="customInitials"
                type="text"
                maxlength="3"
                placeholder="最多3個字元 (如: AB)"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                @input="handleInitialsInput"
              />
            </div>
            
            <!-- 預覽區 -->
            <div class="flex items-center justify-center py-4">
              <div 
                class="w-24 h-24 rounded-full flex items-center justify-center font-semibold text-3xl"
                :class="getTextColorClass(backgroundColor || '#6366f1')"
                :style="{ backgroundColor: backgroundColor || '#6366f1' }"
              >
                {{ customInitials || 'AB' }}
              </div>
            </div>
            
            <!-- 應用按鈕 -->
            <button
              @click.stop="applyInitials"
              :disabled="!customInitials"
              :class="customInitials ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
              class="w-full py-2 px-4 rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              套用文字
            </button>
          </div>
          
          <!-- Emoji 標籤頁 -->
          <div 
            v-if="activeTab === 'emoji'"
          >
            <!-- Emoji 網格 -->
            <div class="grid-wrapper emoji-grid-wrapper h-48 border border-gray-100 rounded-md bg-gray-50 p-2">
              <VirtualScroll
                :items="filteredEmojis"
                :items-per-row="10"
                :row-height="36"
                :container-height="178"
                :buffer="2"
              >
                <template #row="{ items }">
                  <template v-for="(item, index) in items" :key="item ? (item.name || item.emoji || item.categoryId || index) : index">
                    <!-- 分類標題 -->
                    <div 
                      v-if="item && item.type === 'category-header'"
                      class="category-header w-full flex items-center space-x-2 pt-3 pb-1 text-sm font-bold text-gray-400"
                    >
                      <span>{{ item.name }}</span>
                      <div class="flex-1 h-px me-2 ml-2 bg-gray-200"></div>
                    </div>
                    
                    <!-- Emoji 按鈕 -->
                    <button
                      v-else-if="item && item.emoji"
                      @click.stop="selectIcon(getEmojiWithSkinTone(item), 'emoji')"
                      :class="selectedIcon === getEmojiWithSkinTone(item) ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-100'"
                      class="emoji-button p-1 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                      :title="item.name"
                    >
                      <span class="text-xl">{{ getEmojiWithSkinTone(item) }}</span>
                    </button>
                    
                    <!-- 空白佔位符（用於填充完整行） -->
                    <div v-else-if="item && item.type === 'row-filler'" class="p-1"></div>
                    
                  </template>
                </template>
              </VirtualScroll>
            </div>
          </div>

          <!-- Icons 標籤頁 (只有 Heroicons) -->
          <div 
            v-else-if="activeTab === 'icons'"
          >
            <!-- 圖標網格 -->
            <div class="grid-wrapper icon-grid-wrapper h-48 border border-gray-100 rounded-md bg-gray-50 p-2">
              <VirtualScroll
                :items="filteredIcons"
                :items-per-row="10"
                :row-height="36"
                :container-height="176"
                :buffer="2"
                :preserve-scroll-position="true"
              >
                <template #row="{ items }">
                  <template v-for="(item, index) in items" :key="item ? (item.name || item.component || item.class || item.categoryId || index) : index">
                    <!-- 分類標題 -->
                    <div 
                      v-if="item && item.type === 'category-header'"
                      class="category-header w-full flex items-center space-x-2 pt-3 pb-1 text-sm font-bold text-gray-400"
                    >
                      <span>{{ item.name }}</span>
                      <div class="flex-1 h-px me-2 ml-2 bg-gray-200"></div>
                    </div>
                    
                    <!-- Hero Icons 按鈕 -->
                    <button
                      v-else-if="item && item.component"
                      @click.stop="selectIcon(item.component, 'heroicons')"
                      :class="isIconSelected(item.component) ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-100'"
                      class="icon-button p-1.5 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                      :title="item.name"
                    >
                      <component 
                        :is="getIconComponent(item.component)" 
                        class="w-5 h-5 mx-auto text-gray-600" 
                      />
                    </button>
                    
                    <!-- Bootstrap Icons 按鈕 -->
                    <button
                      v-else-if="item && item.class"
                      @click.stop="selectIcon(item.class, 'bootstrap')"
                      :class="isIconSelected(item.class) ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-100'"
                      class="icon-button p-1.5 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                      :title="item.name"
                    >
                      <i :class="item.class + ' text-gray-600'" style="font-size: 1.25rem;"></i>
                    </button>

                    <!-- 空白佔位符（用於填充完整行） -->
                    <div v-else-if="item && item.type === 'row-filler'" class="p-1"></div>
                    
                  </template>
                </template>
              </VirtualScroll>
            </div>
          </div>
          
          <!-- Upload 標籤頁 -->
          <div 
            v-else-if="activeTab === 'upload'"
            @click="triggerFileUpload"
            @dragover.prevent="handleDragOver"
            @dragleave.prevent="handleDragLeave"
            @drop.prevent="handleDrop"
            :class="isDragging ? 'border-primary-400 bg-primary-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'"
            class="h-48 flex flex-col items-center justify-center border-2 border-dashed rounded-md transition-colors cursor-pointer"
          >
            <div class="text-center pointer-events-none space-y-3">
              <i class="bi bi-cloud-arrow-up-fill text-4xl text-gray-400"></i>
              <div>
                <p class="text-sm font-medium text-gray-700">Upload an image</p>
                <p class="text-xs text-gray-500 mt-1">or drag and drop</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 搜尋結果為空的提示 -->
        <div v-if="isSearchEmpty" class="text-center py-8 text-gray-500">
          <p class="text-sm">找不到符合的圖標</p>
          <p class="text-xs text-gray-400 mt-1">請嘗試其他關鍵字</p>
        </div>
        </div>
        
        <!-- ColorPicker 面板 -->
        <div 
          v-if="showColorPicker" 
          class="absolute top-0 bg-white border border-gray-200 rounded-lg shadow-xl w-72 p-4 pt-5"
          :style="colorPickerPosition"
        >
          <!-- 關閉按鈕 -->
          <button
            @click="closeColorPicker"
            class="absolute top-2 right-2 w-6 h-6 text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center"
            title="關閉"
          >
            <i class="bi bi-x-lg"></i>
          </button>
          
          <!-- 預設色彩調色盤 -->
          <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">預設顏色</h4>
            <div class="grid grid-cols-8 gap-2">
              <button
                v-for="color in defaultColors"
                :key="color.value"
                @click="selectColor(color.value)"
                :style="{ backgroundColor: color.value }"
                class="w-6 h-6 rounded border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary-500 transition-all"
                :class="{ 'ring-2 ring-primary-500': localBackgroundColor === color.value }"
                :title="color.name"
              />
            </div>
          </div>
          
          <!-- 淡色系調色盤 -->
          <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">淡色系</h4>
            <div class="grid grid-cols-8 gap-2">
              <button
                v-for="color in lightColors"
                :key="color.value"
                @click="selectColor(color.value)"
                :style="{ backgroundColor: color.value }"
                class="w-6 h-6 rounded border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary-500 transition-all"
                :class="{ 'ring-2 ring-primary-500': localBackgroundColor === color.value }"
                :title="color.name"
              />
            </div>
          </div>
          
          <!-- HTML 色彩輸入 -->
          <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">自訂顏色</h4>
            <div class="flex space-x-2">
              <input
                type="color"
                :value="localBackgroundColor || '#6366f1'"
                @input="handleColorInput"
                @change="handleColorInput"
                class="w-10 h-8 border border-gray-300 rounded cursor-pointer"
              />
              <input
                type="text"
                :value="localBackgroundColor || ''"
                @input="handleTextInput"
                placeholder="#6366f1"
                class="flex-1 px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>
          </div>
          
          <!-- 隨機顏色按鈕 -->
          <div class="flex space-x-2">
            <button
              @click="selectRandomColor"
              class="flex-1 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
              🎲 隨機淡色
            </button>
            <button
              @click="clearColor"
              class="px-3 py-2 text-sm bg-red-50 hover:bg-red-100 text-red-600 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              清除
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    
    <!-- 隱藏的檔案輸入 -->
    <input
      ref="fileInput"
      type="file"
      accept="image/*"
      @change="handleFileUpload"
      class="hidden"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { bootstrapIcons, emojis } from '../../utils/iconSets.js'
import { applySkinTone, supportsSkinTone, removeSkinTone, getCurrentSkinTone } from '../../utils/emojiSkinTone.js'
import heroiconsOutline from '../../utils/heroicons/allHeroicons.js'
import { EMOJI_CATEGORY_INFO } from '../../utils/emojis/index.js'
import bootstrapIconsIndex, { categoryMap as BOOTSTRAP_CATEGORY_INFO } from '../../utils/icons/index.js'
import VirtualScroll from './VirtualScroll.vue'
import SkinToneSelector from './SkinToneSelector.vue'
import IconStyleSelector from './IconStyleSelector.vue'
// 動態導入所有 Heroicons
import * as HeroiconsOutline from '@heroicons/vue/outline'
import * as HeroiconsSolid from '@heroicons/vue/solid'

export default {
  name: 'IconPickerOri',
  components: {
    VirtualScroll,
    SkinToneSelector,
    IconStyleSelector,
    // 註冊所有 Heroicons (Outline 和 Solid)
    ...HeroiconsOutline,
    ...HeroiconsSolid
  },
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    iconType: {
      type: String,
      default: ''
    },
    backgroundColor: {
      type: String,
      default: '#6366f1'
    },
    hidePreview: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue', 'update:iconType', 'file-selected', 'close', 'background-color-change'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    const iconPanel = ref(null)
    const iconPickerRef = ref(null)
    const searchQuery = ref('')
    const activeTab = ref('emoji') // 預設為 emoji 頁簽
    const panelPosition = ref({ top: '0px', left: '0px' })
    const colorPickerPosition = ref({ left: '0px' })
    const selectedIcon = ref(props.modelValue)
    const iconType = ref(props.iconType || '')
    const emojisLoaded = ref(false)
    const selectedSkinTone = ref('') // 預設膚色
    const selectedIconStyle = ref('outline') // 預設圖標樣式
    const fileInput = ref(null)
    const uploadedImage = ref(null)
    const isDragging = ref(false)
    const backgroundColor = ref(props.backgroundColor || '#6366f1')
    const localBackgroundColor = ref(props.backgroundColor || '#6366f1')
    const showColorPicker = ref(false)
    const colorPickerRef = ref(null)
    const eyedropperButton = ref(null)
    const customInitials = ref('') // 字母模式的輸入值
    
    // 預設顏色調色盤
    const defaultColors = [
      { value: '#ef4444', name: '紅色 Red' },
      { value: '#f97316', name: '橙色 Orange' },
      { value: '#f59e0b', name: '黃色 Amber' },
      { value: '#eab308', name: '黃綠色 Yellow' },
      { value: '#84cc16', name: '萊色 Lime' },
      { value: '#22c55e', name: '綠色 Green' },
      { value: '#10b981', name: '翠綠色 Emerald' },
      { value: '#14b8a6', name: '青綠色 Teal' },
      { value: '#06b6d4', name: '青色 Cyan' },
      { value: '#0ea5e9', name: '天空藍 Sky Blue' },
      { value: '#3b82f6', name: '藍色 Blue' },
      { value: '#6366f1', name: '靛藍色 Indigo' },
      { value: '#8b5cf6', name: '紫羅蘭 Violet' },
      { value: '#a855f7', name: '紫色 Purple' },
      { value: '#d946ef', name: '紫紅色 Fuchsia' },
      { value: '#ec4899', name: '桃紅色 Pink' }
    ]
    
    // 淡色系調色盤（增加彩度提升識別度）
    const lightColors = [
      { value: '#fecaca', name: '淡紅色 Light Red' },
      { value: '#fed7aa', name: '淡橙色 Light Orange' },
      { value: '#fde68a', name: '淡黃色 Light Amber' },
      { value: '#fef08a', name: '淡黃綠色 Light Yellow' },
      { value: '#d9f99d', name: '淡萊色 Light Lime' },
      { value: '#bbf7d0', name: '淡綠色 Light Green' },
      { value: '#a7f3d0', name: '淡翠綠色 Light Emerald' },
      { value: '#99f6e4', name: '淡青綠色 Light Teal' },
      { value: '#a5f3fc', name: '淡青色 Light Cyan' },
      { value: '#bae6fd', name: '淡天空藍 Light Sky' },
      { value: '#dbeafe', name: '淡藍色 Light Blue' },
      { value: '#c7d2fe', name: '淡靛藍色 Light Indigo' },
      { value: '#ddd6fe', name: '淡紫羅蘭 Light Violet' },
      { value: '#e9d5ff', name: '淡紫色 Light Purple' },
      { value: '#f5d0fe', name: '淡紫紅色 Light Fuchsia' },
      { value: '#fbcfe8', name: '淡桃紅色 Light Pink' }
    ]
    
    // 監聽 props 變化
    watch(() => props.backgroundColor, (newVal) => {
      if (newVal) {
        backgroundColor.value = newVal
        localBackgroundColor.value = newVal
      }
    })
    
    // 處理背景顏色變化
    const handleBackgroundColorChange = (color) => {
      localBackgroundColor.value = color
      backgroundColor.value = color
      emit('background-color-change', color)
    }
    
    // 開啟顏色選擇器
    const openColorPicker = async () => {
      showColorPicker.value = !showColorPicker.value
      // 當 ColorPicker 開關時，重新計算位置以適應新的寬度
      await nextTick()
      await calculatePosition()
    }
    
    // 關閉顏色選擇器
    const closeColorPicker = async () => {
      showColorPicker.value = false
      // 當 ColorPicker 關閉時，重新計算位置以適應新的寬度
      await nextTick()
      await calculatePosition()
    }
    
    // 選擇顏色
    const selectColor = (color) => {
      handleBackgroundColorChange(color)
    }
    
    // 處理顏色輸入
    const handleColorInput = (event) => {
      handleBackgroundColorChange(event.target.value)
    }
    
    // 處理文字輸入
    const handleTextInput = (event) => {
      const value = event.target.value
      if (value.match(/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/)) {
        handleBackgroundColorChange(value)
      }
    }
    
    // 選擇隨機淡色
    const selectRandomColor = () => {
      const randomColor = lightColors[Math.floor(Math.random() * lightColors.length)]
      selectColor(randomColor.value)
    }
    
    // 清除顏色
    const clearColor = () => {
      handleBackgroundColorChange('')
    }
    
    watch(() => props.modelValue, (newVal) => {
      selectedIcon.value = newVal
      // 如果是 emoji，檢測膚色
      if (newVal && props.iconType === 'emoji') {
        const detectedSkinTone = getCurrentSkinTone(newVal)
        selectedSkinTone.value = detectedSkinTone
      }
      // 如果是 heroicons，檢測樣式
      if (newVal && props.iconType === 'heroicons' && newVal.includes(':')) {
        const [style, iconName] = newVal.split(':')
        if (style === 'solid' || style === 'outline') {
          selectedIconStyle.value = style
          selectedIcon.value = newVal
        }
      }
    })
    
    watch(() => props.iconType, (newVal) => {
      if (newVal) {
        iconType.value = newVal
      }
    })
    
    // 合併 Heroicons 和 Bootstrap Icons
    const heroIcons = heroiconsOutline
    const bsIcons = ref([])
    
    // 非同步載入 Bootstrap Icons
    const loadBootstrapIcons = async () => {
      try {
        await bootstrapIconsIndex.loadAllIcons()
        bsIcons.value = bootstrapIconsIndex.getAllLoadedIcons()
      } catch (error) {
        console.error('Failed to load Bootstrap Icons:', error)
      }
    }
    
    // 在組件掛載時載入
    onMounted(() => {
      loadBootstrapIcons()
    })
    
    // 儲存 Heroicons 組件的引用
    const HeroiconsComponents = {
      outline: HeroiconsOutline,
      solid: HeroiconsSolid
    }
    
    const calculatePosition = async () => {
      let targetElement = iconPickerRef.value
      
      // 如果 hidePreview 為 true，嘗試找到父元素作為定位參考
      if (props.hidePreview && iconPickerRef.value) {
        // 尋找最近的有實際尺寸的父元素，特別是 ImageSelector 的預覽區域
        let parent = iconPickerRef.value.parentElement
        let searchCount = 0
        while (parent && parent !== document.body && searchCount < 10) {
          searchCount++
          const rect = parent.getBoundingClientRect()
          const classList = parent.classList.toString()
          
          // 尋找有明顯尺寸且可能是預覽容器的元素
          if (rect.width >= 48 && rect.height >= 48) {
            // 檢查是否包含預覽相關的類名
            if (classList.includes('group') || classList.includes('relative') || 
                classList.includes('flex') || classList.includes('h-') || 
                classList.includes('w-')) {
              targetElement = parent
              break
            }
          }
          parent = parent.parentElement
        }
        
        // 如果沒有找到合適的父元素，回退到使用 iconPickerRef
        if (targetElement === iconPickerRef.value) {
          parent = iconPickerRef.value.parentElement
          searchCount = 0
          while (parent && parent !== document.body && searchCount < 10) {
            searchCount++
            const rect = parent.getBoundingClientRect()
            
            if (rect.width >= 48 && rect.height >= 48) {
              targetElement = parent
              break
            }
            parent = parent.parentElement
          }
        }
      }
      
      if (!targetElement) return
      
      const rect = targetElement.getBoundingClientRect()
      const viewportHeight = window.innerHeight
      const viewportWidth = window.innerWidth
      
      // 面板尺寸
      const iconPickerWidth = 384 // w-96
      const colorPickerWidth = 288 // w-72
      
      // 動態獲取 IconPicker 面板的實際高度
      let panelHeight = 400 // 預設高度
      if (iconPanel.value) {
        await nextTick() // 確保 DOM 已更新
        const iconPickerElement = iconPanel.value.querySelector('.absolute.top-0.left-0')
        if (iconPickerElement) {
          panelHeight = iconPickerElement.offsetHeight
        }
      }
      
      let top = rect.bottom + 5
      let left = rect.left
      
      // 智慧定位邏輯
      const spaceBelow = viewportHeight - rect.bottom
      const spaceAbove = rect.top
      
      // 檢查是否可以在上方顯示（需要足夠空間且不會產生負值）
      const canFitAbove = spaceAbove >= panelHeight + 10
      const canFitBelow = spaceBelow >= Math.min(panelHeight, 200) // 至少需要 200px 或更少
      
      if (spaceBelow >= panelHeight) {
        // 下方有足夠空間，優先使用下方
        top = rect.bottom + 5
      } else if (canFitAbove && spaceAbove > spaceBelow) {
        // 上方有足夠空間且比下方空間大
        top = rect.top - panelHeight - 5
      } else {
        // 都沒有足夠空間，選擇空間較大的一邊並調整位置
        if (spaceAbove > spaceBelow) {
          // 使用上方，但確保不會產生負值
          top = Math.max(10, rect.top - panelHeight - 5)
        } else {
          // 使用下方
          top = rect.bottom + 5
        }
      }
      
      // 檢查 IconPicker 是否超出視窗右邊
      if (left + iconPickerWidth > viewportWidth) {
        left = viewportWidth - iconPickerWidth - 10
      }
      
      // 檢查是否超出視窗左邊
      if (left < 10) {
        left = 10
      }
      
      panelPosition.value = {
        top: `${top}px`,
        left: `${left}px`
      }
      
      // 計算 ColorPicker 位置
      if (showColorPicker.value) {
        const idealColorPickerLeft = iconPickerWidth + 8 // IconPicker 寬度 + 8px 間距
        const rightSpaceAvailable = viewportWidth - (left + idealColorPickerLeft)
        
        let colorPickerLeft
        if (rightSpaceAvailable >= colorPickerWidth) {
          // 右側有足夠空間，正常顯示
          colorPickerLeft = idealColorPickerLeft
        } else {
          // 右側空間不足，逐漸重疊到 IconPicker 上
          const minGap = 10 // 最小間距
          const maxOverlap = iconPickerWidth - minGap // 最大重疊度
          const availableSpace = rightSpaceAvailable - minGap
          
          if (availableSpace <= 0) {
            // 完全重疊
            colorPickerLeft = iconPickerWidth - colorPickerWidth + minGap
          } else {
            // 部分重疊：根據可用空間計算重疊度
            const overlapRatio = Math.max(0, (colorPickerWidth - availableSpace) / colorPickerWidth)
            const overlap = overlapRatio * maxOverlap
            colorPickerLeft = iconPickerWidth - overlap + 8
          }
        }
        
        colorPickerPosition.value = {
          left: `${colorPickerLeft}px`
        }
      }
    }
    
    const togglePicker = async () => {
      isOpen.value = !isOpen.value
      if (isOpen.value) {
        // 打開時根據當前 iconType 設定正確的標籤頁，但如果沒有選中任何圖標，預設為 emoji
        if (selectedIcon.value && iconType.value === 'initials') {
          activeTab.value = 'initials'
          // 如果有選中的字母，設定到輸入框
          if (selectedIcon.value) {
            customInitials.value = selectedIcon.value
          }
        } else if (selectedIcon.value && (iconType.value === 'heroicons' || iconType.value === 'bootstrap')) {
          activeTab.value = 'icons'
        } else if (selectedIcon.value && iconType.value === 'upload') {
          activeTab.value = 'upload'
        } else if (selectedIcon.value && iconType.value === 'emoji') {
          activeTab.value = 'emoji'
          // 如果當前選中的是 emoji，檢測它的膚色
          if (selectedIcon.value) {
            const detectedSkinTone = getCurrentSkinTone(selectedIcon.value)
            selectedSkinTone.value = detectedSkinTone
          }
        } else {
          // 預設顯示 emoji 頁簽（沒有選中圖標或未知類型時）
          activeTab.value = 'emoji'
        }
        await nextTick()
        await calculatePosition()
      }
    }
    
    const closePicker = () => {
      isOpen.value = false
      emit('close')
    }
    
    
    const selectIcon = (icon, type) => {
      let iconValue = icon
      
      if (type === 'heroicons') {
        // 在圖標名稱前加上樣式前綴
        iconValue = `${selectedIconStyle.value}:${icon}`
      } else if (type === 'bootstrap') {
        // Bootstrap Icons 直接使用 class 名稱
        iconValue = icon
      }
      
      selectedIcon.value = iconValue
      iconType.value = type
      
      // 直接應用選擇的圖標
      emit('update:modelValue', iconValue)
      emit('update:iconType', type)
      closePicker()
    }
    
    const clearIcon = () => {
      if (!selectedIcon.value) return // 如果沒有圖標，不執行任何操作
      
      selectedIcon.value = ''
      iconType.value = ''
      uploadedImage.value = null
      emit('update:modelValue', '')
      emit('update:iconType', '')
      closePicker()
    }
    
    const clearSearch = () => {
      searchQuery.value = ''
    }
    
    // 處理膚色變更
    const handleSkinToneChange = (tone) => {
      selectedSkinTone.value = tone
    }
    
    // 獲取帶有膚色的 emoji
    const getEmojiWithSkinTone = (emojiData) => {
      if (!emojiData || !emojiData.emoji) return ''
      
      // 使用專門的膚色工具函數
      return applySkinTone(emojiData.emoji, selectedSkinTone.value)
    }
    
    // 獲取圖標組件（根據樣式選擇）
    const getIconComponent = (componentName) => {
      const components = selectedIconStyle.value === 'solid' 
        ? HeroiconsSolid 
        : HeroiconsOutline
      return components[componentName] || HeroiconsOutline[componentName]
    }
    
    // 處理圖標樣式變更
    const handleIconStyleChange = (style) => {
      selectedIconStyle.value = style
    }
    
    // 觸發檔案上傳
    const triggerFileUpload = () => {
      if (fileInput.value) {
        fileInput.value.click()
      }
    }
    
    // 處理檔案上傳
    const handleFileUpload = (event) => {
      const file = event.target.files?.[0]
      if (!file) return
      processFile(file)
    }
    
    // 處理檔案處理邏輯
    const processFile = (file) => {
      // 驗證檔案類型
      if (!file.type.startsWith('image/')) {
        alert('請選擇圖片檔案')
        return
      }
      
      // 驗證檔案大小 (2MB)
      const maxSize = 2 * 1024 * 1024
      if (file.size > maxSize) {
        alert('檔案大小不能超過 2MB')
        return
      }
      
      // 創建預覽 URL
      const reader = new FileReader()
      reader.onload = (e) => {
        uploadedImage.value = e.target.result
        selectedIcon.value = e.target.result
        iconType.value = 'upload'
        
        // 直接應用上傳的圖片
        emit('update:modelValue', e.target.result)
        emit('update:iconType', 'upload')
        emit('file-selected', file)
        closePicker()
      }
      reader.readAsDataURL(file)
      
      // 清空輸入以便下次使用
      if (fileInput.value) {
        fileInput.value.value = ''
      }
    }
    
    // 處理拖曳進入
    const handleDragOver = () => {
      isDragging.value = true
    }
    
    // 處理拖曳離開
    const handleDragLeave = () => {
      isDragging.value = false
    }
    
    // 處理拖放
    const handleDrop = (event) => {
      isDragging.value = false
      
      const files = event.dataTransfer?.files
      if (files && files.length > 0) {
        processFile(files[0])
      }
    }
    
    
    // 處理字母輸入
    const handleInitialsInput = () => {
      // 限制為3個字元，自動大寫
      if (customInitials.value) {
        customInitials.value = customInitials.value.toUpperCase().slice(0, 3)
      }
    }
    
    // 計算文字顏色
    const getTextColorClass = (bgColor) => {
      if (!bgColor) return 'text-white'
      
      // 移除 # 符號並轉換為 RGB
      const hex = bgColor.replace('#', '')
      const r = parseInt(hex.substr(0, 2), 16)
      const g = parseInt(hex.substr(2, 2), 16)
      const b = parseInt(hex.substr(4, 2), 16)
      
      // 計算相對亮度（W3C 公式）
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255
      
      // 如果亮度大於 0.5，使用深色文字；否則使用白色文字
      return luminance > 0.5 ? 'text-gray-800' : 'text-white'
    }
    
    // 應用字母作為圖標
    const applyInitials = () => {
      if (!customInitials.value) return
      
      selectedIcon.value = customInitials.value
      iconType.value = 'initials'
      
      // 發送更新
      emit('update:modelValue', customInitials.value)
      emit('update:iconType', 'initials')
      closePicker()
    }
    
    // 當切換到 emoji 標籤頁時，檢測當前選中 emoji 的膚色
    watch(activeTab, (newTab) => {
      if (newTab === 'emoji' && selectedIcon.value && iconType.value === 'emoji') {
        const detectedSkinTone = getCurrentSkinTone(selectedIcon.value)
        selectedSkinTone.value = detectedSkinTone
      }
    })
    
    // 按分類組織的圖標資料（包含分類標題）
    const groupedIcons = computed(() => {
      // 如果有搜尋查詢，返回篩選後的扁平陣列（不分組）
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        const filteredHeroIcons = heroIcons.filter(icon => 
          icon.name.toLowerCase().includes(query) || icon.component.toLowerCase().includes(query)
        )
        const filteredBsIcons = bsIcons.value.filter(icon => 
          icon.name.toLowerCase().includes(query) || icon.class.toLowerCase().includes(query)
        )
        return [...filteredHeroIcons, ...filteredBsIcons]
      }
      
      const result = []
      
      // 1. 添加 Heroicons 分類標題和圖標
      if (heroIcons.length > 0) {
        // 確保當前位置是 10 的倍數
        let currentLength = result.length
        let remainderInRow = currentLength % 10
        if (remainderInRow !== 0) {
          const fillersNeeded = 10 - remainderInRow
          for (let i = 0; i < fillersNeeded; i++) {
            result.push({ type: 'row-filler' })
          }
        }
        
        // 添加 Heroicons 標題
        result.push({
          type: 'category-header',
          categoryId: 'heroicons',
          name: 'Hero Icons',
          icon: '✨'
        })
        
        // 添加 9 個空項目來填滿標題行
        for (let i = 1; i < 10; i++) {
          result.push({ type: 'category-header-filler' })
        }
        
        // 添加 Heroicons
        result.push(...heroIcons)
      }
      
      // 2. 按分類添加 Bootstrap Icons
      const categoryOrder = ['general', 'ui', 'communications', 'files', 'media', 'people', 'alphanumeric', 'others']
      
      categoryOrder.forEach(categoryId => {
        const categoryIcons = bsIcons.value.filter(icon => icon.category === categoryId)
        
        if (categoryIcons.length > 0) {
          const categoryInfo = BOOTSTRAP_CATEGORY_INFO[categoryId]
          
          // 確保當前位置是 10 的倍數
          const currentLength = result.length
          const remainderInRow = currentLength % 10
          if (remainderInRow !== 0) {
            const fillersNeeded = 10 - remainderInRow
            for (let i = 0; i < fillersNeeded; i++) {
              result.push({ type: 'row-filler' })
            }
          }
          
          // 添加分類標題
          result.push({
            type: 'category-header',
            categoryId: categoryId,
            name: categoryInfo.name,
            icon: getCategoryIcon(categoryId)
          })
          
          // 添加 9 個空項目來填滿標題行
          for (let i = 1; i < 10; i++) {
            result.push({ type: 'category-header-filler' })
          }
          
          // 根據選擇的樣式過濾 Bootstrap Icons
          const filteredCategoryIcons = filterBootstrapIconsByStyle(categoryIcons, selectedIconStyle.value)
          result.push(...filteredCategoryIcons)
        }
      })
      
      return result
    })
    
    const filteredIcons = computed(() => {
      return groupedIcons.value
    })
    
    // Bootstrap Icons 分類圖標映射
    const getCategoryIcon = (categoryId) => {
      const iconMap = {
        'general': '🏠',
        'ui': '🎛️',  
        'communications': '💬',
        'files': '📁',
        'media': '🎵',
        'people': '👤',
        'alphanumeric': '🔤', 
        'others': '⚙️'
      }
      return iconMap[categoryId] || '📦'
    }
    
    // 根據樣式過濾 Bootstrap Icons
    const filterBootstrapIconsByStyle = (icons, style) => {
      if (!icons || icons.length === 0) return []
      
      // 建立圖標映射來分析變體關係
      const iconMap = new Map()
      icons.forEach(icon => {
        const className = icon.class || ''
        iconMap.set(className, icon)
      })
      
      return icons.filter(icon => {
        const className = icon.class || ''
        const isFillIcon = className.includes('-fill')
        
        if (style === 'outline') {
          if (isFillIcon) {
            // 如果是 fill 圖標，不顯示
            return false
          } else {
            // 基礎圖標或特殊變體，都顯示
            return true
          }
        } else if (style === 'solid') {
          if (isFillIcon) {
            // 顯示所有 -fill 圖標
            return true
          } else {
            // 基礎圖標：檢查是否有對應的 fill 版本
            const fillVersion = className + '-fill'
            const hasFillVersion = iconMap.has(fillVersion)
            
            if (hasFillVersion) {
              // 如果有 fill 版本，不顯示基礎版本（優先顯示 fill）
              return false
            } else {
              // 沒有 fill 版本的特殊變體，顯示
              return true
            }
          }
        }
        
        return true // 預設顯示所有
      })
    }
    
    // 按分類組織的 emoji 資料（包含分類標題）
    const groupedEmojis = computed(() => {
      // 確保 emojis 是陣列，處理 Proxy 情況
      const emojiArray = Array.isArray(emojis) ? emojis : []
      
      // 如果有搜尋查詢，返回篩選後的扁平陣列（不分組）
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        return emojiArray.filter(emoji => 
          emoji && emoji.name && emoji.name.toLowerCase().includes(query)
        )
      }
      
      // 按分類分組 emoji
      const grouped = {}
      emojiArray.forEach(emoji => {
        if (emoji && emoji.categoryId) {
          if (!grouped[emoji.categoryId]) {
            grouped[emoji.categoryId] = []
          }
          grouped[emoji.categoryId].push(emoji)
        }
      })
      
      
      // 轉換為包含標題的線性陣列，確保分類標題總是在行開頭
      const result = []
      const categoryOrder = ['smileys_emotion', 'people_body', 'animals_nature', 'food_drink', 'travel_places', 'activities', 'objects', 'symbols', 'flags']
      
      categoryOrder.forEach(categoryId => {
        if (grouped[categoryId] && grouped[categoryId].length > 0) {
          const categoryInfo = EMOJI_CATEGORY_INFO[categoryId]
          
          // 確保當前位置是 10 的倍數，這樣標題會在新行開頭
          const currentLength = result.length
          const remainderInRow = currentLength % 10
          if (remainderInRow !== 0) {
            // 添加空白項目填滿當前行
            const fillersNeeded = 10 - remainderInRow
            for (let i = 0; i < fillersNeeded; i++) {
              result.push({ type: 'row-filler' })
            }
          }
          
          // 添加分類標題項目，讓 VirtualScroll 把它當作獨立的一行處理
          result.push({
            type: 'category-header',
            categoryId: categoryId,
            name: categoryInfo.name,
            icon: categoryInfo.icon
          })
          
          // 添加 9 個空項目來填滿這一行，這樣標題就會獨佔一行
          for (let i = 1; i < 10; i++) {
            result.push({ type: 'category-header-filler' })
          }
          
          // 添加該分類的 emoji
          result.push(...grouped[categoryId])
        }
      })
      
      
      return result
    })
    
    const filteredEmojis = computed(() => {
      return groupedEmojis.value
    })
    
    // 檢查搜尋結果是否為空
    const isSearchEmpty = computed(() => {
      if (!searchQuery.value) return false
      
      if (activeTab.value === 'icons') {
        return filteredIcons.value.length === 0
      } else if (activeTab.value === 'emoji') {
        return filteredEmojis.value.length === 0
      }
      
      return false
    })
    
    // 點擊外部關閉
    const handleClickOutside = (event) => {
      // 檢查是否點擊在 IconPicker 內部
      const isInsideIconPicker = iconPanel.value && iconPanel.value.contains(event.target)
      const isIconPickerButton = iconPickerRef.value && iconPickerRef.value.contains(event.target)
      
      // 檢查是否點擊在 ColorPicker 內部
      const colorPickerPanel = document.querySelector('.color-picker .fixed')
      const isInsideColorPicker = colorPickerPanel && colorPickerPanel.contains(event.target)
      
      // 檢查是否點擊在任何 ColorPicker 按鈕上
      const isColorPickerButton = event.target.closest('.color-picker button')
      
      // 檢查是否點擊在滴管按鈕上
      const isEyedropperButton = event.target.closest('button i.bi-eyedropper') || 
                                event.target.matches('button i.bi-eyedropper') ||
                                (event.target.tagName === 'BUTTON' && event.target.querySelector('i.bi-eyedropper'))
      
      // 如果點擊在 ColorPicker 外部，關閉 ColorPicker 但保持 IconPicker 開啟
      if (showColorPicker.value && !isInsideColorPicker && !isColorPickerButton && !isEyedropperButton) {
        showColorPicker.value = false
      }
      
      // IconPicker 只在點擊外部且非 ColorPicker 區域時關閉
      if (!isInsideIconPicker && !isIconPickerButton && !isInsideColorPicker && !isColorPickerButton && !isEyedropperButton) {
        closePicker()
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
      window.addEventListener('resize', async () => {
        if (isOpen.value) await calculatePosition()
      })
      window.addEventListener('scroll', async () => {
        if (isOpen.value) await calculatePosition()
      })
      
      // 觸發 emoji 載入（如果還沒載入）
      if (Array.isArray(emojis) && emojis.length === 0) {
        // Proxy 會自動觸發載入
        // 存取 length 屬性會觸發載入
        const emojiCount = emojis.length
        // 一秒後強制更新
        setTimeout(() => {
          emojisLoaded.value = true
        }, 2000)
      }
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      window.removeEventListener('resize', calculatePosition)
      window.removeEventListener('scroll', calculatePosition)
    })
    
    return {
      isOpen,
      iconPanel,
      iconPickerRef,
      searchQuery,
      activeTab,
      panelPosition,
      colorPickerPosition,
      calculatePosition,
      selectedIcon,
      iconType,
      heroIcons,
      bsIcons,
      emojis: emojis,
      emojisLoaded,
      filteredIcons,
      filteredEmojis,
      isSearchEmpty,
      togglePicker,
      closePicker,
      selectIcon,
      clearIcon,
      clearSearch,
      selectedSkinTone,
      handleSkinToneChange,
      getEmojiWithSkinTone,
      getIconComponent,
      selectedIconStyle,
      handleIconStyleChange,
      fileInput,
      triggerFileUpload,
      handleFileUpload,
      uploadedImage,
      isDragging,
      handleDragOver,
      handleDragLeave,
      handleDrop,
      backgroundColor,
      localBackgroundColor,
      showColorPicker,
      colorPickerRef,
      eyedropperButton,
      openColorPicker,
      closeColorPicker,
      handleBackgroundColorChange,
      defaultColors,
      lightColors,
      selectColor,
      handleColorInput,
      handleTextInput,
      selectRandomColor,
      clearColor,
      customInitials,
      handleInitialsInput,
      applyInitials,
      getTextColorClass,
      getDisplayIcon: (icon) => {
        // 如果圖標包含樣式前綴，移除它
        if (icon && icon.includes(':')) {
          return icon.split(':')[1]
        }
        return icon
      },
      isIconSelected: (iconIdentifier) => {
        // 檢查是否選中
        if (!selectedIcon.value) return false
        
        // 對於 Heroicons，忽略樣式前綴進行比較
        if (selectedIcon.value.includes(':') && iconType.value === 'heroicons') {
          const currentIcon = selectedIcon.value.split(':')[1]
          return currentIcon === iconIdentifier
        }
        
        // 對於 Bootstrap Icons 或其他類型，直接比較
        return selectedIcon.value === iconIdentifier
      }
    }
  }
}
</script>

<style scoped>
.grid-wrapper {
  padding-left: 0.125rem;
  padding-right: 0.125rem;
}
.icon-picker {
  @apply relative inline-block;
}
.emoji-button,
.icon-button {
  width: 30px;
  height: 30px;
}

/* 分類標題行樣式 */
.category-header {
  grid-column: 1 / -1;
}

.icon-button svg {
  width: 1.35rem;
  height: 1.35rem;
}

.icon-filter {
  padding: 0.375rem 0.625rem;
}
</style>

<style>
.icon-grid-wrapper .virtual-scroll-container {
  padding-left: 0.4rem;
  padding-top: 0.3rem;
}
.grid-row.first-row .category-header {
  @apply pt-1;
}
</style>