import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from './stores/auth'
import LoginPage from './pages/LoginPage.vue'
import RegisterPage from './components/RegisterPage.vue'
import Dashboard from './pages/Dashboard.vue'
import ProfilePage from './components/ProfilePage.vue'
import SettingsPage from './components/SettingsPage.vue'
import AdminLayout from './components/admin/AdminLayout.vue'
import AdminUsers from './components/admin/AdminUsers.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'login',
    component: LoginPage,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterPage,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'profile',
    component: ProfilePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'settings',
    component: SettingsPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/dev-tool/icon-size',
    name: 'dev-icon-size-tool',
    component: () => import('./pages/dev-tool/IconSizeTool.vue'),
    meta: { requiresAuth: true, devOnly: true }
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: 'users',
        name: 'admin-users',
        component: AdminUsers
      },
      {
        path: 'organizations',
        name: 'admin-organizations',
        component: () => import('./components/admin/AdminOrganizations.vue')
      },
      {
        path: 'organizations/:id/manage',
        name: 'admin-organization-manage',
        component: () => import('./components/admin/OrganizationManage.vue')
      },
      {
        path: 'system',
        name: 'admin-system',
        component: () => import('./components/admin/AdminSystem.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // 等待認證初始化完成
  if (!authStore.isInitialized) {
    await authStore.initializeAuth()
  }
  
  // 需要認證的頁面
  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next('/login')
    return
  }
  
  // 需要管理員權限的頁面
  if (to.meta.requiresAdmin) {
    const user = authStore.user
    if (!user || !user.is_admin) {
      next('/dashboard')
      return
    }
  }
  
  // 開發工具頁面只在開發模式下可訪問
  if (to.meta.devOnly && import.meta.env.PROD) {
    next('/dashboard')
    return
  }
  
  // 已登入用戶不能訪問登入/註冊頁面
  if (to.meta.requiresGuest && authStore.isLoggedIn) {
    next('/dashboard')
    return
  }
  
  next()
})

export default router