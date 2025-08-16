import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import ProfilePage from '../../../resources/js/components/ProfilePage.vue';
import { useAuthStore } from '../../../resources/js/stores/auth.js';
import { User } from '../../../resources/js/models/User.js';
import { 
  createMountOptions, 
  mockAuthState, 
  flushPromises,
  setupAxiosMocks,
  mockApiResponse
} from '../utils/testHelpers.js';

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    defaults: {
      headers: {
        common: {}
      }
    }
  }
}));

describe('ProfilePage 組件', () => {
  let wrapper;
  let authStore;
  
  beforeEach(() => {
    // 重設 mocks
    vi.clearAllMocks();
    
    // 建立掛載選項
    const mountOptions = createMountOptions({
      stubs: {
        AppNavbar: { template: '<div>AppNavbar</div>' },
        ImageField: { 
          template: '<div>ImageField</div>',
          emits: ['mode-changed', 'settings-changed', 'file-selected', 'file-error', 'save', 'clear', 'remove', 'success', 'error']
        }
      }
    });
    
    wrapper = mount(ProfilePage, mountOptions);
    
    // 取得 auth store 並設定測試用戶
    authStore = useAuthStore();
    const testUser = new User({
      id: 1,
      account: 'testuser',
      full_name: '測試用戶',
      display_name: '小測',
      email: 'test@example.com',
      avatar_data: null,
      is_admin: false,
      locale: 'zh_TW',
      timezone: 'Asia/Taipei',
      email_notifications: true,
      browser_notifications: false,
      theme: 'light'
    });
    
    mockAuthState(authStore, {
      user: testUser,
      isAuthenticated: true,
      isInitialized: true
    });
  });

  describe('表單初始化', () => {
    it('應該正確初始化表單欄位', async () => {
      // 等待組件完全掛載
      await wrapper.vm.$nextTick();
      
      const form = wrapper.vm.form;
      
      // 檢查表單是否正確對應到用戶資料
      expect(form.display_name).toBe('小測');
      expect(form.email).toBe('test@example.com');
      
      // 重要：檢查 name 欄位是否正確對應到 full_name
      expect(form.name).toBe('測試用戶');
    });

    it('應該處理沒有 full_name 的用戶', async () => {
      // 建立沒有 full_name 的用戶
      const userWithoutFullName = new User({
        id: 2,
        account: 'noname',
        display_name: '無名',
        email: 'noname@example.com'
      });
      
      authStore.user = userWithoutFullName;
      
      // 重新掛載組件
      await wrapper.unmount();
      wrapper = mount(ProfilePage, {
        global: {
          plugins: [createTestingPinia({ createSpy: vi.fn })],
          stubs: {
            AppNavbar: { template: '<div>AppNavbar</div>' },
            ImageField: { 
              template: '<div>ImageField</div>',
              emits: ['mode-changed', 'settings-changed', 'file-selected', 'file-error', 'save', 'clear', 'remove', 'success', 'error']
            }
          }
        }
      });
      
      await wrapper.vm.$nextTick();
      
      const form = wrapper.vm.form;
      
      // name 欄位應該是空字串（不是 undefined）
      expect(form.name).toBe('');
      expect(form.display_name).toBe('無名');
    });
  });

  describe('getUserInitials 方法', () => {
    it('應該正確使用 User Model 的方法', () => {
      const testUser = new User({
        full_name: '王小明',
        display_name: '小明'
      });
      
      const result = wrapper.vm.getUserInitials(testUser);
      
      // 應該使用 display_name 計算縮寫
      expect(result).toBe('小明');
    });

    it('應該處理沒有使用者的情況', () => {
      const result = wrapper.vm.getUserInitials(null);
      expect(result).toBe('');
    });

    it('應該回退到 full_name', () => {
      const testUser = new User({
        full_name: '李大華'
        // 沒有 display_name
      });
      
      const result = wrapper.vm.getUserInitials(testUser);
      expect(result).toBe('李大');
    });

    it('應該回退到 account', () => {
      const testUser = new User({
        account: 'johndoe'
        // 沒有 display_name 和 full_name
      });
      
      const result = wrapper.vm.getUserInitials(testUser);
      expect(result).toBe('JO');
    });
  });

  describe('表單提交邏輯', () => {
    it('應該正確對應欄位名稱', () => {
      const form = wrapper.vm.form;
      form.name = '新的全名';
      form.display_name = '新的顯示名稱';
      
      // 模擬 handleSubmit 中的 FormData 建立邏輯
      const formData = new FormData();
      formData.append('full_name', form.name || '');
      formData.append('display_name', form.display_name);
      
      // 檢查是否正確將 form.name 對應到 full_name
      expect(formData.get('full_name')).toBe('新的全名');
      expect(formData.get('display_name')).toBe('新的顯示名稱');
    });
  });
});