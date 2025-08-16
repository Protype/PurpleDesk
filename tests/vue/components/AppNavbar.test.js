import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import AppNavbar from '../../../resources/js/components/AppNavbar.vue';
import { useAuthStore } from '../../../resources/js/stores/auth.js';
import { 
  createMountOptions, 
  mockAuthState, 
  flushPromises 
} from '../utils/testHelpers.js';

// Mock Heroicons
vi.mock('@heroicons/vue/outline', () => ({
  BellIcon: { template: '<div data-testid="bell-icon">🔔</div>' },
  ChevronDownIcon: { template: '<div data-testid="chevron-down-icon">⌄</div>' },
  CogIcon: { template: '<div data-testid="cog-icon">⚙️</div>' },
  OfficeBuildingIcon: { template: '<div data-testid="office-building-icon">🏢</div>' },
  LogoutIcon: { template: '<div data-testid="logout-icon">🚪</div>' }
}));

describe('AppNavbar.vue', () => {
  let wrapper;
  let authStore;
  let router;
  
  beforeEach(() => {
    // 建立測試環境
    const mountOptions = createMountOptions({
      routes: [
        { path: '/dashboard', name: 'dashboard', component: { template: '<div>Dashboard</div>' } },
        { path: '/profile', name: 'profile', component: { template: '<div>Profile</div>' } },
        { path: '/settings', name: 'settings', component: { template: '<div>Settings</div>' } },
        { path: '/admin/users', name: 'admin.users', component: { template: '<div>Admin Users</div>' } },
        { path: '/admin/organizations', name: 'admin.organizations', component: { template: '<div>Admin Organizations</div>' } },
        { path: '/admin/system', name: 'admin.system', component: { template: '<div>Admin System</div>' } },
        { path: '/login', name: 'login', component: { template: '<div>Login</div>' } }
      ]
    });
    
    wrapper = mount(AppNavbar, mountOptions);
    authStore = useAuthStore();
    router = wrapper.vm.$router;
    
    // 重置路由到初始狀態
    router.push('/dashboard');
  });

  describe('組件基本渲染', () => {
    it('應該正確渲染導覽列結構', () => {
      expect(wrapper.find('nav').exists()).toBe(true);
      expect(wrapper.find('.bg-white.shadow').exists()).toBe(true);
    });

    it('應該顯示 PurpleDesk 品牌名稱', () => {
      const brandText = wrapper.find('span');
      expect(brandText.exists()).toBe(true);
      expect(brandText.text()).toContain('PurpleDesk');
    });

    it('應該顯示通知圖標', () => {
      const bellIcon = wrapper.find('[data-testid="bell-icon"]');
      expect(bellIcon.exists()).toBe(true);
    });
  });

  describe('使用者狀態顯示', () => {
    it('當使用者已登入時應該顯示使用者頭像按鈕', () => {
      const mockUser = {
        id: 1,
        account: 'testuser',
        email: 'test@example.com',
        full_name: '測試用戶',
        display_name: '測試用戶',
        is_admin: false,
        avatar_url: null
      };
      
      mockAuthState(authStore, {
        user: mockUser,
        isAuthenticated: true,
        isInitialized: true
      });

      expect(wrapper.find('button').exists()).toBe(true);
      expect(wrapper.find('.h-8.w-8.rounded-full').exists()).toBe(true);
    });

    it('當使用者有頭像時應該顯示頭像圖片', async () => {
      const mockUser = {
        id: 1,
        account: 'testuser',
        email: 'test@example.com',
        full_name: '測試用戶',
        display_name: '測試用戶',
        is_admin: false,
        avatar_url: 'https://example.com/avatar.jpg'
      };
      
      mockAuthState(authStore, {
        user: mockUser,
        isAuthenticated: true,
        isInitialized: true
      });

      await nextTick();

      const avatarImg = wrapper.find('img[alt="測試用戶"]');
      expect(avatarImg.exists()).toBe(true);
      expect(avatarImg.attributes('src')).toBe('https://example.com/avatar.jpg');
    });

    it('當使用者無頭像時應該顯示姓名縮寫', async () => {
      const mockUser = {
        id: 1,
        account: 'testuser',
        email: 'test@example.com',
        full_name: '測試用戶',
        display_name: '測試用戶',
        is_admin: false,
        avatar_url: null
      };
      
      mockAuthState(authStore, {
        user: mockUser,
        isAuthenticated: true,
        isInitialized: true
      });

      await nextTick();

      const avatarText = wrapper.find('.text-white.text-sm.font-medium');
      expect(avatarText.exists()).toBe(true);
      expect(avatarText.text()).toBe('測試');
    });

    it('當使用者是管理員時應該顯示管理員標籤', async () => {
      const mockUser = {
        id: 1,
        account: 'admin',
        email: 'admin@example.com',
        full_name: '管理員',
        display_name: '管理員',
        is_admin: true,
        avatar_url: null
      };
      
      mockAuthState(authStore, {
        user: mockUser,
        isAuthenticated: true,
        isInitialized: true
      });

      await nextTick();

      const adminBadge = wrapper.find('.bg-red-100.text-red-800');
      expect(adminBadge.exists()).toBe(true);
      expect(adminBadge.text()).toBe('管理員');
    });
  });

  describe('下拉選單互動', () => {
    beforeEach(async () => {
      const mockUser = {
        id: 1,
        account: 'testuser',
        email: 'test@example.com',
        full_name: '測試用戶',
        display_name: '測試用戶',
        is_admin: false,
        organizations: [
          { id: 1, name: '測試組織' }
        ]
      };
      
      mockAuthState(authStore, {
        user: mockUser,
        isAuthenticated: true,
        isInitialized: true
      });

      await nextTick();
    });

    it('點擊使用者頭像應該切換下拉選單', async () => {
      // 找到使用者頭像按鈕（第二個 button，第一個是通知按鈕）
      const buttons = wrapper.findAll('button');
      const userButton = buttons[1];
      
      // 初始狀態 showUserMenu 應該是 false
      expect(wrapper.vm.showUserMenu).toBe(false);
      
      // 點擊切換
      await userButton.trigger('click');
      await nextTick();
      
      expect(wrapper.vm.showUserMenu).toBe(true);
      
      // 再次點擊關閉
      await userButton.trigger('click');
      await nextTick();
      
      expect(wrapper.vm.showUserMenu).toBe(false);
    });

    it('下拉選單應該顯示使用者資訊', async () => {
      const userButton = wrapper.find('button');
      await userButton.trigger('click');
      await nextTick();

      const dropdown = wrapper.find('.absolute.right-0');
      expect(dropdown.text()).toContain('測試用戶');
      expect(dropdown.text()).toContain('test@example.com');
      expect(dropdown.text()).toContain('測試組織');
    });

    it('下拉選單應該顯示基本選單項目', async () => {
      // 打開下拉選單
      const buttons = wrapper.findAll('button');
      const userButton = buttons[1];
      await userButton.trigger('click');
      await nextTick();

      // 檢查選單項目是否存在
      const dropdownLinks = wrapper.findAllComponents({ name: 'RouterLink' });
      const profileLink = dropdownLinks.find(link => link.props('to') === '/profile');
      const settingsLink = dropdownLinks.find(link => link.props('to') === '/settings');
      
      // 檢查登出按鈕 (應該是包含 "登出" 文字的按鈕)
      const allButtons = wrapper.findAll('button');
      const logoutButton = allButtons.find(btn => btn.text().includes('登出'));

      expect(profileLink).toBeDefined();
      expect(profileLink.text()).toContain('個人資料');
      expect(settingsLink).toBeDefined();
      expect(settingsLink.text()).toContain('設定');
      expect(logoutButton).toBeDefined();
      expect(logoutButton.text()).toContain('登出');
    });

    it('管理員應該看到額外的管理選項', async () => {
      const mockAdminUser = {
        id: 1,
        account: 'admin',
        email: 'admin@example.com',
        full_name: '管理員',
        display_name: '管理員',
        is_admin: true,
        organizations: []
      };
      
      mockAuthState(authStore, {
        user: mockAdminUser,
        isAuthenticated: true,
        isInitialized: true
      });

      await nextTick();

      // 打開下拉選單
      const buttons = wrapper.findAll('button');
      const userButton = buttons[1];
      await userButton.trigger('click');
      await nextTick();

      // 檢查管理員選單項目
      const dropdownLinks = wrapper.findAllComponents({ name: 'RouterLink' });
      const usersLink = dropdownLinks.find(link => link.props('to') === '/admin/users');
      const orgsLink = dropdownLinks.find(link => link.props('to') === '/admin/organizations');
      const systemLink = dropdownLinks.find(link => link.props('to') === '/admin/system');

      expect(usersLink).toBeDefined();
      expect(usersLink.text()).toContain('使用者管理');
      expect(orgsLink).toBeDefined();
      expect(orgsLink.text()).toContain('組織管理');
      expect(systemLink).toBeDefined();
      expect(systemLink.text()).toContain('系統設定');
    });
  });

  describe('路由導航', () => {
    beforeEach(async () => {
      const mockUser = {
        id: 1,
        account: 'testuser',
        email: 'test@example.com',
        full_name: '測試用戶',
        display_name: '測試用戶',
        is_admin: false
      };
      
      mockAuthState(authStore, {
        user: mockUser,
        isAuthenticated: true,
        isInitialized: true
      });

      await nextTick();
      
      // 打開下拉選單
      const userButton = wrapper.find('button');
      await userButton.trigger('click');
      await nextTick();
    });

    it('點擊個人資料應該導航到 /profile', async () => {
      // 找到個人資料連結
      const dropdownLinks = wrapper.findAllComponents({ name: 'RouterLink' });
      const profileLink = dropdownLinks.find(link => link.props('to') === '/profile');
      
      expect(profileLink).toBeDefined();
      await profileLink.trigger('click');
      await flushPromises();

      expect(router.currentRoute.value.path).toBe('/profile');
    });

    it('點擊設定應該導航到 /settings', async () => {
      // 找到設定連結
      const dropdownLinks = wrapper.findAllComponents({ name: 'RouterLink' });
      const settingsLink = dropdownLinks.find(link => link.props('to') === '/settings');
      
      expect(settingsLink).toBeDefined();
      await settingsLink.trigger('click');
      await flushPromises();

      expect(router.currentRoute.value.path).toBe('/settings');
    });
  });

  describe('登出功能', () => {
    beforeEach(async () => {
      const mockUser = {
        id: 1,
        account: 'testuser',
        email: 'test@example.com',
        full_name: '測試用戶',
        display_name: '測試用戶',
        is_admin: false
      };
      
      mockAuthState(authStore, {
        user: mockUser,
        isAuthenticated: true,
        isInitialized: true
      });

      // Mock authStore.logout 方法
      authStore.logout = vi.fn().mockResolvedValue();

      await nextTick();
      
      // 打開下拉選單
      const userButton = wrapper.find('button');
      await userButton.trigger('click');
      await nextTick();
    });

    it('點擊登出應該呼叫 authStore.logout', async () => {
      const logoutButton = wrapper.find('button:last-child');
      await logoutButton.trigger('click');
      await flushPromises();

      expect(authStore.logout).toHaveBeenCalled();
    });

    it('登出成功應該導航到 /login', async () => {
      const logoutButton = wrapper.find('button:last-child');
      await logoutButton.trigger('click');
      await flushPromises();

      expect(router.currentRoute.value.path).toBe('/login');
    });

    it('登出失敗應該處理錯誤', async () => {
      // Mock 登出失敗
      authStore.logout = vi.fn().mockRejectedValue(new Error('登出失敗'));
      
      // Mock console.error 以避免測試輸出錯誤
      const consoleErrorSpy = vi.spyOn(console, 'error').mockImplementation(() => {});

      const logoutButton = wrapper.find('button:last-child');
      await logoutButton.trigger('click');
      await flushPromises();

      expect(consoleErrorSpy).toHaveBeenCalledWith('登出錯誤:', expect.any(Error));
      
      consoleErrorSpy.mockRestore();
    });
  });
});