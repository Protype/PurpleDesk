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
      <IconStyleSelector
        v-model="selectedStyle"
        @update:modelValue="handleStyleChange"
      />
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
            class="category-header w-full flex items-center space-x-2 pt-3 pb-1 text-sm font-bold text-gray-400"
          >
            <span>{{ item.data.title }}</span>
            <div class="flex-1 h-px me-2 ml-2 bg-gray-200"></div>
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
import IconStyleSelector from './IconStyleSelector.vue'
import { IconDataLoader } from '../services/IconDataLoader.js'
import { useIconVariants } from '../composables/useIconVariants.js'
// 一次性匯入所有 HeroIcons
import * as HeroiconsOutline from '@heroicons/vue/outline'
import * as HeroiconsSolid from '@heroicons/vue/solid'

export default {
  name: 'IconLibraryPanel',
  
  components: {
    VirtualScrollGrid,
    IconPickerSearch,
    IconStyleSelector
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
    const isLoading = ref(true)
    const error = ref(null)
    const allIcons = ref({ data: { heroicons: [], bootstrap: {} }, meta: {} })

    // 服務實例
    const iconDataLoader = new IconDataLoader()
    const iconVariants = useIconVariants()


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

    // 處理過的圖標（統一來源，添加 isSolid 標記，排序）
    const processedIcons = computed(() => {
      const allIconsList = [
        ...(allIcons.value.data?.heroicons || []), 
        ...Object.values(allIcons.value.data?.bootstrap || {}).flat()
      ]
      
      return allIconsList.map(icon => ({
        ...icon,
        isSolid: icon.class?.endsWith('-fill') || 
                 icon.component?.includes('Solid') ||
                 false
      }))
      .sort((a, b) => {
        const nameA = a.name || a.class || a.component || ''
        const nameB = b.name || b.class || b.component || ''
        return nameA.localeCompare(nameB)
      })
    })

    // 過濾後的圖標
    const filteredIcons = computed(() => {
      // 對於 Heroicons，所有圖標都支持 outline 和 solid 樣式
      // 不需要根據樣式篩選圖標，而是在渲染時根據樣式選擇對應元件
      return processedIcons.value
    })

    // 分組後的圖標（考慮搜尋狀態）
    const groupedIcons = computed(() => {
      const query = searchQuery.value.toLowerCase().trim()
      
      if (query) {
        // 搜尋時返回扁平化結果（無分類標題）
        return filteredIcons.value.filter(icon => {
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
      
      // 正常顯示按分類分組 - 只顯示 Heroicons
      const heroIcons = filteredIcons.value.filter(icon => icon.type === 'heroicons')

      const items = []
      
      // 只添加 Heroicons 分類
      if (heroIcons.length > 0) {
        items.push({
          type: 'category-header',
          fullRow: true,
          data: {
            title: 'Heroicons'
          }
        })
        heroIcons.forEach(icon => {
          items.push(icon)
        })
      }

      return items
    })

    // 轉換為 VirtualScrollGrid 所需的格式
    const virtualGridItems = computed(() => {
      const grouped = groupedIcons.value

      return grouped.map((item, index) => {
        if (item.type === 'category-header') {
          return {
            key: `category-${item.data.title}-${index}`,
            type: 'category',
            fullRow: true,
            itemHeight: 40, // 分類標題使用 40px 高度
            data: item.data
          }
        } else {
          return {
            key: `icon-${item.component || item.class}-${index}`,
            type: 'icon',
            data: item
          }
        }
      })
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
      // 根據當前樣式動態解析 HeroIcon 元件
      const style = selectedStyle.value
      const componentName = icon.component
      
      if (!componentName) return null
      
      try {
        // 根據樣式選擇正確的元件集合
        if (style === 'solid') {
          return HeroiconsSolid[componentName]
        } else {
          return HeroiconsOutline[componentName]
        }
      } catch (error) {
        console.warn(`Failed to resolve HeroIcon component: ${componentName}`, error)
        return null
      }
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

    const handleStyleChange = (value) => {
      selectedStyle.value = value
      iconVariants.setIconStyle(value)
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
      isLoading,
      error,
      
      // 計算屬性
      heroIconsCount,
      bootstrapIconsCount,
      processedIcons,
      filteredIcons,
      groupedIcons,
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
  @apply px-2;
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