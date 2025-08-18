/**
 * Emoji 膚色處理工具
 * 使用後端提供的結構化資料進行膚色處理
 */

/**
 * 檢查 emoji 是否支援膚色變體
 * @param {Object} emojiData - emoji 資料物件
 * @param {boolean} emojiData.has_skin_tone - 後端提供的膚色支援標記
 * @returns {boolean} 是否支援膚色變體
 */
export function canApplySkinTone(emojiData) {
  if (!emojiData) return false
  return emojiData.has_skin_tone === true
}

/**
 * 套用膚色到 emoji
 * @param {Object} emojiData - emoji 資料物件
 * @param {string} emojiData.emoji - 基礎 emoji
 * @param {Object} emojiData.skin_variations - 膚色變體物件
 * @param {number|string} skinTone - 膚色代號 (1-5)
 * @returns {string} 套用膚色後的 emoji
 */
export function applySkinTone(emojiData, skinTone) {
  if (!emojiData) return ''
  
  // 如果不支援膚色或沒有指定膚色，返回基礎 emoji
  if (!canApplySkinTone(emojiData) || !skinTone) {
    return emojiData.emoji || ''
  }
  
  // 從 skin_variations 中取得對應的膚色變體
  const variations = emojiData.skin_variations
  if (!variations || typeof variations !== 'object') {
    return emojiData.emoji || ''
  }
  
  // 支援數字和字串類型的 skinTone
  const toneKey = parseInt(skinTone, 10)
  const variation = variations[toneKey]
  
  // DEBUG: 顯示膚色處理細節
  if (emojiData.emoji === '👋' && skinTone) {
    console.log('applySkinTone DEBUG:', {
      emoji: emojiData.emoji,
      skinTone,
      skinToneType: typeof skinTone,
      toneKey,
      variations,
      variation,
      result: variation || emojiData.emoji
    })
  }
  
  // 如果找到變體就返回，否則 fallback 到基礎 emoji
  return variation || emojiData.emoji || ''
}

/**
 * 批次套用膚色到 emoji 列表
 * @param {Array} emojiList - emoji 列表
 * @param {number|string} skinTone - 膚色代號
 * @returns {Array} 套用膚色後的 emoji 列表
 */
export function applyBulkSkinTone(emojiList, skinTone) {
  if (!Array.isArray(emojiList)) return []
  
  return emojiList.map(emojiData => ({
    ...emojiData,
    displayEmoji: applySkinTone(emojiData, skinTone)
  }))
}

/**
 * 取得可用的膚色選項
 * @returns {Array} 膚色選項列表
 */
export function getSkinToneOptions() {
  return [
    { tone: '', name: '預設', emoji: '✋' },
    { tone: 1, name: '淺膚色', emoji: '✋🏻' },
    { tone: 2, name: '中淺膚色', emoji: '✋🏼' },
    { tone: 3, name: '中膚色', emoji: '✋🏽' },
    { tone: 4, name: '中深膚色', emoji: '✋🏾' },
    { tone: 5, name: '深膚色', emoji: '✋🏿' }
  ]
}

/**
 * 驗證膚色變體資料結構
 * @param {Object} emojiData - emoji 資料物件
 * @returns {boolean} 資料結構是否有效
 */
export function validateSkinToneData(emojiData) {
  if (!emojiData || typeof emojiData !== 'object') return false
  
  // 檢查必要欄位
  if (typeof emojiData.emoji !== 'string') return false
  if (typeof emojiData.has_skin_tone !== 'boolean') return false
  
  // 如果支援膚色，檢查 skin_variations
  if (emojiData.has_skin_tone) {
    if (!emojiData.skin_variations || typeof emojiData.skin_variations !== 'object') {
      return false
    }
    
    // 檢查至少有一個有效的膚色變體
    const variations = emojiData.skin_variations
    const hasValidVariation = Object.keys(variations).some(key => {
      const tone = parseInt(key, 10)
      return tone >= 1 && tone <= 5 && typeof variations[key] === 'string'
    })
    
    if (!hasValidVariation) return false
  }
  
  return true
}