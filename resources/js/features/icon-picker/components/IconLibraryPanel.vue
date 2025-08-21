<template>
  <div class="icon-library-panel">
    <!-- 頂部工具欄 -->
    <div class="panel-toolbar flex items-center space-x-3 mb-3">
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
    <div v-if="isLoading" class="loading flex items-center justify-center py-8">
      <div class="text-sm text-gray-500">載入圖標資料中...</div>
    </div>

    <!-- 錯誤狀態 -->
    <div v-else-if="error" class="error flex items-center justify-center py-8">
      <div class="text-sm text-red-500">
        載入失敗：{{ error }}
      </div>
    </div>

    <!-- 圖標網格 -->
    <div v-else-if="filteredIcons.length > 0" class="icon-grid-container">
      <VirtualScrollGrid
        :items="virtualGridItems"
        :items-per-row="10"
        :row-height="34"
        :container-height="176"
        :buffer="2"
        :preserve-scroll-position="true"
        class="px-2 py-1"
      >
        <template #item="{ item, itemIndex }">
          <button
            v-if="item.type === 'icon'"
            @click="selectIcon(item.data)"
            :title="getIconTitle(item.data)"
            class="icon-button p-1 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
            :class="isSelected(item.data) ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-100'"
          >
            <!-- HeroIcon 渲染 -->
            <component
              v-if="item.data.type === 'heroicons'"
              :is="getHeroIconComponent(item.data)"
              class="w-5 h-5 text-gray-700"
            />
            <!-- Bootstrap Icon 渲染 -->
            <i
              v-else-if="item.data.type === 'bootstrap-icons' || item.data.type === 'bootstrap'"
              :class="['bi', getBootstrapIconClass(item.data)]"
              class="text-gray-700"
            />
          </button>
          
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

<script setup>
import { ref, computed, onMounted } from 'vue'
import VirtualScrollGrid from './shared/VirtualScrollGrid.vue'
import IconPickerSearch from './IconPickerSearch.vue'
import IconStyleSelector from './IconStyleSelector.vue'
import { useSearchFilter } from '../composables/useSearchFilter.js'
import { usePreloadedIconData } from '../composables/usePreloadedData.js'
import { useIconVariants } from '../composables/useIconVariants.js'
// 一次性匯入所有 HeroIcons
import * as HeroiconsOutline from '@heroicons/vue/outline'
import * as HeroiconsSolid from '@heroicons/vue/solid'

// Props 和 Emits
defineOptions({
  name: 'IconLibraryPanel'
})

const props = defineProps({
  selectedIcon: {
    type: [String, Object],
    default: null
  },
  iconType: {
    type: String,
    default: 'heroicons'
  },
  itemsPerRow: {
    type: Number,
    default: 10
  }
})

const emit = defineEmits(['icon-select', 'icon-change'])

// 服務層
const iconVariants = useIconVariants()

// 內部狀態
const selectedStyle = ref('outline')

// 使用預載入的圖標資料
const iconProvider = usePreloadedIconData()

// 狀態管理（從預載入提供者取得）
const allIcons = iconProvider.data
const isLoading = iconProvider.loading
const loadError = iconProvider.error

// 錯誤狀態
const error = computed(() => loadError.value?.message || null)

// 處理過的圖標資料 - 適配新的扁平化 API 格式
const processedIconsData = computed(() => {
  if (!allIcons.value?.data) return []
  
  const allIconsList = [
    ...(allIcons.value.data.heroicons || []), 
    ...Object.values(allIcons.value.data.bootstrap || {}).flat()
  ]
  
  // 新的扁平化格式：資料已經是展開的，不需要再展開變體
  const processedIcons = allIconsList.map(icon => {
    return {
      ...icon,
      displayName: icon.name || icon.value || icon.class || icon.component
    }
  })
  
  return processedIcons.sort((a, b) => {
    // 首先按類型排序：HeroIcons 在前，Bootstrap Icons 在後
    const typeA = a.type === 'heroicons' ? 'heroicons' : 'bootstrap-icons'
    const typeB = b.type === 'heroicons' ? 'heroicons' : 'bootstrap-icons' 
    
    if (typeA !== typeB) {
      if (typeA === 'heroicons') return -1
      if (typeB === 'heroicons') return 1
    }
    
    // 相同類型內按名稱排序
    const nameA = a.displayName || a.name || a.value || ''
    const nameB = b.displayName || b.name || b.value || ''
    return nameA.localeCompare(nameB)
  })
})

// 樣式篩選功能 - 根據 has_variants 和 variant_type 篩選
const styleFilteredIcons = computed(() => {
  const style = selectedStyle.value
  
  if (style === 'all') {
    return processedIconsData.value
  }
  
  return processedIconsData.value.filter(icon => {
    // HeroIcons 篩選邏輯
    if (icon.type === 'heroicons') {
      if (style === 'outline') {
        // outline 模式：有變體且是 outline，或無變體
        return (icon.has_variants === true && icon.variant_type === 'outline') ||
               (icon.has_variants === false)
      } else if (style === 'solid') {
        // solid 模式：有變體且是 solid，或無變體
        return (icon.has_variants === true && icon.variant_type === 'solid') ||
               (icon.has_variants === false)
      }
    }
    
    // Bootstrap Icons 篩選邏輯
    if (icon.type === 'bootstrap-icons') {
      if (style === 'outline') {
        // outline 模式：有變體且是 outline，或無變體
        return (icon.has_variants === true && icon.variant_type === 'outline') ||
               (icon.has_variants === false)
      } else if (style === 'solid') {
        // solid 模式：有變體且是 solid，或無變體
        return (icon.has_variants === true && icon.variant_type === 'solid') ||
               (icon.has_variants === false)
      }
    }
    
    return true
  })
})


