<template>
  <div class="relative">
    <button
      type="button"
      @click="toggleDropdown"
      class="p-2 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
      :title="`圖標變體: ${getVariantLabel(currentVariant)}`"
    >
      <!-- 顯示當前選擇的變體圖標 -->
      <i :class="`bi ${getPreviewIcon()} text-gray-600`" style="font-size: 1.25rem;"></i>
    </button>

    <!-- 下拉選單 -->
    <Transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 z-10 mt-1 w-40 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
      >
        <div class="py-1">
          <!-- 自動選擇選項 -->
          <button
            @click="selectVariant('auto')"
            :class="[
              modelValue === 'auto' ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50',
              'w-full px-3 py-2 text-left text-sm flex items-center space-x-2 transition-colors'
            ]"
          >
            <i class="bi bi-square-half w-5 h-5 flex items-center justify-center" style="font-size: 1rem;"></i>
            <span>自動</span>
            <svg 
              v-if="modelValue === 'auto'"
              class="w-4 h-4 ml-auto text-primary-600" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>

          <!-- 標準版選項 -->
          <button
            @click="selectVariant('standard')"
            :class="[
              modelValue === 'standard' ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50',
              'w-full px-3 py-2 text-left text-sm flex items-center space-x-2 transition-colors'
            ]"
          >
            <i class="bi bi-square w-5 h-5 flex items-center justify-center" style="font-size: 1rem;"></i>
            <span>標準</span>
            <svg 
              v-if="modelValue === 'standard'"
              class="w-4 h-4 ml-auto text-primary-600" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>

          <!-- 填充版選項 -->
          <button
            @click="selectVariant('fill')"
            :class="[
              modelValue === 'fill' ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50',
              'w-full px-3 py-2 text-left text-sm flex items-center space-x-2 transition-colors'
            ]"
          >
            <i class="bi bi-square-fill w-5 h-5 flex items-center justify-center" style="font-size: 1rem;"></i>
            <span>填充 (Fill)</span>
            <svg 
              v-if="modelValue === 'fill'"
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
        <div class="px-3 py-2 border-t border-gray-100">
          <p class="text-xs text-gray-500">
            {{ getVariantDescription(modelValue) }}
          </p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'

export default {
  name: 'BSIconVariantSelector',
  props: {
    modelValue: {
      type: String,
      default: 'auto',
      validator: (value) => ['standard', 'fill', 'auto'].includes(value)
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    
    // 當前顯示的變體
    const currentVariant = computed(() => props.modelValue)
    
    // 獲取變體標籤
    const getVariantLabel = (variant) => {
      const labels = {
        standard: '標準',
        fill: '填充',
        auto: '自動'
      }
      return labels[variant] || '自動'
    }
    
    // 獲取變體描述
    const getVariantDescription = (variant) => {
      const descriptions = {
        standard: '線條樣式圖標',
        fill: '填充樣式圖標',
        auto: '顯示所有可用變體'
      }
      return descriptions[variant] || ''
    }
    
    // 獲取預覽圖標
    const getPreviewIcon = () => {
      switch (currentVariant.value) {
        case 'fill':
          return 'bi-square-fill'
        case 'standard':
          return 'bi-square'
        default:
          return 'bi-square-half'
      }
    }
    
    const toggleDropdown = () => {
      isOpen.value = !isOpen.value
    }
    
    const selectVariant = (variant) => {
      emit('update:modelValue', variant)
      isOpen.value = false
    }
    
    // 點擊外部關閉下拉選單
    const handleClickOutside = (event) => {
      const button = event.target.closest('button')
      const dropdown = event.target.closest('.absolute')
      
      if (!button && !dropdown) {
        isOpen.value = false
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })
    
    return {
      isOpen,
      currentVariant,
      toggleDropdown,
      selectVariant,
      getVariantLabel,
      getVariantDescription,
      getPreviewIcon
    }
  }
}
</script>

<style>
.relative > button {
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.relative > button > i {
  font-size: 1em !important;
}
</style>