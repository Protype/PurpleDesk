<template>
  <div class="variant-selector" ref="selectorRef">
    <!-- 變體選擇按鈕 -->
    <button
      type="button"
      @click="toggleSelector"
      :title="currentVariantName"
      class="variant-button"
      :class="{ 'active': isOpen }"
    >
      <!-- 圖標樣式選擇器 -->
      <template v-if="variantType === 'iconStyle'">
        <component 
          v-if="currentVariant.icon"
          :is="currentVariant.icon" 
          class="w-5 h-5 text-gray-600"
        />
        <div v-else class="style-indicator" :class="currentVariant.value">
          <div class="indicator-shape"></div>
        </div>
      </template>
      
      <!-- 膚色選擇器 -->
      <template v-else-if="variantType === 'skinTone'">
        <span class="text-xl">{{ currentVariant.emoji }}</span>
      </template>
      
      <!-- 通用變體選擇器 -->
      <template v-else>
        <span class="variant-preview">{{ currentVariant.label }}</span>
      </template>
    </button>

    <!-- 變體選項下拉選單 -->
    <Teleport to="body">
      <div
        v-if="isOpen"
        ref="dropdownRef"
        class="variant-dropdown"
        :style="dropdownPosition"
        @click.stop
      >
        <div class="variant-options">
          <button
            v-for="variant in variants"
            :key="variant.value"
            @click="selectVariant(variant.value)"
            :title="variant.description || variant.label"
            class="variant-option"
            :class="{ 'selected': modelValue === variant.value }"
          >
            <!-- 圖標樣式選項 -->
            <template v-if="variantType === 'iconStyle'">
              <component 
                v-if="variant.icon"
                :is="variant.icon" 
                class="w-5 h-5"
              />
              <div v-else class="style-indicator" :class="variant.value">
                <div class="indicator-shape"></div>
              </div>
              <span>{{ variant.label }}</span>
            </template>
            
            <!-- 膚色選項 -->
            <template v-else-if="variantType === 'skinTone'">
              <div class="tone-preview" :style="{ backgroundColor: variant.color }"></div>
              <span class="tone-emoji">{{ variant.emoji }}</span>
            </template>
            
            <!-- 通用選項 -->
            <template v-else>
              <span>{{ variant.label }}</span>
            </template>
            
            <!-- 選中指示器 -->
            <svg 
              v-if="modelValue === variant.value"
              class="w-4 h-4 ml-auto text-primary-600" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>

        <!-- 說明文字 -->
        <div v-if="showDescription" class="variant-description">
          <p class="text-xs text-gray-500">
            {{ currentVariant.description || currentVariant.label }}
          </p>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'

