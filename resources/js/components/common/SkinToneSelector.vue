<template>
  <div class="skin-tone-selector" ref="selectorRef">
    <!-- 膚色選擇按鈕 -->
    <button
      type="button"
      @click="toggleSelector"
      :title="currentToneName"
      class="skin-tone-button me-2"
      :class="{ 'active': isOpen }"
    >
      <span class="text-xl">{{ currentTone.emoji }}</span>
    </button>

    <!-- 膚色選項下拉選單 -->
    <Teleport to="body">
      <div
        v-if="isOpen"
        ref="dropdownRef"
        class="skin-tone-dropdown"
        :style="dropdownPosition"
        @click.stop
      >
        <div class="skin-tone-options">
          <button
            v-for="tone in skinTones"
            :key="tone.value"
            @click="selectTone(tone.value)"
            :title="tone.name"
            class="skin-tone-option"
            :class="{ 'selected': modelValue == tone.value || parseInt(modelValue, 10) === tone.value }"
          >
            <span class="tone-preview" :style="{ backgroundColor: tone.color }"></span>
            <span class="tone-emoji">{{ tone.emoji }}</span>
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'

export default {
  name: 'SkinToneSelector',
  props: {
    modelValue: {
      type: String,
      default: '' // 空字串表示預設（黃色）膚色
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const isOpen = ref(false)
    const selectorRef = ref(null)
    const dropdownRef = ref(null)
    const dropdownPosition = ref({ top: '0px', left: '0px' })

    // 膚色選項 - 使用數字格式配合新架構
    const skinTones = [
      { value: '', name: '預設', color: '#FFC83D', emoji: '👋' },
      { value: 1, name: '淺膚色', color: '#F7DECE', emoji: '👋🏻' },
      { value: 2, name: '中淺膚色', color: '#F3D2A2', emoji: '👋🏼' },
      { value: 3, name: '中膚色', color: '#D5AB88', emoji: '👋🏽' },
      { value: 4, name: '中深膚色', color: '#AF7E57', emoji: '👋🏾' },
      { value: 5, name: '深膚色', color: '#7C533E', emoji: '👋🏿' }
    ]

    // 當前選中的膚色
    const currentTone = computed(() => {
      // 支援數字和字串的比較
      const modelValueNum = props.modelValue === '' ? '' : parseInt(props.modelValue, 10) || props.modelValue
      return skinTones.find(t => t.value === modelValueNum || t.value === props.modelValue) || skinTones[0]
    })

    const currentToneName = computed(() => currentTone.value.name)
    const currentToneColor = computed(() => currentTone.value.color)

    // 計算下拉選單位置
    const calculatePosition = async () => {
      if (!selectorRef.value) return

      await nextTick()

      const rect = selectorRef.value.getBoundingClientRect()
      const viewportHeight = window.innerHeight
      const viewportWidth = window.innerWidth
      
      // 下拉選單尺寸
      const dropdownWidth = 280
      const dropdownHeight = 60
      
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

    // 選擇膚色
    const selectTone = (tone) => {
      console.log('SkinToneSelector: selecting tone', tone, typeof tone)
      emit('update:modelValue', tone)
      closeSelector()
    }

    // 點擊外部關閉
    const handleClickOutside = (event) => {
      if (dropdownRef.value && !dropdownRef.value.contains(event.target) &&
          selectorRef.value && !selectorRef.value.contains(event.target)) {
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
      skinTones,
      currentTone,
      currentToneName,
      currentToneColor,
      toggleSelector,
      closeSelector,
      selectTone
    }
  }
}
</script>

<style scoped>
.skin-tone-selector {
  @apply relative inline-block;
}

.skin-tone-button {
  @apply w-8 h-8 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors bg-white flex items-center justify-center;
  width: 34px;
  height: 34px;
}

.skin-tone-button.active {
  @apply ring-2 ring-primary-500 ring-offset-2;
  width: 34px;
  height: 34px;
}

.skin-tone-dropdown {
  @apply fixed z-[10000] p-2 bg-white border border-gray-200 rounded-lg shadow-xl;
}

.skin-tone-options {
  @apply flex space-x-1;
}

.skin-tone-option {
  @apply p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors relative;
}

.skin-tone-option.selected {
  @apply bg-primary-50 ring-2 ring-primary-500;
}

.tone-preview {
  @apply w-8 h-8 rounded-full border-2 border-gray-300 block mb-1;
}

.tone-emoji {
  @apply text-lg block;
}
</style>