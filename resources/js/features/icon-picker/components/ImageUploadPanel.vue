<template>
  <div class="image-upload-panel">
    <!-- 上傳區域 -->
    <div
      @click="triggerFileUpload"
      @dragover.prevent="handleDragOver"
      @dragleave.prevent="handleDragLeave" 
      @drop.prevent="handleDrop"
      :class="[
        'h-48 flex flex-col items-center justify-center border-2 border-dashed rounded-md transition-colors cursor-pointer',
        isDragging ? 'border-primary-400 bg-primary-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'
      ]"
    >
      <div class="text-center pointer-events-none space-y-3">
        <i class="bi bi-cloud-arrow-up-fill text-4xl text-gray-400"></i>
        <div>
          <p class="text-sm font-medium text-gray-700">Upload an image</p>
          <p class="text-xs text-gray-500 mt-1">or drag and drop</p>
        </div>
        <p class="text-xs text-gray-400">PNG, JPG, GIF up to 10MB</p>
      </div>
    </div>

    <!-- 隱藏的檔案輸入 -->
    <input
      ref="fileInput"
      type="file"
      accept="image/*"
      @change="handleFileUpload"
      class="hidden"
    />

    <!-- 錯誤訊息 -->
    <div v-if="error" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-md">
      <div class="flex">
        <i class="bi bi-exclamation-triangle-fill text-red-400 mr-2"></i>
        <p class="text-sm text-red-700">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Props
defineProps({
  selectedIcon: {
    type: [String, Object],
    default: null
  }
})

// Emits
const emit = defineEmits(['icon-select', 'file-selected'])

// 響應式狀態
const fileInput = ref(null)
const isDragging = ref(false)
const error = ref('')

// 最大檔案大小 (10MB)
const MAX_FILE_SIZE = 10 * 1024 * 1024

// 觸發檔案選擇
const triggerFileUpload = () => {
  fileInput.value?.click()
}

// 處理檔案上傳
const handleFileUpload = (event) => {
  const files = event.target?.files
  if (files && files.length > 0) {
    processFile(files[0])
  }
}

// 驗證檔案
const validateFile = (file) => {
  error.value = ''
  
  // 檢查檔案類型
  if (!file.type.startsWith('image/')) {
    error.value = '請選擇圖片檔案'
    return false
  }
  
  // 檢查檔案大小
  if (file.size > MAX_FILE_SIZE) {
    error.value = '檔案大小不能超過 10MB'
    return false
  }
  
  return true
}

// 處理檔案
const processFile = (file) => {
  if (!validateFile(file)) {
    return
  }
  
  const reader = new FileReader()
  reader.onload = (e) => {
    const imageData = e.target.result
    
    // 發送圖標選擇事件
    emit('icon-select', {
      type: 'image',
      value: imageData,
      iconType: 'upload'
    })
    
    // 發送檔案選擇事件
    emit('file-selected', file)
  }
  
  reader.onerror = () => {
    error.value = '檔案讀取失敗，請重試'
  }
  
  reader.readAsDataURL(file)
  
  // 清空檔案輸入以便重複選擇
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// 拖拉處理
const handleDragOver = (event) => {
  event.preventDefault()
  isDragging.value = true
}

const handleDragLeave = (event) => {
  event.preventDefault()
  isDragging.value = false
}

const handleDrop = (event) => {
  event.preventDefault()
  isDragging.value = false
  
  const files = event.dataTransfer?.files
  if (files && files.length > 0) {
    processFile(files[0])
  }
}
</script>

<style scoped>
.image-upload-panel {
  @apply w-full;
}

/* 拖拉狀態動畫 */
.border-primary-400 {
  transition: all 0.2s ease-in-out;
}
</style>