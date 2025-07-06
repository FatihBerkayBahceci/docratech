<!--
  Project: Corporate Healthcare Platform UI Library
  Component: FormGroup
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div :class="groupClasses" class="form-group">
    <label
      v-if="label"
      :for="id"
      :class="labelClasses"
      class="form-label"
    >
      {{ label }}
      <span v-if="required" class="required-indicator" aria-label="required">*</span>
    </label>

    <div class="form-control">
      <slot />
    </div>

    <div v-if="description" class="form-description">
      {{ description }}
    </div>

    <transition name="fade-slide">
      <div v-if="error" class="form-error" role="alert" aria-live="assertive">
        <Icon name="alert-circle" size="xs" class="error-icon" />
        <span>{{ error }}</span>
      </div>
    </transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { computed } from 'vue'
import Icon from '@/components/visual/Icon.vue'

const props = defineProps({
  label: { type: String, default: null },
  description: { type: String, default: null },
  error: { type: String, default: null },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  size: {
    type: String,
    default: 'md',
    validator: value => ['sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: value => ['default', 'inline', 'compact'].includes(value)
  }
})

const id = computed(() => `form-${Math.random().toString(36).substr(2, 9)}`)

const groupClasses = computed(() => [
  'form-group',
  `form-group-${props.size}`,
  `form-group-${props.variant}`,
  {
    'form-group-error': !!props.error,
    'form-group-disabled': props.disabled,
    'form-group-required': props.required
  }
])

const labelClasses = computed(() => [
  'form-label',
  {
    'form-label-required': props.required,
    'form-label-disabled': props.disabled
  }
])
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-card: #181124;
  --color-text: #fff;
  --color-text-secondary: #B1A9C7;
  --color-text-disabled: #A7A3B8;
  --color-danger: #ef4444;
  --color-danger-alpha: rgba(239, 68, 68, 0.18);
  --color-background-disabled: #221736;
  --font-size-xs: 0.85rem;
  --font-size-sm: 1rem;
  --font-size-base: 1.09rem;
  --font-weight-medium: 500;
  --font-weight-semibold: 600;
}

/* Structure & Variants */
.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}
.form-group-inline {
  flex-direction: row;
  align-items: center;
  gap: 1rem;
}
.form-group-inline .form-label {
  min-width: 120px;
  margin-bottom: 0;
}
.form-group-compact {
  gap: 0.25rem;
  margin-bottom: 1rem;
}
.form-group-sm {
  gap: 0.375rem;
  margin-bottom: 1rem;
}
.form-group-lg {
  gap: 0.75rem;
  margin-bottom: 2rem;
}

/* Label */
.form-label {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
  line-height: 1.4;
  margin-bottom: 0.25rem;
  letter-spacing: 0.01em;
  transition: color 0.22s cubic-bezier(.22,1,.36,1);
}
.form-label-required {
  color: var(--color-text);
}
.form-label-disabled {
  color: var(--color-text-disabled);
}

/* Required indicator */
.required-indicator {
  color: var(--color-danger);
  margin-left: 0.25rem;
  font-size: 1em;
}

/* Control */
.form-control {
  position: relative;
}

/* Description */
.form-description {
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
  line-height: 1.4;
  margin-top: 0.25rem;
  transition: color 0.22s;
}

/* Error message */
.form-error {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: var(--font-size-xs);
  color: var(--color-danger);
  line-height: 1.4;
  margin-top: 0.18rem;
  animation: error-shake 0.5s cubic-bezier(0.36,0.07,0.19,0.97) both;
}
.error-icon {
  flex-shrink: 0;
  color: var(--color-danger);
}

/* Error state */
.form-group-error .form-label {
  color: var(--color-danger);
}
.form-group-error .form-control :deep(input),
.form-group-error .form-control :deep(select),
.form-group-error .form-control :deep(textarea) {
  border-color: var(--color-danger);
  box-shadow: 0 0 0 3px var(--color-danger-alpha);
}

/* Disabled state */
.form-group-disabled .form-label {
  color: var(--color-text-disabled);
}
.form-group-disabled .form-control :deep(input),
.form-group-disabled .form-control :deep(select),
.form-group-disabled .form-control :deep(textarea) {
  background: var(--color-background-disabled);
  color: var(--color-text-disabled);
  cursor: not-allowed;
}

/* Micro Animation for Error */
@keyframes error-shake {
  0%, 100% { transform: translateX(0);}
  10%, 30%, 50%, 70%, 90% { transform: translateX(-2px);}
  20%, 40%, 60%, 80% { transform: translateX(2px);}
}

/* Fade-slide micro animation for error */
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(.22,1,.36,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

/* Responsive */
@media (max-width: 640px) {
  .form-group-inline {
    flex-direction: column;
    align-items: stretch;
    gap: 0.5rem;
  }
  .form-group-inline .form-label {
    min-width: auto;
    margin-bottom: 0.25rem;
  }
}
</style>
