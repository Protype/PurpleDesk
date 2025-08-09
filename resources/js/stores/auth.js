import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token'),
    isAuthenticated: false
  }),

  getters: {
    getUser: (state) => state.user,
    isLoggedIn: (state) => !!state.token
  },

  actions: {
    async login(email, password) {
      try {
        const response = await axios.post('/api/login', {
          email,
          password
        })

        const { user, token } = response.data
        
        this.user = user
        this.token = token
        this.isAuthenticated = true
        
        // 儲存 token 到 localStorage
        localStorage.setItem('auth_token', token)
        
        // 設定 axios 預設 header
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return response.data
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    async register(userData) {
      try {
        const response = await axios.post('/api/register', userData)
        
        const { user, token } = response.data
        
        this.user = user
        this.token = token
        this.isAuthenticated = true
        
        localStorage.setItem('auth_token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return response.data
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    async logout() {
      try {
        if (this.token) {
          await axios.post('/api/logout')
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
      }
    },

    async fetchUser() {
      try {
        if (!this.token) {
          throw new Error('No token available')
        }
        
        const response = await axios.get('/api/me')
        this.user = response.data.user
        this.isAuthenticated = true
        
        return response.data.user
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
    },

    async initializeAuth() {
      const token = localStorage.getItem('auth_token')
      
      if (token) {
        this.token = token
        this.isAuthenticated = true
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        // 嘗試取得使用者資訊
        try {
          await this.fetchUser()
        } catch (error) {
          this.clearAuth()
        }
      }
    }
  }
})