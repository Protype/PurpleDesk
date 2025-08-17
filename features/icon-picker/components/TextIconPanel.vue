<template>
  <div class="text-icon-panel space-y-4">
    <!-- 文字輸入區 -->
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
</template>

<script>
import { ref, watch } from 'vue'

export default {
  name: 'TextIconPanel',
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    backgroundColor: {
      type: String,
      default: '#6366f1'
    }
  },
  emits: ['update:modelValue', 'text-selected'],
  setup(props, { emit }) {
    const customInitials = ref('')
    
    // 監聽 modelValue 變化，同步到輸入框
    watch(() => props.modelValue, (newVal) => {
      if (newVal) {
        customInitials.value = newVal
      }
    }, { immediate: true })
    
    // 處理文字輸入 - 限制3字元並自動大寫
    const handleInitialsInput = () => {
      if (customInitials.value) {
        customInitials.value = customInitials.value.toUpperCase().slice(0, 3)
      }
      // 即時更新 modelValue
      emit('update:modelValue', customInitials.value)
    }
    
    // 計算文字顏色 - 根據背景顏色自動選擇深色或淺色文字
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
    
    // 應用文字圖標
    const applyInitials = () => {
      if (!customInitials.value) return
      
      emit('update:modelValue', customInitials.value)
      emit('text-selected', {
        text: customInitials.value,
        backgroundColor: props.backgroundColor
      })
    }
    
    return {
      customInitials,
      handleInitialsInput,
      getTextColorClass,
      applyInitials
    }
  }
}
</script>

<style scoped>
.text-icon-panel {
  /* 基本樣式由 Tailwind 處理 */
}
</style>