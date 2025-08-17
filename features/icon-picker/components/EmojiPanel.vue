<template>
  <div class="emoji-panel">
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
        :row-height="36"
        :container-height="176"
        :buffer="2"
        ref="virtualGrid"
      >
        <template #item="{ item, index }">
          <div
            v-if="item"
            @click="selectEmoji(item)"
            class="emoji-item flex items-center justify-center"
            :class="{ 
              'category-header': item.isCategory,
              'emoji-entry': !item.isCategory 
            }"
            :title="item.isCategory ? item.categoryName : `${item.emoji} ${item.name}`"
          >
            <!-- 分類標題 -->
            <div v-if="item.isCategory" class="category-title">
              {{ item.categoryName }}
            </div>
            
            <!-- Emoji 項目 -->
            <div v-else class="emoji-content">
              {{ item.displayEmoji }}
            </div>
          </div>
        </template>
      </VirtualScrollGrid>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import VirtualScrollGrid from './shared/VirtualScrollGrid.vue'
import { IconDataLoader } from '../services/IconDataLoader.js'

export default {
  name: 'EmojiPanel',
  components: {
    VirtualScrollGrid
  },
  props: {
    // 搜尋條件
    searchQuery: {
      type: String,
      default: ''
    },
    // 選中的膚色
    selectedSkinTone: {
      type: String,
      default: ''
    }
  },
  emits: ['emoji-selected'],
  setup(props, { emit }) {
    // 狀態管理
    const isLoading = ref(true)
    const hasError = ref(false)
    const errorMessage = ref('')
    const rawEmojiData = ref([])
    const virtualGrid = ref(null)
    
    // IconDataLoader 實例
    const iconDataLoader = new IconDataLoader()

    // 載入 emoji 資料
    const loadEmojiData = async () => {
      try {
        isLoading.value = true
        hasError.value = false
        errorMessage.value = ''

        const data = await iconDataLoader.getEmojiData()
        rawEmojiData.value = data || []
      } catch (error) {
        console.error('Failed to load emoji data:', error)
        hasError.value = true
        errorMessage.value = error.message || '未知錯誤'
        rawEmojiData.value = []
      } finally {
        isLoading.value = false
      }
    }

    // 處理膚色的 emoji 資料
    const processedEmojis = computed(() => {
      if (!rawEmojiData.value.length) return []

      return rawEmojiData.value.map(category => ({
        ...category,
        emojis: category.emojis?.map(emoji => ({
          ...emoji,
          displayEmoji: applyModifier(emoji.emoji, props.selectedSkinTone)
        })) || []
      }))
    })

    // 過濾 emoji 資料（基於搜尋條件）
    const filteredEmojis = computed(() => {
      if (!props.searchQuery.trim()) {
        return processedEmojis.value
      }

      const query = props.searchQuery.toLowerCase().trim()
      
      return processedEmojis.value.map(category => {
        const filteredCategoryEmojis = category.emojis?.filter(emoji => {
          // 搜尋 emoji 名稱
          if (emoji.name?.toLowerCase().includes(query)) return true
          
          // 搜尋關鍵字
          if (emoji.keywords?.some(keyword => keyword.toLowerCase().includes(query))) return true
          
          // 搜尋 emoji 本身
          if (emoji.emoji?.includes(query)) return true
          
          return false
        }) || []

        return {
          ...category,
          emojis: filteredCategoryEmojis
        }
      }).filter(category => category.emojis.length > 0) // 只保留有結果的分類
    })

    // 扁平化 emoji 資料用於 VirtualScrollGrid
    const flattenedEmojis = computed(() => {
      const result = []
      
      filteredEmojis.value.forEach(category => {
        // 新增分類標題
        if (category.emojis.length > 0) {
          result.push({
            isCategory: true,
            categoryId: category.categoryId,
            categoryName: category.categoryName
          })
          
          // 新增該分類的 emoji
          category.emojis.forEach(emoji => {
            result.push({
              ...emoji,
              isCategory: false
            })
          })
        }
      })
      
      return result
    })

    // 套用膚色修飾符到 emoji
    const applyModifier = (emoji, skinTone) => {
      if (!skinTone || skinTone === '') return emoji
      
      // 移除現有膚色修飾符
      const baseEmoji = emoji.replace(/[\u{1F3FB}-\u{1F3FF}]/gu, '')
      
      // 簡化的膚色支援檢查 - 基於已知的支援膚色 emoji
      const supportsSkinTone = [
        '👋', '🤚', '🖐', '✋', '🖖', '👌', '🤌', '🤏', '✌', '🤞', '🤟', '🤘', '🤙', 
        '👈', '👉', '👆', '🖕', '👇', '☝', '👍', '👎', '👊', '✊', '🤛', '🤜', 
        '👏', '🙌', '👐', '🤲', '🤝', '🙏', '✍', '💅', '🤳', '💪',
        '🧑', '👨', '👩', '🧒', '👶', '👧', '🧓', '👴', '👵', '🙍', '🙎', 
        '🙅', '🙆', '💁', '🙋', '🧏', '🙇', '🤦', '🤷', '👮', '🕵', '💂', 
        '🥷', '👷', '🤴', '👸', '👳', '👲', '🧕', '🤵', '👰', '🤰', '🤱', 
        '👼', '🎅', '🤶', '🧙', '🧚', '🧛', '🧜', '🧝', '🧞', '🧟', 
        '💆', '💇', '🚶', '🧍', '🧎', '🏃', '💃', '🕺', '🕴', '👯', 
        '🧗', '🤺', '🏇', '⛷', '🏂', '🏌', '🏄', '🚣', '🏊', '⛹', 
        '🏋', '🚴', '🚵', '🤸', '🤼', '🤽', '🤾', '🤹', '🧘', '🛀', '🛌'
      ]
      
      // 檢查是否支援膚色
      const isHumanEmoji = supportsSkinTone.includes(baseEmoji)
      
      if (isHumanEmoji) {
        return baseEmoji + skinTone
      }
      
      return emoji
    }

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

    // 監聽搜尋條件變化
    watch(() => props.searchQuery, () => {
      // 搜尋條件變化時，VirtualScrollGrid 會自動重新渲染
    })

    // 監聽膚色變化
    watch(() => props.selectedSkinTone, () => {
      // 膚色變化時，computed 會自動重新計算
      // 不重置捲軸位置，保持當前瀏覽位置
    })

    // 元件掛載時載入資料
    onMounted(() => {
      loadEmojiData()
    })

    return {
      // Refs
      virtualGrid,
      
      // 狀態
      isLoading,
      hasError,
      errorMessage,
      
      // 計算屬性
      processedEmojis,
      filteredEmojis,
      flattenedEmojis,
      
      // 方法
      selectEmoji,
      loadEmojiData
    }
  }
}
</script>

<style scoped>
.emoji-panel {
  @apply w-full;
}

.emoji-grid-container {
  @apply border border-gray-100 rounded-md bg-gray-50 p-2;
}

.emoji-item {
  @apply w-8 h-8;
}

.emoji-item.category-header {
  @apply w-full bg-gray-300 cursor-default;
  height: 24px;
  grid-column: 1 / -1; /* 佔滿整行 */
}

.emoji-item.category-header:hover {
  @apply bg-gray-300;
}

.emoji-item.emoji-entry {
  @apply cursor-pointer hover:bg-gray-100 rounded p-1 transition-colors;
}

.emoji-item.emoji-entry:hover {
  @apply bg-gray-200 scale-110;
}

.category-title {
  @apply text-xs font-medium text-gray-600 w-full text-left px-2 truncate;
}

.emoji-content {
  @apply text-lg select-none;
}

.loading, .error {
  @apply h-44;
}
</style>