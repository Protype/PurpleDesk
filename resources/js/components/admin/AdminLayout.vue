<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <AppNavbar />

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex">
          <!-- 側邊欄 -->
          <div class="w-64 bg-white rounded-lg shadow mr-6">
            <div class="p-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">系統管理</h2>
            </div>
            
            <nav class="p-4 space-y-2">
              <router-link
                to="/admin/users"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                :class="$route.path === '/admin/users' 
                  ? 'bg-primary-100 text-primary-900' 
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
              >
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                使用者管理
              </router-link>
              
              <router-link
                to="/admin/organizations"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                :class="$route.path === '/admin/organizations' 
                  ? 'bg-primary-100 text-primary-900' 
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
              >
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                單位管理
              </router-link>
              
              <router-link
                to="/admin/system"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                :class="$route.path === '/admin/system' 
                  ? 'bg-primary-100 text-primary-900' 
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
              >
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                </svg>
                系統設定
              </router-link>
            </nav>
          </div>

          <!-- 主要內容 -->
          <div class="flex-1">
            <router-view />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AppNavbar from '../AppNavbar.vue'

export default {
  name: 'AdminLayout',
  components: {
    AppNavbar
  }
}
</script>