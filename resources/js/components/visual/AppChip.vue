<!--
  Project: Kurumsal Sağlık Platformu Frontend
  Component: AppChip (Enterprise-Level, Brandkit Uyumlu)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <span
    :class="chipClasses"
    :style="chipStyles"
    role="button"
    tabindex="0"
    @keydown.delete.prevent="removable && $emit('remove')"
    @keydown.backspace.prevent="removable && $emit('remove')"
    @keydown.enter.prevent="removable && $emit('remove')"
    aria-label="Chip {{ text || 'etiket' }} {{ removable ? 'kaldırılabilir' : '' }}"
  >
    <Icon
      v-if="icon"
      :name="icon"
      size="xs"
      class="app-chip-icon"
      :aria-hidden="true"
      focusable="false"
    />
    <span class="app-chip-text">
      <slot>{{ text }}</slot>
    </span>
    <button
      v-if="removable"
      type="button"
      class="app-chip-remove"
      @click="$emit('remove')"
      :aria-label="`Remove ${text || 'chip'}`"
      tabindex="-1"
    >
      <Icon name="x" size="xs" />
    </button>
  </span>
</template>

<script setup>
import { computed } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  text: { type: String, default: null },
  icon: { type: String, default: null },
  variant: {
    type: String,
    default: 'default',
    validator: (v) =>
      [
        'default',
        'primary',
        'success',
        'warning',
        'danger',
        'info',
        'outlined',
        'filled',
        'soft',
      ].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  removable: { type: Boolean, default: false },
  animated: { type: Boolean, default: true },
})

const chipClasses = computed(() => [
  'app-chip',
  `app-chip-${props.variant}`,
  `app-chip-${props.size}`,
  {
    'app-chip-animated': props.animated,
    'app-chip-with-icon': !!props.icon,
    'app-chip-removable': props.removable,
    'cursor-pointer': props.removable,
    'select-none': true,
    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#9D38CF] focus-visible:ring-offset-1 focus-visible:ring-offset-[#181124] rounded-full':
      true,
  },
])

const chipStyles = computed(() => ({
  '--chip-transition': props.animated
    ? 'all 0.2s cubic-bezier(0.4, 0, 0.2, 1)'
    : 'none',
}))
</script>

<style scoped>
.app-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  border-radius: 1rem;
  white-space: nowrap;
  transition: var(--chip-transition);
  line-height: 1;
  border: 1px solid transparent;
  user-select: none;
  position: relative;
  outline-offset: 2px;
}

.app-chip-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.app-chip-md {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}

.app-chip-lg {
  padding: 0.5rem 1rem;
  font-size: 1rem;
}

.app-chip-icon {
  flex-shrink: 0;
  color: #9d38cf;
  transition: transform 0.3s ease;
}

.app-chip-with-icon.app-chip-animated:hover .app-chip-icon {
  transform: scale(1.1);
}

.app-chip-text {
  line-height: 1;
  color: inherit;
}

.app-chip-remove {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1rem;
  height: 1rem;
  background: transparent;
  border: none;
  color: currentColor;
  cursor: pointer;
  border-radius: 50%;
  transition: all 0.2s;
  opacity: 0.7;
  flex-shrink: 0;
  margin-left: 0.5rem;
  outline-offset: 2px;
}

.app-chip-remove:hover,
.app-chip-remove:focus-visible {
  opacity: 1;
  background: rgba(157, 56, 207, 0.15);
  color: #9d38cf;
  outline: none;
  box-shadow: 0 0 8px #9d38cf88;
}

.app-chip-remove:active {
  transform: scale(0.9);
}

.dark .app-chip-remove:hover,
.dark .app-chip-remove:focus-visible {
  background: rgba(157, 56, 207, 0.25);
}

/* Variants */
.app-chip-default {
  background: #2a183d;
  color: #d1d5db;
  border-color: transparent;
}

.app-chip-primary {
  background: linear-gradient(90deg, #5a1188 0%, #9d38cf 100%);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 10px #9d38cf88;
}

.app-chip-success {
  background: #1ee9bc;
  color: #1a292b;
  border-color: transparent;
  box-shadow: 0 4px 10px #1ee9bc88;
}

.app-chip-warning {
  background: #ffd600;
  color: #3f3f3f;
  border-color: transparent;
  box-shadow: 0 4px 10px #ffd60088;
}

.app-chip-danger {
  background: #fb2773;
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 10px #fb277388;
}

.app-chip-info {
  background: #6d488d;
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 10px #6d488d88;
}

.app-chip-outlined {
  background: transparent;
  color: #9d38cf;
  border-color: #9d38cf;
  box-shadow: none;
}

.app-chip-filled {
  background: #9d38cf;
  color: #fff;
  border-color: #9d38cf;
  box-shadow: 0 4px 10px #9d38cf88;
}

.app-chip-soft {
  background: rgba(157, 56, 207, 0.15);
  color: #9d38cf;
  border-color: transparent;
  box-shadow: none;
}

/* Dark mode variants */
.dark .app-chip-default {
  background: #181124;
  color: #b0a7cf;
  border-color: transparent;
}

.dark .app-chip-primary {
  background: linear-gradient(90deg, #5a1188 0%, #9d38cf 100%);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 14px #9d38cfbb;
}

.dark .app-chip-success {
  background: #0f3e37;
  color: #a6f0e1;
  border-color: transparent;
  box-shadow: 0 4px 10px #1ee9bcbb;
}

.dark .app-chip-warning {
  background: #665100;
  color: #fff5cc;
  border-color: transparent;
  box-shadow: 0 4px 10px #ffd600bb;
}

.dark .app-chip-danger {
  background: #6f1035;
  color: #fda6bd;
  border-color: transparent;
  box-shadow: 0 4px 10px #fb2773bb;
}

.dark .app-chip-info {
  background: #4b366d;
  color: #d9d0f7;
  border-color: transparent;
  box-shadow: 0 4px 10px #6d488dbb;
}

.dark .app-chip-outlined {
  background: transparent;
  color: #b0a7cf;
  border-color: #6d488d;
}

.dark .app-chip-filled {
  background: #bdb0d3;
  color: #2a183d;
  border-color: #bdb0d3;
  box-shadow: 0 4px 10px #9d38cfbb;
}

.dark .app-chip-soft {
  background: rgba(157, 56, 207, 0.25);
  color: #b0a7cf;
  border-color: transparent;
  box-shadow: none;
}

/* Animation */
.app-chip-animated {
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.app-chip-animated:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px #9d38cf44;
}

.dark .app-chip-animated:hover {
  box-shadow: 0 8px 24px #5a118888;
}

.app-chip-removable:hover {
  transform: translateY(-1px);
}
</style>
