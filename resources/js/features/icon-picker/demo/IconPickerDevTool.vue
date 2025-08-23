<template>
  <div 
    v-if="showDevTool" 
    class="fixed bottom-4 right-4 bg-white border border-gray-300 rounded-lg shadow-lg p-3 z-[10000] max-w-xs"
  >
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold text-gray-700">🎛️ IconPicker 開發工具</h3>
      <button 
        @click="toggleDevTool" 
        class="text-gray-400 hover:text-gray-600 text-xs"
        title="關閉"
      >
        ✕
      </button>
    </div>
    
    <div class="space-y-2">
      <div class="text-xs text-gray-600">
        當前版本：<span class="font-mono font-semibold">{{ currentVersion }}</span>
      </div>
      
      <div class="space-y-1">
        <button
          @click="switchToOriginal"
          :class="isOriginal ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-gray-50 text-gray-700 border-gray-300'"
          class="w-full text-left px-2 py-1 text-xs border rounded transition-colors"
        >
          📦 原版 (IconPickerOri)
        </button>
        
        <button
          @click="switchToNew"
          :class="!isOriginal ? 'bg-green-100 text-green-700 border-green-300' : 'bg-gray-50 text-gray-700 border-gray-300'"
          class="w-full text-left px-2 py-1 text-xs border rounded transition-colors"
        >
          ✨ 新版 (IconPicker)
        </button>
      </div>
      
      <div class="text-xs text-gray-500 border-t pt-2">
        <div>快速切換：</div>
        <div class="font-mono">?iconpicker=original</div>
        <div class="font-mono">?iconpicker=new</div>
      </div>
    </div>
  </div>
  
  <!-- 浮動切換按鈕 -->
  <button
    v-if="!showDevTool && isDev"
    @click="toggleDevTool"
    class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg z-[10000] flex items-center justify-center transition-colors"
    title="開啟 IconPicker 開發工具"
  >
    🎛️
  </button>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'IconPickerDevTool',
  setup() {
    const showDevTool = ref(false)
    const isDev = ref(false)
    
    // 檢查是否為開發環境
    onMounted(() => {
      isDev.value = import.meta.env.DEV
      
      // 檢查是否有保存的開發工具狀態
      if (typeof window !== 'undefined') {
        const savedState = localStorage.getItem('iconpicker-devtool-visible')
        if (savedState === 'true') {
          showDevTool.value = true
        }
      }
    })
    
    const isOriginal = computed(() => {
      if (typeof window === 'undefined') return true
      
      const savedSetting = localStorage.getItem('iconpicker-use-original')
      if (savedSetting === 'true') return true
      if (savedSetting === 'false') return false
      
      const urlParams = new URLSearchParams(window.location.search)
      if (urlParams.get('iconpicker') === 'original') return true
      if (urlParams.get('iconpicker') === 'new') return false
      
      return false // 預設新版
    })
    
    const currentVersion = computed(() => {
      return isOriginal.value ? 'IconPickerOri' : 'IconPicker'
    })
    
    const toggleDevTool = () => {
      showDevTool.value = !showDevTool.value
      if (typeof window !== 'undefined') {
        localStorage.setItem('iconpicker-devtool-visible', showDevTool.value.toString())
      }
    }
    
    const switchToOriginal = () => {
      if (typeof window !== 'undefined') {
        localStorage.setItem('iconpicker-use-original', 'true')
        window.location.reload()
      }
    }
    
    const switchToNew = () => {
      if (typeof window !== 'undefined') {
        localStorage.setItem('iconpicker-use-original', 'false')
        window.location.reload()
      }
    }
    
    return {
      showDevTool,
      isDev,
      isOriginal,
      currentVersion,
      toggleDevTool,
      switchToOriginal,
      switchToNew
    }
  }
}
</script>