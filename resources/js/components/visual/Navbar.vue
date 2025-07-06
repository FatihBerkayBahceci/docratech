<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Language: American English (US)
License: MIT
Project: DocraTech Medical Website Platform
-->
<template>
  <nav
    :class="navbarClasses"
    :style="navbarStyles"
    aria-label="Main navigation"
  >
    <!-- Left Section -->
    <div class="flex items-center gap-4">
      <button
        v-if="showMobileMenu"
        class="p-2 rounded-lg hover:bg-docratech-hover transition"
        @click="$emit('mobile-menu-toggle')"
        aria-label="Toggle mobile menu"
      >
        <Icon name="menu" size="md" class="text-docratech-text" />
      </button>

      <div class="flex items-center gap-3">
        <img v-if="logo" :src="logo" :alt="logoAlt" class="h-8 w-auto" />
        <h1 v-if="title" class="text-xl font-semibold text-docratech-primary">{{ title }}</h1>
      </div>

      <slot name="breadcrumb" />
    </div>

    <!-- Center Section -->
    <div v-if="$slots.center" class="hidden md:flex flex-1 justify-center">
      <slot name="center" />
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-4">
      <SearchInput
        v-if="showSearch"
        v-model="searchQuery"
        placeholder="Search..."
        size="sm"
        class="hidden md:block"
        @search="$emit('search', searchQuery)"
      />

      <!-- Notifications -->
      <div class="relative">
        <button
          v-if="showNotifications"
          class="relative p-2 rounded-lg hover:bg-docratech-hover transition"
          @click="toggleNotifications"
          :aria-label="`${notifications.length} notifications`"
        >
          <Icon name="bell" size="md" class="text-docratech-text" />
          <span
            v-if="notifications.length"
            class="absolute -top-1 -right-1 bg-docratech-error text-xs rounded-full h-5 w-5 flex items-center justify-center text-white"
          >{{ notifications.length > 99 ? '99+' : notifications.length }}</span>
        </button>

        <Transition name="fade-scale">
          <div
            v-if="notificationsOpen"
            class="absolute right-0 mt-2 w-80 rounded-lg shadow-xl bg-docratech-surface border border-docratech-border overflow-hidden"
          >
            <div class="flex justify-between p-3 border-b border-docratech-border">
              <h3 class="text-sm font-medium text-docratech-text">Notifications</h3>
              <button class="text-xs text-docratech-primary" @click="clearNotifications">Clear all</button>
            </div>
            <div class="max-h-64 overflow-auto">
              <div
                v-for="notification in notifications"
                :key="notification.id"
                class="flex items-start gap-2 p-3 hover:bg-docratech-hover transition"
              >
                <Icon :name="notification.icon" size="sm" class="text-docratech-primary mt-1" />
                <div class="flex-1">
                  <p class="text-sm text-docratech-text">{{ notification.message }}</p>
                  <span class="text-xs text-docratech-textSecondary">{{ formatTime(notification.time) }}</span>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </div>

      <!-- User Menu -->
      <div class="relative" v-if="showUserMenu">
        <button
          class="flex items-center gap-2 p-2 rounded-lg hover:bg-docratech-hover transition"
          @click="toggleUserMenu"
          :aria-label="`User menu for ${user?.name}`"
        >
          <Avatar :src="user?.avatar" :name="user?.name" size="sm" />
          <span class="hidden md:block text-docratech-text">{{ user?.name }}</span>
          <Icon name="chevron-down" size="sm" class="text-docratech-text" />
        </button>

        <Transition name="fade-scale">
          <div
            v-if="userMenuOpen"
            class="absolute right-0 mt-2 w-64 bg-docratech-surface border border-docratech-border rounded-lg shadow-xl overflow-hidden"
          >
            <div class="flex items-center gap-3 p-4 border-b border-docratech-border">
              <Avatar :src="user?.avatar" :name="user?.name" size="md" />
              <div>
                <h4 class="text-sm font-semibold text-docratech-text">{{ user?.name }}</h4>
                <p class="text-xs text-docratech-textSecondary">{{ user?.email }}</p>
              </div>
            </div>
            <div class="p-2">
              <slot name="user-menu">
                <button class="menu-item"><Icon name="user" /> Profile</button>
                <button class="menu-item"><Icon name="settings" /> Settings</button>
                <hr class="my-1 border-docratech-border">
                <button class="menu-item" @click="$emit('logout')"><Icon name="logout" /> Logout</button>
              </slot>
            </div>
          </div>
        </Transition>
      </div>

      <ThemeToggle v-if="showThemeToggle" size="sm" />
      <slot name="actions" />
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue';
import Icon from './Icon.vue';
import SearchInput from '../form/SearchInput.vue';
import Avatar from '../media/Avatar.vue';
import ThemeToggle from '../theme/ThemeToggle.vue';

const props = defineProps({
  logo: String,
  logoAlt: { type: String, default: 'Logo' },
  title: String,
  user: Object,
  notifications: { type: Array, default: () => [] },
  showSearch: Boolean,
  showNotifications: Boolean,
  showUserMenu: Boolean,
  showThemeToggle: Boolean,
  showMobileMenu: Boolean,
});

const emit = defineEmits(['mobile-menu-toggle', 'search', 'logout', 'clear-notifications']);
const searchQuery = ref('');
const notificationsOpen = ref(false);
const userMenuOpen = ref(false);

const navbarClasses = 'flex justify-between items-center p-4 bg-docratech-surface border-b border-docratech-border sticky top-0 z-50';
const navbarStyles = '--tw-shadow: 0 4px 10px rgba(0,0,0,.1)';

const toggleNotifications = () => (notificationsOpen.value = !notificationsOpen.value);
const toggleUserMenu = () => (userMenuOpen.value = !userMenuOpen.value);
const clearNotifications = () => emit('clear-notifications');
const formatTime = (t) => new Date(t).toLocaleTimeString();
</script>

<style scoped>
.menu-item {
  @apply w-full flex items-center gap-2 px-3 py-2 text-sm text-docratech-text hover:bg-docratech-hover rounded-md transition-colors;
}

.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.2s ease;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
