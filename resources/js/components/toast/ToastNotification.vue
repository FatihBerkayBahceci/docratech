<!--
  Proje: Kurumsal Sağlık Platformu - ToastNotification
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Brandkit uyumlu, animasyonlu, erişilebilir toast
-->

<template>
  <Transition name="toast" appear>
    <div
      v-if="visible"
      :class="['toast-notification', type]"
      role="alert"
      aria-live="assertive"
      tabindex="0"
    >
      <div class="toast-content">
        <!-- Brandkit Iconlar -->
        <span class="toast-icon" v-if="showIcon">
          <component :is="iconComponent" class="w-5 h-5" aria-hidden="true" />
        </span>
        <span class="toast-message">{{ message }}</span>
        <button @click="closeToast" class="toast-close" aria-label="Bildirimi kapat">
          <svg viewBox="0 0 20 20" class="w-4 h-4" fill="none">
            <path d="M5 5l10 10M15 5l-10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
      <div class="toast-progress" v-if="duration > 0">
        <div class="progress-bar" :style="barStyle"></div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { computed, ref, onMounted } from 'vue';

// Brandkit iconları (SVG)
const SuccessIcon = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#23D2A2" stroke-width="2"/><path d="M8.5 13.5l2.5 2 4.5-6" stroke="#23D2A2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>` }
const DangerIcon  = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#FF4473" stroke-width="2"/><path d="M8 8l8 8M16 8l-8 8" stroke="#FF4473" stroke-width="2" stroke-linecap="round"/></svg>` }
const WarningIcon = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#F3B801" stroke-width="2"/><path d="M12 7v5M12 17h.01" stroke="#F3B801" stroke-width="2" stroke-linecap="round"/></svg>` }
const InfoIcon    = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#2ED7E7" stroke-width="2"/><path d="M12 16v-4M12 8h.01" stroke="#2ED7E7" stroke-width="2" stroke-linecap="round"/></svg>` }
const DefaultIcon = { template: `<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" stroke="#9D38CF" stroke-width="2"/><path d="M12 16v-6M12 8h.01" stroke="#9D38CF" stroke-width="2" stroke-linecap="round"/></svg>` }

const props = defineProps({
  type: { type: String, default: 'success' }, // success, danger, warning, info
  message: { type: String, required: true },
  icon: { type: String, default: '' }, // Artık kullanılmıyor (brandkit svg var)
  duration: { type: Number, default: 2500 }
});

const visible = ref(true);

const iconComponent = computed(() => {
  switch (props.type) {
    case 'success': return SuccessIcon
    case 'danger':  return DangerIcon
    case 'warning': return WarningIcon
    case 'info':    return InfoIcon
    default:        return DefaultIcon
  }
});
const showIcon = computed(() => !!props.type);

const barStyle = computed(() => ({
  animation: `progress-shrink ${props.duration}ms linear forwards`
}));

function closeToast() {
  visible.value = false;
  setTimeout(() => {
    emit('close');
  }, 280);
}

const emit = defineEmits(['close']);

onMounted(() => {
  if (props.duration > 0) {
    setTimeout(closeToast, props.duration);
  }
});
</script>

<style scoped>
/* Giriş-Çıkış Animasyonları */
.toast-enter-active { animation: toast-slide-in 0.38s cubic-bezier(0.22,1,0.36,1);}
.toast-leave-active { animation: toast-slide-out 0.29s cubic-bezier(0.22,1,0.36,1);}
@keyframes toast-slide-in {
  0% { opacity: 0; transform: translateX(120%) translateY(-22px) scale(0.96);}
  100% { opacity: 1; transform: translateX(0) translateY(0) scale(1);}
}
@keyframes toast-slide-out {
  0% { opacity: 1; transform: translateX(0) translateY(0) scale(1);}
  100% { opacity: 0; transform: translateX(120%) translateY(-22px) scale(0.96);}
}
/* Brandkit Temel */
.toast-notification {
  position: fixed;
  top: 2rem;
  right: 2rem;
  background: var(--color-bg-card, #24163C);
  border-radius: 1.5rem;
  box-shadow: 0 8px 32px rgba(90,17,136,0.15);
  padding: 0;
  font-size: 1.06rem;
  font-weight: 500;
  z-index: 1080;
  min-width: 320px;
  max-width: 480px;
  overflow: hidden;
  border: 1.5px solid var(--color-border-light, #6D488D);
  outline: none;
  transition: background 0.34s, box-shadow 0.27s, border 0.22s;
  animation: toast-bounce 0.5s cubic-bezier(.62,0,.26,1);
  color: var(--color-text, #FFF);
  backdrop-filter: blur(9px);
}
@keyframes toast-bounce {
  0% { transform: scale(0.86);}
  55% { transform: scale(1.04);}
  100% { transform: scale(1);}
}
.toast-notification:focus-visible {
  border-color: var(--color-primary, #5A1188);
  box-shadow: 0 0 0 3px var(--color-primary-accent, #9D38CF), 0 8px 32px rgba(90,17,136,0.19);
}

/* Brandkit Tip Renkleri ve Gradient */
.toast-notification.success {
  border-left: 5px solid #23D2A2;
  background: linear-gradient(95deg, #21183A 60%, #1E1631 100%);
}
.toast-notification.danger {
  border-left: 5px solid #FF4473;
  background: linear-gradient(92deg, #2A183D 60%, #291646 100%);
}
.toast-notification.warning {
  border-left: 5px solid #F3B801;
  background: linear-gradient(90deg, #22192E 60%, #F3B80111 100%);
}
.toast-notification.info {
  border-left: 5px solid #2ED7E7;
  background: linear-gradient(90deg, #24163C 60%, #2ED7E711 100%);
}

/* Glow Hover (brandkit) */
.toast-notification::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 1.5rem;
  opacity: 0;
  background: radial-gradient(circle at 70% 40%, rgba(154,56,207,0.08) 0%, transparent 70%);
  transition: opacity 0.32s;
  pointer-events: none;
}
.toast-notification:hover::before {
  opacity: 1;
}

/* Content */
.toast-content {
  display: flex;
  align-items: center;
  gap: 1.08rem;
  padding: 1.13rem 1.38rem 1.13rem 1.08rem;
  position: relative;
}
.toast-icon {
  font-size: 1.28rem;
  flex-shrink: 0;
  display: flex;
  align-items: center;
}
.toast-message {
  flex: 1;
  line-height: 1.46;
  color: var(--color-text, #FFF);
}
.toast-close {
  background: none;
  border: none;
  color: var(--color-text-muted, #B7A9D1);
  cursor: pointer;
  padding: 5px;
  border-radius: 0.6rem;
  transition: background 0.18s, color 0.18s, transform 0.2s;
  flex-shrink: 0;
  outline: none;
  margin-right: -0.18rem;
}
.toast-close:hover,
.toast-close:focus {
  background: var(--color-primary-accent, #9D38CF33);
  color: var(--color-primary, #5A1188);
  transform: scale(1.13);
}
.toast-close:active { transform: scale(0.92); }
/* Progress Bar */
.toast-progress {
  height: 4px;
  background: var(--color-border-accent, #9D38CF22);
  overflow: hidden;
}
.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #5A1188, #9D38CF);
  border-radius: 8px;
  animation: progress-shrink linear forwards;
  transform-origin: left;
}
@keyframes progress-shrink {
  0% { transform: scaleX(1);}
  100% { transform: scaleX(0);}
}
/* Responsive */
@media (max-width: 768px) {
  .toast-notification {
    right: 12px;
    left: 12px;
    min-width: auto;
    max-width: none;
  }
}
</style>
