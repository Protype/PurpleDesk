/**
 * 測試用戶資料工廠
 * 提供各種預設和自訂的測試用戶資料
 */

import { vi } from 'vitest';

/**
 * 建立基本用戶資料
 */
export function createUser(overrides = {}) {
  const defaultUser = {
    id: 1,
    account: 'testuser',
    email: 'test@example.com',
    full_name: '測試用戶',
    display_name: '測試用戶',
    is_admin: false,
    locale: 'zh_TW',
    timezone: 'Asia/Taipei',
    email_notifications: true,
    browser_notifications: false,
    theme: 'light',
    avatar_data: null,
    created_at: '2025-01-01T00:00:00.000000Z',
    updated_at: '2025-01-01T00:00:00.000000Z',
    organizations: [],
    teams: []
  };

  return { ...defaultUser, ...overrides };
}

/**
 * 建立管理員用戶
 */
export function createAdminUser(overrides = {}) {
  return createUser({
    id: 1,
    account: 'admin',
    email: 'admin@example.com',
    full_name: '系統管理員',
    display_name: '管理員',
    is_admin: true,
    ...overrides
  });
}

/**
 * 建立一般用戶
 */
export function createRegularUser(overrides = {}) {
  return createUser({
    id: 2,
    account: 'user',
    email: 'user@example.com',
    full_name: '一般用戶',
    display_name: '用戶',
    is_admin: false,
    ...overrides
  });
}

/**
 * 建立設計師用戶
 */
export function createDesignerUser(overrides = {}) {
  return createUser({
    id: 3,
    account: 'designer',
    email: 'designer@example.com',
    full_name: '設計師',
    display_name: '設計師',
    is_admin: false,
    avatar_data: {
      type: 'emoji',
      emoji: '🎨',
      backgroundColor: '#f3f4f6'
    },
    ...overrides
  });
}

/**
 * 建立開發者用戶
 */
export function createDeveloperUser(overrides = {}) {
  return createUser({
    id: 4,
    account: 'developer',
    email: 'dev@example.com',
    full_name: '開發者',
    display_name: '開發者',
    is_admin: false,
    avatar_data: {
      type: 'bootstrap_icon',
      icon: 'bi-code-slash',
      style: 'outline',
      backgroundColor: '#dbeafe',
      iconColor: '#2563eb'
    },
    theme: 'dark',
    ...overrides
  });
}

/**
 * 建立含組織關聯的用戶
 */
export function createUserWithOrganizations(overrides = {}) {
  const organizations = overrides.organizations || [
    createOrganization({ id: 1, name: 'TechCorp' }),
    createOrganization({ id: 2, name: 'DesignStudio' })
  ];

  return createUser({
    organizations,
    ...overrides
  });
}

/**
 * 建立含團隊關聯的用戶
 */
export function createUserWithTeams(overrides = {}) {
  const teams = overrides.teams || [
    createTeam({ id: 1, name: '開發團隊' }),
    createTeam({ id: 2, name: '設計團隊' })
  ];

  return createUser({
    teams,
    ...overrides
  });
}

/**
 * 建立組織資料
 */
export function createOrganization(overrides = {}) {
  const defaultOrganization = {
    id: 1,
    name: 'TechCorp',
    description: '科技公司',
    logo_data: {
      type: 'hero_icon',
      icon: 'office-building',
      style: 'outline',
      backgroundColor: '#faf5ff',
      iconColor: '#7c3aed'
    },
    created_at: '2025-01-01T00:00:00.000000Z',
    updated_at: '2025-01-01T00:00:00.000000Z'
  };

  return { ...defaultOrganization, ...overrides };
}

/**
 * 建立團隊資料
 */
export function createTeam(overrides = {}) {
  const defaultTeam = {
    id: 1,
    name: '開發團隊',
    description: '負責產品開發',
    icon_data: {
      type: 'bootstrap_icon',
      icon: 'bi-people',
      style: 'outline',
      backgroundColor: '#dbeafe',
      iconColor: '#2563eb'
    },
    organization_id: 1,
    created_at: '2025-01-01T00:00:00.000000Z',
    updated_at: '2025-01-01T00:00:00.000000Z'
  };

  return { ...defaultTeam, ...overrides };
}

/**
 * 建立認證 token
 */
