<template>
  <nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <router-link to="/dashboard" class="flex-shrink-0 flex items-center">
            <div class="h-8 w-8 bg-primary-500 rounded-full flex items-center justify-center">
              <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.93 10.7a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
              </svg>
            </div>
            <span class="ml-2 text-xl font-bold text-gray-900">PurpleDesk</span>
            <span v-if="isAdmin" class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">管理員</span>
          </router-link>
        </div>
        
        <!-- 個人選單 -->
        <div class="flex items-center space-x-4">
          <!-- 通知圖示 -->
          <button class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100">
            <BellIcon class="h-5 w-5" />
          </button>

          <!-- 個人選單下拉 -->
          <div class="relative">
            <button
              @click="toggleUserMenu"
              class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
              <!-- 使用者頭像 -->
              <IconDisplay 
                :iconData="user?.getAvatarData?.()" 
                size="8"
                :title="user?.getDisplayName?.()"
              />
              
              <!-- 下拉箭頭 -->
              <ChevronDownIcon class="h-4 w-4 text-gray-500" :class="{ 'rotate-180': showUserMenu }" />
            </button>

            <!-- 下拉選單 -->
            <div
              v-show="showUserMenu"
              class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
              @click.away="showUserMenu = false"
            >
              <!-- 使用者資訊 -->
              <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                  <IconDisplay 
                    :iconData="user?.getAvatarData?.()" 
                    size="10"
                    :title="user?.getDisplayName?.()"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ user?.getDisplayName?.() || '' }}
                    </p>
                    <p class="text-sm text-gray-500 truncate">
                      {{ user?.email }}
                    </p>
                    <p v-if="user?.organizations?.length > 0" class="text-xs text-gray-400 truncate">
                      {{ user.organizations[0].name }}
                      <span v-if="user.organizations.length > 1" class="text-gray-300">
                        +{{ user.organizations.length - 1 }} 個組織
                      </span>
                    </p>
                  </div>
                </div>
              </div>

              <!-- 選單項目 -->
              <div class="py-1">
                <!-- 個人資料 -->
                <router-link
                  to="/profile"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false"
                >
                  <i class="bi bi-person-fill mr-3 text-base text-gray-400"></i>
                  個人資料
                </router-link>

                <!-- 設定 -->
                <router-link
                  to="/settings"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false"
                >
                  <CogIcon class="mr-3 h-4 w-4 text-gray-400" />
                  設定
                </router-link>

                <!-- 管理員專用選項 -->
                <template v-if="isAdmin">
                  <div class="border-t border-gray-200 my-1"></div>
                  
                  <router-link
                    to="/admin/users"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    <i class="bi bi-people-fill mr-3 text-base text-gray-400"></i>
                    使用者管理
                  </router-link>
                  
                  <router-link
                    to="/admin/organizations"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    <OfficeBuildingIcon class="mr-3 h-4 w-4 text-gray-400" />
                    組織管理
                  </router-link>
                  
                  <router-link
                    to="/admin/system"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    <CogIcon class="mr-3 h-4 w-4 text-gray-400" />
                    系統設定
                  </router-link>
                </template>

                <div class="border-t border-gray-200 my-1"></div>
                
                <!-- 登出 -->
                <button
                  @click="handleLogout"
                  class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left"
                >
                  <LogoutIcon class="mr-3 h-4 w-4 text-gray-400" />
                  登出
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { BellIcon, ChevronDownIcon, CogIcon, OfficeBuildingIcon, LogoutIcon } from '@heroicons/vue/outline'
import IconDisplay from './common/IconDisplay.vue'

// Composables
const router = useRouter()
const authStore = useAuthStore()

// Reactive state
const showUserMenu = ref(false)

// Computed properties
const user = computed(() => authStore.user)

const isAdmin = computed(() => {
  return user.value?.isAdmin?.() === true
})

// Helper functions
// getUserInitials function moved to User model

// Event handlers
const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleLogout = async () => {
  try {
    showUserMenu.value = false
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('登出錯誤:', error)
  }
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showUserMenu.value = false
  }
}

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>