<template>
  <div class="min-h-screen bg-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-sm w-full space-y-8">
      <div>
        <div class="mx-auto h-16 w-16 flex items-center justify-center bg-primary-500 rounded-full">
          <svg class="h-10 w-10 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.93 10.7a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
          </svg>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          PurpleDesk
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          專案管理系統
        </p>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <!-- 錯誤訊息 -->
        <div v-if="errorMessage" class="mb-4">
          <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  登入失敗
                </h3>
                <div class="mt-2 text-sm text-red-700">
                  {{ errorMessage }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <div>
            <label for="login" class="sr-only">帳號或電子郵件</label>
            <input
              id="login"
              v-model="form.login"
              name="login"
              type="text"
              required
              class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
              placeholder="帳號或電子郵件"
            />
          </div>
          <div>
            <label for="password" class="sr-only">密碼</label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
              placeholder="密碼"
            />
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              name="remember-me"
              type="checkbox"
              class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
              記住我
            </label>
          </div>

          <div class="text-sm">
            <a href="#" class="font-medium text-primary-500 hover:text-primary-600">
              忘記密碼？
            </a>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="isLoading"
            class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-500 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg
                v-if="isLoading"
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ isLoading ? '登入中...' : '登入' }}
          </button>
        </div>

        <div class="text-center">
          <p class="text-sm text-gray-600">
            還沒有帳號？
            <router-link
              to="/register"
              class="font-medium text-primary-500 hover:text-primary-600"
            >
              立即註冊
            </router-link>
          </p>
        </div>

        <!-- 測試帳號 -->
        <div class="border-t border-gray-200 pt-6">
          <div class="text-center mb-4">
            <h3 class="text-sm font-medium text-gray-700">測試帳號</h3>
            <p class="text-xs text-gray-500 mt-1">點擊下方帳號快速登入</p>
          </div>
          
          <div class="space-y-2">
            <!-- 管理員帳號 -->
            <button
              @click="fillTestAccount('admin')"
              type="button"
              class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">管理員帳號</p>
                  <p class="text-xs text-gray-500">帳號: admin</p>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                  管理員
                </span>
              </div>
            </button>

            <!-- 一般用戶帳號 -->
            <button
              @click="fillTestAccount('user')"
              type="button"
              class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">一般用戶</p>
                  <p class="text-xs text-gray-500">帳號: vincent</p>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  用戶
                </span>
              </div>
            </button>

            <!-- 設計師帳號 -->
            <button
              @click="fillTestAccount('designer')"
              type="button"
              class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">設計師</p>
                  <p class="text-xs text-gray-500">電郵: sophia@cloudtech.com</p>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                  設計師
                </span>
              </div>
            </button>

            <!-- 自由工作者帳號 -->
            <button
              @click="fillTestAccount('freelancer')"
              type="button"
              class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">自由工作者</p>
                  <p class="text-xs text-gray-500">帳號: techwang</p>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  自由工作者
                </span>
              </div>
            </button>
          </div>
          
          <p class="text-xs text-gray-400 text-center mt-3">
            所有測試帳號密碼均為：<code class="bg-gray-100 px-1 rounded">password</code>
          </p>
        </div>

      </form>
    </div>
  </div>
</template>

<script>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'LoginPage',
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    
    const form = reactive({
      login: '',
      password: ''
    })
    
    const isLoading = ref(false)
    const errorMessage = ref('')
    
    const fillTestAccount = (type) => {
      const accounts = {
        admin: {
          login: 'admin',
          password: 'password'
        },
        user: {
          login: 'vincent',
          password: 'password'
        },
        designer: {
          login: 'sophia@cloudtech.com',
          password: 'password'
        },
        freelancer: {
          login: 'techwang',
          password: 'password'
        }
      }
      
      if (accounts[type]) {
        form.login = accounts[type].login
        form.password = accounts[type].password
      }
    }
    
    const handleLogin = async () => {
      if (isLoading.value) return
      
      isLoading.value = true
      errorMessage.value = ''
      
      try {
        await authStore.login(form.login, form.password)
        await router.push('/dashboard')
      } catch (error) {
        errorMessage.value = error.response?.data?.message || '登入失敗，請稍後再試'
      } finally {
        isLoading.value = false
      }
    }
    
    return {
      form,
      isLoading,
      errorMessage,
      fillTestAccount,
      handleLogin
    }
  }
}
</script>