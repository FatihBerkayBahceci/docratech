<!--
  Proje: Kurumsal Sağlık Platformu - Toast Container
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Brandkit uyumlu, animasyonlu, erişilebilir Toast notification UI
-->

<template>
  <div
    class="toast-container"
    :class="containerClasses"
    aria-live="polite"
    aria-atomic="true"
  >
    <TransitionGroup name="toast" tag="div" class="toast-list">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="toast-item group"
        :class="toastClasses(toast)"
        tabindex="0"
        role="status"
        :aria-label="toast.title || toast.message"
      >
        <div class="toast-content">
          <div class="toast-icon">
            <component :is="iconComponent(toast.type)" class="w-6 h-6" />
          </div>
          <div class="toast-body">
            <div v-if="toast.title" class="toast-title">{{ toast.title }}</div>
            <div class="toast-message">{{ toast.message }}</div>
          </div>
          <button
            type="button"
            class="toast-close"
            @click="removeToast(toast.id)"
            aria-label="Bildirimi kapat"
          >
            <svg
              viewBox="0 0 20 20"
              fill="none"
              class="w-4 h-4"
              aria-hidden="true"
            >
              <path d="M5 5l10 10M15 5l-10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <!-- Mikro animasyonlu progress bar -->
        <div v-if="toast.progress !== false" class="toast-progress">
          <div
            class="toast-progress-bar"
            :style="progressStyle(toast)"
          ></div>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { computed, onMounted, onUnmounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'

// BRANDKIT ICONS fallback (isteğe göre daha fazla eklenebilir)
const SuccessIcon = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#23D2A2" stroke-width="2"/><path d="M8.5 13.5l2.5 2 4.5-6" stroke="#23D2A2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>` }
const ErrorIcon = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#FF4473" stroke-width="2"/><path d="M8 8l8 8M16 8l-8 8" stroke="#FF4473" stroke-width="2" stroke-linecap="round"/></svg>` }
const WarningIcon = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#F3B801" stroke-width="2"/><path d="M12 7v5M12 17h.01" stroke="#F3B801" stroke-width="2" stroke-linecap="round"/></svg>` }
const InfoIcon = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#2ED7E7" stroke-width="2"/><path d="M12 16v-4M12 8h.01" stroke="#2ED7E7" stroke-width="2" stroke-linecap="round"/></svg>` }
const DefaultIcon = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#9D38CF" stroke-width="2"/><path d="M12 16v-6M12 8h.01" stroke="#9D38CF" stroke-width="2" stroke-linecap="round"/></svg>` }

const iconComponent = (type) => {
  switch (type) {
    case 'success': return SuccessIcon
    case 'error': return ErrorIcon
    case 'warning': return WarningIcon
    case 'info': return InfoIcon
    default: return DefaultIcon
  }
}

const props = defineProps({
  position: {
    type: String,
    default: 'top-right',
    validator: (value) => [
      'top-left', 'top-center', 'top-right',
      'bottom-left', 'bottom-center', 'bottom-right'
    ].includes(value)
  },
  maxToasts: { type: Number, default: 5 },
  autoClose: { type: Boolean, default: true },
  autoCloseDelay: { type: Number, default: 5000 }
})

const authStore = useAuthStore()
const toasts = computed(() => authStore.toasts.slice(0, props.maxToasts))

const containerClasses = computed(() => [
  `toast-container-${props.position.replace('-', '-')}`
])

const toastClasses = (toast) => [
  `toast-${toast.type || 'default'}`,
  { 'toast-with-progress': toast.progress !== false }
]

const removeToast = (id) => authStore.removeToast(id)

// Progress bar width / animasyonu
const progressStyle = (toast) => ({
  width: `${toast.progress}%`,
  transition: 'width 0.12s linear'
})

// Toast auto-close, enter/leave animasyonu
function autoCloseToasts() {
  if (!props.autoClose) return
  toasts.value.forEach(toast => {
    if (toast.autoClose !== false) {
      setTimeout(() => {
        removeToast(toast.id)
      }, toast.duration || props.autoCloseDelay)
    }
  })
}
onMounted(() => { autoCloseToasts() })
watch(toasts, autoCloseToasts)
</script>

<style scoped>
.toast-container {
  position: fixed;
  z-index: var(--z-index-toast, 1080);
  pointer-events: none;
  width: auto;
  max-width: 100vw;
}
.toast-container-top-left { top: 2rem; left: 2rem; }
.toast-container-top-center { top: 2rem; left: 50%; transform: translateX(-50%);}
.toast-container-top-right { top: 2rem; right: 2rem;}
.toast-container-bottom-left { bottom: 2rem; left: 2rem; }
.toast-container-bottom-center { bottom: 2rem; left: 50%; transform: translateX(-50%);}
.toast-container-bottom-right { bottom: 2rem; right: 2rem;}

.toast-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  max-width: 28rem;
}

