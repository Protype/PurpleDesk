import axios from 'axios';

/**
 * IconService 類別
 * 
 * 提供圖標相關的 API 操作，統一處理錯誤並提供快取機制
 */
export class IconService {
  /**
   * 建構 IconService 實例
   */
  constructor() {
    this.apiBaseUrl = '/api/config/icon';
    this.cache = new Map();
  }

  /**
   * 取得 emoji 資料
   * 
   * @returns {Promise<Object>} emoji 資料物件
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchEmojis() {
    const cacheKey = 'emojis';
    
    // 檢查快取
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey);
    }

    try {
      const response = await axios.get(`${this.apiBaseUrl}/emoji`);
      
      // 驗證回應資料
      this._validateResponseData(response.data);
      
      // 儲存到快取
      this.cache.set(cacheKey, response.data);
      
      return response.data;
    } catch (error) {
      throw this._handleError('Failed to fetch emojis', error);
    }
  }

  /**
   * 取得 hero icons 資料
   * 
   * @returns {Promise<Object>} hero icons 資料物件
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchHeroIcons() {
    const cacheKey = 'heroicons';
    
    // 檢查快取
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey);
    }

    try {
      const response = await axios.get(`${this.apiBaseUrl}/heroicons`);
      
      // 驗證回應資料
      this._validateResponseData(response.data);
      
      // 儲存到快取
      this.cache.set(cacheKey, response.data);
      
      return response.data;
    } catch (error) {
      throw this._handleError('Failed to fetch hero icons', error);
    }
  }

  /**
   * 取得 bootstrap icons 資料
   * 
   * @returns {Promise<Object>} bootstrap icons 資料物件
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchBootstrapIcons() {
    const cacheKey = 'bootstrap_icons';
    
    // 檢查快取
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey);
    }

    try {
      const response = await axios.get(`${this.apiBaseUrl}/bootstrap-icons`);
      
      // 驗證回應資料
      this._validateResponseData(response.data);
      
      // 儲存到快取
      this.cache.set(cacheKey, response.data);
      
      return response.data;
    } catch (error) {
      throw this._handleError('Failed to fetch bootstrap icons', error);
    }
  }

  /**
   * 取得 hero icons 變體資訊
   * 
   * @returns {Promise<Object>} hero icons 變體資訊
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchHeroIconVariants() {
    const cacheKey = 'heroicons_variants';
    
    // 檢查快取
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey);
    }

    try {
      const response = await axios.get(`${this.apiBaseUrl}/heroicons/variants`);
      
      // 驗證回應資料
      this._validateResponseData(response.data);
      
      // 儲存到快取
      this.cache.set(cacheKey, response.data);
      
      return response.data;
    } catch (error) {
      throw this._handleError('Failed to fetch hero icons variants', error);
    }
  }

  /**
   * 取得 bootstrap icons 變體資訊
   * 
   * @returns {Promise<Object>} bootstrap icons 變體資訊
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchBootstrapIconVariants() {
    const cacheKey = 'bootstrap_icons_variants';
    
    // 檢查快取
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey);
    }

    try {
      const response = await axios.get(`${this.apiBaseUrl}/bootstrap-icons/variants`);
      
      // 驗證回應資料
      this._validateResponseData(response.data);
      
      // 儲存到快取
      this.cache.set(cacheKey, response.data);
      
      return response.data;
    } catch (error) {
      throw this._handleError('Failed to fetch bootstrap icons variants', error);
    }
  }

  /**
   * 取得特定樣式的 hero icons
   * 
   * @param {string} style - 圖標樣式 ('outline' | 'solid')
   * @returns {Promise<Object>} 特定樣式的 hero icons 資料
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchHeroIconsByStyle(style) {
    const cacheKey = `heroicons_style_${style}`;
    
    // 檢查快取
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey);
    }

    try {
      const response = await axios.get(`${this.apiBaseUrl}/heroicons/style/${style}`);
      
      // 驗證回應資料
      this._validateResponseData(response.data);
      
      // 儲存到快取
      this.cache.set(cacheKey, response.data);
      
      return response.data;
    } catch (error) {
      throw this._handleError(`Failed to fetch hero icons with style ${style}`, error);
    }
  }

  /**
   * 取得特定樣式的 bootstrap icons
   * 
   * @param {string} style - 圖標樣式 ('outline' | 'solid')
   * @returns {Promise<Object>} 特定樣式的 bootstrap icons 資料
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchBootstrapIconsByStyle(style) {
    const cacheKey = `bootstrap_icons_style_${style}`;
    
    // 檢查快取
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey);
    }

    try {
      const response = await axios.get(`${this.apiBaseUrl}/bootstrap-icons/style/${style}`);
      
      // 驗證回應資料
      this._validateResponseData(response.data);
      
      // 儲存到快取
      this.cache.set(cacheKey, response.data);
      
      return response.data;
    } catch (error) {
      throw this._handleError(`Failed to fetch bootstrap icons with style ${style}`, error);
    }
  }

  /**
   * 清除快取
   * 
   * @param {string} [type] - 要清除的快取類型，如果不指定則清除所有快取
   */
  clearCache(type = null) {
    if (type) {
      // 清除特定類型的快取
      const cacheKeyMap = {
        'emojis': 'emojis',
        'heroicons': 'heroicons',
        'bootstrap_icons': 'bootstrap_icons',
        'heroicons_variants': 'heroicons_variants',
        'bootstrap_icons_variants': 'bootstrap_icons_variants'
      };
      
      const cacheKey = cacheKeyMap[type];
      if (cacheKey && this.cache.has(cacheKey)) {
        this.cache.delete(cacheKey);
      }
      
      // 清除樣式相關的快取
      if (type === 'heroicons' || type === 'bootstrap_icons') {
        const stylesToClear = ['outline', 'solid'];
        stylesToClear.forEach(style => {
          const styleKey = `${type}_style_${style}`;
          if (this.cache.has(styleKey)) {
            this.cache.delete(styleKey);
          }
        });
      }
    } else {
      // 清除所有快取
      this.cache.clear();
    }
  }

  /**
   * 驗證回應資料格式
   * 
   * @private
   * @param {any} data - 要驗證的資料
   * @throws {Error} 資料格式無效時拋出錯誤
   */
  _validateResponseData(data) {
    if (data === null || data === undefined) {
      throw new Error('Invalid response data received');
    }
    
    // 檢查是否為物件且非字串
    if (typeof data !== 'object' || typeof data === 'string') {
      throw new Error('Invalid response data received');
    }
  }

  /**
   * 統一處理 API 錯誤
   * 
   * @private
   * @param {string} message - 錯誤訊息前綴
   * @param {Error} originalError - 原始錯誤
   * @returns {Error} 格式化的錯誤
   */
  _handleError(message, originalError) {
    const error = new Error(`${message}: ${originalError.message}`);
    error.originalError = originalError;
    
    if (originalError.response) {
      error.status = originalError.response.status;
      error.statusText = originalError.response.statusText;
      error.data = originalError.response.data;
    }
    
    return error;
  }
}