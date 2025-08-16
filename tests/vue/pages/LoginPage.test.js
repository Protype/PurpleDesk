import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { createMountOptions, flushPromises, mockApiResponse, mockApiError } from '../utils/testHelpers.js';
import LoginPage from '../../../resources/js/pages/LoginPage.vue';

// 模擬 stores/auth
const mockAuthStore = {
  login: vi.fn()
};

// 模擬 router
const mockRouter = {
  push: vi.fn()
};

vi.mock('../../../resources/js/stores/auth', () => ({
  useAuthStore: () => mockAuthStore
}));

vi.mock('vue-router', async (importOriginal) => {
  const actual = await importOriginal();
  return {
    ...actual,
    useRouter: () => mockRouter
  };
});

describe('LoginPage', () => {
  let wrapper;

  beforeEach(() => {
    // 重置所有 mock
    vi.clearAllMocks();
    
    const mountOptions = createMountOptions({
      routes: [
        { path: '/register', name: 'register', component: { template: '<div>Register</div>' } },
        { path: '/dashboard', name: 'dashboard', component: { template: '<div>Dashboard</div>' } }
      ]
    });

    wrapper = mount(LoginPage, mountOptions);
  });

  describe('組件渲染', () => {
    it('應該正確渲染登入頁面', () => {
      expect(wrapper.find('h2').text()).toBe('PurpleDesk');
      expect(wrapper.find('p').text()).toBe('專案管理系統');
      expect(wrapper.find('input[type="text"]').exists()).toBe(true);
      expect(wrapper.find('input[type="password"]').exists()).toBe(true);
      expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
    });

    it('應該顯示測試帳號區塊', () => {
      expect(wrapper.text()).toContain('測試帳號');
      expect(wrapper.text()).toContain('管理員帳號');
      expect(wrapper.text()).toContain('一般用戶');
      expect(wrapper.text()).toContain('設計師');
      expect(wrapper.text()).toContain('自由工作者');
    });

    it('應該顯示記住我選項和忘記密碼連結', () => {
      expect(wrapper.find('input[type="checkbox"]').exists()).toBe(true);
      expect(wrapper.text()).toContain('記住我');
      expect(wrapper.text()).toContain('忘記密碼？');
    });

    it('應該顯示註冊連結', () => {
      const registerLink = wrapper.findComponent({ name: 'RouterLink' });
      expect(registerLink.exists()).toBe(true);
      expect(registerLink.text()).toBe('立即註冊');
      expect(registerLink.props('to')).toBe('/register');
    });
  });

  describe('表單功能', () => {
    it('應該能夠輸入帳號和密碼', async () => {
      const loginInput = wrapper.find('input[type="text"]');
      const passwordInput = wrapper.find('input[type="password"]');

      await loginInput.setValue('testuser');
      await passwordInput.setValue('testpassword');

      expect(loginInput.element.value).toBe('testuser');
      expect(passwordInput.element.value).toBe('testpassword');
    });

    it('表單應該要求必填欄位', () => {
      const loginInput = wrapper.find('input[type="text"]');
      const passwordInput = wrapper.find('input[type="password"]');

      expect(loginInput.attributes('required')).toBeDefined();
      expect(passwordInput.attributes('required')).toBeDefined();
    });
  });

  describe('測試帳號快速填入', () => {
    it('應該能填入管理員測試帳號', async () => {
      const buttons = wrapper.findAll('button[type="button"]');
      const adminButton = buttons.find(button => button.text().includes('管理員帳號'));
      
      await adminButton.trigger('click');
      
      const loginInput = wrapper.find('input[type="text"]');
      const passwordInput = wrapper.find('input[type="password"]');
      
      expect(loginInput.element.value).toBe('admin');
      expect(passwordInput.element.value).toBe('password');
    });

    it('應該能填入一般用戶測試帳號', async () => {
      const buttons = wrapper.findAll('button[type="button"]');
      const userButton = buttons.find(button => button.text().includes('一般用戶'));
      
      await userButton.trigger('click');
      
      const loginInput = wrapper.find('input[type="text"]');
      const passwordInput = wrapper.find('input[type="password"]');
      
      expect(loginInput.element.value).toBe('vincent');
      expect(passwordInput.element.value).toBe('password');
    });

    it('應該能填入設計師測試帳號', async () => {
      const buttons = wrapper.findAll('button[type="button"]');
      const designerButton = buttons.find(button => button.text().includes('設計師'));
      
      await designerButton.trigger('click');
      
      const loginInput = wrapper.find('input[type="text"]');
      const passwordInput = wrapper.find('input[type="password"]');
      
      expect(loginInput.element.value).toBe('sophia@cloudtech.com');
      expect(passwordInput.element.value).toBe('password');
    });

    it('應該能填入自由工作者測試帳號', async () => {
      const buttons = wrapper.findAll('button[type="button"]');
      const freelancerButton = buttons.find(button => button.text().includes('自由工作者'));
      
      await freelancerButton.trigger('click');
      
      const loginInput = wrapper.find('input[type="text"]');
      const passwordInput = wrapper.find('input[type="password"]');
      
      expect(loginInput.element.value).toBe('techwang');
      expect(passwordInput.element.value).toBe('password');
    });
  });

  describe('登入流程', () => {
    it('成功登入應該導航到 dashboard', async () => {
      // 設定成功的 mock 回應
      mockAuthStore.login.mockResolvedValue({ user: { id: 1, name: 'Test User' } });

      // 填入表單資料
      await wrapper.find('input[type="text"]').setValue('testuser');
      await wrapper.find('input[type="password"]').setValue('testpassword');

      // 提交表單
      await wrapper.find('form').trigger('submit.prevent');
      await flushPromises();

      // 驗證呼叫
      expect(mockAuthStore.login).toHaveBeenCalledWith('testuser', 'testpassword');
      expect(mockRouter.push).toHaveBeenCalledWith('/dashboard');
    });

    it('登入失敗應該顯示錯誤訊息', async () => {
      // 設定失敗的 mock 回應
      const errorResponse = {
        response: {
          data: {
            message: '帳號或密碼錯誤'
          }
        }
      };
      mockAuthStore.login.mockRejectedValue(errorResponse);

      // 填入表單資料
      await wrapper.find('input[type="text"]').setValue('wronguser');
      await wrapper.find('input[type="password"]').setValue('wrongpassword');

      // 提交表單
      await wrapper.find('form').trigger('submit.prevent');
      await flushPromises();

      // 驗證錯誤訊息顯示
      expect(wrapper.text()).toContain('登入失敗');
      expect(wrapper.text()).toContain('帳號或密碼錯誤');
    });

    it('登入時應該顯示載入狀態', async () => {
      // 建立一個 Promise 讓我們控制解析時機
      let resolveLogin;
      const loginPromise = new Promise((resolve) => {
        resolveLogin = resolve;
      });
      mockAuthStore.login.mockReturnValue(loginPromise);

      // 填入表單資料
      await wrapper.find('input[type="text"]').setValue('testuser');
      await wrapper.find('input[type="password"]').setValue('testpassword');

      // 提交表單
      await wrapper.find('form').trigger('submit.prevent');
      await wrapper.vm.$nextTick();

      // 驗證載入狀態
      expect(wrapper.text()).toContain('登入中...');
      expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
      expect(wrapper.find('.animate-spin').exists()).toBe(true);

      // 完成登入
      resolveLogin({ user: { id: 1 } });
      await flushPromises();

      // 驗證載入狀態消失
      expect(wrapper.text()).toContain('登入');
      expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeUndefined();
    });

    it('載入中時不應該重複提交', async () => {
      // 設定一個未解析的 Promise
      const neverResolvePromise = new Promise(() => {});
      mockAuthStore.login.mockReturnValue(neverResolvePromise);

      // 填入表單資料
      await wrapper.find('input[type="text"]').setValue('testuser');
      await wrapper.find('input[type="password"]').setValue('testpassword');

      // 第一次提交
      await wrapper.find('form').trigger('submit.prevent');
      await wrapper.vm.$nextTick();

      // 第二次提交
      await wrapper.find('form').trigger('submit.prevent');

      // 驗證只呼叫一次
      expect(mockAuthStore.login).toHaveBeenCalledTimes(1);
    });
  });

  describe('錯誤處理', () => {
    it('沒有錯誤回應訊息時應該顯示預設錯誤', async () => {
      // 設定沒有詳細訊息的錯誤
      mockAuthStore.login.mockRejectedValue(new Error('Network Error'));

      await wrapper.find('input[type="text"]').setValue('testuser');
      await wrapper.find('input[type="password"]').setValue('testpassword');
      await wrapper.find('form').trigger('submit.prevent');
      await flushPromises();

      expect(wrapper.text()).toContain('登入失敗，請稍後再試');
    });

    it('提交成功後應該清除錯誤訊息', async () => {
      // 先製造一個錯誤
      mockAuthStore.login.mockRejectedValueOnce(new Error('First error'));
      
      await wrapper.find('input[type="text"]').setValue('testuser');
      await wrapper.find('input[type="password"]').setValue('testpassword');
      await wrapper.find('form').trigger('submit.prevent');
      await flushPromises();

      expect(wrapper.text()).toContain('登入失敗，請稍後再試');

      // 再次嘗試登入成功
      mockAuthStore.login.mockResolvedValue({ user: { id: 1 } });
      await wrapper.find('form').trigger('submit.prevent');
      await flushPromises();

      // 錯誤訊息應該消失
      expect(wrapper.text()).not.toContain('登入失敗');
    });
  });
});