// 搜尋過濾功能
const {
  searchQuery,
  filteredData: searchFilteredIcons,
  clearSearch
} = useSearchFilter(computed(() => 
  // 將樣式篩選後的圖標包裝為分類結構供搜尋使用
  [{
    categoryId: 'heroicons',
    categoryName: 'Heroicons',
    icons: styleFilteredIcons.value
  }]
), {
  itemsKey: 'icons',
  searchFunction: (icon, query) => {
    const lowerQuery = query.toLowerCase()
    const name = (icon.name || '').toLowerCase()
    const keywords = (icon.keywords || []).join(' ').toLowerCase()
    const className = (icon.class || '').toLowerCase()
    const component = (icon.component || '').toLowerCase()
    
    return name.includes(lowerQuery) || 
           keywords.includes(lowerQuery) || 
           className.includes(lowerQuery) ||
           component.includes(lowerQuery)
  }
})

// 過濾後的圖標（扁平化）
const filteredIcons = computed(() => {
  return searchFilteredIcons.value.flatMap(category => category.icons || [])
})

// 分組後的圖標（考慮搜尋狀態）
const groupedIcons = computed(() => {
  const query = searchQuery.value.trim()
  
  if (query) {
    // 搜尋時返回扁平化結果（無分類標題）
    return filteredIcons.value
  }
  
  // 正常顯示按分類分組
  const heroIcons = filteredIcons.value.filter(icon => icon.type === 'heroicons')
  const bootstrapIcons = filteredIcons.value.filter(icon => icon.type === 'bootstrap-icons')

  const items = []
  
  // 添加 Heroicons 分類
  if (heroIcons.length > 0) {
    items.push({
      type: 'category-header',
      fullRow: true,
      data: {
        title: 'Heroicons',
        count: heroIcons.length
      }
    })
    items.push(...heroIcons)
  }

  // 添加 Bootstrap Icons 分類
  if (bootstrapIcons.length > 0) {
    // 按分類分組 Bootstrap Icons
    const categories = ['general', 'ui', 'communications', 'files', 'media', 'people', 'alphanumeric', 'others']
    const categoryNames = {
      general: '通用圖標',
      ui: 'UI 介面', 
      communications: '通訊溝通',
      files: '檔案文件',
      media: '媒體播放',
      people: '人物相關',
      alphanumeric: '數字字母',
      others: '其他圖標'
    }
    
    categories.forEach(category => {
      const categoryIcons = bootstrapIcons.filter(icon => icon.category === category)
      if (categoryIcons.length > 0) {
        items.push({
          type: 'category-header',
          fullRow: true,
          data: {
            title: categoryNames[category] || category,
            count: categoryIcons.length
          }
        })
        items.push(...categoryIcons)
      }
    })
  }

  return items
})

// 轉換為 VirtualScrollGrid 所需的格式
const virtualGridItems = computed(() => {
  return groupedIcons.value.map((item, index) => {
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

// 圖標數量統計
const heroIconsCount = computed(() => {
  return allIcons.value?.data?.heroicons?.length || 0
})

const bootstrapIconsCount = computed(() => {
  const bootstrapData = allIcons.value?.data?.bootstrap || {}
  return Object.values(bootstrapData).reduce((total, categoryIcons) => {
    return total + (Array.isArray(categoryIcons) ? categoryIcons.length : 0)
  }, 0)
})

// 快取元件查找結果
const iconComponentCache = new Map()

const getHeroIconComponent = (icon) => {
  const componentName = icon.value  // 新格式使用 value 欄位
  const variant = icon.variant_type || 'outline'  // 新格式使用 variant_type 欄位
  
  if (!componentName) return null
  
  // 使用快取鍵避免重複查找
  const cacheKey = `${componentName}-${variant}`
  if (iconComponentCache.has(cacheKey)) {
    return iconComponentCache.get(cacheKey)
  }
  
  try {
    // 根據圖標的變體選擇正確的元件集合
    let component
    if (variant === 'solid') {
      component = HeroiconsSolid[componentName]
    } else {
      component = HeroiconsOutline[componentName]
    }
    
    // 快取結果
    iconComponentCache.set(cacheKey, component)
    return component
  } catch (error) {
    console.warn(`Failed to resolve HeroIcon component: ${componentName}`, error)
    iconComponentCache.set(cacheKey, null)
    return null
  }
}

const getBootstrapIconClass = (icon) => {
  // 新格式直接使用 value 欄位，不需要再處理變體
  return icon.value || icon.class
}

const getIconTitle = (icon) => {
  return icon.name || icon.value || icon.class || icon.component || '未知圖標'
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
  clearSearch()
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

const reloadIcons = () => {
  iconProvider.reload()
}

// 組件掛載時初始化
onMounted(() => {
  // 同步變體狀態
  selectedStyle.value = iconVariants.currentIconStyle.value
})

// 公開屬性給測試使用
defineExpose({
  processedIcons: processedIconsData,
  filteredIcons,
  selectedStyle,
  searchQuery
})
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
  grid-column: 1 / -1; /* 佔滿整行 */
}

/* 第一行的特殊樣式 */
:deep(.virtual-grid-row.first-row .category-header) {
  /* 針對第一行中的分類標題 */
  @apply pt-1;
}

.empty-state {
  @apply flex-1 min-h-0;
}

.icon-grid-container {
  @apply border border-gray-100 rounded-md bg-gray-50 p-2 px-0.5;
}

/* 確保圖標在不同狀態下的視覺一致性 */
.icon-button .bi {
  font-size: 1.25rem;
}

.icon-button:focus {
  @apply outline-none ring-2 ring-primary-500;
}
</style>