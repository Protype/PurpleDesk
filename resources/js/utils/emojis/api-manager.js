/**
 * Emoji API Manager
 * 從後端 API 載入所有 emoji 資料並在前端處理搜尋等邏輯
 */

import axios from 'axios';

// 膚色修飾符的 Unicode 範圍
const SKIN_TONE_REGEX = /[\u{1F3FB}-\u{1F3FF}]/gu;

class EmojiApiManager {
  constructor() {
    this.initialized = false;
    this.allData = null;
    this.categoriesInfo = null;
    this.categoryCache = new Map();
    this.searchIndex = null;
    this.loadingPromise = null;
  }

  // 初始化並載入所有 emoji 資料
  async initialize() {
    if (this.initialized) {
      return this.allData;
    }

    if (this.loadingPromise) {
      return this.loadingPromise;
    }

    this.loadingPromise = axios.get('/api/config/icon/emoji')
      .then(response => {
        // 驗證 API 回應資料結構
        if (!response.data) {
          throw new Error('No data received from emoji API');
        }
        
        // 新的扁平化 API 格式：{data: {categoryId: [emoji...]}}
        if (!response.data.data || typeof response.data.data !== 'object') {
          throw new Error('Invalid emoji data structure: data missing or invalid');
        }
        
        this.allData = response.data;
        this.categoriesInfo = [];
        
        // 分類名稱對應表
        const categoryNameMap = {
          'smileys_emotion': '表情符號與人物',
          'people_body': '人物與身體', 
          'animals_nature': '動物與自然',
          'food_drink': '食物與飲料',
          'travel_places': '旅遊與地點',
          'activities': '活動',
          'objects': '物品',
          'symbols': '符號',
          'flags': '旗幟'
        };
        
        // 處理新的扁平化分類資料
        Object.entries(this.allData.data || {}).forEach(([categoryId, emojis]) => {
          // 驗證分類資料結構
          if (!Array.isArray(emojis)) {
            console.warn(`Invalid category data for ${categoryId}, expected array, skipping`);
            return;
          }
          
          const categoryName = categoryNameMap[categoryId] || categoryId.replace(/_/g, ' ');
          
          this.categoriesInfo.push({
            id: categoryId,
            name: categoryName,
            icon: emojis[0]?.emoji || '❓', // 使用第一個 emoji 作為圖標
            priority: 'normal'
          });
          
          // 建立分類快取 - 新的扁平化格式直接存儲陣列
          const categoryEmojis = {
            main: emojis.map(emoji => ({
              ...emoji,
              category: categoryName,
              categoryId: categoryId,
              subgroup: categoryName
            }))
          };
          
          this.categoryCache.set(categoryId, categoryEmojis);
        });
        
        // 建立搜尋索引
        this.buildSearchIndex();
        
        this.initialized = true;
        this.loadingPromise = null;
        return this.allData;
      })
      .catch(error => {
        console.error('Failed to load emojis:', error);
        this.loadingPromise = null;
        throw error;
      });

    return this.loadingPromise;
  }

  // 建立搜尋索引
  buildSearchIndex() {
    this.searchIndex = new Map();
    
    this.categoryCache.forEach((categoryData, categoryId) => {
      // 找到分類資訊
      const categoryInfo = this.categoriesInfo.find(cat => cat.id === categoryId);
      
      Object.entries(categoryData).forEach(([subgroupKey, emojis]) => {
        emojis.forEach(emoji => {
          // 只索引基礎 emoji
          if (!SKIN_TONE_REGEX.test(emoji.emoji)) {
            const searchKey = `${emoji.emoji} ${emoji.name} ${categoryInfo?.name || ''} ${emoji.subgroup || ''}`.toLowerCase();
            this.searchIndex.set(emoji.emoji, {
              ...emoji,
              searchKey
            });
          }
        });
      });
    });
  }

