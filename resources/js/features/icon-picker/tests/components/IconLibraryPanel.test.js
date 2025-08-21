import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'
import IconLibraryPanel from '../../components/IconLibraryPanel.vue'

// Mock 相依元件
vi.mock('../../components/shared/VirtualScrollGrid.vue', () => ({
  default: {
    name: 'VirtualScrollGrid',
    template: '<div class="mock-virtual-grid"><slot name="item" v-for="item in items" :item="item" :itemIndex="index" :key="item.key || index"></slot></div>',
    props: ['items', 'itemsPerRow', 'rowHeight', 'containerHeight', 'buffer', 'preserveScrollPosition'],
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

vi.mock('../../components/IconStyleSelector.vue', () => ({
  default: {
    name: 'IconStyleSelector',
    template: '<select class="mock-style-selector" :value="modelValue" @change="$emit(\'update:modelValue\', $event.target.value)"><option value="outline">Outline</option><option value="solid">Solid</option><option value="all">All</option></select>',
    props: ['modelValue'],
    emits: ['update:modelValue']
  }
}))

// Mock preloaded data provider (新的資料格式)
const mockIconData = {
  data: {
    heroicons: [
      {
        id: 'home-outline',
        name: 'Home',
        value: 'HomeIcon',
        type: 'heroicons',
        keywords: ['home', 'house'],
        category: 'general',
        has_variants: true,
        variant_type: 'outline'
      },
      {
        id: 'home-solid',
        name: 'Home',
        value: 'HomeIcon',
        type: 'heroicons',
        keywords: ['home', 'house'],
        category: 'general',
        has_variants: true,
        variant_type: 'solid'
      },
      {
        id: 'user-outline',
        name: 'User',
        value: 'UserIcon',
        type: 'heroicons',
        keywords: ['user', 'person'],
        category: 'people',
        has_variants: true,
        variant_type: 'outline'
      }
    ],
    bootstrap: {
      general: [
        {
          name: 'Activity',
          value: 'bi-activity',
          type: 'bootstrap-icons',
          keywords: ['activity'],
          category: 'general',
          has_variants: false
        },
        {
          name: 'Alarm',
          value: 'bi-alarm',
          type: 'bootstrap-icons',
          keywords: ['alarm', 'clock'],
          category: 'general',
          has_variants: true
        },
        {
          name: 'Alarm Fill',
          value: 'bi-alarm-fill',
          type: 'bootstrap-icons',
          keywords: ['alarm', 'clock'],
          category: 'general',
          has_variants: true
        }
      ],
      ui: [
        {
          name: 'Arrow Up',
          value: 'bi-arrow-up',
          type: 'bootstrap-icons',
          keywords: ['arrow', 'up'],
          category: 'ui',
          has_variants: false
        }
      ]
    }
  },
  meta: {
    total: 6,
    type: 'mixed'
  }
}

// Mock usePreloadedIconData
vi.mock('../../composables/usePreloadedData.js', () => ({
  usePreloadedIconData: vi.fn(() => ({
    data: { value: mockIconData },
    loading: { value: false },
    error: { value: null },
    reload: vi.fn()
  }))
}))

// Mock useIconVariants
vi.mock('../../composables/useIconVariants.js', () => ({
  useIconVariants: vi.fn(() => ({
    currentIconStyle: { value: 'outline' },
    setIconStyle: vi.fn()
  }))
}))

// Mock useSearchFilter
vi.mock('../../composables/useSearchFilter.js', () => ({
  useSearchFilter: vi.fn((data, options) => {
    const searchQuery = { value: '' }
    const clearSearch = vi.fn(() => { searchQuery.value = '' })
    
    return {
      searchQuery,
      filteredData: data,
      clearSearch
    }
  })
}))

// Mock HeroIcons
vi.mock('@heroicons/vue/outline', () => ({
  HomeIcon: { name: 'HomeIcon' },
  UserIcon: { name: 'UserIcon' }
}))

vi.mock('@heroicons/vue/solid', () => ({
  HomeIcon: { name: 'HomeIconSolid' },
  UserIcon: { name: 'UserIconSolid' }
}))

describe('IconLibraryPanel - 新 API 格式適配', () => {
  let wrapper

  const defaultProps = {
    selectedIcon: null,
    iconType: 'heroicons',
    itemsPerRow: 10
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
      expect(wrapper.findComponent({ name: 'IconPickerSearch' }).exists()).toBe(true)
      expect(wrapper.findComponent({ name: 'IconStyleSelector' }).exists()).toBe(true)
    })

    it('應該正確載入扁平化的圖標資料', async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })

      await nextTick()

      const processedIcons = wrapper.vm.processedIconsData
      expect(Array.isArray(processedIcons)).toBe(true)
      
      // 驗證包含所有類型的圖標
      const heroIcons = processedIcons.filter(icon => icon.type === 'heroicons')
      const bootstrapIcons = processedIcons.filter(icon => icon.type === 'bootstrap-icons')
      
      expect(heroIcons.length).toBe(3) // 3 個 HeroIcons
      expect(bootstrapIcons.length).toBe(4) // 4 個 Bootstrap Icons
    })
  })

  describe('樣式篩選功能 - 新邏輯', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
    })

    it('outline 模式應該只顯示 outline 圖標', async () => {
      wrapper.vm.selectedStyle = 'outline'
      await nextTick()

      const filteredIcons = wrapper.vm.styleFilteredIcons
      
      // HeroIcons: 只顯示有變體且是 outline 的，或無變體的
      const heroIcons = filteredIcons.filter(icon => icon.type === 'heroicons')
      heroIcons.forEach(icon => {
        const isValidOutline = (icon.has_variants === true && icon.variant_type === 'outline') ||
                              (icon.has_variants === false)
        expect(isValidOutline).toBe(true)
      })

      // Bootstrap Icons: 只顯示有變體且是 outline 的，或無變體的  
      const bootstrapIcons = filteredIcons.filter(icon => icon.type === 'bootstrap-icons')
      bootstrapIcons.forEach(icon => {
        const isValidOutline = (icon.has_variants === true && icon.variant_type === 'outline') ||
                              (icon.has_variants === false)
        expect(isValidOutline).toBe(true)
      })
    })

    it('solid 模式應該只顯示 solid 圖標', async () => {
      wrapper.vm.selectedStyle = 'solid'
      await nextTick()

      const filteredIcons = wrapper.vm.styleFilteredIcons
      
      // HeroIcons: 只顯示有變體且是 solid 的，或無變體的
      const heroIcons = filteredIcons.filter(icon => icon.type === 'heroicons')
      heroIcons.forEach(icon => {
        const isValidSolid = (icon.has_variants === true && icon.variant_type === 'solid') ||
                            (icon.has_variants === false)
        expect(isValidSolid).toBe(true)
      })

      // Bootstrap Icons: 只顯示有變體且是 solid 的，或無變體的
      const bootstrapIcons = filteredIcons.filter(icon => icon.type === 'bootstrap-icons')
      bootstrapIcons.forEach(icon => {
        const isValidSolid = (icon.has_variants === true && icon.variant_type === 'solid') ||
                            (icon.has_variants === false)
        expect(isValidSolid).toBe(true)
      })
    })

    it('all 模式應該顯示所有圖標', async () => {
      wrapper.vm.selectedStyle = 'all'
      await nextTick()

      const filteredIcons = wrapper.vm.styleFilteredIcons
      const processedIcons = wrapper.vm.processedIconsData
      
      expect(filteredIcons.length).toBe(processedIcons.length)
    })

    it('樣式切換不應該修改原始資料', async () => {
      const originalLength = wrapper.vm.processedIconsData.length
      
      wrapper.vm.selectedStyle = 'outline'
      await nextTick()
      
      wrapper.vm.selectedStyle = 'solid' 
      await nextTick()
      
      wrapper.vm.selectedStyle = 'all'
      await nextTick()

      expect(wrapper.vm.processedIconsData.length).toBe(originalLength)
    })
  })

  describe('圖標渲染邏輯', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
    })

    it('應該正確渲染 Bootstrap Icon 的 class', () => {
      const bootstrapIcon = {
        type: 'bootstrap-icons',
        value: 'bi-activity'
      }

      const result = wrapper.vm.getBootstrapIconClass(bootstrapIcon)
      expect(result).toBe('bi-activity')
    })

    it('應該正確渲染 HeroIcon 元件', () => {
      const heroIcon = {
        type: 'heroicons',
        value: 'HomeIcon',
        variant_type: 'outline'
      }

      const component = wrapper.vm.getHeroIconComponent(heroIcon)
      expect(component).toBeTruthy()
    })

    it('應該正確處理圖標標題', () => {
      const iconWithName = { name: 'Home', type: 'heroicons' }
      expect(wrapper.vm.getIconTitle(iconWithName)).toBe('Home')

      const iconWithValue = { value: 'bi-activity', type: 'bootstrap-icons' }
      expect(wrapper.vm.getIconTitle(iconWithValue)).toBe('bi-activity')

      const emptyIcon = {}
      expect(wrapper.vm.getIconTitle(emptyIcon)).toBe('未知圖標')
    })
  })

  describe('分類顯示功能', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
    })

    it('應該正確分組顯示圖標', () => {
      wrapper.vm.searchQuery = '' // 確保不是搜尋模式
      const groupedIcons = wrapper.vm.groupedIcons

      expect(Array.isArray(groupedIcons)).toBe(true)

      // 檢查是否包含分類標題
      const hasCategoryHeaders = groupedIcons.some(item => item.type === 'category-header')
      expect(hasCategoryHeaders).toBe(true)

      // 檢查 HeroIcons 分類
      const heroHeader = groupedIcons.find(item => 
        item.type === 'category-header' && 
        item.data && 
        item.data.title === 'Heroicons'
      )
      expect(heroHeader).toBeTruthy()

      // 檢查 Bootstrap Icons 分類
      const generalHeader = groupedIcons.find(item =>
        item.type === 'category-header' &&
        item.data &&
        item.data.title === '通用圖標'
      )
      expect(generalHeader).toBeTruthy()
    })

    it('搜尋時應該返回扁平化結果', () => {
      wrapper.vm.searchQuery = 'home'
      const groupedIcons = wrapper.vm.groupedIcons

      // 搜尋時不應該有分類標題
      const hasCategoryHeaders = groupedIcons.some(item => item.type === 'category-header')
      expect(hasCategoryHeaders).toBe(false)

      // 所有項目都應該是圖標
      groupedIcons.forEach(item => {
        expect(['heroicons', 'bootstrap-icons']).toContain(item.type)
      })
    })
  })

  describe('VirtualScrollGrid 整合', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
    })

    it('應該正確轉換為 VirtualScrollGrid 格式', () => {
      const virtualItems = wrapper.vm.virtualGridItems

      expect(Array.isArray(virtualItems)).toBe(true)
      
      virtualItems.forEach(item => {
        expect(item).toHaveProperty('key')
        expect(item).toHaveProperty('type')
        expect(item).toHaveProperty('data')
        
        if (item.type === 'category') {
          expect(item).toHaveProperty('fullRow', true)
          expect(item).toHaveProperty('itemHeight', 40)
        }
      })
    })

    it('應該正確設置 VirtualScrollGrid 屬性', () => {
      const virtualGrid = wrapper.findComponent({ name: 'VirtualScrollGrid' })
      
      expect(virtualGrid.exists()).toBe(true)
      expect(virtualGrid.props('itemsPerRow')).toBe(10)
      expect(virtualGrid.props('rowHeight')).toBe(34)
      expect(virtualGrid.props('containerHeight')).toBe(176)
      expect(virtualGrid.props('buffer')).toBe(2)
      expect(virtualGrid.props('preserveScrollPosition')).toBe(true)
    })
  })

  describe('圖標選擇功能', () => {
    beforeEach(async () => {
      wrapper = mount(IconLibraryPanel, {
        props: defaultProps
      })
      await nextTick()
    })

    it('應該觸發圖標選擇事件', () => {
      const mockIcon = {
        name: 'Home',
        value: 'HomeIcon',
        type: 'heroicons'
      }

      wrapper.vm.selectIcon(mockIcon)

      expect(wrapper.emitted('icon-select')).toBeTruthy()
      expect(wrapper.emitted('icon-select')[0][0]).toEqual(mockIcon)

      expect(wrapper.emitted('icon-change')).toBeTruthy()
      expect(wrapper.emitted('icon-change')[0][0]).toEqual({
        icon: mockIcon,
        type: 'heroicons',
        style: 'outline'
      })
    })

    it('應該正確識別選中的圖標', () => {
      wrapper = mount(IconLibraryPanel, {
        props: {
          ...defaultProps,
          selectedIcon: 'HomeIcon'
        }
      })

      const selectedIcon = { value: 'HomeIcon' }
      const otherIcon = { value: 'UserIcon' }

      expect(wrapper.vm.isSelected(selectedIcon)).toBe(true)
      expect(wrapper.vm.isSelected(otherIcon)).toBe(false)
    })
  })

  describe('錯誤和載入狀態', () => {
    it('應該正確顯示載入狀態', () => {
      // Mock loading state
      const mockUsePreloadedIconData = vi.mocked(
        require('../../composables/usePreloadedData.js').usePreloadedIconData
      )
      mockUsePreloadedIconData.mockReturnValueOnce({
        data: { value: null },
        loading: { value: true },
        error: { value: null },
        reload: vi.fn()
      })

      wrapper = mount(IconLibraryPanel, { props: defaultProps })

      expect(wrapper.find('.loading').exists()).toBe(true)
      expect(wrapper.text()).toContain('載入圖標資料中')
    })

    it('應該正確顯示錯誤狀態', () => {
      const mockUsePreloadedIconData = vi.mocked(
        require('../../composables/usePreloadedData.js').usePreloadedIconData
      )
      mockUsePreloadedIconData.mockReturnValueOnce({
        data: { value: null },
        loading: { value: false },
        error: { value: { message: '載入失敗' } },
        reload: vi.fn()
      })

      wrapper = mount(IconLibraryPanel, { props: defaultProps })

      expect(wrapper.find('.error').exists()).toBe(true)
      expect(wrapper.text()).toContain('載入失敗')
    })

    it('應該正確顯示空狀態', async () => {
      wrapper = mount(IconLibraryPanel, { props: defaultProps })

      // 模擬搜尋無結果
      wrapper.vm.searchQuery = 'nonexistent-icon'
      await nextTick()

      expect(wrapper.find('.empty-state').exists()).toBe(true)
      expect(wrapper.text()).toContain('找不到符合')
    })
  })
})