<!--
  DocraTech Medical Platform - Enhanced Card Component
  Features: Multiple variants, hover effects, accessibility, responsive design
  Brand Colors: DocraTech Light Mode Only
-->

<template>
  <component
    :is="tag"
    :class="cardClasses"
    :tabindex="interactive ? '0' : undefined"
    :role="role"
    :aria-labelledby="titleId"
    :aria-describedby="descriptionId"
    @click="handleClick"
    @keydown="handleKeydown"
    ref="cardRef"
  >
    <!-- Card Header -->
    <header 
      v-if="$slots.header || title"
      class="card-header"
      :class="headerClasses"
    >
      <div v-if="title" class="card-title" :id="titleId">
        <component 
          v-if="titleIcon"
          :is="titleIcon"
          class="w-5 h-5 text-docratech-primary flex-shrink-0"
          aria-hidden="true"
        />
        <h3 class="font-semibold text-docratech-text">{{ title }}</h3>
      </div>
      <slot name="header" />
      
      <!-- Actions in header -->
      <div v-if="$slots.actions" class="card-actions">
        <slot name="actions" />
      </div>
    </header>

    <!-- Card Body -->
    <main 
      class="card-body"
      :class="bodyClasses"
      :id="descriptionId"
    >
      <slot />
    </main>

    <!-- Card Footer -->
    <footer 
      v-if="$slots.footer"
      class="card-footer"
      :class="footerClasses"
    >
      <slot name="footer" />
    </footer>

    <!-- Loading Overlay -->
    <div 
      v-if="loading"
      class="card-loading-overlay"
      aria-hidden="true"
    >
      <div class="loading-spinner w-8 h-8 text-docratech-primary"></div>
    </div>

    <!-- Ripple Effect for Interactive Cards -->
    <span 
      v-if="interactive && ripple"
      class="card-ripple"
      aria-hidden="true"
    >
      <span 
        v-for="rippleEffect in ripples" 
        :key="rippleEffect.id"
        class="absolute rounded-full bg-docratech-primary bg-opacity-20 animate-ping"
        :style="rippleEffect.style"
      ></span>
    </span>
  </component>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'

/**
 * DocraTech Medical Platform - Enhanced Card Component
 * Features: Multiple variants, states, accessibility, animations
 */

defineOptions({ 
  name: 'AppCard',
  inheritAttrs: false 
})

// Props
const props = defineProps({
  // Content
  title: {
    type: String,
    default: ''
  },
  titleIcon: {
    type: [String, Object],
    default: null
  },
  
  // Appearance
  variant: {
    type: String,
    default: 'default',
    validator: (value) => [
      'default', 'elevated', 'outlined', 'filled', 'gradient'
    ].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  
  // Layout
  padding: {
    type: String,
    default: 'normal',
    validator: (value) => ['none', 'sm', 'normal', 'lg'].includes(value)
  },
  spacing: {
    type: String,
    default: 'normal',
    validator: (value) => ['none', 'sm', 'normal', 'lg'].includes(value)
  },
  
  // Behavior
  interactive: {
    type: Boolean,
    default: false
  },
  hoverable: {
    type: Boolean,
    default: false
  },
  clickable: {
    type: Boolean,
    default: false
  },
  
  // Effects
  shadow: {
    type: Boolean,
    default: true
  },
  rounded: {
    type: Boolean,
    default: true
  },
  ripple: {
    type: Boolean,
    default: true
  },
  
  // State
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  
  // HTML attributes
  tag: {
    type: String,
    default: 'div'
  },
  role: {
    type: String,
    default: null
  }
})

// Emits
const emit = defineEmits(['click', 'keydown'])

// Template refs
const cardRef = ref(null)

// State
const ripples = ref([])
let rippleCount = 0

// Computed properties
const titleId = computed(() => props.title ? `card-title-${Math.random().toString(36).substr(2, 9)}` : undefined)
const descriptionId = computed(() => `card-desc-${Math.random().toString(36).substr(2, 9)}`)

const cardClasses = computed(() => {
  const classes = [
    'card',
    `card--${props.variant}`,
    `card--${props.size}`,
    
    // Padding
    paddingClasses.value,
    
    // States
    {
      'card--interactive': props.interactive || props.clickable,
      'card--hoverable': props.hoverable,
      'card--disabled': props.disabled,
      'card--loading': props.loading,
      'card--shadow': props.shadow,
      'card--rounded': props.rounded
    }
  ]
  
  return classes.filter(Boolean)
})

const headerClasses = computed(() => [
  spacingClasses.value.header
])

const bodyClasses = computed(() => [
  spacingClasses.value.body
])

const footerClasses = computed(() => [
  spacingClasses.value.footer
])

const paddingClasses = computed(() => {
  const paddingMap = {
    none: 'p-0',
    sm: 'p-4',
    normal: 'p-6',
    lg: 'p-8'
  }
  return paddingMap[props.padding] || paddingMap.normal
})

const spacingClasses = computed(() => {
  const spacingMap = {
    none: { header: '', body: '', footer: '' },
    sm: { header: 'mb-2', body: '', footer: 'mt-2' },
    normal: { header: 'mb-4', body: '', footer: 'mt-4' },
    lg: { header: 'mb-6', body: '', footer: 'mt-6' }
  }
  return spacingMap[props.spacing] || spacingMap.normal
})

// Methods
const handleClick = (event) => {
  if (props.disabled || props.loading) return
  
  if (props.interactive || props.clickable) {
    // Create ripple effect
    if (props.ripple) {
      createRipple(event)
    }
    
    emit('click', event)
  }
}

const handleKeydown = (event) => {
  if (props.disabled || props.loading) return
  
  if (props.interactive || props.clickable) {
    // Handle Enter and Space keys
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault()
      handleClick(event)
    }
  }
  
  emit('keydown', event)
}

