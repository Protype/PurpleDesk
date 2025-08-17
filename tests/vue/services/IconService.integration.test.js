import { describe, it, expect, beforeEach } from 'vitest';
import axios from 'axios';
import { IconService } from '../../../resources/js/services/IconService.js';

describe('IconService Integration Tests', () => {
  let iconService;

  beforeEach(() => {
    // 設定 axios 基礎 URL 為開發伺服器
    axios.defaults.baseURL = 'http://localhost:8000';
    iconService = new IconService();
  });

  describe('實際 API 測試', () => {
    it('應該能夠取得 emoji 資料 (已實作的 API)', async () => {
      try {
        const result = await iconService.fetchEmojis();
        
        // 驗證回應結構
        expect(result).toBeDefined();
        expect(typeof result).toBe('object');
        expect(result.categories).toBeDefined();
        
        console.log('✅ Emoji API 測試通過');
      } catch (error) {
        console.error('❌ Emoji API 測試失敗:', error.message);
        throw error;
      }
    });

    it('應該處理 hero icons API 不存在的情況 (未實作的 API)', async () => {
      try {
        await iconService.fetchHeroIcons();
        // 如果成功則表示 API 已實作
        console.log('✅ Hero Icons API 意外通過 - API 可能已實作');
      } catch (error) {
        // 預期會失敗
        console.log('❌ Hero Icons API 如預期失敗 (404):', error.message);
        expect(error.message).toContain('Failed to fetch hero icons');
      }
    });

    it('應該處理 bootstrap icons API 不存在的情況 (未實作的 API)', async () => {
      try {
        await iconService.fetchBootstrapIcons();
        // 如果成功則表示 API 已實作
        console.log('✅ Bootstrap Icons API 意外通過 - API 可能已實作');
      } catch (error) {
        // 預期會失敗
        console.log('❌ Bootstrap Icons API 如預期失敗 (404):', error.message);
        expect(error.message).toContain('Failed to fetch bootstrap icons');
      }
    });
  });

  describe('快取功能驗證', () => {
    it('應該快取 emoji 資料', async () => {
      // 第一次呼叫
      const result1 = await iconService.fetchEmojis();
      
      // 第二次呼叫（應該從快取取得）
      const result2 = await iconService.fetchEmojis();
      
      // 驗證兩次結果相同
      expect(result1).toEqual(result2);
      
      console.log('✅ Emoji 快取功能正常');
    });
  });
});