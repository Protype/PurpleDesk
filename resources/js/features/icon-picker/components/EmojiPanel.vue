<template>
  <div class="emoji-panel">
    <!-- 工具欄 -->
    <div class="panel-toolbar flex items-center space-x-2 mb-3">
      <IconPickerSearch
        v-model="searchQuery"
        placeholder="搜尋 Emoji..."
        class="flex-1"
      />
      <SkinToneSelector
        v-model="selectedSkinTone"
      />
    </div>

    <!-- 載入狀態 -->
    <div v-if="isLoading" class="loading flex items-center justify-center py-8">
      <div class="text-sm text-gray-500">載入 Emoji 資料中...</div>
    </div>

    <!-- 錯誤狀態 -->
    <div v-else-if="hasError" class="error flex items-center justify-center py-8">
      <div class="text-sm text-red-500">
        載入失敗：{{ errorMessage }}
      </div>
    </div>

    <!-- Emoji 網格 -->
    <div v-else class="emoji-grid-container">
      <VirtualScrollGrid
        :items="flattenedEmojis"
        :items-per-row="10"
        :row-height="34"
        :container-height="176"
        :buffer="2"
        :preserve-scroll-position="true"
        class="px-2 py-1"
        ref="virtualGrid"
      >
        <template #item="{ item, index }">
          <!-- 分類標題 -->
          <div 
            v-if="item && item.type === 'category-header'"
            class="category-header w-full flex items-center space-x-2 pt-3 pb-1 text-sm font-bold text-gray-400"
          >
            <span>{{ item.categoryName }}</span>
            <div class="flex-1 h-px me-2 ml-2 bg-gray-200"></div>
          </div>
          
          <!-- Emoji 按鈕 -->
          <button
            v-else-if="item && item.type === 'emoji-item'"
            @click="selectEmoji(item)"
            :class="selectedEmoji === item.displayEmoji ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-100'"
            class="emoji-button p-1 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
            :title="`${item.displayEmoji} ${item.name}`"
          >
            <span class="text-xl">{{ item.displayEmoji }}</span>
          </button>
        </template>
      </VirtualScrollGrid>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import VirtualScrollGrid from './shared/VirtualScrollGrid.vue'
import IconPickerSearch from './IconPickerSearch.vue'
import SkinToneSelector from '../../../components/common/SkinToneSelector.vue'
import { useSearchFilter } from '../composables/useSearchFilter.js'
import { usePreloadedEmojiData } from '../composables/usePreloadedData.js'
import { applySkinTone } from '../utils/emojiSkinToneHandler.js'

// Props
defineOptions({
  name: 'EmojiPanel'
})

const props = defineProps({
  selectedEmoji: {
    type: String,
    default: ''
  }
})

// Emits
const emit = defineEmits(['emoji-selected'])

// 內部狀態
const selectedSkinTone = ref(0)
const virtualGrid = ref(null)

// 使用預載入的 emoji 資料
const emojiProvider = usePreloadedEmojiData()

// 狀態管理（從預載入提供者取得）
const rawEmojiData = emojiProvider.data
const isLoading = emojiProvider.loading
const loadError = emojiProvider.error

// 錯誤狀態計算
const hasError = computed(() => !!loadError.value)
const errorMessage = computed(() => loadError.value?.message || '未知錯誤')

// 處理膚色的 emoji 資料
const processedEmojis = computed(() => {
  if (!rawEmojiData.value?.length) return []

  return rawEmojiData.value.map(category => ({
    ...category,
    emojis: category.emojis?.map(emoji => ({
      ...emoji,
      displayEmoji: applySkinTone(emoji, selectedSkinTone.value)
    })) || []
  }))
})

// 搜尋過濾功能
const {
  searchQuery,
  filteredData: filteredEmojis,
  clearSearch
} = useSearchFilter(processedEmojis)

// 扁平化 emoji 資料用於 VirtualScrollGrid
const flattenedEmojis = computed(() => {
  const result = []
  
  filteredEmojis.value.forEach(category => {
    if (category.emojis && category.emojis.length > 0) {
      // 添加分類標題，使用 fullRow 和 itemHeight 屬性
      result.push({
        type: 'category-header',
        isCategory: true,
        fullRow: true,
        itemHeight: 40, // 分類標題使用 40px 高度
        categoryId: category.categoryId,
        categoryName: category.categoryName
      })
      
      // 添加該分類的 emoji
      category.emojis.forEach(emoji => {
        result.push({
          ...emoji,
          type: 'emoji-item',
          isCategory: false
        })
      })
    }
  })
  
  return result
})

// 選擇 emoji
const selectEmoji = (item) => {
  if (item.isCategory) return // 分類標題不可選擇
  
  const emojiData = {
    emoji: item.displayEmoji,
    name: item.name,
    category: item.category,
    type: 'emoji'
  }
  
  emit('emoji-selected', emojiData)
}
</script>

<style scoped>
.emoji-panel {
  @apply w-full;
}

.panel-toolbar {
  @apply flex-shrink-0;
}

.emoji-grid-container {
  @apply border border-gray-100 rounded-md bg-gray-50 p-2 px-0.5;
}

/* 分類標題行樣式 */
.category-header {
  grid-column: 1 / -1; /* 佔滿整行 */
}

/* 第一行的特殊樣式 */
:deep(.virtual-grid-row.first-row .category-header) {
  /* 針對第一行中的分類標題 */
  @apply pt-1;
}

/* Emoji 按鈕樣式 */
.emoji-button {
  width: 30px;
  height: 30px;
  @apply flex items-center justify-center;
}

.loading, .error {
  @apply h-44;
}
</style>