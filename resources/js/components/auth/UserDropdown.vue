<!--
  Project: Enterprise Health User Dropdown
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div class="user-dropdown relative" ref="dropdownRef">
    <button
      type="button"
      class="user-dropdown-trigger flex items-center gap-3 p-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-brand-400 dark:focus:ring-brand-600"
      @click="toggleDropdown"
      :aria-expanded="isOpen.toString()"
      aria-haspopup="true"
      aria-label="User menu"
      :aria-controls="'user-menu'"
    >
      <Avatar :src="user?.avatar" :alt="user?.name" size="sm" class="flex-shrink-0" />
      <div class="user-info hidden sm:flex flex-col min-w-0 truncate">
        <span class="user-name font-semibold text-sm text-brand-900 dark:text-brand-100 truncate">
          {{ user?.name || 'User' }}
        </span>
        <span class="user-role text-xs text-brand-500 dark:text-brand-400 truncate">
          {{ user?.role || 'Guest' }}
        </span>
      </div>
      <Icon
        name="chevron-down"
        size="sm"
        class="dropdown-icon transition-transform duration-200"
        :class="{ 'dropdown-icon-rotated': isOpen }"
        aria-hidden="true"
      />
    </button>

    <Transition name="dropdown">
      <div
        v-if="isOpen"
        id="user-menu"
        class="user-dropdown-menu absolute top-full right-0 mt-2 w-60 bg-white dark:bg-brand-950 border border-brand-100 dark:border-brand-800 rounded-xl shadow-lg z-50 overflow-hidden focus:outline-none"
        role="menu"
        tabindex="-1"
        @keydown.escape.prevent="closeDropdown"
        @keydown.tab="handleTabKey"
      >
        <div class="dropdown-header flex items-center gap-3 p-4 border-b border-brand-100 dark:border-brand-800 bg-brand-50 dark:bg-brand-900 select-none">
          <Avatar :src="user?.avatar" :alt="user?.name" size="md" />
          <div class="header-info min-w-0 truncate">
            <div class="header-name font-semibold text-brand-900 dark:text-brand-100 truncate">
              {{ user?.name || 'User' }}
            </div>
            <div class="header-email text-xs text-brand-500 dark:text-brand-400 truncate">
              {{ user?.email || 'user@example.com' }}
            </div>
          </div>
        </div>

        <div class="dropdown-divider border-t border-brand-100 dark:border-brand-800"></div>

        <nav class="dropdown-items flex flex-col" role="none">
          <router-link
            to="/profile"
            class="dropdown-item flex items-center gap-3 px-4 py-3 text-sm text-brand-900 dark:text-brand-100 hover:bg-brand-50 dark:hover:bg-brand-900 focus:outline-none focus:bg-brand-50 dark:focus:bg-brand-900"
            role="menuitem"
            tabindex="0"
            @click="closeDropdown"
          >
            <Icon name="user" size="sm" />
            <span>Profile</span>
          </router-link>

          <router-link
            to="/settings"
            class="dropdown-item flex items-center gap-3 px-4 py-3 text-sm text-brand-900 dark:text-brand-100 hover:bg-brand-50 dark:hover:bg-brand-900 focus:outline-none focus:bg-brand-50 dark:focus:bg-brand-900"
            role="menuitem"
            tabindex="0"
            @click="closeDropdown"
          >
            <Icon name="settings" size="sm" />
            <span>Settings</span>
          </router-link>

          <div class="dropdown-divider border-t border-brand-100 dark:border-brand-800 my-1"></div>

          <button
            type="button"
            class="dropdown-item dropdown-item-danger flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900 focus:outline-none focus:bg-red-50 dark:focus:bg-red-900"
            role="menuitem"
            tabindex="0"
            @click="handleLogout"
          >
            <Icon name="log-out" size="sm" />
            <span>Sign Out</span>
          </button>
        </nav>
      </div>
    </Transition>
  </div>
</template>

<script setup>
// Project: Enterprise Health User Dropdown
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Avatar from '@/components/media/Avatar.vue'
import Icon from '@/components/visual/Icon.vue'

const router = useRouter()
const authStore = useAuthStore()
const dropdownRef = ref(null)
const isOpen = ref(false)

const user = computed(() => authStore.user)

function toggleDropdown() {
  isOpen.value = !isOpen.value
}

function closeDropdown() {
  isOpen.value = false
}

async function handleLogout() {
  try {
    await authStore.logout()
    closeDropdown()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

function handleClickOutside(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

function handleEscape(event) {
  if (event.key === 'Escape') {
    closeDropdown()
  }
}

function handleTabKey(event) {
  // Trap focus inside dropdown if open
  const focusableElements = dropdownRef.value?.querySelectorAll(
    'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
  )
  if (!focusableElements || focusableElements.length === 0) return

  const firstEl = focusableElements[0]
  const lastEl = focusableElements[focusableElements.length - 1]

  if (event.shiftKey) {
    // Shift + Tab
    if (document.activeElement === firstEl) {
      lastEl.focus()
      event.preventDefault()
    }
  } else {
    // Tab
    if (document.activeElement === lastEl) {
      firstEl.focus()
      event.preventDefault()
    }
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscape)
})
</script>

<style scoped>
.user-dropdown {
  position: relative;
}

.user-dropdown-trigger {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0.75rem;
  background: transparent;
  border: none;
  border-radius: 0.75rem;
  cursor: pointer;
  color: var(--color-text);
  transition: background-color 0.3s ease;
}

.user-dropdown-trigger:hover,
.user-dropdown-trigger:focus-visible {
  background: var(--color-background-hover);
  outline: none;
}

.user-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.user-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text);
  line-height: 1.2;
}

.user-role {
  font-size: 0.75rem;
  color: var(--color-text-secondary);
  line-height: 1.2;
}

.dropdown-icon {
  color: var(--color-text-secondary);
  transition: transform 0.2s ease;
}

.dropdown-icon-rotated {
  transform: rotate(180deg);
}

.user-dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  width: 15rem;
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: 1rem;
  box-shadow: var(--shadow-lg);
  margin-top: 0.5rem;
  z-index: 9999;
  overflow: hidden;
}

.dropdown-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border-light);
  user-select: none;
}

.header-info {
  min-width: 0;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.header-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text);
  margin-bottom: 0.25rem;
}

.header-email {
  font-size: 0.75rem;
  color: var(--color-text-secondary);
}

.dropdown-divider {
  height: 1px;
  background: var(--color-border-light);
  margin: 0.5rem 0;
}

.dropdown-items {
  display: flex;
  flex-direction: column;
  padding: 0.5rem 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  color: var(--color-text);
  text-decoration: none;
  background: transparent;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.dropdown-item:hover,
.dropdown-item:focus-visible {
  background: var(--color-background-hover);
  color: var(--color-primary);
  outline: none;
}

.dropdown-item-danger {
  color: var(--color-danger);
}

.dropdown-item-danger:hover,
.dropdown-item-danger:focus-visible {
  background: var(--color-danger-alpha);
  color: var(--color-danger);
  outline: none;
}

/* Dropdown animations */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

/* Responsive */
@media (max-width: 640px) {
  .user-info {
    display: none;
  }
  .user-dropdown-trigger {
    padding: 0.25rem;
  }
  .user-dropdown-menu {
    width: 12rem;
    right: -0.5rem;
  }
}
</style>