const createRipple = (event) => {
  if (!cardRef.value) return
  
  const rect = cardRef.value.getBoundingClientRect()
  const size = Math.max(rect.width, rect.height) * 0.6
  const x = event.clientX - rect.left - size / 2
  const y = event.clientY - rect.top - size / 2
  
  const ripple = {
    id: ++rippleCount,
    style: {
      left: x + 'px',
      top: y + 'px',
      width: size + 'px',
      height: size + 'px'
    }
  }
  
  ripples.value.push(ripple)
  
  // Remove ripple after animation
  setTimeout(() => {
    const index = ripples.value.findIndex(r => r.id === ripple.id)
    if (index > -1) {
      ripples.value.splice(index, 1)
    }
  }, 800)
}

const focus = () => {
  nextTick(() => {
    cardRef.value?.focus()
  })
}

// Expose methods
defineExpose({
  focus,
  cardRef
})
</script>

<style scoped>
/* Base Card Styles */
.card {
  @apply relative bg-white border border-docratech-borderLight transition-all duration-200 overflow-hidden;
}

.card--rounded {
  @apply rounded-2xl;
}

.card--shadow {
  @apply shadow-soft;
}

/* Variant Styles */
.card--default {
  @apply bg-white border-docratech-borderLight;
}

.card--elevated {
  @apply bg-white border-0 shadow-medium;
}

.card--outlined {
  @apply bg-white border-2 border-primary border-opacity-20 shadow-none;
}

.card--filled {
  @apply bg-gray-50 border-docratech-borderLight;
}

.card--gradient {
  @apply bg-gradient-to-br from-white to-gray-50 border-docratech-borderLight;
}

/* Size Variants */
.card--sm {
  @apply max-w-sm;
}

.card--md {
  @apply max-w-md;
}

.card--lg {
  @apply max-w-lg;
}

.card--xl {
  @apply max-w-xl;
}

/* Interactive States */
.card--interactive,
.card--hoverable {
  @apply cursor-pointer;
}

.card--interactive:hover,
.card--hoverable:hover {
  @apply shadow-medium -translate-y-1;
}

.card--interactive:focus,
.card--hoverable:focus {
  @apply outline-none ring-2 ring-primary ring-opacity-50 ring-offset-2;
}

.card--interactive:active {
  @apply transform translate-y-0 shadow-soft;
}

/* Disabled State */
.card--disabled {
  @apply opacity-60 cursor-not-allowed pointer-events-none;
}

/* Loading State */
.card--loading {
  @apply pointer-events-none;
}

.card-loading-overlay {
  @apply absolute inset-0 bg-white bg-opacity-80 backdrop-blur-sm;
  @apply flex items-center justify-center z-10;
}

/* Header Styles */
.card-header {
  @apply flex items-start justify-between;
}

.card-title {
  @apply flex items-center space-x-2 flex-1;
}

.card-actions {
  @apply flex items-center space-x-2 ml-4;
}

/* Body Styles */
.card-body {
  @apply text-docratech-textSecondary leading-relaxed;
}

/* Footer Styles */
.card-footer {
  @apply flex items-center justify-between border-t border-docratech-borderLight pt-4;
}

/* Ripple Effect */
.card-ripple {
  @apply absolute inset-0 overflow-hidden pointer-events-none;
}

/* Animations */
@keyframes card-enter {
  from {
    opacity: 0;
    transform: translateY(10px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.card {
  animation: card-enter 0.3s ease-out;
}

/* Accessibility Improvements */
@media (prefers-reduced-motion: reduce) {
  .card,
  .card--interactive:hover,
  .card--hoverable:hover,
  .card--interactive:active {
    transition: none !important;
    animation: none !important;
    transform: none !important;
  }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
  .card {
    @apply border-4 border-black;
  }
}

/* Print Styles */
@media print {
  .card {
    @apply shadow-none border border-gray-400 bg-white;
    break-inside: avoid;
  }
  
  .card-loading-overlay,
  .card-ripple {
    @apply hidden;
  }
}

/* Mobile Optimizations */
@media (max-width: 640px) {
  .card {
    @apply mx-2;
  }
  
  .card-header {
    @apply flex-col items-start space-y-2;
  }
  
  .card-actions {
    @apply ml-0 w-full justify-end;
  }
  
  .card-footer {
    @apply flex-col items-stretch space-y-2 pt-4;
  }
}
</style>
