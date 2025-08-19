import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { ref, nextTick } from 'vue'
import { mount } from '@vue/test-utils'
import { useAsyncState } from '@/features/icon-picker/composables/useAsyncState.js'

describe('useAsyncState', () => {
  let mockAsyncFn
  
  beforeEach(() => {
    mockAsyncFn = vi.fn()
  })
  
  afterEach(() => {
    vi.clearAllMocks()
  })

  describe('基本功能', () => {
    it('應該初始化預設狀態', () => {
      const { data, loading, error } = useAsyncState()
      
      expect(data.value).toBeNull()
      expect(loading.value).toBe(false)
      expect(error.value).toBeNull()
    })
    
    it('應該支援初始資料', () => {
      const initialData = { test: 'value' }
      const { data } = useAsyncState({ initialData })
      
      expect(data.value).toEqual(initialData)
    })
    
    it('應該支援立即載入', async () => {
      mockAsyncFn.mockResolvedValue({ result: 'success' })
      
      const { data, loading, execute } = useAsyncState({
        immediate: true,
        asyncFn: mockAsyncFn
      })
      
      expect(loading.value).toBe(true)
      await nextTick()
      expect(mockAsyncFn).toHaveBeenCalledTimes(1)
    })
  })

  describe('執行非同步操作', () => {
    it('應該正確處理成功的非同步操作', async () => {
      const expectedResult = { data: 'test' }
      mockAsyncFn.mockResolvedValue(expectedResult)
      
      const { data, loading, error, execute } = useAsyncState()
      
      const promise = execute(mockAsyncFn)
      
      expect(loading.value).toBe(true)
      expect(error.value).toBeNull()
      
      const result = await promise
      
      expect(loading.value).toBe(false)
      expect(data.value).toEqual(expectedResult)
      expect(result).toEqual(expectedResult)
      expect(error.value).toBeNull()
    })
    
    it('應該正確處理失敗的非同步操作', async () => {
      const expectedError = new Error('Test error')
      mockAsyncFn.mockRejectedValue(expectedError)
      
      const { data, loading, error, execute } = useAsyncState()
      
      try {
        await execute(mockAsyncFn)
      } catch (err) {
        expect(err).toBe(expectedError)
      }
      
      expect(loading.value).toBe(false)
      expect(data.value).toBeNull()
      expect(error.value).toBe(expectedError)
    })
    
    it('應該支援傳遞參數給非同步函數', async () => {
      mockAsyncFn.mockResolvedValue('result')
      
      const { execute } = useAsyncState()
      
      await execute(mockAsyncFn, 'arg1', 'arg2')
      
      expect(mockAsyncFn).toHaveBeenCalledWith('arg1', 'arg2')
    })
  })

  describe('錯誤處理', () => {
    it('應該支援自訂錯誤處理器', async () => {
      const customError = new Error('Custom error')
      const errorHandler = vi.fn(() => customError)
      mockAsyncFn.mockRejectedValue(new Error('Original error'))
      
      const { error, execute } = useAsyncState({
        onError: errorHandler
      })
      
      try {
        await execute(mockAsyncFn)
      } catch (err) {
        expect(err).toBe(customError)
      }
      
      expect(errorHandler).toHaveBeenCalled()
      expect(error.value).toBe(customError)
    })
    
    it('應該在重新執行時清除之前的錯誤', async () => {
      mockAsyncFn.mockRejectedValueOnce(new Error('First error'))
      mockAsyncFn.mockResolvedValueOnce('success')
      
      const { error, execute } = useAsyncState()
      
      // 第一次執行失敗
      try {
        await execute(mockAsyncFn)
      } catch {}
      
      expect(error.value).toBeTruthy()
      
      // 第二次執行成功
      await execute(mockAsyncFn)
      
      expect(error.value).toBeNull()
    })
  })

  describe('資料轉換', () => {
    it('應該支援資料轉換函數', async () => {
      const rawData = { value: 100 }
      const transformedData = { value: 200 }
      const transform = vi.fn(() => transformedData)
      mockAsyncFn.mockResolvedValue(rawData)
      
      const { data, execute } = useAsyncState({
        transform
      })
      
      await execute(mockAsyncFn)
      
      expect(transform).toHaveBeenCalledWith(rawData)
      expect(data.value).toEqual(transformedData)
    })
    
    it('應該在轉換函數出錯時處理錯誤', async () => {
      const transformError = new Error('Transform error')
      const transform = vi.fn(() => { throw transformError })
      mockAsyncFn.mockResolvedValue({ data: 'test' })
      
      const { data, error, execute } = useAsyncState({
        transform
      })
      
      try {
        await execute(mockAsyncFn)
      } catch (err) {
        expect(err).toBe(transformError)
      }
      
      expect(data.value).toBeNull()
      expect(error.value).toBe(transformError)
    })
  })

  describe('重置功能', () => {
    it('應該能重置所有狀態', async () => {
      mockAsyncFn.mockResolvedValue({ data: 'test' })
      
      const { data, loading, error, execute, reset } = useAsyncState()
      
      // 執行操作
      await execute(mockAsyncFn)
      expect(data.value).toBeTruthy()
      
      // 重置
      reset()
      
      expect(data.value).toBeNull()
      expect(loading.value).toBe(false)
      expect(error.value).toBeNull()
    })
    
    it('應該支援重置到初始資料', () => {
      const initialData = { initial: true }
      const { data, reset } = useAsyncState({ initialData })
      
      // 手動變更資料
      data.value = { changed: true }
      expect(data.value).toEqual({ changed: true })
      
      // 重置到初始值
      reset()
      expect(data.value).toEqual(initialData)
    })
  })

  describe('在 Vue 元件中使用', () => {
    it('應該在元件中正常工作', async () => {
      const TestComponent = {
        template: '<div>{{ data ? data.message : "loading" }}</div>',
        setup() {
          const { data, execute } = useAsyncState()
          
          const load = () => execute(() => 
            Promise.resolve({ message: 'loaded' })
          )
          
          return { data, load }
        }
      }
      
      const wrapper = mount(TestComponent)
      expect(wrapper.text()).toBe('loading')
      
      await wrapper.vm.load()
      await nextTick()
      
      expect(wrapper.text()).toBe('loaded')
    })
  })

  describe('並發處理', () => {
    it('應該取消前一個進行中的請求', async () => {
      let resolve1, resolve2
      const promise1 = new Promise(r => { resolve1 = r })
      const promise2 = new Promise(r => { resolve2 = r })
      
      const fn1 = vi.fn(() => promise1)
      const fn2 = vi.fn(() => promise2)
      
      const { data, execute } = useAsyncState()
      
      // 開始第一個請求
      const exec1 = execute(fn1)
      expect(data.value).toBeNull()
      
      // 開始第二個請求（應該取消第一個）
      const exec2 = execute(fn2)
      
      // 完成第一個請求（應該被忽略）
      resolve1({ from: 'first' })
      await exec1.catch(() => {}) // 可能會被取消
      
      // 完成第二個請求
      resolve2({ from: 'second' })
      await exec2
      
      expect(data.value).toEqual({ from: 'second' })
    })
  })
})