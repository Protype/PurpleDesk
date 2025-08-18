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
    },
    // 選中的 emoji
    selectedEmoji: {
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

    // 扁平化 emoji 資料用於 VirtualScrollGrid（使用新的 fullRow 屬性）
    const flattenedEmojis = computed(() => {
      const result = []
      
      filteredEmojis.value.forEach(category => {
        if (category.emojis && category.emojis.length > 0) {
          // 添加分類標題，使用 fullRow 屬性讓它獨佔一行
          result.push({
            type: 'category-header',
            isCategory: true,
            fullRow: true,
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