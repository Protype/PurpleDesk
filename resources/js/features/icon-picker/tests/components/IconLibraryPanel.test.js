import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'
import IconLibraryPanel from '../../components/IconLibraryPanel.vue'

// Mock 相依元件
vi.mock('../../components/shared/VirtualScrollGrid.vue', () => ({
  default: {
    name: 'VirtualScrollGrid',
    template: '<div class="mock-virtual-grid"><slot name="item" v-for="item in items" :item="item" :key="item.key"></slot></div>',
    props: ['items', 'itemsPerRow', 'rowHeight', 'containerHeight'],
    emits: []
  }
}))

vi.mock('../../components/IconPickerSearch.vue', () => ({
  default: {
    name: 'IconPickerSearch',
    template: '<input class="mock-search" :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" />',
    props: ['modelValue', 'placeholder'],
    emits: ['update:modelValue', 'search', 'clear']
  }
}))

vi.mock('../../components/VariantSelector.vue', () => ({
  default: {
    name: 'VariantSelector',
    template: '<select class="mock-variant-selector" :value="modelValue" @change="$emit(\'variant-change\', { value: $event.target.value })"><option value="outline">Outline</option><option value="solid">Solid</option></select>',
    props: ['modelValue', 'variantType', 'variants'],
    emits: ['variant-change']
  }
}))

// Mock IconDataLoader
vi.mock('../../services/IconDataLoader.js', () => ({
  IconDataLoader: vi.fn().mockImplementation(() => ({
    getIconLibraryData: vi.fn().mockResolvedValue({
      data: {
        heroicons: [
          { component: 'AcademicCapIcon', name: 'academic-cap', type: 'heroicons', variants: { outline: { component: 'AcademicCapIcon' } } },
          { component: 'AdjustmentsIcon', name: 'adjustments', type: 'heroicons', variants: { outline: { component: 'AdjustmentsIcon' } } }
        ],
        bootstrap: {
          general: [
            { class: 'bi-alarm', name: 'alarm', type: 'bootstrap', category: 'general' },
            { class: 'bi-bell', name: 'bell', type: 'bootstrap', category: 'general' }
          ]
        }
      },
      meta: {}
    })
  }))
}))

// Mock useIconVariants composable
vi.mock('../../composables/useIconVariants.js', () => ({
  useIconVariants: vi.fn(() => ({
    currentIconStyle: { value: 'outline' },
    getVariantOptions: vi.fn().mockReturnValue([
      { label: 'Outline', value: 'outline' },
      { label: 'Solid', value: 'solid' }
    ]),
    setIconStyle: vi.fn()
  }))
}))

