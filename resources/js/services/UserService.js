import axios from 'axios';
import { User } from '../models/User.js';

/**
 * UserService 類別
 * 
 * 提供使用者相關的 API 操作，統一處理錯誤並回傳 User 模型實例
 */
export class UserService {
  /**
   * 建構 UserService 實例
   */
  constructor() {
    this.apiBaseUrl = '/api';
  }
  /**
   * 取得當前登入使用者資料
   * 
   * @returns {Promise<User>} User 實例
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchCurrentUser() {
    try {
      const response = await axios.get(`${this.apiBaseUrl}/me`);
      return new User(response.data.user);
    } catch (error) {
      throw this._handleError('Failed to fetch current user', error);
    }
  }

  /**
   * 根據 ID 取得使用者資料
   * 
   * @param {number} userId - 使用者 ID
   * @returns {Promise<User>} User 實例
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async fetchUserById(userId) {
    try {
      const response = await axios.get(`${this.apiBaseUrl}/users/${userId}`);
      return new User(response.data.user);
    } catch (error) {
      throw this._handleError('Failed to fetch user', error);
    }
  }

  /**
   * 更新使用者資料
   * 
   * @param {number} userId - 使用者 ID
   * @param {Object} updateData - 要更新的資料
   * @returns {Promise<User>} 更新後的 User 實例
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async updateUser(userId, updateData) {
    try {
      const response = await axios.put(`${this.apiBaseUrl}/users/${userId}`, updateData);
      return new User(response.data.user);
    } catch (error) {
      throw this._handleError('Failed to update user', error);
    }
  }

  /**
   * 更新使用者頭像
   * 
   * @param {number} userId - 使用者 ID
   * @param {Object} avatarData - 頭像資料
   * @returns {Promise<User>} 更新後的 User 實例
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async updateAvatar(userId, avatarData) {
    try {
      // 處理圖片上傳
      if (avatarData.type === 'image' && avatarData.file) {
        const formData = new FormData();
        formData.append('avatar', avatarData.file);
        
        const response = await axios.post(
          `${this.apiBaseUrl}/users/${userId}/avatar`,
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );
        return new User(response.data.user);
      }

      // 處理其他類型頭像資料（文字、emoji 等）
      const response = await axios.patch(`${this.apiBaseUrl}/users/${userId}/avatar`, {
        avatar_data: avatarData
      });
      return new User(response.data.user);
    } catch (error) {
      throw this._handleError('Failed to update avatar', error);
    }
  }

  /**
   * 移除使用者頭像
   * 
   * @param {number} userId - 使用者 ID
   * @returns {Promise<User>} 更新後的 User 實例
   * @throws {Error} API 請求失敗時拋出錯誤
   */
  async removeAvatar(userId) {
    try {
      const response = await axios.delete(`${this.apiBaseUrl}/users/${userId}/avatar`);
      return new User(response.data.user);
    } catch (error) {
      throw this._handleError('Failed to remove avatar', error);
    }
  }

  /**
   * 統一處理 API 錯誤
   * 
   * @private
   * @param {string} message - 錯誤訊息
   * @param {Error} originalError - 原始錯誤
   * @returns {Error} 格式化的錯誤
   */
  _handleError(message, originalError) {
    const error = new Error(message);
    error.originalError = originalError;
    
    if (originalError.response) {
      error.status = originalError.response.status;
      error.data = originalError.response.data;
    }
    
    return error;
  }
}