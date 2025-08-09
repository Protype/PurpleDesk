<template>
  <div class="bg-white shadow rounded-lg">
    <!-- 頁面標題 -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">單位管理</h1>
          <p class="mt-1 text-sm text-gray-600">管理系統中的所有組織單位</p>
        </div>
        <button 
          @click="showCreateModal = true"
          class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium"
        >
          新增單位
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
            placeholder="搜尋單位..."
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          />
        </div>
      </div>
    </div>

    <!-- 單位列表 -->
    <div class="overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              單位資訊
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              成員數量
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              建立時間
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              操作
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="org in filteredOrganizations" :key="org.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded bg-primary-100 flex items-center justify-center overflow-hidden">
                  <img
                    v-if="org.logo_url"
                    :src="org.logo_url"
                    :alt="org.name"
                    class="h-full w-full object-cover"
                  />
                  <svg v-else class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ org.name }}
                  </div>
                  <div class="text-sm text-gray-500">{{ org.description }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ org.users_count || 0 }} 位成員
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(org.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button 
                @click="editOrganization(org)"
                class="text-primary-600 hover:text-primary-900 mr-3"
              >
                編輯
              </button>
              <button 
                @click="deleteOrganization(org)"
                class="text-red-600 hover:text-red-900"
                :disabled="org.users_count > 0"
                :class="{ 'opacity-50 cursor-not-allowed': org.users_count > 0 }"
              >
                刪除
              </button>
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
    <div v-else-if="filteredOrganizations.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">沒有找到單位</h3>
      <p class="mt-1 text-sm text-gray-500">請嘗試調整搜尋條件或新增單位</p>
    </div>

    <!-- 新增/編輯單位 Modal（簡化版） -->
    <div v-if="showCreateModal || editingOrganization" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ editingOrganization ? '編輯單位' : '新增單位' }}
            </h3>
            
            <div class="space-y-4">
              <!-- Logo 上傳 -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">組織 Logo</label>
                <div class="flex items-start space-x-4">
                  <div class="h-16 w-16 bg-primary-100 rounded flex items-center justify-center overflow-hidden">
                    <img
                      v-if="logoPreview || (editingOrganization?.logo_url)"
                      :src="logoPreview || editingOrganization.logo_url"
                      :alt="formData.name"
                      class="h-full w-full object-cover"
                    />
                    <svg v-else class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  
                  <div class="flex-1">
                    <div
                      ref="logoDropZone"
                      @drop="handleLogoDrop"
                      @dragover="handleLogoDragOver"
                      @dragenter="handleLogoDragEnter"
                      @dragleave="handleLogoDragLeave"
                      :class="{
                        'border-primary-500 bg-primary-50': isLogoDragOver,
                        'border-gray-300': !isLogoDragOver
                      }"
                      class="border-2 border-dashed rounded-lg p-3 text-center transition-colors cursor-pointer hover:border-primary-400 hover:bg-primary-25"
                      @click="$refs.logoFileInput.click()"
                    >
                      <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                      </svg>
                      <p class="mt-1 text-xs text-gray-600">
                        <span class="font-medium text-primary-500">點擊上傳</span>
                        或拖曳檔案至此
                      </p>
                      <p class="text-xs text-gray-500">支援 JPG, PNG 格式，檔案大小不超過 2MB</p>
                    </div>
                    
                    <input
                      ref="logoFileInput"
                      type="file"
                      accept="image/*"
                      @change="handleLogoFileChange"
                      class="hidden"
                    />
                  </div>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">單位名稱</label>
                <input
                  v-model="formData.name"
                  type="text"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">描述</label>
                <textarea
                  v-model="formData.description"
                  rows="3"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                ></textarea>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="saveOrganization"
              :disabled="!formData.name"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ editingOrganization ? '更新' : '建立' }}
            </button>
            <button
              @click="cancelEdit"
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
import { ref, computed, onMounted, reactive } from 'vue'
import axios from 'axios'

