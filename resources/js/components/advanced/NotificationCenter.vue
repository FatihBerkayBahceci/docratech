<!--
  Project: Enterprise Health Notification Center (Brandkit Revamp)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section
    class="w-full max-w-lg flex flex-col rounded-3xl border border-border dark:border-border-dark bg-bg-primary dark:bg-bg-dark shadow-brand-md overflow-hidden notification-center-root"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between px-6 py-4 border-b border-border dark:border-border-dark bg-bg-header dark:bg-bg-header-dark select-none"
    >
      <h3 class="font-semibold text-lg text-brand dark:text-brand-dark tracking-tight">Bildirimler</h3>
      <div class="flex gap-3">
        <AppButton variant="ghost" size="sm" @click="markAllAsRead" aria-label="Tümünü okundu işaretle">
          Tümünü Okundu İşaretle
        </AppButton>
        <AppButton variant="ghost" size="sm" @click="clearAll" aria-label="Tümünü temizle">
          Tümünü Temizle
        </AppButton>
      </div>
    </div>

    <!-- Content -->
    <div
      class="flex-1 overflow-y-auto max-h-[410px] relative notification-center-scroll"
      tabindex="0"
      role="list"
      aria-label="Bildirim listesi"
    >
      <!-- Empty State -->
      <transition name="fade">
        <div v-if="notifications.length === 0" class="flex items-center justify-center py-10">
          <EmptyState
            title="Bildirim yok"
            description="Henüz bildiriminiz bulunmuyor."
            icon="bell"
          />
        </div>
      </transition>

      <!-- Notification List -->
      <transition-group
        name="notif-list"
        tag="div"
        class="flex flex-col"
        role="list"
      >
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="flex gap-4 px-6 py-4 border-b border-border dark:border-border-dark items-start cursor-pointer relative group transition-colors duration-150 notification-item-root rounded-xl"
          :class="[
            !notification.read
              ? 'bg-brand/10 dark:bg-accent/20 border-l-4 border-brand dark:border-accent animate-pop'
              : '',
            notification.highlighted
              ? 'bg-brand-50/40 dark:bg-brand-700/50 border-l-4 border-brand-300 dark:border-brand-200'
              : ''
          ]"
          @click="handleNotificationClick(notification)"
          tabindex="0"
          role="listitem"
          :aria-label="`Bildirim: ${notification.title}`"
        >
          <!-- Icon -->
          <div class="mt-1 flex-shrink-0">
            <Icon
              :name="getNotificationIcon(notification.type)"
              size="sm"
              :class="getNotificationIconClass(notification.type)"
            />
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-4 mb-1">
              <span
                class="font-semibold text-brand dark:text-brand-dark text-base truncate"
              >
                {{ notification.title }}
              </span>
              <span
                class="text-xs text-brand-400 dark:text-brand-500 shrink-0 select-none"
                aria-label="Bildirim zamanı"
              >
                {{ formatTime(notification.timestamp) }}
              </span>
            </div>
            <p class="text-sm text-brand-400 dark:text-brand-300 leading-tight mb-2 line-clamp-2">
              {{ notification.message }}
            </p>

            <!-- Actions -->
            <div v-if="notification.actions" class="flex gap-3 flex-wrap">
              <AppButton
                v-for="action in notification.actions"
                :key="action.key"
                :variant="action.variant || 'secondary'"
                :size="action.size ? (action.size === 'sm' ? 'small' : action.size === 'md' ? 'medium' : action.size === 'lg' ? 'large' : action.size) : 'small'"
                @click.stop="handleAction(notification, action)"
                class="transition-transform hover:scale-105 rounded-lg"
              >
                {{ action.label }}
              </AppButton>
            </div>
          </div>

          <!-- Menu -->
          <div class="relative flex-shrink-0">
            <AppButton
              variant="secondary"
              size="small"
              aria-label="Bildirim menüsü"
              class="hover:scale-110 transition"
              @click.stop="toggleNotificationMenu(notification.id)"
            >
              <Icon name="more-vertical" size="sm" />
            </AppButton>

            <transition name="fade-slide">
              <div
                v-if="openMenu === notification.id"
                class="absolute z-30 right-0 mt-2 w-44 bg-bg-primary dark:bg-bg-dark rounded-3xl border border-border dark:border-border-dark shadow-brand-lg py-2 animate-pop"
                @click.stop
              >
                <button
                  v-if="!notification.read"
                  class="w-full flex items-center gap-2 px-5 py-2 text-sm text-brand-700 dark:text-brand-200 hover:bg-brand-50 dark:hover:bg-brand-800 transition rounded-t-3xl focus:outline-none focus:ring-2 focus:ring-brand"
                  @click="markAsRead(notification)"
                >
                  <Icon name="check" size="sm" />
                  Okundu İşaretle
                </button>
                <button
                  class="w-full flex items-center gap-2 px-5 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-brand-800 transition rounded-b-3xl focus:outline-none focus:ring-2 focus:ring-red-500"
                  @click="removeNotification(notification)"
                >
                  <Icon name="trash" size="sm" />
                  Sil
                </button>
              </div>
            </transition>
          </div>
        </div>
      </transition-group>
    </div>

    <!-- Footer -->
    <div
      v-if="notifications.length > 0"
      class="flex items-center justify-between px-6 py-4 border-t border-border dark:border-border-dark bg-bg-header dark:bg-bg-header-dark text-xs text-brand-400 dark:text-brand-300 select-none"
      aria-live="polite"
    >
      <div class="flex gap-4">
        <span class="font-semibold text-brand dark:text-brand-dark">{{ unreadCount }} okunmamış</span>
        <span>{{ notifications.length }} toplam</span>
      </div>
      <AppButton
        variant="outline"
        size="sm"
        class="hover:scale-105 transition rounded-lg"
        @click="loadMore"
        aria-label="Daha fazla yükle"
      >
        Daha Fazla Yükle
      </AppButton>
    </div>
  </section>
