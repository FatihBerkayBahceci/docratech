import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'

// Import global styles
import '../css/app.css'

// Create app instance
const app = createApp(App)

// Create Pinia instance with state persistence
const pinia = createPinia()

// Use plugins
app.use(pinia)
app.use(router)

// Initialize auth store to restore authentication state
import { useAuthStore } from './stores/auth'
const authStore = useAuthStore()
authStore.initialize()

// Error tracking to prevent spam
const errorCache = new Map()
const ERROR_COOLDOWN = 5000 // 5 seconds

// Global error handler for production-ready error tracking
app.config.errorHandler = (err, instance, info) => {
  // Create unique error key to prevent spam
  const errorKey = `${err.message}-${info}`
  const now = Date.now()
  
  // Check if we've seen this error recently
  if (errorCache.has(errorKey)) {
    const lastSeen = errorCache.get(errorKey)
    if (now - lastSeen < ERROR_COOLDOWN) {
      return // Skip logging this error
    }
  }
  
  // Update error cache
  errorCache.set(errorKey, now)
  
  // Clean old entries
  for (const [key, timestamp] of errorCache.entries()) {
    if (now - timestamp > ERROR_COOLDOWN * 2) {
      errorCache.delete(key)
    }
  }
  
  // Log error with context
  console.group('❌ DocraTech Error')
  console.error('Message:', err.message)
  console.error('Error:', err)
  console.error('Context:', info)
  
  // Add component context if available
  if (instance && instance.$options) {
    console.error('Component:', instance.$options.name || instance.$options.__name || 'Anonymous')
  }
  
  // Add helpful debugging info for common errors
  if (err.message.includes('Cannot read properties of undefined')) {
    console.warn('💡 Tip: This error usually means data is not loaded yet. Check your v-if conditions or add null checks.')
  }
  
  if (err.message.includes('charAt')) {
    console.warn('💡 Tip: Trying to call charAt on undefined/null. Add string validation: value?.charAt?.(0) or value || ""')
  }
  
  console.groupEnd()
  
  // TODO: Integrate with error tracking service (e.g., Sentry)
  // errorTrackingService.captureException(err, { extra: { info, instance } })
}

// Global warning handler for development
app.config.warnHandler = (msg, instance, trace) => {
  if (process.env.NODE_ENV === 'development') {
    console.warn('⚠️ DocraTech Warning:', msg)
    console.warn('📋 Warning Trace:', trace)
  }
}

// Global utility properties for DocraTech Medical Platform
app.config.globalProperties.$docratech = {
  // Date formatting utilities
  formatDate: (date, locale = 'en-US') => {
    if (!date) return ''
    return new Date(date).toLocaleDateString(locale, {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  },
  
  formatDateTime: (date, locale = 'en-US') => {
    if (!date) return ''
    return new Date(date).toLocaleString(locale, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  },
  
  formatTime: (date, locale = 'en-US') => {
    if (!date) return ''
    return new Date(date).toLocaleTimeString(locale, {
      hour: '2-digit',
      minute: '2-digit'
    })
  },
  
  // Medical/Healthcare specific formatting
  formatCurrency: (amount, currency = 'USD') => {
    if (amount == null) return '$0.00'
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency
    }).format(amount)
  },
  
  formatPhone: (phone) => {
    if (!phone) return ''
    const cleaned = phone.replace(/\D/g, '')
    if (cleaned.length === 10) {
      return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)}-${cleaned.slice(6)}`
    }
    return phone
  },
  
  // Text utilities
  truncate: (text, length = 50) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
  },
  
  capitalize: (text) => {
    if (!text) return ''
    return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase()
  },
  
  // Status formatting for medical platform
  getStatusColor: (status) => {
    const statusColors = {
      active: 'success',
      inactive: 'warning',
      suspended: 'error',
      pending: 'info',
      approved: 'success',
      rejected: 'error',
      online: 'success',
      offline: 'warning'
    }
    return statusColors[status?.toLowerCase()] || 'info'
  },
  
  // URL/slug utilities
  slugify: (text) => {
    if (!text) return ''
    return text
      .toLowerCase()
      .replace(/[^\w\s-]/g, '')
      .replace(/[\s_-]+/g, '-')
      .replace(/^-+|-+$/g, '')
  }
}

// Global directives for enhanced UX
app.directive('focus', {
  mounted(el) {
    el.focus()
  }
})

app.directive('click-outside', {
  beforeMount(el, binding) {
    el.clickOutsideEvent = function(event) {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event, el)
      }
    }
    document.body.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.body.removeEventListener('click', el.clickOutsideEvent)
  }
})

// Performance monitoring
if (process.env.NODE_ENV === 'development') {
  app.config.performance = true
}

// Mount the application
console.log('🏥 DocraTech Medical Platform - Starting Application...')
app.mount('#app')
console.log('✅ DocraTech Application Successfully Mounted')

// Clean up loading screen after mount
const loadingScreen = document.getElementById('loading-screen')
if (loadingScreen) {
  setTimeout(() => {
    loadingScreen.style.opacity = '0'
    setTimeout(() => {
      loadingScreen.remove()
    }, 300)
  }, 100)
}

// Export app instance for potential testing or external access
export default app 