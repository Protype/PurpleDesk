<template>
  <component 
    :is="currentIconPickerComponent" 
    v-bind="$attrs" 
    @update:modelValue="$emit('update:modelValue', $event)"
    @update:iconType="$emit('update:iconType', $event)"
    @file-selected="$emit('file-selected', $event)"
    @close="$emit('close')"
    @background-color-change="$emit('background-color-change', $event)"
  />
</template>

<script>
import { computed } from 'vue'
import IconPicker from '../IconPicker.vue'

export default {
  name: 'IconPickerProxy',
  components: {
    IconPicker
  },
  emits: ['update:modelValue', 'update:iconType', 'file-selected', 'close', 'background-color-change'],
  setup() {
    // 檢查環境變數控制版本選擇
    // 在開發環境中可以通過 localStorage 或 URL 參數切換版本
    const useOriginalVersion = computed(() => {
      // 檢查 localStorage 設定
      if (typeof window !== 'undefined') {
        const savedSetting = localStorage.getItem('iconpicker-use-original')
        if (savedSetting === 'true') return true
        if (savedSetting === 'false') return false
        
        // 檢查 URL 參數
        const urlParams = new URLSearchParams(window.location.search)
        if (urlParams.get('iconpicker') === 'original') return true
        if (urlParams.get('iconpicker') === 'new') return false
      }
      
      // 檢查環境變數
      if (import.meta.env.VITE_ICONPICKER_VERSION === 'original') return true
      
      // 預設使用新版（展示 ImageUploadPanel）
      return false
    })
    
    const currentIconPickerComponent = computed(() => {
      // 原版已移除，統一使用新版 IconPicker
      return 'IconPicker'
    })
    
    // 提供全域方法供開發者工具使用
    if (typeof window !== 'undefined' && import.meta.env.DEV) {
      window.switchIconPicker = (version) => {
        if (version === 'original' || version === 'new') {
          localStorage.setItem('iconpicker-use-original', version === 'original')
          window.location.reload()
        }
      }
      
      // 在 console 顯示版本資訊
      if (!window._iconPickerProxyInit) {
        console.log('%c🎛️ IconPicker 版本資訊', 'color: #6366f1; font-weight: bold;')
        console.log('📍 目前統一使用新版 IconPicker')
        console.log('💡 原版 IconPickerOri 已移除，可通過 git tag backup/icon-picker-ori-v1.0 回溯')
        window._iconPickerProxyInit = true
      }
    }
    
    return {
      currentIconPickerComponent
    }
  }
}
</script>