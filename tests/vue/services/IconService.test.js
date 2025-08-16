import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import axios from 'axios';
import { IconService } from '../../../resources/js/services/IconService.js';

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn(),
    create: vi.fn(() => ({
      get: vi.fn()
    }))
  }
}));

describe('IconService', () => {
  let iconService;
  let mockAxios;

  beforeEach(() => {
    iconService = new IconService();
    mockAxios = axios;
    vi.clearAllMocks();
  });

  afterEach(() => {
    // 清除快取
    iconService.clearCache();
  });

  describe('fetchEmojis', () => {
    it('應該成功取得 emoji 資料', async () => {
      const mockEmojiData = {
        categories: {
          smileys_emotion: {
            name: '表情與情感',
            icon: '😀',
            priority: 1,
            subgroups: {
              face_smiling: {
                name: '微笑表情',
                emojis: [
                  { emoji: '😀', name: 'grinning face' },
                  { emoji: '😃', name: 'grinning face with big eyes' }
                ]
              }
            }
          }
        }
      };

      mockAxios.get.mockResolvedValue({ data: mockEmojiData });

      const result = await iconService.fetchEmojis();

      expect(mockAxios.get).toHaveBeenCalledWith('/api/config/icon/emoji');
      expect(result).toEqual(mockEmojiData);
    });

    it('應該快取 emoji 資料，避免重複請求', async () => {
      const mockData = { categories: {} };
      mockAxios.get.mockResolvedValue({ data: mockData });

      // 第一次請求
      await iconService.fetchEmojis();
      // 第二次請求
      await iconService.fetchEmojis();

      // 只應該呼叫一次 API
      expect(mockAxios.get).toHaveBeenCalledTimes(1);
    });

    it('應該處理 API 錯誤', async () => {
      const errorMessage = 'Network Error';
      mockAxios.get.mockRejectedValue(new Error(errorMessage));

      await expect(iconService.fetchEmojis()).rejects.toThrow(
        'Failed to fetch emojis: Network Error'
      );
    });

    it('應該處理 HTTP 錯誤狀態', async () => {
      const httpError = new Error('Request failed');
      httpError.response = {
        status: 404,
        data: { message: 'Not found' }
      };
      mockAxios.get.mockRejectedValue(httpError);

      await expect(iconService.fetchEmojis()).rejects.toThrow(
        'Failed to fetch emojis: Request failed'
      );
    });
  });

  describe('fetchHeroIcons', () => {
    it('應該成功取得 hero icons 資料', async () => {
      const mockHeroIconsData = {
        categories: {
          general: {
            name: '通用',
            icons: [
              { name: 'home', styles: ['outline', 'solid'] },
              { name: 'user', styles: ['outline', 'solid'] }
            ]
          }
        }
      };

      mockAxios.get.mockResolvedValue({ data: mockHeroIconsData });

      const result = await iconService.fetchHeroIcons();

      expect(mockAxios.get).toHaveBeenCalledWith('/api/config/icon/heroicons');
      expect(result).toEqual(mockHeroIconsData);
    });

    it('應該快取 hero icons 資料', async () => {
      const mockData = { categories: {} };
      mockAxios.get.mockResolvedValue({ data: mockData });

      await iconService.fetchHeroIcons();
      await iconService.fetchHeroIcons();

      expect(mockAxios.get).toHaveBeenCalledTimes(1);
    });

    it('應該處理 API 錯誤', async () => {
      mockAxios.get.mockRejectedValue(new Error('API Error'));

      await expect(iconService.fetchHeroIcons()).rejects.toThrow(
        'Failed to fetch hero icons: API Error'
      );
    });
  });

  describe('fetchBootstrapIcons', () => {
    it('應該成功取得 bootstrap icons 資料', async () => {
      const mockBootstrapIconsData = {
        categories: {
          general: {
            name: '通用',
            icons: [
              { class: 'bi-house', name: 'house', category: 'general' },
              { class: 'bi-person', name: 'person', category: 'general' }
            ]
          }
        }
      };

      mockAxios.get.mockResolvedValue({ data: mockBootstrapIconsData });

      const result = await iconService.fetchBootstrapIcons();

      expect(mockAxios.get).toHaveBeenCalledWith('/api/config/icon/bootstrap_icons');
      expect(result).toEqual(mockBootstrapIconsData);
    });

    it('應該快取 bootstrap icons 資料', async () => {
      const mockData = { categories: {} };
      mockAxios.get.mockResolvedValue({ data: mockData });

      await iconService.fetchBootstrapIcons();
      await iconService.fetchBootstrapIcons();

      expect(mockAxios.get).toHaveBeenCalledTimes(1);
    });

    it('應該處理 API 錯誤', async () => {
      mockAxios.get.mockRejectedValue(new Error('Service Unavailable'));

      await expect(iconService.fetchBootstrapIcons()).rejects.toThrow(
        'Failed to fetch bootstrap icons: Service Unavailable'
      );
    });
  });

  describe('快取機制', () => {
    it('應該為不同的資料類型維護獨立的快取', async () => {
      const mockEmojiData = { categories: { emoji: 'data' } };
      const mockHeroData = { categories: { hero: 'data' } };

      mockAxios.get
        .mockResolvedValueOnce({ data: mockEmojiData })
        .mockResolvedValueOnce({ data: mockHeroData });

      const emojiResult = await iconService.fetchEmojis();
      const heroResult = await iconService.fetchHeroIcons();

      expect(emojiResult).toEqual(mockEmojiData);
      expect(heroResult).toEqual(mockHeroData);
      expect(mockAxios.get).toHaveBeenCalledTimes(2);
    });

    it('應該能夠清除快取', async () => {
      const mockData = { categories: {} };
      mockAxios.get.mockResolvedValue({ data: mockData });

      // 第一次請求，建立快取
      await iconService.fetchEmojis();
      expect(mockAxios.get).toHaveBeenCalledTimes(1);

      // 清除快取
      iconService.clearCache();

      // 第二次請求，應該重新呼叫 API
      await iconService.fetchEmojis();
      expect(mockAxios.get).toHaveBeenCalledTimes(2);
    });

    it('應該能夠清除特定類型的快取', async () => {
      const mockData = { categories: {} };
      mockAxios.get.mockResolvedValue({ data: mockData });

      // 建立兩種快取
      await iconService.fetchEmojis();
      await iconService.fetchHeroIcons();
      expect(mockAxios.get).toHaveBeenCalledTimes(2);

      // 清除 emoji 快取
      iconService.clearCache('emojis');

      // emoji 重新請求，hero icons 使用快取
      await iconService.fetchEmojis();
      await iconService.fetchHeroIcons();
      expect(mockAxios.get).toHaveBeenCalledTimes(3); // 只多一次 emoji 請求
    });
  });

  describe('錯誤處理', () => {
    it('應該統一處理網路錯誤', async () => {
      const networkError = new Error('Network Error');
      mockAxios.get.mockRejectedValue(networkError);

      await expect(iconService.fetchEmojis()).rejects.toThrow('Failed to fetch emojis: Network Error');
    });

    it('應該處理 HTTP 錯誤狀態碼', async () => {
      const httpError = new Error('Request failed with status code 500');
      httpError.response = {
        status: 500,
        statusText: 'Internal Server Error',
        data: { error: 'Server error' }
      };
      mockAxios.get.mockRejectedValue(httpError);

      await expect(iconService.fetchEmojis()).rejects.toThrow(
        'Failed to fetch emojis: Request failed with status code 500'
      );
    });

    it('應該處理無效的回應資料', async () => {
      // 模擬回傳 null 或 undefined
      mockAxios.get.mockResolvedValue({ data: null });

      await expect(iconService.fetchEmojis()).rejects.toThrow(
        'Invalid response data received'
      );
    });

    it('應該處理回應格式錯誤', async () => {
      // 模擬回傳非預期格式
      mockAxios.get.mockResolvedValue({ data: 'invalid format' });

      await expect(iconService.fetchEmojis()).rejects.toThrow(
        'Invalid response data received'
      );
    });
  });

  describe('初始化檢查', () => {
    it('應該正確建立 IconService 實例', () => {
      expect(iconService).toBeInstanceOf(IconService);
      expect(typeof iconService.fetchEmojis).toBe('function');
      expect(typeof iconService.fetchHeroIcons).toBe('function');
      expect(typeof iconService.fetchBootstrapIcons).toBe('function');
      expect(typeof iconService.clearCache).toBe('function');
    });
  });
});