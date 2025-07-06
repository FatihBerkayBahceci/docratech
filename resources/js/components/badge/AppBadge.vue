<template>
  <span 
    :class="badgeClasses" 
    :style="badgeStyles"
    :title="title"
    role="status"
    aria-live="polite"
    tabindex="0"
    @focus="handleFocus"
    @blur="handleBlur"
  >
    <Icon v-if="icon" :name="icon" size="xs" class="app-badge-icon" />
    <span v-if="$slots.default" class="app-badge-text">
      <slot />
    </span>
    <span v-else-if="text" class="app-badge-text">{{ text }}</span>
  </span>
</template>

<script setup>
defineOptions({ name: 'AppBadge' })
// Project: Enterprise Health AppBadge
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { computed, ref, useSlots } from 'vue'
import Icon from '../visual/Icon.vue'

const props = defineProps({
  text: { type: String, default: null },
  icon: { type: String, default: null },
  variant: {
    type: String,
    default: 'default',
    validator: v => [
      'default', 'primary', 'success', 'warning', 'danger', 'info',
      'gray', 'blue', 'green', 'yellow', 'red', 'purple', 'pink'
    ].includes(v)
  },
  size: {
    type: String,
    default: 'md',
    validator: v => ['sm', 'md', 'lg'].includes(v)
  },
  animated: { type: Boolean, default: false },
  title: { type: String, default: null }
})

const slots = useSlots()
const isFocused = ref(false)

const badgeClasses = computed(() => [
  'app-badge',
  `app-badge-${props.variant}`,
  `app-badge-${props.size}`,
  {
    'app-badge-animated': props.animated,
    'app-badge-with-icon': props.icon,
    'app-badge-text-only': !props.icon && (props.text || slots.default),
    'app-badge-focused': isFocused.value
  }
])

const badgeStyles = computed(() => ({
  '--badge-transition': props.animated ? 'all 0.2s cubic-bezier(0.4, 0, 0.2, 1)' : 'none'
}))

function handleFocus() {
  isFocused.value = true
}
function handleBlur() {
  isFocused.value = false
}
</script>

<style scoped>
.app-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 0.375rem;
  white-space: nowrap;
  transition: var(--badge-transition);
  line-height: 1;
  outline-offset: 2px;
}

.app-badge-sm {
  padding: 0.125rem 0.375rem;
  font-size: 0.625rem;
}

.app-badge-md {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.app-badge-lg {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}

.app-badge-icon {
  flex-shrink: 0;
}

.app-badge-text {
  line-height: 1;
}

/* Variants */
.app-badge-default { background: #f3f4f6; color: #374151; }
.app-badge-primary { background: #eff6ff; color: #1d4ed8; }
.app-badge-success { background: #f0fdf4; color: #166534; }
.app-badge-warning { background: #fffbeb; color: #d97706; }
.app-badge-danger { background: #fef2f2; color: #dc2626; }
.app-badge-info { background: #eff6ff; color: #1d4ed8; }
.app-badge-gray { background: #f3f4f6; color: #374151; }
.app-badge-blue { background: #eff6ff; color: #1d4ed8; }
.app-badge-green { background: #f0fdf4; color: #166534; }
.app-badge-yellow { background: #fffbeb; color: #d97706; }
.app-badge-red { background: #fef2f2; color: #dc2626; }
.app-badge-purple { background: #faf5ff; color: #7c3aed; }
.app-badge-pink { background: #fdf2f8; color: #be185d; }

/* Dark mode */
.dark .app-badge-default { background: #374151; color: #d1d5db; }
.dark .app-badge-primary { background: #1e3a8a; color: #93c5fd; }
.dark .app-badge-success { background: #14532d; color: #86efac; }
.dark .app-badge-warning { background: #92400e; color: #fbbf24; }
.dark .app-badge-danger { background: #7f1d1d; color: #fca5a5; }
.dark .app-badge-info { background: #1e3a8a; color: #93c5fd; }
.dark .app-badge-gray { background: #374151; color: #d1d5db; }
.dark .app-badge-blue { background: #1e3a8a; color: #93c5fd; }
.dark .app-badge-green { background: #14532d; color: #86efac; }
.dark .app-badge-yellow { background: #92400e; color: #fbbf24; }
.dark .app-badge-red { background: #7f1d1d; color: #fca5a5; }
.dark .app-badge-purple { background: #581c87; color: #c4b5fd; }
.dark .app-badge-pink { background: #831843; color: #f9a8d4; }

/* Animation */
.app-badge-animated {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.app-badge-animated:hover {
  transform: scale(1.05);
}

/* Focus style for accessibility */
.app-badge-focused {
  box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.5);
  outline: none;
  transform: scale(1.07);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
