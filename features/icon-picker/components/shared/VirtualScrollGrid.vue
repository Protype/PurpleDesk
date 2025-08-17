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
          v-for="rowIndex in visibleRowIndexes"
          :key="rowIndex"
          class="virtual-grid-row"
          :style="{ 
            height: `${rowHeight}px`,
            display: 'flex',
            alignItems: 'center'
          }"
        >
          <!-- 渲染該行的項目 -->
          <template
            v-for="colIndex in itemsPerRow"
            :key="`${rowIndex}-${colIndex}`"
          >
            <div
              v-if="getItemAt(rowIndex, colIndex - 1)"
              class="virtual-grid-item"
              :style="{ 
                flex: `0 0 ${100 / itemsPerRow}%`,
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center'
              }"
            >
              <!-- 使用 slot 渲染項目內容 -->
              <slot 
                name="item" 
                :item="getItemAt(rowIndex, colIndex - 1)"
                :index="getItemIndex(rowIndex, colIndex - 1)"
                :row="rowIndex"
                :col="colIndex - 1"
              >
                <!-- 預設渲染 -->
                <div>{{ getItemAt(rowIndex, colIndex - 1)?.name || '' }}</div>
              </slot>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'

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
      default: 36
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
    }
  },
  setup(props) {
    // 響應式數據
    const containerRef = ref(null)
    const scrollTop = ref(0)
    const rafId = ref(null)

    // 計算屬性
    const totalRows = computed(() => {
      return Math.ceil(props.items.length / props.itemsPerRow)
    })

    const totalHeight = computed(() => {
      return totalRows.value * props.rowHeight
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

    const visibleItems = computed(() => {
      // 如果容器高度為 0，不顯示任何項目
      if (props.containerHeight <= 0) {
        return []
      }

      const items = []
      const start = Math.max(0, startRow.value - props.buffer)
      const end = endRow.value

      for (let rowIndex = start; rowIndex < end; rowIndex++) {
        for (let colIndex = 0; colIndex < props.itemsPerRow; colIndex++) {
          const itemIndex = rowIndex * props.itemsPerRow + colIndex
          if (itemIndex < props.items.length) {
            items.push({
              item: props.items[itemIndex],
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
      return props.items[itemIndex] || null
    }

    const getItemIndex = (rowIndex, colIndex) => {
      return rowIndex * props.itemsPerRow + colIndex
    }

    const handleScroll = (event) => {
      // 取消之前的 RAF
      if (rafId.value) {
        cancelAnimationFrame(rafId.value)
      }

      // 使用 RAF 來優化滾動性能
      rafId.value = requestAnimationFrame(() => {
        scrollTop.value = event.target.scrollTop
      })

      // 同步更新 scrollTop 以便測試
      if (typeof event.target.scrollTop === 'number') {
        scrollTop.value = event.target.scrollTop
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

    // 監聽 props 變化，重置滾動位置
    watch([() => props.items, () => props.itemsPerRow], () => {
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
      visibleItems,
      scrollTop,

      // 方法
      getItemAt,
      getItemIndex,
      handleScroll
    }
  }
}
</script>

<style scoped>
.virtual-scroll-grid {
  position: relative;
  overflow: auto;
}

.virtual-grid-row {
  width: 100%;
}

.virtual-grid-item {
  min-height: 100%;
}

/* 滾動條樣式優化 */
.virtual-scroll-grid::-webkit-scrollbar {
  width: 6px;
}

.virtual-scroll-grid::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.virtual-scroll-grid::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.virtual-scroll-grid::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>