</template>

<script setup>
// Project: Enterprise Health Notification Center (Brandkit Revamp)
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted, onUnmounted } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'
import EmptyState from '../empty/EmptyState.vue'

const props = defineProps({
  notifications: { type: Array, default: () => [] },
  maxNotifications: { type: Number, default: 50 }
})
const emit = defineEmits([
  'notification-click',
  'notification-action',
  'mark-read',
  'remove',
  'load-more'
])

const openMenu = ref(null)
const unreadCount = computed(() => props.notifications.filter(n => !n.read).length)

function getNotificationIcon(type) {
  const icons = {
    info: 'info',
    success: 'check-circle',
    warning: 'alert-triangle',
    error: 'x-circle',
    default: 'bell'
  }
  return icons[type] || icons.default
}
function getNotificationIconClass(type) {
  const base = 'text-brand-400'
  if (!type) return base
  return {
    info: 'text-brand-400',
    success: 'text-green-500',
    warning: 'text-yellow-500',
    error: 'text-red-500'
  }[type] || base
}
function formatTime(timestamp) {
  const now = new Date()
  const time = new Date(timestamp)
  const diff = now - time
  const minutes = Math.floor(diff / (1000 * 60))
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  if (minutes < 1) return 'Az önce'
  if (minutes < 60) return `${minutes} dakika önce`
  if (hours < 24) return `${hours} saat önce`
  if (days < 7) return `${days} gün önce`
  return time.toLocaleDateString('tr-TR')
}

function handleNotificationClick(notification) {
  if (!notification.read) markAsRead(notification)
  emit('notification-click', notification)
}
function handleAction(notification, action) {
  emit('notification-action', { notification, action })
}
function markAsRead(notification) {
  emit('mark-read', notification)
  openMenu.value = null
}
function markAllAsRead() {
  props.notifications.filter(n => !n.read).forEach(n => emit('mark-read', n))
}
function removeNotification(notification) {
  emit('remove', notification)
  openMenu.value = null
}
function clearAll() {
  props.notifications.forEach(n => emit('remove', n))
}
function loadMore() {
  emit('load-more')
}
function toggleNotificationMenu(notificationId) {
  openMenu.value = openMenu.value === notificationId ? null : notificationId
}

// Close menu on outside click
function handleClickOutside(event) {
  if (!event.target.closest('.notification-item-root')) {
    openMenu.value = null
  }
}
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
:root {
  --brand: #5A1188;
  --accent: #9D38CF;
  --brand-dark: #2A183D;
  --bg-primary: #181124;
  --bg-dark: #211733;
  --bg-header: #251A38;
  --bg-header-dark: #1B1530;
  --border: #6D488D;
  --border-dark: #9D38CF;
  --shadow-brand-md: 0 8px 24px 0 #9D38CF40;
  --shadow-brand-lg: 0 12px 36px 0 #9D38CF30;
}

.bg-bg-primary {
  background-color: var(--bg-primary);
}
.bg-bg-dark {
  background-color: var(--bg-dark);
}
.bg-bg-header {
  background-color: var(--bg-header);
}
.bg-bg-header-dark {
  background-color: var(--bg-header-dark);
}
.text-brand {
  color: var(--brand);
}
.text-brand-dark {
  color: var(--brand-dark);
}
.text-accent {
  color: var(--accent);
}
.border-border {
  border-color: var(--border);
}
.border-border-dark {
  border-color: var(--border-dark);
}
.shadow-brand-md {
  box-shadow: var(--shadow-brand-md);
}
.shadow-brand-lg {
  box-shadow: var(--shadow-brand-lg);
}

/* Scrollbars */
.notification-center-scroll::-webkit-scrollbar {
  width: 6px;
}
.notification-center-scroll::-webkit-scrollbar-thumb {
  background-color: var(--border);
  border-radius: 4px;
  opacity: 0.6;
}
.dark .notification-center-scroll::-webkit-scrollbar-thumb {
  background-color: var(--border-dark);
  opacity: 0.8;
}

/* Animations */
@keyframes pop {
  0% {
    transform: scale(0.98);
  }
  60% {
    transform: scale(1.03);
  }
  100% {
    transform: none;
  }
}
.animate-pop {
  animation: pop 0.4s cubic-bezier(0.46, 1.7, 0.49, 0.71) 1;
}
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.21s cubic-bezier(0.51, 0, 0.21, 1);
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}
.fade-slide-enter-to,
.fade-slide-leave-from {
  opacity: 1;
  transform: none;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.17s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
  opacity: 1;
}
.notif-list-enter-active,
.notif-list-leave-active {
  transition: all 0.19s cubic-bezier(0.51, 0, 0.21, 1);
}
.notif-list-enter-from,
.notif-list-leave-to {
  opacity: 0;
  transform: translateY(8px) scale(0.97);
}
.notif-list-enter-to,
.notif-list-leave-from {
  opacity: 1;
  transform: none;
}

/* Responsive */
@media (max-width: 640px) {
  .notification-center-root {
    border-radius: 1.5rem;
  }
  .px-6 {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
  .py-4 {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
  }
}
</style>
