<template>
  <div class="icon-library-panel">
    <!-- 頂部工具欄 -->
    <div class="panel-toolbar flex items-center space-x-3 mb-4">
      <!-- 搜尋欄 -->
      <IconPickerSearch
        v-model="searchQuery"
        placeholder="搜尋圖標..."
        @search="handleSearch"
        @clear="handleSearchClear"
        class="flex-1"
      />
      
      <!-- 變體樣式選擇器 -->
      <VariantSelector
        v-model="selectedStyle"
        variant-type="iconStyle"
        :variants="iconStyleOptions"
        @variant-change="handleStyleChange"
      />
    </div>

    <!-- 圖標庫標籤 -->
    <div class="library-tabs flex border-b border-gray-200 mb-4">
      <button
        @click="activeLibrary = 'heroicons'"
        :class="activeLibrary === 'heroicons' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
        class="px-3 py-2 text-sm font-medium transition-colors"
      >
        HeroIcons ({{ heroIconsCount }})
      </button>
      <button
        @click="activeLibrary = 'bootstrap'"
        :class="activeLibrary === 'bootstrap' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700'"
        class="px-3 py-2 text-sm font-medium transition-colors ml-4"
      >
        Bootstrap Icons ({{ bootstrapIconsCount }})
      </button>
    </div>

    <!-- 載入狀態 -->
    <div v-if="isLoading" class="flex items-center justify-center py-8">
      <div class="flex items-center space-x-2 text-gray-500">
        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <span>載入圖標...</span>
      </div>
    </div>

    <!-- 錯誤狀態 -->
    <div v-else-if="error" class="flex items-center justify-center py-8">
      <div class="text-center">
        <div class="text-red-500 mb-2">
          <i class="bi bi-exclamation-triangle text-2xl"></i>
        </div>
        <p class="text-red-600 text-sm">{{ error }}</p>
        <button 
          @click="reloadIcons"
          class="mt-2 text-primary-600 hover:text-primary-700 text-sm underline"
        >
          重新載入
        </button>
      </div>
    </div>

    <!-- 圖標網格 -->
    <div v-else-if="filteredIcons.length > 0" class="icon-grid-container">
      <VirtualScrollGrid
        :items="virtualGridItems"
        :items-per-row="itemsPerRow"
        :row-height="36"
        :container-height="400"
      >
        <template #item="{ item, itemIndex }">
          <div
            v-if="item.type === 'icon'"
            @click="selectIcon(item.data)"
            :title="getIconTitle(item.data)"
            class="icon-item w-8 h-8 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors cursor-pointer flex items-center justify-center"
            :class="{ 'bg-primary-50 ring-2 ring-primary-500': isSelected(item.data) }"
          >
            <!-- HeroIcon 渲染 -->
            <component
              v-if="item.data.type === 'heroicons'"
              :is="getHeroIconComponent(item.data)"
              class="w-5 h-5 text-gray-700"
            />
            <!-- Bootstrap Icon 渲染 -->
            <i
              v-else-if="item.data.type === 'bootstrap'"
              :class="['bi', getBootstrapIconClass(item.data)]"
              class="text-gray-700"
            />
          </div>
          
          <!-- 分類標題 -->
          <div
            v-else-if="item.type === 'category'"
            class="category-header w-full text-xs font-medium text-gray-500 py-1"
          >
            {{ item.data.title }} ({{ item.data.count }})
          </div>
        </template>
      </VirtualScrollGrid>
    </div>

    <!-- 空狀態 -->
    <div v-else class="empty-state flex flex-col items-center justify-center py-12">
      <div class="text-gray-400 mb-4">
        <i class="bi bi-search text-3xl"></i>
      </div>
      <p class="text-gray-500 text-sm text-center">
        {{ searchQuery ? `找不到符合「${searchQuery}」的圖標` : '沒有可用的圖標' }}
      </p>
      <button
        v-if="searchQuery"
        @click="clearSearch"
        class="mt-2 text-primary-600 hover:text-primary-700 text-sm underline"
      >
        清除搜尋條件
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import VirtualScrollGrid from './shared/VirtualScrollGrid.vue'
import IconPickerSearch from './IconPickerSearch.vue'
import VariantSelector from './VariantSelector.vue'
import { IconDataLoader } from '../services/IconDataLoader.js'
import { useIconVariants } from '../composables/useIconVariants.js'

