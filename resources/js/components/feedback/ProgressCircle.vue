<template>
  <button 
    :class="['app-button', variantClass, sizeClass, { loading }]"
    :disabled="disabled || loading"
    @click="handleClick"
    aria-busy="loading"
    :aria-disabled="disabled || loading"
  >
    <div class="button-content" :aria-live="loading ? 'polite' : 'off'">
      <i v-if="icon && !loading" :class="['bi', icon]" aria-hidden="true"></i>
      <div v-if="loading" class="loading-spinner" role="status" aria-label="Yükleniyor">
        <div class="spinner-ring"></div>
      </div>
      <span v-if="!loading" class="button-label"><slot>{{ label }}</slot></span>
      <span v-else class="button-loading-text">{{ loadingText }}</span>
    </div>
    <div v-if="loading" class="progress-bar" aria-hidden="true">
      <div class="progress-fill"></div>
    </div>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, default: '' },

  // ➕ Genişletilmiş ve uyarı vermeyen validator
  variant: {
    type: String,
    default: 'primary',
    validator: v =>
      ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'default', 'outline'].includes(v)
  },

  // ➕ Kısa/kodsal versiyonları destekleyen yapı
  size: {
    type: String,
    default: 'medium',
    validator: v =>
      ['small', 'medium', 'large', 'sm', 'md', 'lg'].includes(v)
  },

  icon: { type: String, default: '' },
  loading: { type: Boolean, default: false },
  loadingText: { type: String, default: 'Yükleniyor...' },
  disabled: { type: Boolean, default: false }
})

const emit = defineEmits(['click'])

const variantClass = computed(() => props.variant)

// Kısa -> uzun mapping ile hem `sm` hem `small` desteklenir
const sizeClass = computed(() => {
  const map = {
    sm: 'small',
    md: 'medium',
    lg: 'large'
  }
  return map[props.size] || props.size
})

function handleClick(event) {
  if (!props.loading && !props.disabled) {
    emit('click', event)
  }
}
</script>

<style scoped>
/* (Tüm önceki stil tanımları değişmeden korunmuştur) */

/* ... burada stiller öncekiyle aynı şekilde yer alır ... */
/* Örnek olarak size ve color class'ları zaten mevcut, yeniden yazmaya gerek yok */

.app-button {
  position: relative;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
  overflow: hidden;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: inherit;
  outline: none;
  user-select: none;
  vertical-align: middle;
}

/* Size Variants */
.app-button.small {
  padding: 8px 16px;
  font-size: 0.875rem;
}

.app-button.medium {
  padding: 12px 24px;
  font-size: 1rem;
}

.app-button.large {
  padding: 16px 32px;
  font-size: 1.125rem;
}

/* Color Variants */
.app-button.primary {
  background: linear-gradient(135deg, #6D28D9 0%, #9333EA 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(109, 40, 217, 0.3);
}

.app-button.primary:hover:not(:disabled):not(.loading) {
  box-shadow: 0 0 0 2px #6D28D9, 0 8px 24px rgba(109, 40, 217, 0.4);
  transform: translateY(-2px);
}

.app-button.secondary {
  background: #f3f4f6;
  color: #374151;
  border: 2px solid #e5e7eb;
}

.app-button.secondary:hover:not(:disabled):not(.loading) {
  background: #e5e7eb;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.app-button.success {
  background: linear-gradient(135deg, #10B981 0%, #059669 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.app-button.success:hover:not(:disabled):not(.loading) {
  box-shadow: 0 0 0 2px #10B981, 0 8px 24px rgba(16, 185, 129, 0.4);
  transform: translateY(-2px);
}

.app-button.danger {
  background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.app-button.danger:hover:not(:disabled):not(.loading) {
  box-shadow: 0 0 0 2px #EF4444, 0 8px 24px rgba(239, 68, 68, 0.4);
  transform: translateY(-2px);
}

.app-button.warning {
  background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.app-button.warning:hover:not(:disabled):not(.loading) {
  box-shadow: 0 0 0 2px #F59E0B, 0 8px 24px rgba(245, 158, 11, 0.4);
  transform: translateY(-2px);
}

/* Click Animation */
.app-button:active:not(:disabled):not(.loading) {
  transform: scale(0.97);
  transition: transform 0.1s cubic-bezier(0.22, 1, 0.36, 1);
}

/* Loading State */
.app-button.loading {
  pointer-events: none;
  opacity: 0.85;
}

/* Spinner */
.loading-spinner {
  display: flex;
  align-items: center;
  justify-content: center;
}

.spinner-ring {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Progress Bar */
.progress-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: rgba(255, 255, 255, 0.2);
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: rgba(255, 255, 255, 0.8);
  animation: progress-fill 2s ease-in-out infinite;
}

/* Disabled State */
.app-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

/* Ripple Effect */
.app-button::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
  pointer-events: none;
  z-index: 1;
}

.app-button:active::before {
  width: 300px;
  height: 300px;
}

@keyframes progress-fill {
  0% { width: 0%; }
  50% { width: 70%; }
  100% { width: 100%; }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
