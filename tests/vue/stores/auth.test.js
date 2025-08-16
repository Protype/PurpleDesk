import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '../../../resources/js/stores/auth.js';
import { User } from '../../../resources/js/models/User.js';

// Mock axios
vi.mock('axios', () => ({
  default: {
    post: vi.fn(),
    get: vi.fn(),
    defaults: {
      headers: {
        common: {}
      }
    }
  }
}));

// Mock localStorage
const localStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn()
};
Object.defineProperty(window, 'localStorage', {
  value: localStorageMock
});

describe('AuthStore', () => {
  let authStore;

  beforeEach(() => {
    // 建立新的 Pinia 實例
    setActivePinia(createPinia());
    authStore = useAuthStore();
    
    // 清除所有 mock
    vi.clearAllMocks();
    
    // 重置 axios headers
    axios.defaults.headers.common = {};
  });

  afterEach(() => {
    // 清理狀態
    authStore.$reset();
  });

  describe('初始狀態', () => {
    it('應該有正確的初始狀態', () => {
      expect(authStore.user).toBeNull();
      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
      expect(authStore.isInitialized).toBe(false);
    });

    it('getters 應該正確計算狀態', () => {
      expect(authStore.getUser).toBeNull();
      expect(authStore.isLoggedIn).toBe(false);
      
      // 設定 token 後應該回傳 true
      authStore.token = 'test-token';
      expect(authStore.isLoggedIn).toBe(true);
    });
  });

  describe('登入功能', () => {
    const mockUser = { id: 1, account: 'testuser', email: 'test@example.com' };
    const mockToken = 'test-auth-token';

    it('成功登入應該更新狀態並儲存 token', async () => {
      const mockResponse = {
        data: {
          user: mockUser,
          token: mockToken
        }
      };
      
      axios.post.mockResolvedValue(mockResponse);

      const result = await authStore.login('testuser', 'password');

      expect(axios.post).toHaveBeenCalledWith('/api/login', {
        login: 'testuser',
        password: 'password'
      });

      expect(authStore.user).toBeInstanceOf(User);
      expect(authStore.user.id).toBe(mockUser.id);
      expect(authStore.user.account).toBe(mockUser.account);
      expect(authStore.token).toBe(mockToken);
      expect(authStore.isAuthenticated).toBe(true);
      
      expect(localStorageMock.setItem).toHaveBeenCalledWith('auth_token', mockToken);
      expect(axios.defaults.headers.common['Authorization']).toBe(`Bearer ${mockToken}`);
      
      expect(result).toEqual(mockResponse.data);
    });

    it('登入失敗應該清除認證資訊', async () => {
      const mockError = new Error('Login failed');
      axios.post.mockRejectedValue(mockError);

      await expect(authStore.login('wrong', 'credentials')).rejects.toThrow('Login failed');

      expect(authStore.user).toBeNull();
      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
      
      expect(localStorageMock.removeItem).toHaveBeenCalledWith('auth_token');
      expect(axios.defaults.headers.common['Authorization']).toBeUndefined();
    });
  });

  describe('註冊功能', () => {
    const mockUser = { id: 1, account: 'newuser', email: 'new@example.com' };
    const mockToken = 'new-auth-token';

    it('成功註冊應該更新狀態並儲存 token', async () => {
      const mockResponse = {
        data: {
          user: mockUser,
          token: mockToken
        }
      };
      
      axios.post.mockResolvedValue(mockResponse);

      const userData = {
        account: 'newuser',
        email: 'new@example.com',
        password: 'password',
        password_confirmation: 'password'
      };

      const result = await authStore.register(userData);

      expect(axios.post).toHaveBeenCalledWith('/api/register', userData);

      expect(authStore.user).toBeInstanceOf(User);
      expect(authStore.user.id).toBe(mockUser.id);
      expect(authStore.user.account).toBe(mockUser.account);
      expect(authStore.token).toBe(mockToken);
      expect(authStore.isAuthenticated).toBe(true);
      
      expect(localStorageMock.setItem).toHaveBeenCalledWith('auth_token', mockToken);
      expect(axios.defaults.headers.common['Authorization']).toBe(`Bearer ${mockToken}`);
      
      expect(result).toEqual(mockResponse.data);
    });

    it('註冊失敗應該清除認證資訊', async () => {
      const mockError = new Error('Registration failed');
      axios.post.mockRejectedValue(mockError);

      const userData = {
        account: 'existinguser',
        email: 'existing@example.com',
        password: 'password'
      };

      await expect(authStore.register(userData)).rejects.toThrow('Registration failed');

      expect(authStore.user).toBeNull();
      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
    });
  });

  describe('登出功能', () => {
    beforeEach(() => {
      // 設定已登入狀態
      authStore.user = { id: 1, account: 'testuser' };
      authStore.token = 'test-token';
      authStore.isAuthenticated = true;
      axios.defaults.headers.common['Authorization'] = 'Bearer test-token';
    });

    it('成功登出應該清除所有認證資訊', async () => {
      axios.post.mockResolvedValue({});

      await authStore.logout();

      expect(axios.post).toHaveBeenCalledWith('/api/logout');
      
      expect(authStore.user).toBeNull();
      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
      
      expect(localStorageMock.removeItem).toHaveBeenCalledWith('auth_token');
      expect(axios.defaults.headers.common['Authorization']).toBeUndefined();
    });

    it('API 登出失敗仍應該清除本地認證資訊', async () => {
      const consoleErrorSpy = vi.spyOn(console, 'error').mockImplementation(() => {});
      axios.post.mockRejectedValue(new Error('Logout API failed'));

      await authStore.logout();

      expect(consoleErrorSpy).toHaveBeenCalledWith('Logout error:', expect.any(Error));
      
      // 即使 API 失敗，本地狀態仍應該清除
      expect(authStore.user).toBeNull();
      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
      
      consoleErrorSpy.mockRestore();
    });

    it('沒有 token 時也能正常登出', async () => {
      authStore.token = null;

      await authStore.logout();

      // 不應該呼叫 logout API
      expect(axios.post).not.toHaveBeenCalled();
      
      // 但仍應該清除狀態
      expect(authStore.user).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
    });
  });

  describe('取得使用者資訊', () => {
    const mockUser = { id: 1, account: 'testuser', email: 'test@example.com' };

    beforeEach(() => {
      authStore.token = 'test-token';
    });

    it('成功取得使用者資訊應該更新狀態', async () => {
      const mockResponse = {
        data: {
          user: mockUser
        }
      };
      
      axios.get.mockResolvedValue(mockResponse);

      const result = await authStore.fetchUser();

      expect(axios.get).toHaveBeenCalledWith('/api/me');
      expect(authStore.user).toBeInstanceOf(User);
      expect(authStore.user.id).toBe(mockUser.id);
      expect(authStore.user.account).toBe(mockUser.account);
      expect(authStore.isAuthenticated).toBe(true);
      expect(result).toBeInstanceOf(User);
    });

    it('沒有 token 時應該拋出錯誤', async () => {
      authStore.token = null;

      await expect(authStore.fetchUser()).rejects.toThrow('No token available');
      
      expect(axios.get).not.toHaveBeenCalled();
      expect(authStore.user).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
    });

    it('API 失敗應該清除認證資訊', async () => {
      axios.get.mockRejectedValue(new Error('API failed'));

      await expect(authStore.fetchUser()).rejects.toThrow('Failed to fetch current user');

      expect(authStore.user).toBeNull();
      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
      
      expect(localStorageMock.removeItem).toHaveBeenCalledWith('auth_token');
    });
  });

  describe('清除認證資訊', () => {
    beforeEach(() => {
      // 設定已登入狀態
      authStore.user = { id: 1, account: 'testuser' };
      authStore.token = 'test-token';
      authStore.isAuthenticated = true;
      axios.defaults.headers.common['Authorization'] = 'Bearer test-token';
    });

    it('應該清除所有認證相關狀態', () => {
      authStore.clearAuth();

      expect(authStore.user).toBeNull();
      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
      
      expect(localStorageMock.removeItem).toHaveBeenCalledWith('auth_token');
      expect(axios.defaults.headers.common['Authorization']).toBeUndefined();
    });
  });

  describe('認證初始化', () => {
    const mockUser = { id: 1, account: 'testuser', email: 'test@example.com' };
    const mockToken = 'stored-token';

    it('有有效 token 時應該初始化認證狀態', async () => {
      localStorageMock.getItem.mockReturnValue(mockToken);
      axios.get.mockResolvedValue({
        data: { user: mockUser }
      });

      await authStore.initializeAuth();

      expect(localStorageMock.getItem).toHaveBeenCalledWith('auth_token');
      expect(authStore.token).toBe(mockToken);
      expect(axios.defaults.headers.common['Authorization']).toBe(`Bearer ${mockToken}`);
      
      expect(axios.get).toHaveBeenCalledWith('/api/me');
      expect(authStore.user).toBeInstanceOf(User);
      expect(authStore.user.id).toBe(mockUser.id);
      expect(authStore.user.account).toBe(mockUser.account);
      expect(authStore.isAuthenticated).toBe(true);
      expect(authStore.isInitialized).toBe(true);
    });

    it('沒有 token 時應該跳過初始化', async () => {
      localStorageMock.getItem.mockReturnValue(null);

      await authStore.initializeAuth();

      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
      expect(authStore.isInitialized).toBe(true);
      
      expect(axios.get).not.toHaveBeenCalled();
    });

    it('token 無效時應該清除認證資訊', async () => {
      const consoleWarnSpy = vi.spyOn(console, 'warn').mockImplementation(() => {});
      
      localStorageMock.getItem.mockReturnValue(mockToken);
      axios.get.mockRejectedValue(new Error('Token expired'));

      await authStore.initializeAuth();

      expect(consoleWarnSpy).toHaveBeenCalledWith('Failed to initialize auth:', 'Failed to fetch current user');
      
      expect(authStore.user).toBeNull();
      expect(authStore.token).toBeNull();
      expect(authStore.isAuthenticated).toBe(false);
      expect(authStore.isInitialized).toBe(true);
      
      expect(localStorageMock.removeItem).toHaveBeenCalledWith('auth_token');
      
      consoleWarnSpy.mockRestore();
    });
  });
});