  // 取得載入狀態
  getEmojiLoadingStatus() {
    if (!this.initialized) {
      return {
        total: 0,
        loaded: 0,
        loading: this.loadingPromise ? 1 : 0,
        progress: 0,
        categories: []
      };
    }

    const total = this.categoriesInfo.length;
    
    return {
      total,
      loaded: total,
      loading: 0,
      progress: 100,
      categories: this.categoriesInfo.map(c => c.id)
    };
  }

  // 預載入（實際上是載入全部）
  async preloadPopularEmojiCategories() {
    return this.initialize();
  }

  // 載入所有（已經在初始化時載入）
  async loadEmojisByPriority() {
    return this.initialize();
  }

  // 載入特定分類（從快取返回）
  async loadCategory(categoryId) {
    await this.initialize();
    return this.categoryCache.get(categoryId) || {};
  }

  // 搜尋 emoji
  async searchEmojis(query) {
    if (!query || query.trim().length === 0) {
      return [];
    }

    await this.initialize();
    
    const searchTerm = query.toLowerCase().trim();
    const results = [];

    if (this.searchIndex) {
      for (const [emoji, data] of this.searchIndex.entries()) {
        if (data.searchKey.includes(searchTerm)) {
          results.push({
            emoji: data.emoji,
            name: data.name,
            category: data.category,
            categoryId: data.categoryId,
            subgroup: data.subgroup
          });
          
          if (results.length >= 50) {
            break;
          }
        }
      }
    }

    return results;
  }

  // 根據分類取得 emoji
  async getEmojisByCategory(categoryId) {
    await this.initialize();
    
    const categoryData = this.categoryCache.get(categoryId);
    if (!categoryData) {
      return [];
    }
    
    const emojis = [];
    Object.values(categoryData).forEach(subgroupEmojis => {
      subgroupEmojis.forEach(emoji => {
        // 過濾膚色變體
        if (!SKIN_TONE_REGEX.test(emoji.emoji)) {
          emojis.push(emoji);
        }
      });
    });
    
    return emojis;
  }

  // 取得分類資訊
  async getCategoriesInfo() {
    await this.initialize();
    return this.categoriesInfo;
  }

  // 取得記憶體統計
  getMemoryStats() {
    if (!this.initialized) {
      return {
        loadedCategories: 0,
        totalEmojis: 0,
        searchIndexSize: 0,
        estimatedMemoryKB: 0
      };
    }
    
    const searchIndexSize = this.searchIndex ? this.searchIndex.size : 0;
    
    // 計算總 emoji 數量
    let totalEmojis = 0;
    this.categoryCache.forEach((categoryData) => {
      Object.values(categoryData).forEach((emojis) => {
        totalEmojis += Array.isArray(emojis) ? emojis.length : 0;
      });
    });
    
    return {
      loadedCategories: this.categoriesInfo.length,
      totalEmojis: totalEmojis,
      searchIndexSize,
      estimatedMemoryKB: Math.round((totalEmojis * 150 + searchIndexSize * 50) / 1024)
    };
  }

  // 清除快取（實際上重新載入）
  clearCache() {
    this.initialized = false;
    this.allData = null;
    this.categoriesInfo = null;
    this.categoryCache.clear();
    this.searchIndex = null;
    this.loadingPromise = null;
  }
}

// 建立單例
const emojiApiManager = new EmojiApiManager();

// 匯出相容的 API
export const EMOJI_CATEGORIES = new Proxy({}, {
  get(target, prop) {
    return () => emojiApiManager.loadCategory(prop);
  }
});

// 匯出分類資訊
export const EMOJI_CATEGORY_INFO = new Proxy({}, {
  get(target, prop) {
    if (!emojiApiManager.initialized || !emojiApiManager.categoriesInfo) {
      return undefined;
    }
    const category = emojiApiManager.categoriesInfo.find(cat => cat.id === prop);
    return category ? {
      id: category.id,
      name: category.name,
      icon: category.icon,
      priority: category.priority
    } : undefined;
  }
});

export default emojiApiManager;