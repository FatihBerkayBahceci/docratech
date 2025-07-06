<template>
  <button 
    :class="buttonClasses"
    :disabled="disabled || loading"
    :type="type"
    :aria-busy="loading"
    :aria-disabled="disabled || loading"
    :aria-label="ariaLabel || label"
    @click="handleClick"
    @focus="onFocus"
    @blur="onBlur"
    ref="buttonRef"
  >
    <!-- Ripple Effect Container -->
    <span 
      v-if="!disabled && ripple" 
      class="absolute inset-0 overflow-hidden rounded-2xl"
      aria-hidden="true"
    >
      <span 
        v-for="rippleEffect in ripples" 
        :key="rippleEffect.id"
        class="absolute rounded-full bg-white bg-opacity-30 animate-ping"
        :style="rippleEffect.style"
      ></span>
    </span>

    <!-- Button Content -->
    <span class="relative z-10 flex items-center justify-center gap-2">
      <!-- Loading Spinner -->
      <span 
        v-if="loading" 
        class="loading-spinner"
        role="status"
        :aria-label="loadingText"
      ></span>
      
      <!-- Icon (when not loading) -->
      <component 
        v-else-if="icon"
        :is="icon"
        class="w-5 h-5 flex-shrink-0"
        aria-hidden="true"
      />
      
      <!-- Button Text -->
      <span 
        v-if="label || $slots.default"
        class="font-semibold leading-none"
        :class="{ 'opacity-0': loading && !loadingText }"
      >
        <slot>{{ loading && loadingText ? loadingText : label }}</slot>
      </span>
    </span>

    <!-- Focus Ring -->
    <span 
      class="absolute inset-0 rounded-2xl ring-2 ring-docratech-primary ring-opacity-0 transition-all duration-200"
      :class="{ 'ring-opacity-100 ring-offset-2': focused }"
      aria-hidden="true"
    ></span>
  </button>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'

/**
 * DocraTech Medical Platform - Enhanced Button Component
 * Features: Accessibility, Ripple Effects, Loading States, Multiple Variants
 * Brand Colors: Light Mode Only
 */

defineOptions({ 
  name: 'AppButton',
  inheritAttrs: false 
})

// Props with comprehensive validation
const props = defineProps({
  // Content
  label: { 
    type: String, 
    default: '' 
  },
  icon: { 
    type: [String, Object], 
    default: null 
  },
  
  // Appearance
  variant: { 
    type: String, 
    default: 'primary',
    validator: (value) => [
      'primary', 'secondary', 'ghost', 'outline',
      'success', 'warning', 'error', 'info'
    ].includes(value)
  },
  size: { 
    type: String, 
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  
  // Behavior
  type: { 
    type: String, 
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  disabled: { 
    type: Boolean, 
    default: false 
  },
  loading: { 
    type: Boolean, 
    default: false 
  },
  loadingText: { 
    type: String, 
    default: 'Loading...' 
  },
  
  // Styling options
  fullWidth: { 
    type: Boolean, 
    default: false 
  },
  rounded: { 
    type: Boolean, 
    default: true 
  },
  ripple: { 
    type: Boolean, 
    default: true 
  },
  
  // Accessibility
  ariaLabel: { 
    type: String, 
    default: null 
  }
})

// Emits
const emit = defineEmits(['click', 'focus', 'blur'])

// Template refs
const buttonRef = ref(null)

// State
const focused = ref(false)
const ripples = ref([])
let rippleCount = 0

// Computed classes
const buttonClasses = computed(() => {
  const classes = [
    // Base styles
    'relative',
    'inline-flex',
    'items-center',
    'justify-center',
    'font-semibold',
    'text-center',
    'transition-all',
    'duration-200',
    'ease-out',
    'cursor-pointer',
    'select-none',
    'focus:outline-none',
    'overflow-hidden',
    
    // Rounded corners
    props.rounded ? 'rounded-2xl' : 'rounded-none',
    
    // Full width
    props.fullWidth ? 'w-full' : 'inline-flex',
    
    // Size variants
    sizeClasses.value,
    
    // Variant styles
    variantClasses.value,
    
    // Disabled state
    (props.disabled || props.loading) && 'cursor-not-allowed opacity-60',
    
    // Loading state
    props.loading && 'pointer-events-none'
  ]
  
  return classes.filter(Boolean)
})

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'px-3 py-1.5 text-xs min-h-[28px]',
    sm: 'px-4 py-2 text-sm min-h-[32px]',
    md: 'px-6 py-3 text-base min-h-[40px]',
    lg: 'px-8 py-4 text-lg min-h-[48px]',
    xl: 'px-10 py-5 text-xl min-h-[56px]'
  }
  return sizes[props.size] || sizes.md
})

