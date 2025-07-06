<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Component: Divider
Project: DocraTech Medical Website Platform
Language: American English (US)
License: MIT

- Fully TailwindCSS utility-first, dark mode ready
- Brand gradient & theme
- Slot + text support (label in middle or custom content)
- ARIA support for accessibility
- Animation & transition (fade in, ready for motion)
- testId prop for unit/UI tests
- Responsive & SSR friendly
-->

<template>
  <div
    :role="orientation === 'horizontal' ? 'separator' : 'presentation'"
    :aria-orientation="orientation"
    :class="dividerClasses"
    :data-testid="testId"
    tabindex="-1"
  >
    <!-- Horizontal with text or slot -->
    <template v-if="orientation === 'horizontal'">
      <div v-if="text" class="divider-content">
        <span class="divider-text">{{ text }}</span>
      </div>
      <slot v-else-if="$slots.default" />
    </template>
    <!-- Vertical, custom slot or empty -->
    <template v-else>
      <slot />
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  orientation: {
    type: String,
    default: 'horizontal',
    validator: value => ['horizontal', 'vertical'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: value => ['sm', 'md', 'lg'].includes(value)
  },
  color: {
    type: String,
    default: 'gray',
    validator: value =>
      ['gray', 'primary', 'secondary', 'light', 'dark'].includes(value)
  },
  spacing: {
    type: String,
    default: 'md',
    validator: value =>
      ['none', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  text: String,
  testId: {
    type: String,
    default: 'divider'
  }
})

// Dynamic utility classes, enterprise ready
const dividerClasses = computed(() => [
  'relative flex items-center transition-all duration-300 will-change-auto select-none',
  props.orientation === 'horizontal'
    ? 'w-full'
    : 'h-full min-h-[20px] flex-col',
  // Size
  props.orientation === 'horizontal'
    ? ({
      sm: 'h-px',
      md: 'h-[2px]',
      lg: 'h-[3px]'
    }[props.size])
    : ({
      sm: 'w-px h-full',
      md: 'w-[2px] h-full',
      lg: 'w-[3px] h-full'
    }[props.size]),
  // Color
  {
    'bg-[#e5e7eb] dark:bg-[#232035]': props.color === 'gray',
    'bg-gradient-to-r from-[#5A1188] to-[#9D38CF]': props.color === 'primary',
    'bg-gradient-to-r from-[#3b82f6] to-[#60a5fa]': props.color === 'secondary',
    'bg-[#f3f4f6] dark:bg-[#22212A]': props.color === 'light',
    'bg-[#2A183D]': props.color === 'dark'
  },
  // Spacing
  props.orientation === 'horizontal'
    ? ({
      none: 'my-0',
      sm: 'my-2',
      md: 'my-4',
      lg: 'my-6',
      xl: 'my-8'
    }[props.spacing])
    : ({
      none: 'mx-0',
      sm: 'mx-2',
      md: 'mx-4',
      lg: 'mx-6',
      xl: 'mx-8'
    }[props.spacing])
])
</script>

<style scoped>
.divider-content {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 32px;
  padding: 0 16px;
}
.divider-content::before {
  content: '';
  position: absolute;
  left: 0; right: 0; top: 50%;
  height: 1px;
  background: inherit;
  z-index: 1;
  transform: translateY(-50%);
  opacity: 0.7;
  pointer-events: none;
}
.divider-text {
  position: relative;
  background: #fff;
  padding: 0 14px;
  font-size: 1em;
  font-weight: 600;
  color: #6b7280;
  z-index: 2;
  border-radius: 7px;
  box-shadow: 0 2px 8px 0 rgba(90,17,136,0.06);
  animation: divider-text-fade-in 0.5s cubic-bezier(0.22,1,0.36,1);
  line-height: 1.5;
  transition: background 0.25s, color 0.25s;
}
@media (prefers-color-scheme: dark) {
  .divider-text {
    background: #181124;
    color: #b8afd2;
  }
}
.bg-gradient-to-r.divider-content .divider-text {
  background: linear-gradient(90deg,#ede9fe 0%,#9D38CF 100%);
  color: #5A1188;
}
@keyframes divider-text-fade-in {
  0% { opacity: 0; transform: scale(0.93);}
  100% { opacity: 1; transform: scale(1);}
}
</style>
