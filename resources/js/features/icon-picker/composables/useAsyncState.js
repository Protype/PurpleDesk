import { ref, shallowRef } from 'vue'

/**
 * 統一的非同步狀態管理 Composable
 * 
 * 提供標準化的 loading、error、data 狀態管理
 * 支援自動執行、錯誤處理、資料轉換等功能
 * 
 * @param {Object} options - 配置選項
 * @param {any} options.initialData - 初始資料
 * @param {boolean} options.immediate - 是否立即執行
 * @param {Function} options.asyncFn - 立即執行的非同步函數
 * @param {Function} options.onError - 錯誤處理函數
 * @param {Function} options.transform - 資料轉換函數
 * @returns {Object} 包含狀態和操作方法的物件
 */
export function useAsyncState(options = {}) {
  const {
    initialData = null,
    immediate = false,
    asyncFn = null,
    onError = null,
    transform = null
  } = options

  // 狀態管理
  const data = shallowRef(initialData)
  const loading = ref(false)
  const error = ref(null)
  
  // 用於追蹤當前執行的請求，實現並發控制
  let currentExecution = null
  let executionCounter = 0

  /**
   * 執行非同步操作
   * 
   * @param {Function} asyncFunction - 要執行的非同步函數
   * @param {...any} args - 傳遞給非同步函數的參數
   * @returns {Promise} 執行結果的 Promise
   */
  const execute = async (asyncFunction, ...args) => {
    // 增加執行計數器，用於並發控制
    const currentId = ++executionCounter
    currentExecution = currentId

    // 重置狀態
    loading.value = true
    error.value = null

    try {
      // 執行非同步操作
      const result = await asyncFunction(...args)
      
      // 檢查是否被新的請求取代
      if (currentExecution !== currentId) {
        return result // 靜默返回結果，但不更新狀態
      }

      // 資料轉換
      let finalData = result
      if (transform) {
        try {
          finalData = transform(result)
        } catch (transformError) {
          throw transformError
        }
      }

      // 更新成功狀態
      data.value = finalData
      loading.value = false
      
      return result
    } catch (err) {
      // 檢查是否被新的請求取代
      if (currentExecution !== currentId) {
        throw err // 重新拋出錯誤，但不更新狀態
      }

      // 錯誤處理
      let finalError = err
      if (onError) {
        try {
          finalError = onError(err) || err
        } catch (handlerError) {
          finalError = handlerError
        }
      }

      // 更新錯誤狀態
      error.value = finalError
      loading.value = false
      
      throw finalError
    }
  }

  /**
   * 重置所有狀態到初始值
   */
  const reset = () => {
    data.value = initialData
    loading.value = false
    error.value = null
    currentExecution = null
  }

  // 立即執行
  if (immediate && asyncFn) {
    execute(asyncFn).catch(() => {
      // 忽略立即執行的錯誤，錯誤已經存儲在 error.value 中
    })
  }

  return {
    // 狀態
    data,
    loading,
    error,
    
    // 方法
    execute,
    reset
  }
}