<template>
  <div 
    ref="containerRef"
    class="virtual-scroll-grid"
    :style="{ height: `${containerHeight}px`, overflow: 'auto' }"
    @scroll="handleScroll"
  >
    <!-- 總高度佔位符，用於正確的滾動條 -->
    <div :style="{ height: `${totalHeight}px`, position: 'relative' }">
      <!-- 可見項目容器 -->
      <div 
        :style="{ 
          transform: `translateY(${offsetY}px)`,
          position: 'absolute',
          top: 0,
          left: 0,
          right: 0
        }"
      >
        <!-- 渲染可見的行 -->
        <div
          v-for="rowData in visibleRowsData"
          :key="rowData.rowIndex"
          :class="[
            'virtual-grid-row',
            { 'first-row': rowData.isFirstRow }
          ]"
          :style="{ 
            height: `${rowData.height}px`,
            display: 'grid',
            gridTemplateColumns: `repeat(${itemsPerRow}, 1fr)`,
            alignItems: 'center',
            gap: '2px'
          }"
        >
          <!-- 渲染該行的項目 -->
          <template v-for="item in rowData.items" :key="item.key">
            <!-- 🐛 修復：跳過 filler 類型的項目，不渲染 DOM -->
            <div
              v-if="item.type !== 'filler'"
              class="virtual-grid-item"
              :style="{ 
                gridColumn: item.fullRow ? '1 / -1' : 'span 1',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center'
              }"
            >
              <!-- 使用 slot 渲染項目內容 -->
              <slot 
                v-if="item.type === 'item'"
                name="item" 
                :item="item.data"
                :index="item.index"
                :row="item.row"
                :col="item.col"
              >
                <!-- 預設渲染 -->
                <div>{{ item.data?.name || '' }}</div>
              </slot>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch, nextTick, shallowRef } from 'vue'