export function createAuthToken(overrides = {}) {
  const defaultToken = {
    token: 'test-auth-token-' + Math.random().toString(36).substring(7),
    type: 'Bearer',
    expires_at: new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString() // 24小時後過期
  };

  return { ...defaultToken, ...overrides };
}

/**
 * 建立過期的 token
 */
export function createExpiredToken(overrides = {}) {
  return createAuthToken({
    token: 'expired-token-' + Math.random().toString(36).substring(7),
    expires_at: new Date(Date.now() - 60 * 60 * 1000).toISOString(), // 1小時前過期
    ...overrides
  });
}

/**
 * 建立註冊表單資料
 */
export function createRegistrationData(overrides = {}) {
  const defaultData = {
    account: 'newuser',
    full_name: '新用戶',
    display_name: '新用戶',
    email: 'newuser@example.com',
    password: 'password123',
    password_confirmation: 'password123'
  };

  return { ...defaultData, ...overrides };
}

/**
 * 建立登入表單資料
 */
export function createLoginData(overrides = {}) {
  const defaultData = {
    login: 'testuser',
    password: 'password123'
  };

  return { ...defaultData, ...overrides };
}

/**
 * 建立用戶更新資料
 */
export function createUserUpdateData(overrides = {}) {
  const defaultData = {
    account: 'updateduser',
    full_name: '更新用戶',
    display_name: '更新用戶',
    email: 'updated@example.com'
  };

  return { ...defaultData, ...overrides };
}

/**
 * 建立密碼更新資料
 */
export function createPasswordUpdateData(overrides = {}) {
  const defaultData = {
    current_password: 'oldpassword',
    password: 'newpassword123',
    password_confirmation: 'newpassword123'
  };

  return { ...defaultData, ...overrides };
}

/**
 * 建立設定更新資料
 */
export function createSettingsUpdateData(overrides = {}) {
  const defaultData = {
    locale: 'zh_TW',
    timezone: 'Asia/Taipei',
    email_notifications: true,
    browser_notifications: false,
    theme: 'light'
  };

  return { ...defaultData, ...overrides };
}

/**
 * 建立錯誤回應資料
 */
export function createErrorResponse(overrides = {}) {
  const defaultError = {
    message: '操作失敗',
    errors: {},
    status: 500
  };

  return { ...defaultError, ...overrides };
}

/**
 * 建立驗證錯誤回應
 */
export function createValidationErrorResponse(errors = {}) {
  const defaultErrors = {
    account: ['帳號欄位為必填'],
    email: ['電子郵件格式不正確'],
    password: ['密碼至少需要8個字符']
  };

  return createErrorResponse({
    message: '表單驗證失敗',
    errors: { ...defaultErrors, ...errors },
    status: 422
  });
}

/**
 * 建立多個用戶的陣列
 */
export function createUserList(count = 5, customData = []) {
  const users = [];
  
  for (let i = 0; i < count; i++) {
    const userData = customData[i] || {};
    users.push(createUser({
      id: i + 1,
      account: `user${i + 1}`,
      email: `user${i + 1}@example.com`,
      full_name: `用戶 ${i + 1}`,
      display_name: `用戶 ${i + 1}`,
      ...userData
    }));
  }
  
  return users;
}

/**
 * 建立分頁回應資料
 */
export function createPaginatedResponse(data = [], overrides = {}) {
  const defaultPagination = {
    data,
    current_page: 1,
    per_page: 10,
    total: data.length,
    last_page: Math.ceil(data.length / 10),
    from: 1,
    to: Math.min(10, data.length),
    links: {
      first: '/api/users?page=1',
      last: `/api/users?page=${Math.ceil(data.length / 10)}`,
      prev: null,
      next: data.length > 10 ? '/api/users?page=2' : null
    }
  };

  return { ...defaultPagination, ...overrides };
}

/**
 * 建立 mock API 回應包裝器
 */
export function wrapApiResponse(data, status = 200) {
  return {
    status,
    statusText: status === 200 ? 'OK' : 'Error',
    data,
    headers: {
      'content-type': 'application/json'
    },
    config: {}
  };
}

/**
 * 建立 mock 錯誤回應
 */
export function wrapApiError(errorData, status = 500) {
  const error = new Error(errorData.message || 'API Error');
  error.response = wrapApiResponse(errorData, status);
  return error;
}