.toast-item {
  background: var(--color-bg-card, #24163C);
  border: 1.5px solid var(--color-border-light, #6D488D);
  border-radius: var(--border-radius, 1.5rem);
  box-shadow: 0 6px 40px 0 var(--color-shadow, rgba(154,56,207,0.13));
  overflow: hidden;
  pointer-events: auto;
  min-width: 320px;
  max-width: 100%;
  position: relative;
  animation: toast-fadein 0.5s;
  transition: box-shadow 0.26s cubic-bezier(.6,0,.2,1);
  outline: none;
}
.toast-item:focus-visible {
  box-shadow: 0 0 0 3px var(--color-primary-accent, #9D38CF), 0 6px 40px 0 var(--color-shadow, rgba(154,56,207,0.13));
  border-color: var(--color-primary, #5A1188);
}
.toast-item:active {
  animation: toast-active 0.15s;
}
@keyframes toast-fadein {
  from { opacity: 0; transform: translateX(60px) scale(0.97);}
  to   { opacity: 1; transform: none;}
}
@keyframes toast-active {
  0% { transform: scale(1);}
  40% { transform: scale(1.03);}
  100% { transform: scale(1);}
}

.toast-content {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1.1rem 1.25rem 1.1rem 1rem;
}

.toast-icon {
  flex-shrink: 0;
  margin-top: 0.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.25rem;
  height: 2.25rem;
}
.toast-success .toast-icon svg { stroke: #23D2A2; }
.toast-error .toast-icon svg { stroke: #FF4473; }
.toast-warning .toast-icon svg { stroke: #F3B801; }
.toast-info .toast-icon svg { stroke: #2ED7E7; }
.toast-default .toast-icon svg { stroke: #9D38CF; }

.toast-body {
  flex: 1;
  min-width: 0;
}
.toast-title {
  font-size: var(--font-size-base, 1rem);
  font-weight: var(--font-weight-semibold, 600);
  color: var(--color-text, #fff);
  margin-bottom: 0.17rem;
  line-height: 1.22;
  letter-spacing: -0.015em;
}
.toast-message {
  font-size: var(--font-size-sm, 0.96rem);
  color: var(--color-text-secondary, #E4DDF5);
  line-height: 1.44;
  word-break: break-word;
}

.toast-close {
  flex-shrink: 0;
  background: transparent;
  border: none;
  color: var(--color-text-muted, #B7A9D1);
  cursor: pointer;
  padding: 0.28rem;
  border-radius: 0.5rem;
  transition: background 0.18s, color 0.2s;
  margin-top: -0.24rem;
  margin-right: -0.22rem;
  outline: none;
}
.toast-close:hover, .toast-close:focus {
  background: var(--color-primary-accent, #9D38CF);
  color: #fff;
}

.toast-progress {
  height: 4px;
  background: var(--color-border-accent, #9D38CF);
  overflow: hidden;
}
.toast-progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #5A1188 0%, #9D38CF 100%);
  border-radius: 8px;
  box-shadow: 0 1px 4px 0 rgba(154,56,207,0.13);
  transition: width 0.12s linear;
}
.toast-success .toast-progress-bar { background: linear-gradient(90deg, #23D2A2 0%, #9D38CF 100%);}
.toast-error .toast-progress-bar { background: linear-gradient(90deg, #FF4473 0%, #9D38CF 100%);}
.toast-warning .toast-progress-bar { background: linear-gradient(90deg, #F3B801 0%, #9D38CF 100%);}
.toast-info .toast-progress-bar { background: linear-gradient(90deg, #2ED7E7 0%, #9D38CF 100%);}

/* Animasyonlu TransitionGroup (giriş/çıkış) */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.36s cubic-bezier(0.44,0,0.36,1);
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(72px) scale(0.94);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(80px) scale(0.94);
}
.toast-move {
  transition: transform 0.36s cubic-bezier(0.44,0,0.36,1);
}

/* Responsive */
@media (max-width: 640px) {
  .toast-container, .toast-container-top-center, .toast-container-bottom-center {
    left: 0.7rem; right: 0.7rem; transform: none !important;
  }
  .toast-list { max-width: none;}
  .toast-item { min-width: 0;}
  .toast-content { padding: 0.77rem 0.8rem;}
}
</style>