export default {
  name: 'VariantSelector',
  props: {
    /**
     * 當前選中的變體值
     */
    modelValue: {
      type: [String, Number],
      default: ''
    },
    
    /**
     * 變體類型
     * - 'iconStyle': 圖標樣式變體 (outline/solid)
     * - 'skinTone': 膚色變體 (0-5)
     * - 'custom': 自定義變體
     */
    variantType: {
      type: String,
      default: 'iconStyle',
      validator: (value) => ['iconStyle', 'skinTone', 'custom'].includes(value)
    },
    
    /**
     * 變體選項陣列
     * 格式: [{ value, label, description?, emoji?, color?, icon? }]
     */
    variants: {
      type: Array,
      default: () => []
    },
    
    /**
     * 是否顯示說明文字
     */
    showDescription: {
      type: Boolean,
      default: true
    },
    
    /**
     * 下拉選單寬度
     */
    dropdownWidth: {
      type: Number,
      default: 200
    }
  },
  
  emits: ['update:modelValue', 'variant-change'],
  
  setup(props, { emit }) {
    const isOpen = ref(false)
    const selectorRef = ref(null)
    const dropdownRef = ref(null)
    const dropdownPosition = ref({ top: '0px', left: '0px' })

    // 預設變體選項
    const defaultVariants = computed(() => {
      if (props.variantType === 'iconStyle') {
        return [
          { 
            value: 'outline', 
            label: 'Outline', 
            description: '線條樣式',
            icon: null // 將由父元件或使用方提供
          },
          { 
            value: 'solid', 
            label: 'Solid', 
            description: '實心樣式',
            icon: null // 將由父元件或使用方提供
          }
        ]
      } else if (props.variantType === 'skinTone') {
        return [
          { value: 0, label: '預設', emoji: '👋', color: '#FFC83D' },
          { value: 1, label: '淺膚色', emoji: '👋🏻', color: '#F7DECE' },
          { value: 2, label: '中淺膚色', emoji: '👋🏼', color: '#F3D2A2' },
          { value: 3, label: '中膚色', emoji: '👋🏽', color: '#D5AB88' },
          { value: 4, label: '中深膚色', emoji: '👋🏾', color: '#AF7E57' },
          { value: 5, label: '深膚色', emoji: '👋🏿', color: '#7C533E' }
        ]
      }
      return []
    })

    // 實際使用的變體選項
    const variants = computed(() => {
      return props.variants.length > 0 ? props.variants : defaultVariants.value
    })

    // 當前選中的變體
    const currentVariant = computed(() => {
      const variant = variants.value.find(v => v.value === props.modelValue)
      return variant || variants.value[0] || { value: '', label: '', description: '' }
    })

    // 當前變體名稱
    const currentVariantName = computed(() => {
      const variant = currentVariant.value
      if (props.variantType === 'iconStyle') {
        return `圖標樣式: ${variant.label}`
      } else if (props.variantType === 'skinTone') {
        return `膚色: ${variant.label}`
      }
      return variant.label || variant.description
    })

    // 計算下拉選單位置
    const calculatePosition = async () => {
      if (!selectorRef.value) return

      await nextTick()

      const rect = selectorRef.value.getBoundingClientRect()
      const viewportHeight = window.innerHeight
      const viewportWidth = window.innerWidth
      
      // 下拉選單尺寸
      const dropdownWidth = props.dropdownWidth
      const dropdownHeight = variants.value.length * 40 + 60 // 估算高度
      
      let top = rect.bottom + 5
      let left = rect.left
      
      // 檢查是否超出視窗底部
      if (top + dropdownHeight > viewportHeight) {
        top = rect.top - dropdownHeight - 5
      }
      
      // 檢查是否超出視窗右邊
      if (left + dropdownWidth > viewportWidth) {
        left = viewportWidth - dropdownWidth - 10
      }
      
      // 檢查是否超出視窗左邊
      if (left < 10) {
        left = 10
      }
      
      dropdownPosition.value = {
        top: `${top}px`,
        left: `${left}px`
      }
    }

    // 切換選擇器
    const toggleSelector = async () => {
      isOpen.value = !isOpen.value
      if (isOpen.value) {
        await calculatePosition()
      }
    }

    // 關閉選擇器
    const closeSelector = () => {
      isOpen.value = false
    }

    // 選擇變體
    const selectVariant = (value) => {
      emit('update:modelValue', value)
      emit('variant-change', {
        type: props.variantType,
        value: value,
        variant: variants.value.find(v => v.value === value)
      })
      closeSelector()
    }

    // 點擊外部關閉
    const handleClickOutside = (event) => {
      const button = event.target.closest('.variant-selector')
      const dropdown = event.target.closest('.variant-dropdown')
      
      if (!button && !dropdown) {
        closeSelector()
      }
    }

    // 處理視窗調整
    const handleResize = () => {
      if (isOpen.value) {
        calculatePosition()
      }
    }

    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
      window.addEventListener('resize', handleResize)
      window.addEventListener('scroll', handleResize)
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      window.removeEventListener('resize', handleResize)
      window.removeEventListener('scroll', handleResize)
    })

    return {
      isOpen,
      selectorRef,
      dropdownRef,
      dropdownPosition,
      variants,
      currentVariant,
      currentVariantName,
      toggleSelector,
      closeSelector,
      selectVariant
    }
  }
}
</script>

<style scoped>
.variant-selector {
  @apply relative inline-block;
}

.variant-button {
  @apply w-8 h-8 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors bg-white flex items-center justify-center;
  width: 34px;
  height: 34px;
}

.variant-button.active {
  @apply ring-2 ring-primary-500 ring-offset-2;
}

.variant-dropdown {
  @apply fixed z-[10000] p-2 bg-white border border-gray-200 rounded-lg shadow-xl;
  min-width: 180px;
}

.variant-options {
  @apply space-y-1;
}

.variant-option {
  @apply w-full px-3 py-2 text-left text-sm flex items-center space-x-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors;
}

.variant-option.selected {
  @apply bg-primary-50 ring-2 ring-primary-500;
}

.variant-description {
  @apply mt-2 pt-2 border-t border-gray-100;
}

.style-indicator {
  @apply w-5 h-5 flex items-center justify-center;
}

.indicator-shape {
  @apply w-3 h-3 border-2 border-gray-600 transition-all;
}

.style-indicator.outline .indicator-shape {
  @apply bg-transparent border-gray-600;
}

.style-indicator.solid .indicator-shape {
  @apply bg-gray-600 border-gray-600;
}

.tone-preview {
  @apply w-6 h-6 rounded-full border-2 border-gray-300;
}

.tone-emoji {
  @apply text-lg;
}

.variant-preview {
  @apply text-xs font-medium text-gray-600;
}
</style>