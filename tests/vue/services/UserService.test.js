import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import axios from 'axios';
import { UserService } from '../../../resources/js/services/UserService.js';
import { User } from '../../../resources/js/models/User.js';

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    patch: vi.fn(),
    delete: vi.fn()
  }
}));

describe('UserService', () => {
  let userService;

  beforeEach(() => {
    userService = new UserService();
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.resetAllMocks();
  });

  describe('fetchCurrentUser 方法', () => {
    it('應該從 API 取得當前使用者並回傳 User 實例', async () => {
      const mockUserData = {
        id: 1,
        account: 'test_user',
        full_name: '測試使用者',
        display_name: '測試',
        email: 'test@example.com',
        is_admin: false
      };

      axios.get.mockResolvedValue({
        data: { user: mockUserData }
      });

      const user = await userService.fetchCurrentUser();

      expect(axios.get).toHaveBeenCalledWith('/api/me');
      expect(user).toBeInstanceOf(User);
      expect(user.id).toBe(1);
      expect(user.fullName).toBe('測試使用者');
      expect(user.email).toBe('test@example.com');
    });

    it('應該處理 API 錯誤', async () => {
      const errorResponse = {
        response: {
          status: 401,
          data: { message: 'Unauthorized' }
        }
      };

      axios.get.mockRejectedValue(errorResponse);

      await expect(userService.fetchCurrentUser()).rejects.toThrow('Failed to fetch current user');
    });

    it('應該處理網路錯誤', async () => {
      axios.get.mockRejectedValue(new Error('Network Error'));

      await expect(userService.fetchCurrentUser()).rejects.toThrow('Failed to fetch current user');
    });
  });

  describe('updateUser 方法', () => {
    it('應該更新使用者資料並回傳 User 實例', async () => {
      const userId = 1;
      const updateData = {
        display_name: '新的顯示名稱',
        email: 'newemail@example.com'
      };

      const mockUpdatedUser = {
        id: 1,
        account: 'test_user',
        full_name: '測試使用者',
        display_name: '新的顯示名稱',
        email: 'newemail@example.com',
        is_admin: false
      };

      axios.put.mockResolvedValue({
        data: { user: mockUpdatedUser }
      });

      const user = await userService.updateUser(userId, updateData);

      expect(axios.put).toHaveBeenCalledWith(`/api/users/${userId}`, updateData);
      expect(user).toBeInstanceOf(User);
      expect(user.displayName).toBe('新的顯示名稱');
      expect(user.email).toBe('newemail@example.com');
    });

    it('應該處理更新錯誤', async () => {
      const userId = 1;
      const updateData = { email: 'invalid-email' };

      axios.put.mockRejectedValue({
        response: {
          status: 422,
          data: { 
            message: 'Validation Error',
            errors: { email: ['Email format is invalid'] }
          }
        }
      });

      await expect(userService.updateUser(userId, updateData)).rejects.toThrow('Failed to update user');
    });
  });

  describe('updateAvatar 方法', () => {
    it('應該上傳頭像並回傳更新的 User 實例', async () => {
      const userId = 1;
      const avatarData = {
        type: 'image',
        file: new File([''], 'avatar.jpg', { type: 'image/jpeg' })
      };

      const mockUpdatedUser = {
        id: 1,
        account: 'test_user',
        full_name: '測試使用者',
        avatar_url: '/uploads/avatars/avatar_123.jpg'
      };

      axios.post.mockResolvedValue({
        data: { user: mockUpdatedUser }
      });

      const user = await userService.updateAvatar(userId, avatarData);

      expect(axios.post).toHaveBeenCalledWith(
        `/api/users/${userId}/avatar`,
        expect.any(FormData),
        expect.objectContaining({
          headers: expect.objectContaining({
            'Content-Type': 'multipart/form-data'
          })
        })
      );
      expect(user).toBeInstanceOf(User);
      expect(user.avatarUrl).toBe('/uploads/avatars/avatar_123.jpg');
    });

    it('應該更新文字頭像資料', async () => {
      const userId = 1;
      const avatarData = {
        type: 'text',
        text: '測試',
        backgroundColor: '#6366f1',
        textColor: '#ffffff'
      };

      const mockUpdatedUser = {
        id: 1,
        account: 'test_user',
        full_name: '測試使用者',
        avatar_data: avatarData
      };

      axios.patch.mockResolvedValue({
        data: { user: mockUpdatedUser }
      });

      const user = await userService.updateAvatar(userId, avatarData);

      expect(axios.patch).toHaveBeenCalledWith(`/api/users/${userId}/avatar`, {
        avatar_data: avatarData
      });
      expect(user).toBeInstanceOf(User);
      expect(user.avatarData).toEqual(avatarData);
    });

    it('應該處理頭像上傳錯誤', async () => {
      const userId = 1;
      const avatarData = {
        type: 'image',
        file: new File([''], 'large-file.jpg', { type: 'image/jpeg' })
      };

      axios.post.mockRejectedValue({
        response: {
          status: 413,
          data: { message: 'File too large' }
        }
      });

      await expect(userService.updateAvatar(userId, avatarData)).rejects.toThrow('Failed to update avatar');
    });
  });

  describe('removeAvatar 方法', () => {
    it('應該移除使用者頭像', async () => {
      const userId = 1;

      const mockUpdatedUser = {
        id: 1,
        account: 'test_user',
        full_name: '測試使用者',
        avatar_url: null,
        avatar_data: null
      };

      axios.delete.mockResolvedValue({
        data: { user: mockUpdatedUser }
      });

      const user = await userService.removeAvatar(userId);

      expect(axios.delete).toHaveBeenCalledWith(`/api/users/${userId}/avatar`);
      expect(user).toBeInstanceOf(User);
      expect(user.avatarUrl).toBeNull();
      expect(user.avatarData).toBeNull();
    });

    it('應該處理移除頭像錯誤', async () => {
      const userId = 1;

      axios.delete.mockRejectedValue({
        response: {
          status: 404,
          data: { message: 'User not found' }
        }
      });

      await expect(userService.removeAvatar(userId)).rejects.toThrow('Failed to remove avatar');
    });
  });

  describe('fetchUserById 方法', () => {
    it('應該根據 ID 取得使用者資料', async () => {
      const userId = 1;
      const mockUserData = {
        id: 1,
        account: 'test_user',
        full_name: '測試使用者',
        email: 'test@example.com'
      };

      axios.get.mockResolvedValue({
        data: { user: mockUserData }
      });

      const user = await userService.fetchUserById(userId);

      expect(axios.get).toHaveBeenCalledWith(`/api/users/${userId}`);
      expect(user).toBeInstanceOf(User);
      expect(user.id).toBe(1);
      expect(user.fullName).toBe('測試使用者');
    });

    it('應該處理用戶不存在的情況', async () => {
      const userId = 999;

      axios.get.mockRejectedValue({
        response: {
          status: 404,
          data: { message: 'User not found' }
        }
      });

      await expect(userService.fetchUserById(userId)).rejects.toThrow('Failed to fetch user');
    });
  });

  describe('錯誤處理', () => {
    it('應該統一處理 API 錯誤格式', async () => {
      const errorResponse = {
        response: {
          status: 500,
          data: { message: 'Internal Server Error' }
        }
      };

      axios.get.mockRejectedValue(errorResponse);

      try {
        await userService.fetchCurrentUser();
      } catch (error) {
        expect(error.message).toBe('Failed to fetch current user');
        expect(error.originalError).toBeDefined();
        expect(error.status).toBe(500);
      }
    });

    it('應該處理沒有 response 的錯誤', async () => {
      const networkError = new Error('Network Error');
      
      axios.get.mockRejectedValue(networkError);

      try {
        await userService.fetchCurrentUser();
      } catch (error) {
        expect(error.message).toBe('Failed to fetch current user');
        expect(error.originalError).toBe(networkError);
      }
    });
  });
});