export default {
  name: 'AdminOrganizations',
  setup() {
    const organizations = ref([])
    const isLoading = ref(true)
    const searchQuery = ref('')
    const showCreateModal = ref(false)
    const editingOrganization = ref(null)
    const logoPreview = ref(null)
    const isLogoDragOver = ref(false)
    const logoFile = ref(null)
    
    const formData = reactive({
      name: '',
      description: ''
    })
    
    const filteredOrganizations = computed(() => {
      if (!searchQuery.value) return organizations.value
      
      const query = searchQuery.value.toLowerCase()
      return organizations.value.filter(org => 
        org.name.toLowerCase().includes(query) ||
        (org.description && org.description.toLowerCase().includes(query))
      )
    })
    
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }
    
    const fetchOrganizations = async () => {
      try {
        isLoading.value = true
        const response = await axios.get('/api/admin/organizations')
        organizations.value = response.data
      } catch (error) {
        console.error('Failed to fetch organizations:', error)
      } finally {
        isLoading.value = false
      }
    }
    
    const validateAndProcessLogoFile = (file) => {
      // 驗證檔案大小 (2MB)
      if (file.size > 2 * 1024 * 1024) {
        alert('檔案大小不能超過 2MB')
        return false
      }
      
      // 驗證檔案類型
      if (!file.type.startsWith('image/')) {
        alert('請選擇圖片檔案')
        return false
      }
      
      logoFile.value = file
      
      // 產生預覽圖
      const reader = new FileReader()
      reader.onload = (e) => {
        logoPreview.value = e.target.result
      }
      reader.readAsDataURL(file)
      
      return true
    }
    
    const handleLogoFileChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        validateAndProcessLogoFile(file)
      }
    }
    
    // Logo 拖曳事件處理
    const handleLogoDragEnter = (e) => {
      e.preventDefault()
      isLogoDragOver.value = true
    }
    
    const handleLogoDragOver = (e) => {
      e.preventDefault()
      isLogoDragOver.value = true
    }
    
    const handleLogoDragLeave = (e) => {
      e.preventDefault()
      isLogoDragOver.value = false
    }
    
    const handleLogoDrop = (e) => {
      e.preventDefault()
      isLogoDragOver.value = false
      
      const files = e.dataTransfer.files
      if (files.length > 0) {
        validateAndProcessLogoFile(files[0])
      }
    }

    const editOrganization = (org) => {
      editingOrganization.value = org
      formData.name = org.name
      formData.description = org.description || ''
      logoPreview.value = null
      logoFile.value = null
    }
    
    const cancelEdit = () => {
      showCreateModal.value = false
      editingOrganization.value = null
      formData.name = ''
      formData.description = ''
      logoPreview.value = null
      logoFile.value = null
    }
    
    const saveOrganization = async () => {
      try {
        const formDataToSend = new FormData()
        formDataToSend.append('name', formData.name)
        formDataToSend.append('description', formData.description || '')
        
        if (logoFile.value) {
          formDataToSend.append('avatar', logoFile.value)
        }
        
        if (editingOrganization.value) {
          // 更新
          await axios.post(`/api/organizations/${editingOrganization.value.id}`, formDataToSend, {
            headers: { 'Content-Type': 'multipart/form-data' },
            params: { '_method': 'PUT' }
          })
        } else {
          // 新增
          await axios.post('/api/organizations', formDataToSend, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        }
        
        await fetchOrganizations()
        cancelEdit()
      } catch (error) {
        console.error('Failed to save organization:', error)
        alert('操作失敗，請稍後再試')
      }
    }
    
    const deleteOrganization = async (org) => {
      if (!confirm(`確定要刪除「${org.name}」嗎？`)) return
      
      try {
        await axios.delete(`/api/organizations/${org.id}`)
        await fetchOrganizations()
      } catch (error) {
        console.error('Failed to delete organization:', error)
        alert('刪除失敗，請稍後再試')
      }
    }
    
    onMounted(() => {
      fetchOrganizations()
    })
    
    return {
      organizations,
      isLoading,
      searchQuery,
      showCreateModal,
      editingOrganization,
      formData,
      logoPreview,
      isLogoDragOver,
      filteredOrganizations,
      formatDate,
      editOrganization,
      cancelEdit,
      saveOrganization,
      deleteOrganization,
      handleLogoFileChange,
      handleLogoDragEnter,
      handleLogoDragOver,
      handleLogoDragLeave,
      handleLogoDrop
    }
  }
}
</script>