<template>
  <div id="app" class="min-h-screen bg-docratech-background antialiased">
    <!-- Global Loading Indicator -->
    <Transition name="fade">
      <div 
        v-if="isLoading" 
        class="fixed inset-0 bg-white bg-opacity-90 backdrop-blur-sm z-50 content-center"
      >
        <div class="text-center">
          <div class="loading-spinner w-8 h-8 text-primary mx-auto mb-4"></div>
          <p class="text-docratech-textSecondary font-medium">Loading DocraTech...</p>
        </div>
      </div>
    </Transition>

    <!-- Main Application Content -->
    <router-view v-slot="{ Component, route }">
      <Transition 
        :name="getTransitionName(route)" 
        mode="out-in"
        @before-enter="onBeforeEnter"
        @after-enter="onAfterEnter"
      >
        <!-- Auth Layout (Login, Register, etc.) -->
        <div v-if="route.meta?.layout === 'auth'" class="min-h-screen">
          <component 
            :is="Component" 
            :key="route.path"
            class="animate-fade-in"
          />
        </div>
        
        <!-- App Layout (Dashboard, Users, etc.) -->
        <AppLayout v-else-if="route.meta?.layout === 'app'">
          <template #sidebar="{ isCollapsed }">
            <NavMenu :isCollapsed="isCollapsed" />
          </template>
          <template #navbar-actions>
            <UserDropdown />
          </template>
          <component 
            :is="Component" 
            :key="route.path"
            class="animate-fade-in"
          />
        </AppLayout>
        
        <!-- Default Layout -->
        <component 
          v-else
          :is="Component" 
          :key="route.path"
          class="animate-fade-in"
        />
      </Transition>
    </router-view>

    <!-- Global Toast Container -->
    <Teleport to="body">
      <div class="toast-container"></div>
    </Teleport>

    <!-- Global Modals Container -->
    <Teleport to="body">
      <div id="modal-root"></div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/components/layout/AppLayout.vue'
import NavMenu from '@/components/navigation/NavMenu.vue'
import UserDropdown from '@/components/auth/UserDropdown.vue'

// Composables
const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

// Reactive state
const isLoading = ref(false)
const pageTitle = ref('DocraTech Medical Platform')

// Computed properties
const currentRouteName = computed(() => route.name)
const isAuthPage = computed(() => 
  ['login', 'register', 'forgot-password', 'reset-password'].includes(route.name)
)

// Methods
const getTransitionName = (route) => {
  // Different transitions for different route types
  if (route.meta?.transition) {
    return route.meta.transition
  }
  
  if (isAuthPage.value) {
    return 'slide-fade'
  }
  
  return 'fade'
}

const onBeforeEnter = () => {
  isLoading.value = false
}

const onAfterEnter = () => {
  // Update page title
  if (route.meta?.title) {
    document.title = route.meta.title
    pageTitle.value = route.meta.title
  }
  
  // Scroll to top on route change
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleRouteError = (error) => {
  console.error('❌ Route navigation error:', error)
  // Handle route errors gracefully
  if (error.name !== 'NavigationDuplicated') {
    router.push('/dashboard').catch(() => {
      // Fallback to login if dashboard fails
      router.push('/login')
    })
  }
}

// Lifecycle hooks
onMounted(() => {
  // Initialize application
  console.log('🏥 DocraTech App Component Mounted')
  
  // Set initial page title
  if (route.meta?.title) {
    document.title = route.meta.title
  }
  
  // Handle route errors
  router.onError(handleRouteError)
})

// Watchers
watch(currentRouteName, (newRoute, oldRoute) => {
  console.log(`📍 Route changed from ${oldRoute} to ${newRoute}`)
})

// Global error boundary
const onErrorCaptured = (error, instance, info) => {
  console.error('❌ Error captured in App.vue:', error)
  console.error('📍 Instance:', instance)
  console.error('🔍 Info:', info)
  
  // Prevent error from propagating
  return false
}
</script>

<style scoped>
/* Page Transitions */
.fade-enter-active, 
.fade-leave-active {
  transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from, 
.fade-leave-to {
  opacity: 0;
}

.slide-fade-enter-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

/* Scale transition for modals */
.scale-fade-enter-active,
.scale-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.scale-fade-enter-from {
  opacity: 0;
  transform: scale(0.9);
}

.scale-fade-leave-to {
  opacity: 0;
  transform: scale(1.1);
}

/* Enhanced focus management */
#app:focus-within {
  /* Improve focus visibility throughout the app */
}

/* Smooth scrolling enhancement */
html {
  scroll-behavior: smooth;
}

/* Performance optimizations */
.router-view {
  will-change: transform, opacity;
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
  .fade-enter-active,
  .fade-leave-active,
  .slide-fade-enter-active,
  .slide-fade-leave-active,
  .scale-fade-enter-active,
  .scale-fade-leave-active {
    transition-duration: 0.01ms !important;
  }
}

/* Print styles */
@media print {
  .toast-container,
  #modal-root {
    display: none !important;
  }
}
</style>