const variantClasses = computed(() => {
  const variants = {
    primary: [
      'bg-gradient-to-r from-[#5A1188] to-[#9D38CF]',
      'text-white',
      'shadow-md',
      'hover:shadow-lg hover:scale-105',
      'active:scale-95',
      'border-2 border-transparent'
    ],
    secondary: [
      'bg-white',
      'text-[#5A1188]',
      'border-2 border-[#5A1188]',
      'shadow-sm',
      'hover:bg-[#5A1188] hover:text-white hover:shadow-md',
      'active:scale-95'
    ],
    ghost: [
      'bg-transparent',
      'text-[#5A1188]',
      'border-2 border-transparent',
      'hover:bg-gray-50',
      'active:bg-gray-100'
    ],
    outline: [
      'bg-transparent',
      'text-gray-700',
      'border-2 border-gray-300',
      'hover:border-[#5A1188] hover:text-[#5A1188]',
      'active:bg-gray-50'
    ],
    success: [
      'bg-green-600',
      'text-white',
      'shadow-md',
      'hover:shadow-lg hover:scale-105',
      'active:scale-95',
      'border-2 border-transparent'
    ],
    warning: [
      'bg-yellow-500',
      'text-white',
      'shadow-md',
      'hover:shadow-lg hover:scale-105',
      'active:scale-95',
      'border-2 border-transparent'
    ],
    error: [
      'bg-red-600',
      'text-white',
      'shadow-md',
      'hover:shadow-lg hover:scale-105',
      'active:scale-95',
      'border-2 border-transparent'
    ],
    info: [
      'bg-blue-600',
      'text-white',
      'shadow-md',
      'hover:shadow-lg hover:scale-105',
      'active:scale-95',
      'border-2 border-transparent'
    ]
  }
  
  return variants[props.variant]?.join(' ') || variants.primary.join(' ')
})

// Methods
const handleClick = (event) => {
  if (props.disabled || props.loading) return
  
  // Create ripple effect
  if (props.ripple) {
    createRipple(event)
  }
  
  emit('click', event)
}

const onFocus = (event) => {
  focused.value = true
  emit('focus', event)
}

const onBlur = (event) => {
  focused.value = false
  emit('blur', event)
}

const createRipple = (event) => {
  if (!buttonRef.value) return
  
  const rect = buttonRef.value.getBoundingClientRect()
  const size = Math.max(rect.width, rect.height)
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
  }, 600)
}

// Focus management for accessibility
const focus = () => {
  nextTick(() => {
    buttonRef.value?.focus()
  })
}

const blur = () => {
  nextTick(() => {
    buttonRef.value?.blur()
  })
}

// Expose public methods
defineExpose({
  focus,
  blur,
  buttonRef
})
</script>

<style scoped>
/* Enhanced loading spinner with DocraTech branding */
.loading-spinner {
  @apply inline-block w-5 h-5 border-2 border-current border-t-transparent rounded-full animate-spin;
}

/* Ripple animation */
@keyframes ripple {
  0% {
    transform: scale(0);
    opacity: 0.6;
  }
  100% {
    transform: scale(1);
    opacity: 0;
  }
}

/* Enhanced hover effects for specific variants */
button:hover:not(:disabled):not(.loading) {
  box-shadow: 0 0 20px rgba(90, 17, 136, 0.3);
}

.success:hover:not(:disabled):not(.loading) {
  box-shadow: 0 0 20px rgba(5, 150, 105, 0.3);
}

.warning:hover:not(:disabled):not(.loading) {
  box-shadow: 0 0 20px rgba(217, 119, 6, 0.3);
}

.error:hover:not(:disabled):not(.loading) {
  box-shadow: 0 0 20px rgba(220, 38, 38, 0.3);
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
  .transition-all,
  .animate-spin,
  .animate-ping {
    transition: none !important;
    animation: none !important;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  button {
    border-width: 3px;
  }
}

/* Print styles */
@media print {
  button {
    background: white !important;
    color: black !important;
    border: 2px solid black !important;
  }
}
</style>
