<!--
  DocraTech Medical Platform - Modern Navigation Menu
  Features: Permission-based visibility, modern design, smooth transitions
-->

<template>
  <nav class="flex flex-col gap-2">
    <!-- Dashboard -->
    <router-link 
      to="/dashboard" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/dashboard' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Dashboard</span>
    </router-link>

    <!-- User Management -->
    <router-link 
      v-if="canView('users')"
      to="/users" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/users' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Users</span>
    </router-link>

    <!-- Role Management -->
    <router-link 
      v-if="canView('roles')"
      to="/roles" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/roles' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Roles</span>
    </router-link>

    <!-- Permission Management -->
    <router-link 
      v-if="canView('permissions')"
      to="/permissions" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/permissions' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Permissions</span>
    </router-link>

    <!-- Clinics -->
    <router-link 
      v-if="canView('clinics')"
      to="/clinics" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/clinics' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Clinics</span>
    </router-link>

    <!-- Patients -->
    <router-link 
      v-if="canView('patients')"
      to="/patients" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/patients' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Patients</span>
      <div v-if="!isCollapsed" class="nav-badge">24</div>
    </router-link>

    <!-- Activity Logs -->
    <router-link 
      v-if="canView('logs')"
      to="/logs" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/logs' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Activity Logs</span>
    </router-link>

    <!-- Settings -->
    <router-link 
      v-if="canView('settings')"
      to="/settings" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/settings' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Settings</span>
    </router-link>

    <!-- Notifications -->
    <router-link 
      to="/notifications" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/notifications' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Notifications</span>
    </router-link>

    <!-- Support -->
    <router-link 
      to="/support" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/support' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Support</span>
    </router-link>

    <!-- Test Page (Development only) -->
    <router-link 
      v-if="isDevelopment"
      to="/test" 
      class="nav-item group"
      :class="{ 'nav-item-active': $route.path === '/test' }"
    >
      <div class="nav-icon">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
        </svg>
      </div>
      <span v-if="!isCollapsed" class="nav-label">Test (No Perms)</span>
    </router-link>

    <!-- Debug Info (Development only) -->
    <div v-if="isDevelopment && !isCollapsed" class="mt-6 p-3 bg-gray-50 rounded-lg text-xs border">
      <div class="text-gray-700 font-medium mb-2">🔧 Debug Info:</div>
      <div class="space-y-1 text-gray-600">
        <div>👤 User: {{ authStore.user?.first_name || 'Guest' }}</div>
        <div>🎭 Roles: {{ Array.from(authStore.roles || []).join(', ') || 'None' }}</div>
        <div>👑 Is Admin: {{ authStore.isAdmin ? 'Yes' : 'No' }}</div>
        <div>🔐 Permissions: {{ Array.from(authStore.permissions || []).length || 0 }}</div>
        <div>👁️ Can view: {{ getVisibleItems().join(', ') || 'None' }}</div>
        <div>🔑 Token: {{ authStore.token ? 'Yes' : 'No' }}</div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

const route = useRoute()
const authStore = useAuthStore()

const isDevelopment = computed(() => {
  return process.env.NODE_ENV === 'development'
})

const canView = (permission) => {
  // For admin users, allow everything
  if (authStore.isAdmin) {
    return true
  }
  
  // Check specific permission
  return authStore.hasPermission(`${permission}.view`)
}

const getVisibleItems = () => {
  const items = []
  if (canView('users')) items.push('users')
  if (canView('roles')) items.push('roles')
  if (canView('permissions')) items.push('permissions')
  if (canView('clinics')) items.push('clinics')
  if (canView('patients')) items.push('patients')
  if (canView('logs')) items.push('logs')
  if (canView('settings')) items.push('settings')
  return items
}
</script>

<style scoped>
.nav-item {
  @apply flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer;
  @apply text-gray-600 hover:text-docratech-primary hover:bg-gray-50;
}

.nav-item:hover {
  @apply transform scale-[1.02] shadow-sm;
}

.nav-item:active {
  @apply scale-[0.98];
}

.nav-item-active {
  @apply bg-gradient-to-r from-docratech-primary to-docratech-primaryAccent text-white shadow-lg;
}

.nav-item-active:hover {
  @apply text-white;
}

.nav-icon {
  @apply flex items-center justify-center transition-transform duration-200;
}

.group:hover .nav-icon {
  @apply scale-110;
}

.nav-label {
  @apply flex-1 font-medium;
}

.nav-badge {
  @apply ml-auto px-2 py-1 bg-docratech-primaryAccent text-white text-xs rounded-full font-medium;
}

.nav-item-active .nav-badge {
  @apply bg-white/20 text-white;
}
</style> 