export default {
  name: 'IconLibraryPanel',
  
  components: {
    VirtualScrollGrid,
    IconPickerSearch,
    VariantSelector
  },

  props: {
    /**
     * 當前選中的圖標
     */
    selectedIcon: {
      type: [String, Object],
      default: null
    },
    
    /**
     * 圖標類型
     */
    iconType: {
      type: String,
      default: 'heroicons'
    },

    /**
     * 每行顯示的圖標數量
     */
    itemsPerRow: {
      type: Number,
      default: 10
    }
  },

  emits: ['icon-select', 'icon-change'],

  setup(props, { emit }) {
    // 響應式狀態
    const searchQuery = ref('')
    const selectedStyle = ref('outline')
    const activeLibrary = ref('heroicons')
    const isLoading = ref(true)
    const error = ref(null)
    const allIcons = ref({ data: { heroicons: [], bootstrap: {} }, meta: {} })

    // 服務實例
    const iconDataLoader = new IconDataLoader()
    const iconVariants = useIconVariants()

    // 圖標樣式選項
    const iconStyleOptions = computed(() => iconVariants.getVariantOptions('iconStyle'))

    // 圖標數量統計
    const heroIconsCount = computed(() => {
      return allIcons.value.data?.heroicons?.length || 0
    })

    const bootstrapIconsCount = computed(() => {
      const bootstrapData = allIcons.value.data?.bootstrap || {}
      return Object.values(bootstrapData).reduce((total, categoryIcons) => {
        return total + (Array.isArray(categoryIcons) ? categoryIcons.length : 0)
      }, 0)
    })

    // 過濾後的圖標
    const filteredIcons = computed(() => {
      const query = searchQuery.value.toLowerCase().trim()
      const library = activeLibrary.value
      const style = selectedStyle.value

      let icons = []

      if (library === 'heroicons') {
        icons = allIcons.value.data?.heroicons || []
      } else if (library === 'bootstrap') {
        const bootstrapData = allIcons.value.data?.bootstrap || {}
        icons = Object.values(bootstrapData).flat()
      }

      // 應用搜尋過濾
      if (query) {
        icons = icons.filter(icon => {
          const name = (icon.name || '').toLowerCase()
          const keywords = (icon.keywords || []).join(' ').toLowerCase()
          const className = (icon.class || '').toLowerCase()
          const component = (icon.component || '').toLowerCase()
          
          return name.includes(query) || 
                 keywords.includes(query) || 
                 className.includes(query) ||
                 component.includes(query)
        })
      }

      // 應用樣式過濾
      if (library === 'bootstrap' && style === 'outline') {
        icons = icons.filter(icon => !icon.class || !icon.class.includes('-fill'))
      } else if (library === 'bootstrap' && style === 'solid') {
        // 對於 solid 樣式，優先顯示 -fill 版本，如果沒有則顯示基礎版本
        const processedIcons = new Map()
        
        icons.forEach(icon => {
          const baseClass = icon.class?.replace(/-fill$/, '') || icon.class
          const isFillIcon = icon.class?.includes('-fill')
          
          if (!processedIcons.has(baseClass)) {
            processedIcons.set(baseClass, icon)
          } else if (isFillIcon) {
            // 如果遇到 -fill 版本，替換基礎版本
            processedIcons.set(baseClass, icon)
          }
        })
        
        icons = Array.from(processedIcons.values())
      }

      return icons
    })

    // 轉換為 VirtualScrollGrid 所需的格式
    const virtualGridItems = computed(() => {
      const icons = filteredIcons.value
      const itemsPerRow = props.itemsPerRow

      if (activeLibrary.value === 'heroicons') {
        // HeroIcons 直接轉換
        return icons.map((icon, index) => ({
          key: `hero-${icon.component}-${index}`,
          type: 'icon',
          data: icon
        }))
      } else {
        // Bootstrap Icons 按分類分組
        const categories = {}
        icons.forEach(icon => {
          const category = icon.category || 'other'
          if (!categories[category]) {
            categories[category] = []
          }
          categories[category].push(icon)
        })

        const items = []
        Object.entries(categories).forEach(([categoryName, categoryIcons]) => {
          // 添加分類標題
          items.push({
            key: `category-${categoryName}`,
            type: 'category',
            fullRow: true,
            data: {
              title: getCategoryDisplayName(categoryName),
              count: categoryIcons.length
            }
          })

          // 添加該分類的圖標
          categoryIcons.forEach((icon, index) => {
            items.push({
              key: `bootstrap-${icon.class}-${index}`,
              type: 'icon',
              data: icon
            })
          })
        })

        return items
      }
    })

    // 工具方法
    const getCategoryDisplayName = (categoryName) => {
      const categoryNames = {
        general: '一般',
        ui: '介面',
        communications: '通訊',
        files: '檔案',
        media: '媒體',
        people: '人物',
        alphanumeric: '字母數字',
        others: '其他'
      }
      return categoryNames[categoryName] || categoryName
    }

    const getIconTitle = (icon) => {
      return icon.name || icon.class || icon.component || '未知圖標'
    }

    const getHeroIconComponent = (icon) => {
      // 根據當前樣式返回對應的 component
      const style = selectedStyle.value
      if (style === 'solid') {
        // 檢查是否有 solid 變體
        return icon.variants?.solid?.component || icon.component
      }
      return icon.variants?.outline?.component || icon.component
    }

    const getBootstrapIconClass = (icon) => {
      const style = selectedStyle.value
      if (style === 'solid' && icon.variants?.solid?.class) {
        return icon.variants.solid.class
      }
      return icon.class
    }

    const isSelected = (icon) => {
      if (!props.selectedIcon) return false
      
      if (typeof props.selectedIcon === 'string') {
        return props.selectedIcon === (icon.component || icon.class)
      }
      
      return props.selectedIcon === icon
    }

    // 事件處理方法
    const handleSearch = (query) => {
      // 搜尋事件已由 v-model 處理
    }

    const handleSearchClear = () => {
      searchQuery.value = ''
    }

    const handleStyleChange = (event) => {
      selectedStyle.value = event.value
      iconVariants.setIconStyle(event.value)
    }

    const selectIcon = (icon) => {
      emit('icon-select', icon)
      emit('icon-change', {
        icon: icon,
        type: icon.type,
        style: selectedStyle.value
      })
    }

    const clearSearch = () => {
      searchQuery.value = ''
    }

    const reloadIcons = async () => {
      await loadIcons()
    }

    // 載入圖標資料
    const loadIcons = async () => {
      try {
        isLoading.value = true
        error.value = null
        
        const data = await iconDataLoader.getIconLibraryData(selectedStyle.value)
        allIcons.value = data
      } catch (err) {
        error.value = err.message || '載入圖標時發生錯誤'
        console.error('Failed to load icons:', err)
      } finally {
        isLoading.value = false
      }
    }

    // 監聽樣式變化重新載入圖標
    watch(selectedStyle, async (newStyle) => {
      await loadIcons()
    })

    // 組件掛載時載入圖標
    onMounted(async () => {
      // 初始化變體狀態
      selectedStyle.value = iconVariants.currentIconStyle.value
      await loadIcons()
    })

    return {
      // 響應式狀態
      searchQuery,
      selectedStyle,
      activeLibrary,
      isLoading,
      error,
      
      // 計算屬性
      iconStyleOptions,
      heroIconsCount,
      bootstrapIconsCount,
      filteredIcons,
      virtualGridItems,
      
      // 方法
      handleSearch,
      handleSearchClear,
      handleStyleChange,
      selectIcon,
      clearSearch,
      reloadIcons,
      getIconTitle,
      getHeroIconComponent,
      getBootstrapIconClass,
      isSelected
    }
  }
}
</script>

<style scoped>
.icon-library-panel {
  @apply flex flex-col h-full;
}

.panel-toolbar {
  @apply flex-shrink-0;
}

.library-tabs {
  @apply flex-shrink-0;
}

.icon-grid-container {
  @apply flex-1 min-h-0;
}

.icon-item {
  @apply relative;
}

.icon-item:hover::after {
  content: '';
  @apply absolute inset-0 bg-gray-100 rounded pointer-events-none;
}

.icon-item.selected::after {
  content: '';
  @apply absolute inset-0 bg-primary-50 rounded pointer-events-none;
}

.category-header {
  @apply border-b border-gray-200 bg-gray-50 px-2;
}

.empty-state {
  @apply flex-1 min-h-0;
}

/* 確保圖標在不同狀態下的視覺一致性 */
.icon-item .bi {
  font-size: 1.25rem;
}

.icon-item:focus {
  @apply outline-none ring-2 ring-primary-500;
}
</style>