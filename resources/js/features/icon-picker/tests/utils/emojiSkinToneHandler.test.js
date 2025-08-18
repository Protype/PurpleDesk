/**
 * EmojiSkinToneHandler 測試
 * 測試膚色處理邏輯
 */

import { describe, it, expect } from 'vitest'
import { canApplySkinTone, applySkinTone } from '../../utils/emojiSkinToneHandler.js'

describe('emojiSkinToneHandler', () => {
  describe('canApplySkinTone', () => {
    it('should return true for emoji with has_skin_tone flag', () => {
      const emojiData = {
        emoji: '👋',
        name: 'waving hand',
        has_skin_tone: true,
        skin_variations: {
          1: '👋🏻',
          2: '👋🏼',
          3: '👋🏽',
          4: '👋🏾',
          5: '👋🏿'
        }
      }

      expect(canApplySkinTone(emojiData)).toBe(true)
    })

    it('should return false for emoji without has_skin_tone flag', () => {
      const emojiData = {
        emoji: '😀',
        name: 'grinning face',
        has_skin_tone: false
      }

      expect(canApplySkinTone(emojiData)).toBe(false)
    })

    it('should return false for emoji with has_skin_tone set to false', () => {
      const emojiData = {
        emoji: '🎉',
        name: 'party popper',
        has_skin_tone: false
      }

      expect(canApplySkinTone(emojiData)).toBe(false)
    })

    it('should return false for emoji without has_skin_tone property', () => {
      const emojiData = {
        emoji: '🚀',
        name: 'rocket'
      }

      expect(canApplySkinTone(emojiData)).toBe(false)
    })
  })

  describe('applySkinTone', () => {
    const mockEmojiData = {
      emoji: '👋',
      name: 'waving hand',
      has_skin_tone: true,
      skin_variations: {
        1: '👋🏻',
        2: '👋🏼',
        3: '👋🏽',
        4: '👋🏾',
        5: '👋🏿'
      }
    }

    it('should return base emoji when no skin tone selected', () => {
      expect(applySkinTone(mockEmojiData, null)).toBe('👋')
      expect(applySkinTone(mockEmojiData, undefined)).toBe('👋')
      expect(applySkinTone(mockEmojiData, '')).toBe('👋')
      expect(applySkinTone(mockEmojiData, 0)).toBe('👋')
    })

    it('should return correct skin tone variation', () => {
      expect(applySkinTone(mockEmojiData, 1)).toBe('👋🏻')
      expect(applySkinTone(mockEmojiData, 2)).toBe('👋🏼')
      expect(applySkinTone(mockEmojiData, 3)).toBe('👋🏽')
      expect(applySkinTone(mockEmojiData, 4)).toBe('👋🏾')
      expect(applySkinTone(mockEmojiData, 5)).toBe('👋🏿')
    })

    it('should return base emoji when skin tone not available in variations', () => {
      expect(applySkinTone(mockEmojiData, 6)).toBe('👋')
      expect(applySkinTone(mockEmojiData, -1)).toBe('👋')
    })

    it('should return base emoji for non-skin-tone-capable emoji', () => {
      const nonSkinToneEmoji = {
        emoji: '😀',
        name: 'grinning face',
        has_skin_tone: false
      }

      expect(applySkinTone(nonSkinToneEmoji, 1)).toBe('😀')
      expect(applySkinTone(nonSkinToneEmoji, 3)).toBe('😀')
    })

    it('should handle missing skin_variations gracefully', () => {
      const incompleteSkinToneEmoji = {
        emoji: '👋',
        name: 'waving hand',
        has_skin_tone: true
        // 缺少 skin_variations
      }

      expect(applySkinTone(incompleteSkinToneEmoji, 1)).toBe('👋')
      expect(applySkinTone(incompleteSkinToneEmoji, 3)).toBe('👋')
    })

    it('should handle partial skin_variations', () => {
      const partialSkinToneEmoji = {
        emoji: '👋',
        name: 'waving hand',
        has_skin_tone: true,
        skin_variations: {
          1: '👋🏻',
          3: '👋🏽'
          // 缺少 2, 4, 5
        }
      }

      expect(applySkinTone(partialSkinToneEmoji, 1)).toBe('👋🏻')
      expect(applySkinTone(partialSkinToneEmoji, 2)).toBe('👋') // fallback
      expect(applySkinTone(partialSkinToneEmoji, 3)).toBe('👋🏽')
      expect(applySkinTone(partialSkinToneEmoji, 4)).toBe('👋') // fallback
    })

    it('should handle string skin tone values', () => {
      expect(applySkinTone(mockEmojiData, '1')).toBe('👋🏻')
      expect(applySkinTone(mockEmojiData, '3')).toBe('👋🏽')
      expect(applySkinTone(mockEmojiData, '5')).toBe('👋🏿')
    })
  })

  describe('edge cases', () => {
    it('should handle null or undefined emoji data', () => {
      expect(canApplySkinTone(null)).toBe(false)
      expect(canApplySkinTone(undefined)).toBe(false)
      
      expect(applySkinTone(null, 1)).toBe('')
      expect(applySkinTone(undefined, 1)).toBe('')
    })

    it('should handle empty emoji data', () => {
      const emptyEmoji = {}
      
      expect(canApplySkinTone(emptyEmoji)).toBe(false)
      expect(applySkinTone(emptyEmoji, 1)).toBe('')
    })

    it('should handle emoji data with invalid skin_variations', () => {
      const invalidVariationsEmoji = {
        emoji: '👋',
        name: 'waving hand',
        has_skin_tone: true,
        skin_variations: null
      }

      expect(applySkinTone(invalidVariationsEmoji, 1)).toBe('👋')
    })
  })
})