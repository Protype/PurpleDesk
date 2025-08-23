<template>
  <div class="image-upload-panel">
    <!-- 上傳區域 -->
    <div
      @click="triggerFileUpload"
      @dragover.prevent="handleDragOver"
      @dragleave.prevent="handleDragLeave" 
      @drop.prevent="handleDrop"
      :class="[
        'h-48 flex flex-col items-center justify-center border-2 border-dashed rounded-md transition-colors cursor-pointer relative',
        isDragging ? 'border-primary-400 bg-primary-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100',
        isUploading ? 'cursor-not-allowed' : 'cursor-pointer'
      ]"
    >
      <!-- 載入狀態遮罩 -->
      <div 
        v-if="isUploading"
        class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center rounded-md z-10"
      >
        <div class="text-center space-y-3">
          <div class="loading-spinner mx-auto"></div>
          <p class="text-sm font-medium text-gray-700">處理圖片中...</p>
        </div>
      </div>
      
      <!-- 正常上傳界面 -->
      <div class="text-center pointer-events-none space-y-3" :class="{ 'opacity-30': isUploading }">
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
import { ref, onUnmounted } from 'vue'

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
const isUploading = ref(false)
const error = ref('')

// 最大檔案大小 (10MB)
const MAX_FILE_SIZE = 10 * 1024 * 1024

// 觸發檔案選擇
const triggerFileUpload = () => {
  // 如果正在上傳，不允許觸發新的檔案選擇
  if (isUploading.value) {
    return
  }
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
const processFile = async (file) => {
  if (!validateFile(file)) {
    return
  }
  
  // 開始載入狀態
  isUploading.value = true
  error.value = ''
  
  try {
    // 建立預覽 URL（但不保存到內部狀態）
    const blobUrl = URL.createObjectURL(file)
    
    // 模擬處理時間 (實際上可能是圖片壓縮、上傳等)
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // 發送圖標選擇事件
    emit('icon-select', {
      type: 'image',
      value: blobUrl, // 傳遞 Blob URL，但由接收方負責管理
      file: file, // 傳遞原始檔案物件供後續處理
      iconType: 'upload'
    })
    
    // 發送檔案選擇事件
    emit('file-selected', file)
    
  } catch (err) {
    error.value = '圖片處理失敗，請重試'
    console.error('Image processing error:', err)
  } finally {
    // 結束載入狀態
    isUploading.value = false
  }
  
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
  
  // 如果正在上傳，忽略新的拖放
  if (isUploading.value) {
    return
  }
  
  const files = event.dataTransfer?.files
  if (files && files.length > 0) {
    processFile(files[0])
  }
}

// 注意：不在這裡清理 Blob URL，因為它們會被傳遞給其他元件使用
// Blob URL 的生命週期應該由使用它們的元件（如 IconDisplay）管理
</script>

<style scoped>
.image-upload-panel {
  @apply w-full;
}

/* 拖拉狀態動畫 */
.border-primary-400 {
  transition: all 0.2s ease-in-out;
}

/* 轉圈載入動畫 */
.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #6366f1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* 上傳區域過場動畫 */
.image-upload-panel > div {
  transition: opacity 0.3s ease-in-out;
}
</style>