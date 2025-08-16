<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <AppNavbar />

    <!-- 主要內容 -->
    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white shadow rounded-lg">
          <!-- 頁面標題 -->
          <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">個人資料</h1>
            <p class="mt-1 text-sm text-gray-600">管理您的帳號設定與個人資訊</p>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-6 p-6">
            <!-- 頭像設定 -->
            <ImageField
              ref="imageField"
              label="頭像"
              :current-image-url="user?.avatar_url"
              :image-alt="user?.display_name || user?.full_name || user?.account"
              :initials="getUserInitials(user)"
              size="large"
              shape="circle"
              :show-save-button="true"
              :show-clear-button="true"
              save-button-text="儲存"
              clear-button-text="清除"
              remove-button-text="移除頭像"
              remove-confirm-title="移除頭像"
              remove-confirm-message="確定要移除頭像嗎？此操作無法復原。"
              :uploading="isLoading"
              :saving="isSavingAvatar"
              :clearing="isClearingAvatar"
              :removing="isRemovingAvatar"
              @mode-changed="handleModeChanged"
              @settings-changed="handleSettingsChanged"
              @file-selected="handleAvatarSelected"
              @file-error="handleFileError"
              @save="handleAvatarSave"
              @clear="handleAvatarClear"
              @remove="handleAvatarRemove"
              @success="handleSuccess"
              @error="handleError"
            />

            <!-- 基本資料 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="display_name" class="block text-sm font-medium text-gray-700">顯示名稱 *</label>
                <input
                  id="display_name"
                  v-model="form.display_name"
                  type="text"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="errors.display_name" class="text-sm text-red-600">{{ errors.display_name[0] }}</span>
              </div>

              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">姓名</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="errors.name" class="text-sm text-red-600">{{ errors.name[0] }}</span>
              </div>

              <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-700">電子郵件 *</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="errors.email" class="text-sm text-red-600">{{ errors.email[0] }}</span>
              </div>

              <div class="md:col-span-2" v-if="user?.organizations?.length > 0">
                <label class="block text-sm font-medium text-gray-700">所屬組織</label>
                <div class="mt-1 space-y-2">
                  <div v-for="org in user.organizations" :key="org.id" class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <span class="text-sm">{{ org.name }}</span>
                    <span class="text-xs text-gray-500 px-2 py-1 bg-white rounded">
                      {{ org.pivot.role === 'owner' ? '擁有者' : org.pivot.role === 'admin' ? '管理員' : '成員' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- 密碼更改 -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">更改密碼</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                  <label for="current_password" class="block text-sm font-medium text-gray-700">目前密碼</label>
                  <input
                    id="current_password"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    placeholder="如不更改密碼請留空"
                  />
                  <span v-if="errors.current_password" class="text-sm text-red-600">{{ errors.current_password[0] }}</span>
                </div>

                <div>
                  <label for="password" class="block text-sm font-medium text-gray-700">新密碼</label>
                  <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    placeholder="如不更改密碼請留空"
                  />
                  <span v-if="errors.password" class="text-sm text-red-600">{{ errors.password[0] }}</span>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">確認新密碼</label>
                  <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    placeholder="再次輸入新密碼"
                  />
                </div>
              </div>
            </div>

            <!-- 操作按鈕 -->
            <div class="border-t border-gray-200 pt-6 flex justify-end space-x-3">
              <button
                type="submit"
                :disabled="isLoading"
                class="bg-primary-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ isLoading ? '儲存中...' : '儲存變更' }}
              </button>
            </div>
          </form>


          <!-- 錯誤訊息 -->
          <div v-if="errorMessage" class="mx-6 mb-6">
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <XCircleIcon class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-red-800">
                    更新失敗
                  </h3>
                  <div class="mt-2 text-sm text-red-700">
                    {{ errorMessage }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    
    <!-- 成功訊息 Toast -->
    <div v-if="successMessage" class="fixed bottom-4 right-4 z-50">
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg max-w-sm">
        <div class="flex items-center">
          <CheckCircleIcon class="h-5 w-5 text-green-500 mr-2" />
          <span class="text-sm font-medium">{{ successMessage }}</span>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script>
import { reactive, ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import AppNavbar from './AppNavbar.vue'
import axios from 'axios'
import { XCircleIcon, CheckCircleIcon } from '@heroicons/vue/outline'
import ConfirmDialog from './common/ConfirmDialog.vue'
import ImageField from './common/ImageField.vue'

export default {
  name: 'ProfilePage',
  components: {
    AppNavbar,
    XCircleIcon,
    CheckCircleIcon,
    ConfirmDialog,
    ImageField
  },
  setup() {
    const authStore = useAuthStore()
    const isLoading = ref(false)
    const successMessage = ref('')
    const errorMessage = ref('')
    const errors = ref({})
    const organizations = ref([])
    const avatarPreview = ref(null)
    const showRemoveAvatarConfirm = ref(false)
    const isRemovingAvatar = ref(false)
    const isSavingAvatar = ref(false)
    const isClearingAvatar = ref(false)
    
    const user = computed(() => authStore.user)
    
    const form = reactive({
      name: '',
      display_name: '',
      email: '',
      current_password: '',
      password: '',
      password_confirmation: '',
      avatar: null,
      avatar_icon: null,
      avatar_type: null,
      avatar_settings: null,
      remove_avatar: false
    })
    
    const getUserInitials = (user) => {
      if (!user) return ''
      // 使用 User Model 的 getInitials 方法
      if (user.getInitials && typeof user.getInitials === 'function') {
        return user.getInitials()
      }
      // 回退邏輯：display_name → full_name → account → 空字串
      const name = user.display_name || user.full_name || user.account
      if (!name) return ''
      
      // 檢查是否含有中文字符
      if (/[\u4e00-\u9fa5]/.test(name)) {
        return name.slice(0, 2);
      }
      
      // 英文名稱處理
      const words = name.split(' ').filter(word => word.length > 0);
      if (words.length > 1) {
        // 多個單字取首字母
        return words
          .map(n => n[0])
          .join('')
          .toUpperCase()
          .slice(0, 2);
      } else {
        // 單一單字取前兩個字母
        return name.slice(0, 2).toUpperCase();
      }
    }
    
    const fetchOrganizations = async () => {
      try {
        const response = await axios.get('/api/organizations')
        organizations.value = response.data
      } catch (error) {
        console.error('Failed to fetch organizations:', error)
      }
    }
    
    const handleAvatarSelected = (file) => {
      console.log('handleAvatarSelected called with:', file)
      if (file && file instanceof File) {
        console.log('Valid file selected:', file.name, file.type, file.size)
        form.avatar = file
        errorMessage.value = '' // 清除任何現有錯誤訊息
        // 注意：不再自動儲存，而是等待用戶點擊儲存按鈕
      } else {
        console.log('Invalid file or non-file data:', file)
      }
    }
    
    const handleFileError = (error) => {
      errorMessage.value = error
    }
    
    const handleAvatarUpload = async (file) => {
      // 僅設定檔案到表單狀態，不自動儲存
      form.avatar = file
      console.log('File uploaded to form state, waiting for manual save')
    }
    
    const handleAvatarRemove = async () => {
      await confirmRemoveAvatar()
    }
    
    const handleSuccess = (message) => {
      successMessage.value = message
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
    
    const handleError = (error) => {
      errorMessage.value = error
    }
    
    const handleAvatarSave = async () => {
      if (!form.avatar && !form.remove_avatar && !form.avatar_icon && !form.avatar_settings) {
        errorMessage.value = '沒有需要儲存的頭像變更'
        return
      }
      
      isSavingAvatar.value = true
      
      try {
        const formData = new FormData()
        formData.append('account', user.value.account || '')
        formData.append('full_name', form.name || '')
        formData.append('display_name', form.display_name)
        formData.append('email', form.email)
        
        if (form.avatar) {
          formData.append('avatar', form.avatar)
        }
        if (form.avatar_icon) {
          formData.append('avatar_icon', form.avatar_icon)
          formData.append('avatar_type', 'icon')
        }
        if (form.avatar_settings) {
          formData.append('avatar_settings', JSON.stringify(form.avatar_settings))
          formData.append('avatar_type', 'initial')
        }
        if (form.remove_avatar) {
          formData.append('remove_avatar', '1')
        }
        
        const response = await axios.post('/api/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        // 更新本地用戶資料
        await authStore.fetchUser()
        
        // 清空頭像相關狀態
        form.avatar = null
        form.avatar_icon = null
        form.avatar_type = null
        form.avatar_settings = null
        form.remove_avatar = false
        
        successMessage.value = '頭像已成功儲存'
        setTimeout(() => {
          successMessage.value = ''
        }, 3000)
        
      } catch (error) {
        console.error('儲存頭像失敗:', error)
        const errorMsg = error.response?.data?.message || '儲存頭像失敗，請稍後再試'
        errorMessage.value = errorMsg
      } finally {
        isSavingAvatar.value = false
      }
    }
    
    const handleAvatarClear = async () => {
      isClearingAvatar.value = true
      
      try {
        const formData = new FormData()
        formData.append('account', user.value.account || '')
        formData.append('full_name', form.name || '')
        formData.append('display_name', form.display_name)
        formData.append('email', form.email)
        formData.append('remove_avatar', '1')
        
        const response = await axios.post('/api/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        // 清空本地狀態
        form.avatar = null
        form.remove_avatar = false
        
        // 更新本地用戶資料
        await authStore.fetchUser()
        
        successMessage.value = '頭像已成功清除'
        setTimeout(() => {
          successMessage.value = ''
        }, 3000)
        
      } catch (error) {
        console.error('清除頭像失敗:', error)
        const errorMsg = error.response?.data?.message || '清除頭像失敗，請稍後再試'
        errorMessage.value = errorMsg
      } finally {
        isClearingAvatar.value = false
      }
    }
    
    const handleModeChanged = (data) => {
      // 處理模式變更 (字母/圖標/上傳)
      console.log('Mode changed:', data)
      // 注意：不再自動儲存，只記錄變更
    }
    
    const handleSettingsChanged = (data) => {
      // 處理設定變更 (顏色、圖標等)
      console.log('Settings changed:', data)
      // 儲存設定變更到表單狀態
      if (data.type === 'icon' && data.icon) {
        // 將圖標資訊存到表單中用於後續儲存
        form.avatar_icon = data.icon
        form.avatar_type = 'icon'
      } else if (data.type === 'initial' && data.settings) {
        form.avatar_settings = data.settings
        form.avatar_type = 'initial'
      }
    }
    
    const showRemoveAvatarDialog = () => {
      showRemoveAvatarConfirm.value = true
    }
    
    const confirmRemoveAvatar = async () => {
      isRemovingAvatar.value = true
      
      try {
        // 立即發送請求到後端移除頭像
        const formData = new FormData()
        formData.append('account', user.value.account || '')
        formData.append('full_name', form.name || '')
        formData.append('display_name', form.display_name)
        formData.append('email', form.email)
        formData.append('remove_avatar', '1')
        
        const response = await axios.post('/api/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        // 清空本地狀態
        form.avatar = null
        form.remove_avatar = false
        avatarPreview.value = null
        
        // 更新本地用戶資料
        await authStore.fetchUser()
        
        successMessage.value = '頭像已成功移除'
        
        // 關閉對話框
        showRemoveAvatarConfirm.value = false
        
        // 3秒後清除成功訊息
        setTimeout(() => {
          successMessage.value = ''
        }, 3000)
        
      } catch (error) {
        console.error('移除頭像失敗:', error)
        const errorMsg = error.response?.data?.message || '移除頭像失敗，請稍後再試'
        errorMessage.value = errorMsg
      } finally {
        isRemovingAvatar.value = false
      }
    }
    
    const cancelRemoveAvatar = () => {
      showRemoveAvatarConfirm.value = false
    }
    
    const handleSubmit = async () => {
      if (isLoading.value) {
        console.log('Already loading, skipping')
        return
      }
      
      isLoading.value = true
      errorMessage.value = ''
      successMessage.value = ''
      errors.value = {}
      
      try {
        const formData = new FormData()
        formData.append('account', user.value.account || '')
        formData.append('full_name', form.name || '')
        formData.append('display_name', form.display_name)
        formData.append('email', form.email)
        
        if (form.current_password) {
          formData.append('current_password', form.current_password)
        }
        if (form.password) {
          formData.append('password', form.password)
          formData.append('password_confirmation', form.password_confirmation)
        }
        if (form.avatar) {
          formData.append('avatar', form.avatar)
        }
        if (form.remove_avatar) {
          formData.append('remove_avatar', '1')
        }
        
        const response = await axios.post('/api/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        // 更新本地用戶資料
        await authStore.fetchUser()
        
        successMessage.value = '個人資料已成功更新'
        
        // 3秒後清除成功訊息
        setTimeout(() => {
          successMessage.value = ''
        }, 3000)
        
        // 清空密碼欄位和頭像相關狀態
        form.current_password = ''
        form.password = ''
        form.password_confirmation = ''
        form.avatar = null
        form.avatar_icon = null
        form.avatar_type = null
        form.avatar_settings = null
        form.remove_avatar = false
        avatarPreview.value = null
        
      } catch (error) {
        if (error.response?.status === 422) {
          errors.value = error.response.data.errors || {}
        } else {
          errorMessage.value = error.response?.data?.message || '更新失敗，請稍後再試'
        }
      } finally {
        isLoading.value = false
      }
    }
    
    // 初始化表單資料
    onMounted(async () => {
      await fetchOrganizations()
      
      if (user.value) {
        form.name = user.value.full_name || ''
        form.display_name = user.value.display_name || ''
        form.email = user.value.email || ''
      }
    })
    
    return {
      user,
      form,
      isLoading,
      successMessage,
      errorMessage,
      errors,
      organizations,
      avatarPreview,
      isSavingAvatar,
      isClearingAvatar,
      getUserInitials,
      handleAvatarSelected,
      handleFileError,
      handleAvatarUpload,
      handleAvatarSave,
      handleAvatarClear,
      handleAvatarRemove,
      handleSuccess,
      handleError,
      handleModeChanged,
      handleSettingsChanged,
      showRemoveAvatarDialog,
      confirmRemoveAvatar,
      cancelRemoveAvatar,
      showRemoveAvatarConfirm,
      isRemovingAvatar,
      handleSubmit
    }
  }
}
</script>