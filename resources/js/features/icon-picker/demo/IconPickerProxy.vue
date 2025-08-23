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
import IconPickerOri from '@/components/common/IconPickerOri.vue'

export default {
  name: 'IconPickerProxy',
  components: {
    IconPicker,
    IconPickerOri
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
      return useOriginalVersion.value ? 'IconPickerOri' : 'IconPicker'
    })
    
    // 提供全域方法供開發者工具使用
    if (typeof window !== 'undefined' && import.meta.env.DEV) {
      window.switchIconPicker = (version) => {
        if (version === 'original' || version === 'new') {
          localStorage.setItem('iconpicker-use-original', version === 'original')
          window.location.reload()
        }
      }
      
      // 在 console 顯示切換說明
      if (!window._iconPickerProxyInit) {
        console.log('%c🎛️ IconPicker 版本控制', 'color: #6366f1; font-weight: bold;')
        console.log('💡 切換版本：')
        console.log('   • switchIconPicker("original") - 切換到原版')
        console.log('   • switchIconPicker("new") - 切換到新版')
        console.log('   • 或在 URL 加上 ?iconpicker=original 或 ?iconpicker=new')
        console.log(`📍 當前使用版本：${useOriginalVersion.value ? '原版 (IconPickerOri)' : '新版 (IconPicker)'}`)
        window._iconPickerProxyInit = true
      }
    }
    
    return {
      currentIconPickerComponent
    }
  }
}
</script>