describe('IconLibraryPanel', () => {
  let wrapper

  const defaultProps = {
    selectedIcon: null,
    iconType: 'heroicons',
    itemsPerRow: 8
  }

  beforeEach(() => {
    vi.clearAllMocks()
  })

  describe('元件初始化', () => {
    it('應該正確渲染基本結構', async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })

      await nextTick()

      expect(wrapper.find('.icon-library-panel').exists()).toBe(true)
      expect(wrapper.find('.panel-toolbar').exists()).toBe(true)
      expect(wrapper.find('.library-tabs').exists()).toBe(true)
    })

    it('應該正確渲染搜尋欄和變體選擇器', async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })

      await nextTick()

      expect(wrapper.findComponent({ name: 'IconPickerSearch' }).exists()).toBe(true)
      expect(wrapper.findComponent({ name: 'VariantSelector' }).exists()).toBe(true)
    })

    it('應該正確渲染圖標庫標籤', async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })

      await nextTick()

      const buttons = wrapper.findAll('button')
      const heroButton = buttons.find(btn => btn.text().includes('HeroIcons'))
      const bootstrapButton = buttons.find(btn => btn.text().includes('Bootstrap Icons'))
      
      expect(heroButton).toBeTruthy()
      expect(bootstrapButton).toBeTruthy()
    })
  })

  describe('圖標庫切換功能', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
      // 等待資料載入
      await new Promise(resolve => setTimeout(resolve, 50))
    })

    it('預設應該選中 HeroIcons', () => {
      expect(wrapper.vm.activeLibrary).toBe('heroicons')
    })

    it('應該能切換到 Bootstrap Icons', async () => {
      const buttons = wrapper.findAll('button')
      const bootstrapButton = buttons.find(btn => 
        btn.text().includes('Bootstrap Icons')
      )
      
      await bootstrapButton.trigger('click')
      expect(wrapper.vm.activeLibrary).toBe('bootstrap')
    })

    it('應該正確顯示圖標數量', async () => {
      // 等待資料載入完成
      await new Promise(resolve => setTimeout(resolve, 50))
      
      expect(wrapper.vm.heroIconsCount).toBe(2)
      expect(wrapper.vm.bootstrapIconsCount).toBe(2)
    })
  })

  describe('搜尋功能', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 50))
    })

    it('應該能更新搜尋查詢', async () => {
      const searchInput = wrapper.findComponent({ name: 'IconPickerSearch' })
      
      await searchInput.vm.$emit('update:modelValue', 'alarm')
      await nextTick()
      
      expect(wrapper.vm.searchQuery).toBe('alarm')
    })

    it('應該根據搜尋查詢過濾圖標', async () => {
      // 切換到 bootstrap 庫以測試搜尋功能
      wrapper.vm.activeLibrary = 'bootstrap'
      wrapper.vm.searchQuery = 'alarm'
      await nextTick()
      
      const filtered = wrapper.vm.filteredIcons
      expect(filtered).toHaveLength(1)
      expect(filtered[0].name).toBe('alarm')
    })

    it('應該支援清除搜尋', async () => {
      wrapper.vm.searchQuery = 'test'
      const searchComponent = wrapper.findComponent({ name: 'IconPickerSearch' })
      
      await searchComponent.vm.$emit('clear')
      await nextTick()
      
      expect(wrapper.vm.searchQuery).toBe('')
    })
  })

  describe('樣式變體功能', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 50))
    })

    it('預設應該選中 outline 樣式', () => {
      expect(wrapper.vm.selectedStyle).toBe('outline')
    })

    it('應該能切換樣式變體', async () => {
      const variantSelector = wrapper.findComponent({ name: 'VariantSelector' })
      
      await variantSelector.vm.$emit('variant-change', { value: 'solid' })
      await nextTick()
      
      expect(wrapper.vm.selectedStyle).toBe('solid')
    })
  })

  describe('圖標選擇功能', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 50))
    })

    it('應該觸發圖標選擇事件', async () => {
      const mockIcon = { 
        component: 'AcademicCapIcon', 
        name: 'academic-cap', 
        type: 'heroicons' 
      }
      
      wrapper.vm.selectIcon(mockIcon)
      await nextTick()
      
      expect(wrapper.emitted('icon-select')).toBeTruthy()
      expect(wrapper.emitted('icon-select')[0][0]).toEqual(mockIcon)
      
      expect(wrapper.emitted('icon-change')).toBeTruthy()
      expect(wrapper.emitted('icon-change')[0][0]).toEqual({
        icon: mockIcon,
        type: 'heroicons',
        style: 'outline'
      })
    })

    it('應該正確識別選中的圖標', async () => {
      wrapper = mount(IconLibraryPanel, {
        props: {
          ...defaultProps,
          selectedIcon: 'AcademicCapIcon'
        }
      })
      
      const mockIcon = { component: 'AcademicCapIcon' }
      expect(wrapper.vm.isSelected(mockIcon)).toBe(true)
      
      const otherIcon = { component: 'AdjustmentsIcon' }
      expect(wrapper.vm.isSelected(otherIcon)).toBe(false)
    })
  })

  describe('載入狀態', () => {
    it('應該顯示載入狀態', async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      
      // 設定載入狀態
      wrapper.vm.isLoading = true
      await nextTick()
      
      expect(wrapper.find('.flex.items-center.justify-center').exists()).toBe(true)
      expect(wrapper.text()).toContain('載入圖標...')
    })

    it('應該顯示錯誤狀態', async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      
      wrapper.vm.isLoading = false
      wrapper.vm.error = '載入失敗'
      await nextTick()
      
      expect(wrapper.text()).toContain('載入失敗')
      const buttons = wrapper.findAll('button')
      const reloadButton = buttons.find(btn => btn.text().includes('重新載入'))
      expect(reloadButton).toBeTruthy()
    })

    it('應該顯示空狀態', async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      
      wrapper.vm.isLoading = false
      wrapper.vm.error = null
      wrapper.vm.searchQuery = 'nonexistent'
      await nextTick()
      
      expect(wrapper.find('.empty-state').exists()).toBe(true)
      expect(wrapper.text()).toContain('找不到符合')
    })
  })

  describe('工具方法', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
    })

    it('應該正確取得圖標標題', () => {
      const iconWithName = { name: 'test-icon' }
      expect(wrapper.vm.getIconTitle(iconWithName)).toBe('test-icon')
      
      const iconWithClass = { class: 'bi-test' }
      expect(wrapper.vm.getIconTitle(iconWithClass)).toBe('bi-test')
      
      const iconWithComponent = { component: 'TestIcon' }
      expect(wrapper.vm.getIconTitle(iconWithComponent)).toBe('TestIcon')
      
      const emptyIcon = {}
      expect(wrapper.vm.getIconTitle(emptyIcon)).toBe('未知圖標')
    })

    it('應該正確取得 HeroIcon 元件', () => {
      const icon = { 
        component: 'AcademicCapIcon',
        variants: { 
          outline: { component: 'AcademicCapIcon' },
          solid: { component: 'AcademicCapSolidIcon' }
        }
      }
      
      wrapper.vm.selectedStyle = 'outline'
      expect(wrapper.vm.getHeroIconComponent(icon)).toBe('AcademicCapIcon')
      
      wrapper.vm.selectedStyle = 'solid'
      expect(wrapper.vm.getHeroIconComponent(icon)).toBe('AcademicCapSolidIcon')
    })

    it('應該正確取得 Bootstrap Icon 類別', () => {
      const icon = { 
        class: 'bi-alarm',
        variants: { 
          solid: { class: 'bi-alarm-fill' }
        }
      }
      
      wrapper.vm.selectedStyle = 'outline'
      expect(wrapper.vm.getBootstrapIconClass(icon)).toBe('bi-alarm')
      
      wrapper.vm.selectedStyle = 'solid'
      expect(wrapper.vm.getBootstrapIconClass(icon)).toBe('bi-alarm-fill')
    })
  })

  describe('VirtualScrollGrid 整合', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 50))
    })

    it('應該正確轉換項目為 VirtualScrollGrid 格式', async () => {
      const items = wrapper.vm.virtualGridItems
      expect(Array.isArray(items)).toBe(true)
      expect(items.length).toBeGreaterThan(0)
      
      const firstItem = items[0]
      expect(firstItem).toHaveProperty('key')
      expect(firstItem).toHaveProperty('type')
      expect(firstItem).toHaveProperty('data')
    })

    it('應該正確渲染 VirtualScrollGrid 元件', () => {
      const virtualGrid = wrapper.findComponent({ name: 'VirtualScrollGrid' })
      expect(virtualGrid.exists()).toBe(true)
      expect(virtualGrid.props('itemsPerRow')).toBe(8)
      expect(virtualGrid.props('rowHeight')).toBe(36)
      expect(virtualGrid.props('containerHeight')).toBe(400)
    })
  })
})