export default {
  name: 'VirtualScrollGrid',
  props: {
    // 要顯示的項目陣列
    items: {
      type: Array,
      default: () => []
    },
    // 每行項目數量
    itemsPerRow: {
      type: Number,
      default: 10
    },
    // 每行高度
    rowHeight: {
      type: Number,
      default: 34
    },
    // 容器高度
    containerHeight: {
      type: Number,
      default: 176
    },
    // 緩衝區行數（在可見區域上下額外渲染的行數）
    buffer: {
      type: Number,
      default: 2
    },
    // 保持滾動位置（預設 false，僅在項目變化時重置）
    preserveScrollPosition: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    // 響應式數據
    const containerRef = ref(null)
    const scrollTop = ref(0)
    const rafId = ref(null)
    const lastScrollTime = ref(0)
    const isScrolling = ref(false)
    
    // 使用 shallowRef 優化大型陣列性能
    const itemsRef = shallowRef(props.items)
    
    // 取得項目高度的方法
    const getItemHeight = (item) => {
      // 如果項目指定了高度，使用指定高度
      if (item?.itemHeight && typeof item.itemHeight === 'number') {
        return item.itemHeight
      }
      // 否則使用預設行高
      return props.rowHeight
    }
    
    // 處理 fullRow 項目，自動補位
    const processedItems = computed(() => {
      const result = []
      let currentRowItems = 0
      
      for (let i = 0; i < itemsRef.value.length; i++) {
        const item = itemsRef.value[i]
        
        if (item?.fullRow === true) {
          // 處理獨立行項目
          // 1. 先填充當前行剩餘空位
          const fillersNeeded = currentRowItems > 0 ? props.itemsPerRow - currentRowItems : 0
          for (let j = 0; j < fillersNeeded; j++) {
            result.push({ type: 'auto-filler', data: null })
          }
          
          // 2. 添加 fullRow 項目，保持原有 type
          result.push(item)
          currentRowItems = 0 // 重置行計數
        } else {
          // 一般項目，保持原有的 type
          result.push(item)
          currentRowItems = (currentRowItems + 1) % props.itemsPerRow
        }
      }
      
      // 🐛 修復：處理最後一行的填充
      // 如果最後一行沒有填滿，添加必要的 filler
      if (currentRowItems > 0) {
        const finalFillersNeeded = props.itemsPerRow - currentRowItems
        for (let j = 0; j < finalFillersNeeded; j++) {
          result.push({ type: 'auto-filler', data: null })
        }
      }
      
      return result
    })

    // 計算屬性（增加記憶化優化）
    const totalRows = computed(() => {
      return Math.ceil(processedItems.value.length / props.itemsPerRow)
    })

    const totalHeight = computed(() => {
      let height = 0
      let currentRow = []
      
      // 遍歷所有處理後的項目，計算實際高度
      processedItems.value.forEach(item => {
        if (item?.fullRow === true) {
          // 先完成當前行（如果有項目的話）
          if (currentRow.length > 0) {
            height += Math.max(...currentRow.map(getItemHeight))
            currentRow = []
          }
          // fullRow 項目獨佔一行，使用其指定高度
          height += getItemHeight(item)
        } else {
          currentRow.push(item)
          if (currentRow.length === props.itemsPerRow) {
            // 一行滿了，使用該行最高的項目高度
            height += Math.max(...currentRow.map(getItemHeight))
            currentRow = []
          }
        }
      })
      
      // 處理最後一行（如果有未完成的行）
      if (currentRow.length > 0) {
        height += Math.max(...currentRow.map(getItemHeight))
      }
      
      return height
    })

    const visibleRows = computed(() => {
      if (props.containerHeight <= 0 || props.rowHeight <= 0) {
        return 0
      }
      return Math.ceil(props.containerHeight / props.rowHeight)
    })

    const bufferedRows = computed(() => {
      return visibleRows.value + (props.buffer * 2)
    })

    const startRow = computed(() => {
      return Math.floor(scrollTop.value / props.rowHeight)
    })

    const endRow = computed(() => {
      const end = startRow.value + bufferedRows.value
      return Math.min(end, totalRows.value)
    })

    const offsetY = computed(() => {
      return Math.max(0, (startRow.value - props.buffer) * props.rowHeight)
    })

    const visibleRowIndexes = computed(() => {
      const start = Math.max(0, startRow.value - props.buffer)
      const end = endRow.value
      const indexes = []
      
      for (let i = start; i < end; i++) {
        indexes.push(i)
      }
      
      return indexes
    })
    
    // 計算每行的實際數據（🐛 修復：正確處理 processedItems 的不規則排列）
    const visibleRowsData = computed(() => {
      const rows = []
      const items = processedItems.value
      const start = Math.max(0, startRow.value - props.buffer)
      const end = endRow.value
      
      if (items.length === 0) return rows
      
      // 🐛 修復：先將所有 processedItems 按行分組，再取可見行
      const allRows = []
      let currentRowItems = []
      let currentRowIndex = 0
      
      // 將所有項目按行分組
      for (let i = 0; i < items.length; i++) {
        const item = items[i]
        const isFullRow = item?.fullRow === true
        
        currentRowItems.push({
          key: `${currentRowIndex}-${currentRowItems.length}`,
          type: item?.type === 'auto-filler' ? 'filler' : 'item',
          fullRow: isFullRow,
          data: item,
          index: i,
          row: currentRowIndex,
          col: currentRowItems.length
        })
        
        // 行結束條件：滿行或遇到 fullRow 項目
        if (isFullRow || currentRowItems.length >= props.itemsPerRow) {
          // 計算該行的高度
          let rowHeight = props.rowHeight // 預設行高
          currentRowItems.forEach(rowItem => {
            if (rowItem.data) {
              const itemHeight = getItemHeight(rowItem.data)
              rowHeight = Math.max(rowHeight, itemHeight)
            }
          })
          
          allRows.push({
            rowIndex: currentRowIndex,
            items: [...currentRowItems], // 複製陣列
            height: rowHeight, // 加入行高資訊
            isFirstRow: currentRowIndex === 0
          })
          
          currentRowItems = []
          currentRowIndex++
        }
      }
      
      // 處理最後一行（如果有未完成的行）
      if (currentRowItems.length > 0) {
        // 計算該行的高度
        let rowHeight = props.rowHeight // 預設行高
        currentRowItems.forEach(rowItem => {
          if (rowItem.data) {
            const itemHeight = getItemHeight(rowItem.data)
            rowHeight = Math.max(rowHeight, itemHeight)
          }
        })
        
        allRows.push({
          rowIndex: currentRowIndex,
          items: [...currentRowItems],
          height: rowHeight, // 加入行高資訊
          isFirstRow: currentRowIndex === 0
        })
      }
      
      // 取出可見範圍的行
      for (let rowIndex = start; rowIndex < end && rowIndex < allRows.length; rowIndex++) {
        rows.push(allRows[rowIndex])
      }
      
      return rows
    })

    // 優化 visibleItems 計算，減少不必要的重新計算
    const visibleItems = computed(() => {
      // 如果容器高度為 0，不顯示任何項目
      if (props.containerHeight <= 0) {
        return []
      }

      const items = []
      const start = Math.max(0, startRow.value - props.buffer)
      const end = endRow.value
      const itemsArray = processedItems.value

      for (let rowIndex = start; rowIndex < end; rowIndex++) {
        for (let colIndex = 0; colIndex < props.itemsPerRow; colIndex++) {
          const itemIndex = rowIndex * props.itemsPerRow + colIndex
          if (itemIndex < itemsArray.length) {
            items.push({
              item: itemsArray[itemIndex],
              index: itemIndex,
              row: rowIndex,
              col: colIndex
            })
          }
        }
      }

      return items
    })

    // 方法
    const getItemAt = (rowIndex, colIndex) => {
      const itemIndex = rowIndex * props.itemsPerRow + colIndex
      return processedItems.value[itemIndex] || null
    }

    const getItemIndex = (rowIndex, colIndex) => {
      return rowIndex * props.itemsPerRow + colIndex
    }

    // 防抖和節流優化的滾動處理
    const handleScroll = (event) => {
      const now = performance.now()
      const newScrollTop = event.target.scrollTop
      
      // 防抖：如果滾動變化很小，忽略
      if (Math.abs(newScrollTop - scrollTop.value) < 1) {
        return
      }

      // 節流：限制更新頻率
      if (now - lastScrollTime.value < 8) { // ~120fps 限制
        return
      }

      // 取消之前的 RAF
      if (rafId.value) {
        cancelAnimationFrame(rafId.value)
      }

      isScrolling.value = true
      lastScrollTime.value = now

      // 使用 RAF 來優化滾動性能
      rafId.value = requestAnimationFrame(() => {
        scrollTop.value = newScrollTop
        
        // 滾動結束後清理
        setTimeout(() => {
          isScrolling.value = false
        }, 150)
      })

      // 同步更新 scrollTop 以便測試
      if (typeof newScrollTop === 'number') {
        scrollTop.value = newScrollTop
      }
    }

    // 滾動位置恢復
    const restoreScrollPosition = (position) => {
      if (containerRef.value && typeof position === 'number') {
        containerRef.value.scrollTop = position
        scrollTop.value = position
      }
    }

    // 生命週期
    onMounted(() => {
      // 確保容器正確初始化
      nextTick(() => {
        if (containerRef.value) {
          scrollTop.value = containerRef.value.scrollTop
        }
      })
    })

    onUnmounted(() => {
      if (rafId.value) {
        cancelAnimationFrame(rafId.value)
      }
    })

    // 監聽 props 變化，優化滾動位置處理
    watch(() => props.items, (newItems) => {
      // 更新 shallowRef
      itemsRef.value = newItems
      
      // 只有在非保持滾動位置模式下才重置
      if (!props.preserveScrollPosition) {
        scrollTop.value = 0
        if (containerRef.value) {
          containerRef.value.scrollTop = 0
        }
      }
    }, { flush: 'sync' })

    watch(() => props.itemsPerRow, () => {
      // itemsPerRow 變化時總是重置滾動位置
      scrollTop.value = 0
      if (containerRef.value) {
        containerRef.value.scrollTop = 0
      }
    })

    // 暴露給模板和測試
    return {
      // 模板用的 refs
      containerRef,

      // 計算屬性（供測試用）
      totalRows,
      totalHeight,
      visibleRows,
      bufferedRows,
      startRow,
      endRow,
      offsetY,
      visibleRowIndexes,
      visibleRowsData,
      visibleItems,
      scrollTop,
      processedItems,
      
      // 新增的響應式數據
      isScrolling,

      // 方法
      getItemAt,
      getItemIndex,
      getItemHeight,
      handleScroll,
      restoreScrollPosition
    }
  }
}
</script>

<style scoped>
.virtual-scroll-grid {
  position: relative;
  overflow-y: auto;
  overflow-x: hidden;
}

.virtual-grid-row {
  width: 100%;
}

.virtual-grid-item {
  min-height: 100%;
}

/* Webkit 瀏覽器的滾動條樣式 (Chrome, Safari, Edge) */
.virtual-scroll-grid::-webkit-scrollbar {
  width: 6px;
}

.virtual-scroll-grid::-webkit-scrollbar-track {
  background: transparent;
  border-radius: 3px;
  margin: 2px 0; /* 上下留白 */
}

.virtual-scroll-grid::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
  border: 1px solid #f1f5f9; /* 細邊框 */
}

.virtual-scroll-grid::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.virtual-scroll-grid::-webkit-scrollbar-corner {
  background: transparent;
}

/* Firefox 的滾動條樣式 */
.virtual-scroll-grid {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}
</style>