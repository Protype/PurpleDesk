/**
 * User Model 類別
 * 
 * 提供統一的使用者資料結構與操作方法
 */
export class User {
  /**
   * 建構 User 實例
   * 
   * @param {Object} data - 使用者資料物件
   */
  constructor(data = {}) {
    // 基本資料
    this.id = data.id;
    this.account = data.account;
    this.fullName = data.full_name;
    this.displayName = data.display_name;
    this.email = data.email;
    
    // 頭像資料
    this.avatarUrl = data.avatar_url;
    this._parseAvatarData(data.avatar_data);
    
    // 關聯資料
    this.organizations = data.organizations || [];
    this.teams = data.teams || [];
    
    // 使用者設定
    this._isAdmin = data.is_admin || false;
    this.locale = data.locale;
    this.timezone = data.timezone;
    this.emailNotifications = data.email_notifications;
    this.browserNotifications = data.browser_notifications;
    this.theme = data.theme;
    
    // 保存原始資料供 toJSON 使用
    this._originalData = data;
  }

  /**
   * 解析頭像資料
   * 
   * @private
   * @param {string|Object} avatarData - 頭像資料（JSON 字串或物件）
   */
  _parseAvatarData(avatarData) {
    if (!avatarData) {
      this.avatarData = null;
      return;
    }

    // 處理 JSON 字串
    if (typeof avatarData === 'string') {
      try {
        this.avatarData = JSON.parse(avatarData);
      } catch (error) {
        console.warn('Invalid avatar data JSON:', error);
        this.avatarData = null;
      }
      return;
    }

    // 直接使用物件
    this.avatarData = avatarData;
  }

  /**
   * 取得使用者名稱縮寫
   * 
   * @returns {string} 名稱縮寫
   */
  getInitials() {
    const name = this.displayName || this.fullName;
    if (!name) return '';

    // 檢查是否含有中文字符
    if (/[\u4e00-\u9fa5]/.test(name)) {
      return name.slice(0, 2);
    }

    // 英文名稱取首字母
    return name.split(' ')
      .map(n => n[0])
      .join('')
      .toUpperCase()
      .slice(0, 2);
  }

  /**
   * 取得顯示名稱
   * 優先順序：display_name → full_name → account
   * 
   * @returns {string} 顯示名稱
   */
  getDisplayName() {
    return this.displayName || this.fullName || this.account || '';
  }

  /**
   * 取得頭像資料
   * 如果沒有 avatar_data，會生成預設的文字頭像
   * 
   * @returns {Object} 頭像資料物件
   */
  getAvatarData() {
    // 如果已有 avatar_data，直接回傳
    if (this.avatarData) {
      return this.avatarData;
    }

    // 生成預設文字頭像
    const displayName = this.getDisplayName();
    let text = '';
    
    if (displayName) {
      // 檢查是否含有中文字符
      if (/[\u4e00-\u9fa5]/.test(displayName)) {
        text = displayName.slice(0, 2);
      } else {
        // 英文名稱處理
        const words = displayName.split(' ').filter(word => word.length > 0);
        if (words.length > 1) {
          // 多個單字取首字母
          text = words
            .map(n => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
        } else {
          // 單一單字取前兩個字母
          text = displayName.slice(0, 2).toUpperCase();
        }
      }
    }

    return {
      type: 'text',
      text: text,
      backgroundColor: '#6366f1', // indigo-500
      textColor: '#ffffff'
    };
  }

  /**
   * 檢查是否有頭像
   * 
   * @returns {boolean} 是否有頭像
   */
  hasAvatar() {
    if (this.avatarUrl) return true;
    if (this.avatarData && this.avatarData.type === 'image') return true;
    return false;
  }

  /**
   * 取得頭像 URL
   * 
   * @returns {string|null} 頭像 URL
   */
  getAvatarUrl() {
    if (this.avatarUrl) return this.avatarUrl;
    if (this.avatarData && this.avatarData.type === 'image') {
      return this.avatarData.path;
    }
    return null;
  }

  /**
   * 檢查是否為管理員
   * 
   * @returns {boolean} 是否為管理員
   */
  isAdmin() {
    return this._isAdmin === true;
  }

  /**
   * 轉換為 JSON 格式（API 相容格式）
   * 
   * @returns {Object} 原始資料格式
   */
  toJSON() {
    return this._originalData;
  }
}