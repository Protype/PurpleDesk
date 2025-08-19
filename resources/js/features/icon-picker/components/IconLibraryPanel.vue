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
      
      <!-- 圖標樣式選擇器 -->
      <IconStyleSelector
        v-if="filteredIcons.length > 0"
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
          
          <!-- 空白佔位符 -->
          <div v-else-if="item.type === 'filler'" class="p-1"></div>
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
import IconStyleSelector from '../../../components/common/IconStyleSelector.vue'
import { IconDataLoader } from '../services/IconDataLoader.js'
import { useIconVariants } from '../composables/useIconVariants.js'

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

    // 圖標樣式選項
    const iconStyleOptions = computed(() => iconVariants.getVariantOptions('iconStyle'))


    // 按分類組織的圖標資料（包含分類標題）
    const groupedIcons = computed(() => {
      // 如果有搜尋查詢，返回篩選後的扁平陣列（不分組）
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        const heroIcons = allIcons.value.data?.heroicons || []
        const bsIconsData = allIcons.value.data?.bootstrap || {}
        const bsIcons = Object.values(bsIconsData).flat()
        
        const filteredHeroIcons = heroIcons.filter(icon => 
          icon.name?.toLowerCase().includes(query) || icon.component?.toLowerCase().includes(query)
        )
        const filteredBsIcons = bsIcons.filter(icon => 
          icon.name?.toLowerCase().includes(query) || icon.class?.toLowerCase().includes(query)
        )
        return [...filteredHeroIcons, ...filteredBsIcons]
      }
      
      const result = []
      
      // 1. 添加 Heroicons 分類標題和圖標
      const heroIcons = allIcons.value.data?.heroicons || []
      if (heroIcons.length > 0) {
        // 確保當前位置是 10 的倍數
        let currentLength = result.length
        let remainderInRow = currentLength % 10
        if (remainderInRow !== 0) {
          const fillersNeeded = 10 - remainderInRow
          for (let i = 0; i < fillersNeeded; i++) {
            result.push({ type: 'row-filler' })
          }
        }
        
        // 添加 Heroicons 標題
        result.push({
          type: 'category-header',
          categoryId: 'heroicons',
          name: 'Hero Icons',
          icon: '✨'
        })
        
        // 添加 9 個空項目來填滿標題行
        for (let i = 1; i < 10; i++) {
          result.push({ type: 'category-header-filler' })
        }
        
        // 添加 Heroicons
        result.push(...heroIcons.map(icon => ({...icon, type: 'heroicons'})))
      }
      
      // 2. 按分類添加 Bootstrap Icons
      const categoryOrder = ['general', 'ui', 'communications', 'files', 'media', 'people', 'alphanumeric', 'others']
      const bsIconsData = allIcons.value.data?.bootstrap || {}
      
      categoryOrder.forEach(categoryId => {
        const categoryIcons = bsIconsData[categoryId] || []
        
        if (categoryIcons.length > 0) {
          // 確保當前位置是 10 的倍數
          const currentLength = result.length
          const remainderInRow = currentLength % 10
          if (remainderInRow !== 0) {
            const fillersNeeded = 10 - remainderInRow
            for (let i = 0; i < fillersNeeded; i++) {
              result.push({ type: 'row-filler' })
            }
          }
          
          // 添加分類標題
          result.push({
            type: 'category-header',
            categoryId: categoryId,
            name: getCategoryDisplayName(categoryId),
            icon: getCategoryIcon(categoryId)
          })
          
          // 添加 9 個空項目來填滿標題行
          for (let i = 1; i < 10; i++) {
            result.push({ type: 'category-header-filler' })
          }
          
          // 根據選擇的樣式過濾 Bootstrap Icons
          const filteredCategoryIcons = filterBootstrapIconsByStyle(categoryIcons, selectedStyle.value)
          result.push(...filteredCategoryIcons.map(icon => ({...icon, type: 'bootstrap'})))
        }
      })
      
      return result
    })
    
    const filteredIcons = computed(() => {
      return groupedIcons.value
    })

    // 轉換為 VirtualScrollGrid 所需的格式
    const virtualGridItems = computed(() => {
      const items = filteredIcons.value
      
      return items.map((item, index) => {
        if (item.type === 'category-header') {
          return {
            key: `category-${item.categoryId}-${index}`,
            type: 'category',
            fullRow: true,
            data: {
              title: `${item.icon} ${item.name}`,
              count: 0 // 簡化處理，不顯示數量
            }
          }
        } else if (item.type === 'category-header-filler' || item.type === 'row-filler') {
          return {
            key: `filler-${index}`,
            type: 'filler',
            data: null
          }
        } else if (item.component || item.class) {
          // 這是實際的圖標項目
          return {
            key: item.component ? `hero-${item.component}-${index}` : `bootstrap-${item.class}-${index}`,
            type: 'icon',
            data: item
          }
        } else {
          // fallback
          return {
            key: `unknown-${index}`,
            type: 'filler',
            data: null
          }
        }
      })
    })

    // Bootstrap Icons 分類圖標映射
    const getCategoryIcon = (categoryId) => {
      const iconMap = {
        'general': '🏠',
        'ui': '🎛️',  
        'communications': '💬',
        'files': '📁',
        'media': '🎵',
        'people': '👤',
        'alphanumeric': '🔤', 
        'others': '⚙️'
      }
      return iconMap[categoryId] || '📦'
    }
    
    // 根據樣式過濾 Bootstrap Icons
    const filterBootstrapIconsByStyle = (icons, style) => {
      if (!icons || icons.length === 0) return []
      
      // 建立圖標映射來分析變體關係
      const iconMap = new Map()
      icons.forEach(icon => {
        const className = icon.class || ''
        iconMap.set(className, icon)
      })
      
      return icons.filter(icon => {
        const className = icon.class || ''
        const isFillIcon = className.includes('-fill')
        
        if (style === 'outline') {
          if (isFillIcon) {
            // 如果是 fill 圖標，不顯示
            return false
          } else {
            // 基礎圖標或特殊變體，都顯示
            return true
          }
        } else if (style === 'solid') {
          if (isFillIcon) {
            // 顯示所有 -fill 圖標
            return true
          } else {
            // 基礎圖標：檢查是否有對應的 fill 版本
            const fillVersion = className + '-fill'
            const hasFillVersion = iconMap.has(fillVersion)
            
            if (hasFillVersion) {
              // 如果有 fill 版本，不顯示基礎版本（優先顯示 fill）
              return false
            } else {
              // 沒有 fill 版本的特殊變體，顯示
              return true
            }
          }
        }
        
        return true // 預設顯示所有
      })
    }
    
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

    const handleStyleChange = (style) => {
      selectedStyle.value = style
      iconVariants.setIconStyle(style)
    }

    const selectIcon = (icon) => {
      emit('icon-select', icon)
      emit('icon-change', {
        icon: icon,
        type: icon.type || (icon.component ? 'heroicons' : 'bootstrap'),
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
      iconStyleOptions,
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
  @apply w-full;
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