import { describe, it, expect, vi, beforeEach } from 'vitest';
import { User } from '../../../resources/js/models/User.js';

describe('User Model', () => {
  describe('建構函數', () => {
    it('應該接受使用者資料物件並建立 User 實例', () => {
      const userData = {
        id: 1,
        account: 'john_doe',
        full_name: '王小明',
        display_name: '小明',
        email: 'john@example.com',
        avatar_data: null,
        avatar_url: null,
        is_admin: false,
        locale: 'zh_TW',
        timezone: 'Asia/Taipei',
        email_notifications: true,
        browser_notifications: true,
        theme: 'light'
      };

      const user = new User(userData);

      expect(user.id).toBe(1);
      expect(user.account).toBe('john_doe');
      expect(user.fullName).toBe('王小明');
      expect(user.displayName).toBe('小明');
      expect(user.email).toBe('john@example.com');
      expect(user.isAdmin()).toBe(false);
    });

    it('應該處理空的或未定義的資料', () => {
      const user = new User();
      
      expect(user.id).toBeUndefined();
      expect(user.fullName).toBeUndefined();
    });

    it('應該正確轉換 avatar_data JSON 字串', () => {
      const userData = {
        avatar_data: '{"type":"text","text":"王M","backgroundColor":"#6366f1","textColor":"#ffffff"}'
      };

      const user = new User(userData);

      expect(user.avatarData).toEqual({
        type: 'text',
        text: '王M',
        backgroundColor: '#6366f1',
        textColor: '#ffffff'
      });
    });
  });

  describe('getInitials 方法', () => {
    it('應該為中文名稱回傳前2個字符', () => {
      const user = new User({ full_name: '王小明' });
      expect(user.getInitials()).toBe('王小');
    });

    it('應該為英文名稱回傳首字母大寫', () => {
      const user = new User({ full_name: 'John Doe' });
      expect(user.getInitials()).toBe('JD');
    });

    it('應該為單一英文名稱回傳首字母', () => {
      const user = new User({ full_name: 'John' });
      expect(user.getInitials()).toBe('J');
    });

    it('應該優先使用 display_name', () => {
      const user = new User({ 
        full_name: '王小明',
        display_name: 'Alex'
      });
      expect(user.getInitials()).toBe('A');
    });

    it('應該處理空名稱', () => {
      const user = new User({ full_name: '' });
      expect(user.getInitials()).toBe('');
    });

    it('應該限制最多2個字符', () => {
      const user = new User({ full_name: 'John Michael Smith Doe' });
      expect(user.getInitials()).toBe('JM');
    });
  });

  describe('getDisplayName 方法', () => {
    it('應該優先回傳 display_name', () => {
      const user = new User({
        account: 'johndoe',
        full_name: '王小明',
        display_name: '小明'
      });
      expect(user.getDisplayName()).toBe('小明');
    });

    it('應該回傳 full_name 如果沒有 display_name', () => {
      const user = new User({ 
        account: 'johndoe',
        full_name: '王小明' 
      });
      expect(user.getDisplayName()).toBe('王小明');
    });

    it('應該回傳 account 如果都沒有 display_name 和 full_name', () => {
      const user = new User({ account: 'johndoe' });
      expect(user.getDisplayName()).toBe('johndoe');
    });

    it('應該回傳空字串如果三者都沒有', () => {
      const user = new User({});
      expect(user.getDisplayName()).toBe('');
    });
  });

  describe('getAvatarData 方法', () => {
    it('應該回傳提供的 avatar_data', () => {
      const avatarData = { type: 'text', text: 'AB', backgroundColor: '#6366f1' };
      const user = new User({ avatar_data: avatarData });
      expect(user.getAvatarData()).toEqual(avatarData);
    });

    it('應該回傳預設 avatar_data 當沒有提供時', () => {
      const user = new User({ full_name: '王小明' });
      const result = user.getAvatarData();
      
      expect(result.type).toBe('text');
      expect(result.text).toBe('王小');
      expect(result.backgroundColor).toBeDefined();
      expect(result.textColor).toBeDefined();
    });

    it('應該回傳使用 display_name 的預設 avatar_data', () => {
      const user = new User({ 
        full_name: '王小明',
        display_name: '小王' 
      });
      const result = user.getAvatarData();
      
      expect(result.type).toBe('text');
      expect(result.text).toBe('小王');
    });

    it('應該在沒有名稱時使用 account', () => {
      const user = new User({ account: 'johndoe' });
      const result = user.getAvatarData();
      
      expect(result.type).toBe('text');
      expect(result.text).toBe('JO');
    });
  });


  describe('isAdmin 方法', () => {
    it('應該在 is_admin 為 true 時回傳 true', () => {
      const user = new User({ is_admin: true });
      expect(user.isAdmin()).toBe(true);
    });

    it('應該在 is_admin 為 false 時回傳 false', () => {
      const user = new User({ is_admin: false });
      expect(user.isAdmin()).toBe(false);
    });

    it('應該在未設定時回傳 false', () => {
      const user = new User({});
      expect(user.isAdmin()).toBe(false);
    });
  });

  describe('toJSON 方法', () => {
    it('應該回傳原始資料格式', () => {
      const userData = {
        id: 1,
        account: 'test',
        full_name: '測試用戶',
        is_admin: true
      };
      
      const user = new User(userData);
      const json = user.toJSON();

      expect(json).toEqual({
        id: 1,
        account: 'test',
        full_name: '測試用戶',
        is_admin: true
      });
    });
  });
});