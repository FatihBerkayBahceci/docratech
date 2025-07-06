<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Component: Container
Project: DocraTech Medical Website Platform
Language: American English (US)
License: MIT

Features:
- Full TailwindCSS utility-first, brand colors ready
- Fluid/boxed/maxWidth/gradient/bg/variant support
- Optional header/footer slot with sticky and accessibility props
- Skeleton/loading state, test-id, ARIA, animation, SSR friendly
- Focus management, motion, advanced shadow/border handling
-->

<template>
  <section
    :id="id"
    :class="containerClasses"
    :aria-label="ariaLabel"
    :tabindex="tabindex"
    :style="containerStyle"
    data-testid="container"
    ref="containerRef"
    @focus="onFocus"
  >
    <!-- Sticky Header Slot (optional, region labeling, a11y) -->
    <header
      v-if="$slots.header"
      :class="['container-header', { 'sticky top-0 z-30 bg-inherit/80 backdrop-blur-sm': stickyHeader }]"
      aria-label="Section Header"
    >
      <slot name="header" />
    </header>
    <!-- Main Content -->
    <div class="relative w-full">
      <slot />
      <!-- Skeleton / loading state -->
      <Transition name="fade-in">
        <div v-if="loading" class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 dark:bg-[#181124]/80 pointer-events-none">
          <slot name="skeleton">
            <div class="w-16 h-16 rounded-full border-4 border-primary animate-spin border-t-transparent" aria-label="Loading..."></div>
          </slot>
        </div>
      </Transition>
    </div>
    <!-- Footer Slot (optional) -->
    <footer v-if="$slots.footer" class="container-footer" aria-label="Section Footer">
      <slot name="footer" />
    </footer>
  </section>
</template>

<script setup>
// --- Imports
import { computed, ref, onMounted } from 'vue'

// --- Props
const props = defineProps({
  fluid: Boolean,
  maxWidth: {
    type: String,
    default: 'xl',
    validator: v => ['sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(v)
  },
  padding: {
    type: String,
    default: '' // Use Tailwind defaults if not set
  },
  background: {
    type: String,
    default: 'white',
    validator: v =>
      ['white', 'gray', 'primary', 'secondary', 'transparent', 'dark'].includes(
        v
      )
  },
  shadow: {
    type: String,
    default: 'none',
    validator: v => ['none', 'sm', 'md', 'lg', 'xl'].includes(v)
  },
  bordered: {
    type: Boolean,
    default: false
  },
  stickyHeader: {
    type: Boolean,
    default: false
  },
  animated: {
    type: Boolean,
    default: true
  },
  ariaLabel: {
    type: String,
    default: 'Content section'
  },
  tabindex: {
    type: [Number, String],
    default: -1
  },
  loading: {
    type: Boolean,
    default: false
  },
  id: {
    type: String,
    default: undefined
  }
})

// --- Refs & methods for focus/scroll/SSR-friendly usage
const containerRef = ref(null)
function onFocus() {
  // Optional: for keyboard navigation or accessibility highlight
}

// --- Computed Classes
const containerClasses = computed(() => [
  'relative w-full mx-auto transition-all duration-300 outline-none',
  props.fluid ? 'max-w-full px-4 md:px-8' : `max-w-${props.maxWidth}`,
  // Background
  {
    'bg-white': props.background === 'white',
    'bg-gray-50': props.background === 'gray',
    'bg-gradient-to-br from-[#ede9fe] to-[#9D38CF]':
      props.background === 'primary',
    'bg-gradient-to-br from-[#f0f9ff] to-[#e0f2fe]':
      props.background === 'secondary',
    'bg-transparent': props.background === 'transparent',
    'bg-[#181124]': props.background === 'dark'
  },
  // Shadow
  {
    'shadow-none': props.shadow === 'none',
    'shadow-sm': props.shadow === 'sm',
    'shadow-md': props.shadow === 'md',
    'shadow-lg': props.shadow === 'lg',
    'shadow-xl': props.shadow === 'xl'
  },
  // Border
  {
    'border border-[#e5e7eb] dark:border-[#2A183D]': props.bordered
  },
  // Animation
  { 'animate-fade-in-up': props.animated }
])

const containerStyle = computed(() => {
  if (props.padding) {
    return { padding: props.padding }
  }
  return {}
})
</script>

<style scoped>
@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(16px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-in-enter-active, .fade-in-leave-active {
  transition: opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-in-enter-from, .fade-in-leave-to {
  opacity: 0;
}
/* Header/Footer slot styling for enterprise look */
.container-header {
  padding-bottom: 0.5rem;
  margin-bottom: 1.25rem;
}
.container-footer {
  padding-top: 0.75rem;
  margin-top: 1.25rem;
}
</style>
