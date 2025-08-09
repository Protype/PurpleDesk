<template>
  <div class="space-y-6">
    <!-- 系統統計 -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">系統統計</h2>
      </div>
      
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-primary-50 overflow-hidden rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">總使用者數</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ stats.total_users || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-green-50 overflow-hidden rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">總單位數</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ stats.total_organizations || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-blue-50 overflow-hidden rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">本月新增使用者</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ stats.users_this_month || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-purple-50 overflow-hidden rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">本月新增單位</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ stats.organizations_this_month || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 系統資訊 -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">系統資訊</h2>
      </div>
      
      <div class="p-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
          <div>
            <dt class="text-sm font-medium text-gray-500">系統名稱</dt>
            <dd class="mt-1 text-sm text-gray-900">PurpleDesk 專案管理系統</dd>
          </div>
          
          <div>
            <dt class="text-sm font-medium text-gray-500">版本</dt>
            <dd class="mt-1 text-sm text-gray-900">v1.0.0</dd>
          </div>
          
          <div>
            <dt class="text-sm font-medium text-gray-500">Laravel 版本</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ laravelVersion }}</dd>
          </div>
          
          <div>
            <dt class="text-sm font-medium text-gray-500">PHP 版本</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ phpVersion }}</dd>
          </div>
          
          <div>
            <dt class="text-sm font-medium text-gray-500">資料庫</dt>
            <dd class="mt-1 text-sm text-gray-900">MySQL</dd>
          </div>
          
          <div>
            <dt class="text-sm font-medium text-gray-500">快取系統</dt>
            <dd class="mt-1 text-sm text-gray-900">File Cache</dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- 系統設定 -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">系統設定</h2>
      </div>
      
      <div class="p-6">
        <div class="space-y-6">
          <!-- 註冊設定 -->
          <div>
            <label class="flex items-center">
              <input
                v-model="settings.allow_registration"
                type="checkbox"
                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-900">允許使用者註冊</span>
            </label>
            <p class="mt-1 text-sm text-gray-500">開啟後，新使用者可以自行註冊帳號</p>
          </div>
          
          <!-- 電子郵件驗證 -->
          <div>
            <label class="flex items-center">
              <input
                v-model="settings.email_verification"
                type="checkbox"
                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-900">需要電子郵件驗證</span>
            </label>
            <p class="mt-1 text-sm text-gray-500">新註冊的使用者需要驗證電子郵件才能使用系統</p>
          </div>
          
          <!-- 維護模式 -->
          <div>
            <label class="flex items-center">
              <input
                v-model="settings.maintenance_mode"
                type="checkbox"
                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-900">維護模式</span>
            </label>
            <p class="mt-1 text-sm text-gray-500">開啟後，只有管理員可以存取系統</p>
          </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-gray-200">
          <button
            @click="saveSettings"
            :disabled="isSaving"
            class="bg-primary-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
          >
            {{ isSaving ? '儲存中...' : '儲存設定' }}
          </button>
        </div>
      </div>
    </div>

    <!-- 成功訊息 -->
    <div v-if="successMessage" class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
      {{ successMessage }}
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'AdminSystem',
  setup() {
    const stats = ref({})
    const isLoading = ref(true)
    const isSaving = ref(false)
    const successMessage = ref('')
    const laravelVersion = ref('11.x')
    const phpVersion = ref('8.2+')
    
    const settings = reactive({
      allow_registration: true,
      email_verification: false,
      maintenance_mode: false
    })
    
    const fetchStats = async () => {
      try {
        const response = await axios.get('/api/admin/stats')
        stats.value = response.data
      } catch (error) {
        console.error('Failed to fetch stats:', error)
      } finally {
        isLoading.value = false
      }
    }
    
    const saveSettings = async () => {
      isSaving.value = true
      
      try {
        // 模擬 API 呼叫
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        successMessage.value = '設定已成功儲存'
        
        // 3秒後清除訊息
        setTimeout(() => {
          successMessage.value = ''
        }, 3000)
        
      } catch (error) {
        console.error('Failed to save settings:', error)
      } finally {
        isSaving.value = false
      }
    }
    
    onMounted(() => {
      fetchStats()
    })
    
    return {
      stats,
      isLoading,
      isSaving,
      successMessage,
      laravelVersion,
      phpVersion,
      settings,
      saveSettings
    }
  }
}
</script>