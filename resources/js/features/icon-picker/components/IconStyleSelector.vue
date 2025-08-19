<template>
  <div class="relative">
    <button
      type="button"
      @click="toggleDropdown"
      class="p-2 me-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
      :title="`圖標樣式: ${currentStyle === 'outline' ? 'Outline' : 'Solid'}`"
    >
      <!-- 顯示當前選擇的樣式圖標 -->
      <component 
        v-if="currentStyle === 'outline'"
        :is="OutlineIcon" 
        class="w-5 h-5 text-gray-600"
      />
      <component 
        v-else
        :is="SolidIcon" 
        class="w-5 h-5 text-gray-600"
      />
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
        class="absolute right-0 z-10 mt-1 w-36 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
      >
        <div class="py-1">
          <!-- Outline 選項 -->
          <button
            @click="selectStyle('outline')"
            :class="[
              modelValue === 'outline' ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50',
              'w-full px-3 py-2 text-left text-sm flex items-center space-x-2 transition-colors'
            ]"
          >
            <component :is="OutlineIcon" class="w-5 h-5" />
            <span>Outline</span>
            <svg 
              v-if="modelValue === 'outline'"
              class="w-4 h-4 ml-auto text-primary-600" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>

          <!-- Solid 選項 -->
          <button
            @click="selectStyle('solid')"
            :class="[
              modelValue === 'solid' ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50',
              'w-full px-3 py-2 text-left text-sm flex items-center space-x-2 transition-colors'
            ]"
          >
            <component :is="SolidIcon" class="w-5 h-5" />
            <span>Solid</span>
            <svg 
              v-if="modelValue === 'solid'"
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
            {{ modelValue === 'outline' ? '線條樣式' : '填充樣式' }}
          </p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
// 使用 Adjustments 圖標作為樣式選擇器的圖標
import { AdjustmentsIcon as OutlineAdjustmentsIcon } from '@heroicons/vue/outline'
import { AdjustmentsIcon as SolidAdjustmentsIcon } from '@heroicons/vue/solid'

export default {
  name: 'IconStyleSelector',
  components: {
    OutlineAdjustmentsIcon,
    SolidAdjustmentsIcon
  },
  props: {
    modelValue: {
      type: String,
      default: 'outline',
      validator: (value) => ['outline', 'solid'].includes(value)
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    
    // 當前顯示的樣式
    const currentStyle = computed(() => props.modelValue)
    
    // 根據當前樣式選擇對應的圖標
    const OutlineIcon = computed(() => OutlineAdjustmentsIcon)
    const SolidIcon = computed(() => SolidAdjustmentsIcon)
    
    const toggleDropdown = () => {
      isOpen.value = !isOpen.value
    }
    
    const selectStyle = (style) => {
      emit('update:modelValue', style)
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
      currentStyle,
      OutlineIcon,
      SolidIcon,
      toggleDropdown,
      selectStyle
    }
  }
}
</script>

<style scoped>
.relative > button {
  width: 34px;
  height: 34px;
}
</style>