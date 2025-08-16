<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 導航欄 -->
    <AppNavbar />

    <!-- 主要內容 -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="border-4 border-dashed border-gray-200 rounded-lg p-8">
          <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
              <DocumentTextIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">歡迎使用 PurpleDesk</h1>
            <p class="text-gray-600 mb-6">您的專案管理系統已準備就緒</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto">
              <div
                v-for="feature in features"
                :key="feature.id"
                class="bg-white p-6 rounded-lg shadow"
              >
                <div class="text-primary-600 mb-2 text-center">
                  <component
                    v-if="feature.iconType === 'hero'"
                    :is="feature.icon"
                    class="h-8 w-8 mx-auto"
                  />
                  <i
                    v-else-if="feature.iconType === 'bootstrap'"
                    :class="[feature.icon, 'text-3xl']"
                  ></i>
                </div>
                <h3 class="font-medium text-gray-900">{{ feature.title }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ feature.status }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { ref } from 'vue'
import AppNavbar from '../components/AppNavbar.vue'
import { DocumentTextIcon, ClipboardCheckIcon } from '@heroicons/vue/outline'

export default {
  name: 'Dashboard',
  components: {
    AppNavbar,
    DocumentTextIcon,
    ClipboardCheckIcon
  },
  setup() {
    const features = ref([
      {
        id: 1,
        title: '專案管理',
        status: '即將推出',
        icon: 'ClipboardCheckIcon',
        iconType: 'hero'
      },
      {
        id: 2,
        title: '任務追蹤',
        status: '即將推出',
        icon: 'DocumentTextIcon',
        iconType: 'hero'
      },
      {
        id: 3,
        title: '團隊協作',
        status: '即將推出',
        icon: 'bi-people-fill',
        iconType: 'bootstrap'
      }
    ])

    return {
      features
    }
  }
}
</script>