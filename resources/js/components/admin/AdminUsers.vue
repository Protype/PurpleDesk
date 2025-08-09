<template>
  <div class="bg-white shadow rounded-lg">
    <!-- 頁面標題 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">使用者管理</h1>
          <p class="mt-1 text-sm text-gray-600">管理系統中的所有使用者帳號</p>
        </div>
        <button 
          @click="showCreateModal = true"
          class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium"
        >
          新增使用者
        </button>
      </div>
    </div>

    <!-- 搜尋列 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex space-x-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="搜尋使用者..."
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
        </div>
        <select
          v-model="selectedOrganization"
          class="px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
        >
          <option value="">所有單位</option>
          <option v-for="org in organizations" :key="org.id" :value="org.id">
            {{ org.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- 使用者列表 -->
    <div class="overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              使用者
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              所屬單位
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              註冊時間
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              狀態
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              操作
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in filteredUsers" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-primary-600 flex items-center justify-center overflow-hidden">
                  <img
                    v-if="user.avatar_url"
                    :src="user.avatar_url"
                    :alt="user.name"
                    class="h-full w-full object-cover"
                  />
                  <span v-else class="text-white text-sm font-medium">
                    {{ getUserInitials(user) }}
                  </span>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.display_name || user.name }}
                  </div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <span v-if="user.organization" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                {{ user.organization.name }}
              </span>
              <span v-else class="text-gray-500">無</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                啟用
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button 
                @click="editUser(user)"
                class="text-primary-600 hover:text-primary-900 mr-3"
              >
                編輯
              </button>
              <button class="text-red-600 hover:text-red-900">停用</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 載入狀態 -->
    <div v-if="isLoading" class="flex flex-col items-center justify-center py-12">
      <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-gray-500 mt-2">載入中...</p>
    </div>

    <!-- 空狀態 -->
    <div v-else-if="filteredUsers.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">沒有找到使用者</h3>
      <p class="mt-1 text-sm text-gray-500">請嘗試調整搜尋條件</p>
    </div>

    <!-- 新增/編輯使用者 Modal -->
    <div v-if="showCreateModal || editingUser" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ editingUser ? '編輯使用者' : '新增使用者' }}
            </h3>
            
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">姓名 *</label>
                <input
                  v-model="userForm.name"
                  type="text"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="userFormErrors.name" class="text-sm text-red-600">{{ userFormErrors.name[0] }}</span>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">暱稱</label>
                <input
                  v-model="userForm.display_name"
                  type="text"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">電子郵件 *</label>
                <input
                  v-model="userForm.email"
                  type="email"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="userFormErrors.email" class="text-sm text-red-600">{{ userFormErrors.email[0] }}</span>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">所屬單位</label>
                <select
                  v-model="userForm.organization_id"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                >
                  <option value="">請選擇單位</option>
                  <option v-for="org in organizations" :key="org.id" :value="org.id">
                    {{ org.name }}
                  </option>
                </select>
              </div>
              
              <div v-if="!editingUser">
                <label class="block text-sm font-medium text-gray-700">密碼 *</label>
                <input
                  v-model="userForm.password"
                  type="password"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
                <span v-if="userFormErrors.password" class="text-sm text-red-600">{{ userFormErrors.password[0] }}</span>
              </div>
              
              <div v-if="!editingUser">
                <label class="block text-sm font-medium text-gray-700">確認密碼 *</label>
                <input
                  v-model="userForm.password_confirmation"
                  type="password"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="saveUser"
              :disabled="isCreatingUser || !userForm.name || !userForm.email || (!editingUser && (!userForm.password || !userForm.password_confirmation))"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ isCreatingUser ? '處理中...' : (editingUser ? '更新' : '建立') }}
            </button>
            <button
              @click="cancelUserEdit"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              取消
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'AdminUsers',
  setup() {
    const users = ref([])
    const organizations = ref([])
    const isLoading = ref(true)
    const searchQuery = ref('')
    const selectedOrganization = ref('')
    const showCreateModal = ref(false)
    const editingUser = ref(null)
    const isCreatingUser = ref(false)
    const userFormErrors = ref({})
    
    const userForm = ref({
      name: '',
      display_name: '',
      email: '',
      password: '',
      password_confirmation: '',
      organization_id: ''
    })
    
    const getUserInitials = (user) => {
      if (!user) return ''
      const name = user.display_name || user.name
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
    
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }
    
    const filteredUsers = computed(() => {
      let filtered = users.value
      
      // 依搜尋關鍵字篩選
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(user => 
          user.name.toLowerCase().includes(query) ||
          user.email.toLowerCase().includes(query) ||
          (user.display_name && user.display_name.toLowerCase().includes(query))
        )
      }
      
      // 依單位篩選
      if (selectedOrganization.value) {
        filtered = filtered.filter(user => 
          user.organization_id == selectedOrganization.value
        )
      }
      
      return filtered
    })
    
    const fetchUsers = async () => {
      try {
        isLoading.value = true
        const response = await axios.get('/api/admin/users')
        users.value = response.data
      } catch (error) {
        console.error('Failed to fetch users:', error)
      } finally {
        isLoading.value = false
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
    
    const resetUserForm = () => {
      userForm.value = {
        name: '',
        display_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        organization_id: ''
      }
      userFormErrors.value = {}
    }
    
    const cancelUserEdit = () => {
      showCreateModal.value = false
      editingUser.value = null
      resetUserForm()
    }
    
    const editUser = (user) => {
      editingUser.value = user
      userForm.value = {
        name: user.name,
        display_name: user.display_name || '',
        email: user.email,
        password: '',
        password_confirmation: '',
        organization_id: user.organization_id || ''
      }
      userFormErrors.value = {}
    }
    
    const saveUser = async () => {
      if (isCreatingUser.value) return
      
      isCreatingUser.value = true
      userFormErrors.value = {}
      
      try {
        if (editingUser.value) {
          // 更新使用者
          const updateData = {
            name: userForm.value.name,
            display_name: userForm.value.display_name,
            email: userForm.value.email,
            organization_id: userForm.value.organization_id || null
          }
          
          await axios.put(`/api/admin/users/${editingUser.value.id}`, updateData)
        } else {
          // 新增使用者
          await axios.post('/api/admin/users', userForm.value)
        }
        
        await fetchUsers()
        cancelUserEdit()
        
      } catch (error) {
        if (error.response?.status === 422) {
          userFormErrors.value = error.response.data.errors || {}
        } else {
          alert('操作失敗，請稍後再試')
        }
      } finally {
        isCreatingUser.value = false
      }
    }
    
    onMounted(async () => {
      await Promise.all([fetchUsers(), fetchOrganizations()])
    })
    
    return {
      users,
      organizations,
      isLoading,
      searchQuery,
      selectedOrganization,
      filteredUsers,
      showCreateModal,
      editingUser,
      isCreatingUser,
      userForm,
      userFormErrors,
      getUserInitials,
      formatDate,
      editUser,
      saveUser,
      cancelUserEdit
    }
  }
}
</script>