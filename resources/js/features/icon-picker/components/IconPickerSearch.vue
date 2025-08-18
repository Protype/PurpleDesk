<template>
  <div class="relative flex-1">
    <input
      ref="inputRef"
      :value="modelValue"
      type="text"
      :placeholder="placeholder"
      class="icon-filter w-full text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
      @input="handleInput"
      @focus="handleFocus"
      @blur="handleBlur"
      @keyup.enter="handleSearch"
      @keyup.escape="handleClear"
    />
    <button
      v-if="modelValue"
      @click="handleClear"
      class="clear-button absolute right-2 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center"
      type="button"
      title="清除搜尋"
    >
      ×
    </button>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'IconPickerSearch',
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: 'Filter...'
    }
  },
  emits: ['update:modelValue', 'input', 'focus', 'blur', 'search', 'clear'],
  setup(props, { emit }) {
    const inputRef = ref(null)

    /**
     * 處理輸入事件
     */
    const handleInput = (event) => {
      const value = event.target.value
      emit('update:modelValue', value)
      emit('input', value)
    }

    /**
     * 處理 focus 事件
     */
    const handleFocus = (event) => {
      emit('focus', event)
    }

    /**
     * 處理 blur 事件
     */
    const handleBlur = (event) => {
      emit('blur', event)
    }

    /**
     * 處理搜尋事件（Enter 鍵）
     */
    const handleSearch = () => {
      emit('search', props.modelValue)
    }

    /**
     * 處理清除事件
     */
    const handleClear = () => {
      emit('update:modelValue', '')
      emit('clear')
      
      // 清除後重新聚焦到輸入框
      if (inputRef.value) {
        inputRef.value.focus()
      }
    }

    /**
     * 聚焦到搜尋輸入框
     */
    const focus = () => {
      if (inputRef.value) {
        inputRef.value.focus()
      }
    }

    /**
     * 失焦搜尋輸入框
     */
    const blur = () => {
      if (inputRef.value) {
        inputRef.value.blur()
      }
    }

    return {
      inputRef,
      handleInput,
      handleFocus,
      handleBlur,
      handleSearch,
      handleClear,
      focus,
      blur
    }
  }
}
</script>

<style scoped>
.icon-filter {
  padding: 0.375rem 0.625rem;
}

/* 確保清除按鈕在輸入框有內容時有足夠的右邊距 */
.icon-filter:not(:placeholder-shown) {
  padding-right: 2rem;
}

.clear-button {
  font-size: 1.2rem;
  line-height: 1;
  cursor: pointer;
  user-select: none;
}

.clear-button:hover {
  color: #4b5563;
}

.clear-button:active {
  transform: translate(-50%, -50%) scale(0.95);
}
</style>