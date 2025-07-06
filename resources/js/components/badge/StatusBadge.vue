<!--
  DocraTech Medical Platform - Status Badge Component
  Features: Multiple status variants, animations, accessibility, enhanced modern design
-->

<template>
  <span
    :class="badgeClasses"
    :aria-label="`Status: ${statusLabel}`"
    role="status"
  >
    <!-- Enhanced Background Effect -->
    <span 
      v-if="enhanced"
      class="absolute inset-0 bg-gradient-to-r opacity-20 rounded-full blur-sm"
      :class="enhancedBgClasses"
      aria-hidden="true"
    ></span>

    <!-- Status Icon -->
    <span 
      v-if="showIcon"
      :class="iconClasses"
      aria-hidden="true"
    >
      <svg 
        v-if="status === 'active'"
        :class="iconSizeClasses" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      <svg 
        v-else-if="status === 'inactive'"
        :class="iconSizeClasses" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <svg 
        v-else-if="status === 'pending'"
        :class="iconSizeClasses" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
      </svg>
      <svg 
        v-else-if="status === 'suspended'"
        :class="iconSizeClasses" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      <svg 
        v-else
        :class="iconSizeClasses" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
      </svg>
    </span>

    <!-- Status Dot (for enhanced mode) -->
    <span 
      v-if="enhanced" 
      class="relative flex-shrink-0"
      :class="dotSizeClasses"
    >
      <span 
        class="absolute inset-0 rounded-full"
        :class="[dotClasses, { 'animate-ping': status === 'active' && animated }]"
      ></span>
      <span 
        class="relative inline-block rounded-full"
        :class="dotClasses"
      ></span>
    </span>

    <!-- Status Text -->
    <span :class="textClasses">{{ statusLabel }}</span>
    
    <!-- Pulse Animation for Active Status -->
    <span 
      v-if="status === 'active' && animated && !enhanced"
      class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
      :class="pulseClasses"
      aria-hidden="true"
    ></span>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: (value) => ['active', 'inactive', 'pending', 'suspended', 'online', 'offline'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'dot', 'pill', 'minimal'].includes(value)
  },
  enhanced: {
    type: Boolean,
    default: false
  },
  showIcon: {
    type: Boolean,
    default: true
  },
  animated: {
    type: Boolean,
    default: true
  }
})

const statusLabel = computed(() => {
  const labels = {
    active: 'Active',
    inactive: 'Inactive',
    pending: 'Pending',
    suspended: 'Suspended',
    online: 'Online',
    offline: 'Offline'
  }
  return labels[props.status] || props.status.charAt(0).toUpperCase() + props.status.slice(1)
})

const badgeClasses = computed(() => {
  const baseClasses = [
    'relative',
    'inline-flex',
    'items-center',
    'font-medium',
    'transition-all',
    'duration-300'
  ]

  // Enhanced vs Regular styling
  if (props.enhanced) {
    baseClasses.push(
      'backdrop-blur-sm',
      'rounded-xl',
      'shadow-lg',
      'border-0',
      'hover:shadow-xl',
      'hover:scale-105'
    )
  } else {
    baseClasses.push(
      'rounded-full',
      'border'
    )
  }

  // Size classes
  const sizeClasses = {
    sm: props.enhanced ? 'px-3 py-2 text-xs gap-2' : 'px-2 py-1 text-xs gap-1',
    md: props.enhanced ? 'px-4 py-2.5 text-sm gap-2.5' : 'px-2.5 py-1.5 text-xs gap-1.5',
    lg: props.enhanced ? 'px-5 py-3 text-base gap-3' : 'px-3 py-2 text-sm gap-2',
    xl: props.enhanced ? 'px-6 py-4 text-lg gap-4' : 'px-4 py-3 text-base gap-3'
  }

  // Status-based color classes
  const statusClasses = {
    active: props.enhanced ? [
      'bg-emerald-100/70',
      'text-emerald-800',
      'hover:bg-emerald-200/70'
    ] : [
      'bg-green-100',
      'text-green-800',
      'border-green-200',
      'hover:bg-green-200'
    ],
    inactive: props.enhanced ? [
      'bg-gray-100/70',
      'text-gray-700',
      'hover:bg-gray-200/70'
    ] : [
      'bg-gray-100',
      'text-gray-800',
      'border-gray-200',
      'hover:bg-gray-200'
    ],
    pending: props.enhanced ? [
      'bg-amber-100/70',
      'text-amber-800',
      'hover:bg-amber-200/70'
    ] : [
      'bg-yellow-100',
      'text-yellow-800',
      'border-yellow-200',
      'hover:bg-yellow-200'
    ],
    suspended: props.enhanced ? [
      'bg-red-100/70',
      'text-red-800',
      'hover:bg-red-200/70'
    ] : [
      'bg-red-100',
      'text-red-800',
      'border-red-200',
      'hover:bg-red-200'
    ],
    online: props.enhanced ? [
      'bg-blue-100/70',
      'text-blue-800',
      'hover:bg-blue-200/70'
    ] : [
      'bg-blue-100',
      'text-blue-800',
      'border-blue-200',
      'hover:bg-blue-200'
    ],
    offline: props.enhanced ? [
      'bg-slate-100/70',
      'text-slate-600',
      'hover:bg-slate-200/70'
    ] : [
      'bg-gray-100',
      'text-gray-600',
      'border-gray-200',
      'hover:bg-gray-200'
    ]
  }

  return [
    ...baseClasses,
    sizeClasses[props.size],
    ...(statusClasses[props.status] || statusClasses.inactive)
  ].flat()
})

const iconClasses = computed(() => {
  const classes = ['flex-shrink-0', 'relative', 'z-10']
  
  // Add pulse animation for active status
  if (props.status === 'active' && props.animated && !props.enhanced) {
    classes.push('animate-pulse')
  }
  
  return classes
})

const iconSizeClasses = computed(() => {
  const sizes = {
    sm: 'w-3 h-3',
    md: 'w-4 h-4',
    lg: 'w-5 h-5',
    xl: 'w-6 h-6'
  }
  return sizes[props.size]
})

const dotSizeClasses = computed(() => {
  const sizes = {
    sm: 'w-2 h-2',
    md: 'w-2.5 h-2.5',
    lg: 'w-3 h-3',
    xl: 'w-4 h-4'
  }
  return sizes[props.size]
})

const dotClasses = computed(() => {
  const statusDotClasses = {
    active: 'bg-emerald-500',
    inactive: 'bg-gray-400',
    pending: 'bg-amber-500',
    suspended: 'bg-red-500',
    online: 'bg-blue-500',
    offline: 'bg-slate-400'
  }
  
  return statusDotClasses[props.status] || statusDotClasses.inactive
})

const textClasses = computed(() => {
  const classes = ['relative', 'z-10']
  
  if (props.enhanced) {
    classes.push('font-semibold')
  } else {
    classes.push('font-medium')
  }
  
  return classes
})

const enhancedBgClasses = computed(() => {
  const statusBgClasses = {
    active: 'from-emerald-400 to-green-500',
    inactive: 'from-gray-400 to-slate-500',
    pending: 'from-amber-400 to-orange-500',
    suspended: 'from-red-400 to-rose-500',
    online: 'from-blue-400 to-indigo-500',
    offline: 'from-slate-400 to-gray-500'
  }
  
  return statusBgClasses[props.status] || statusBgClasses.inactive
})

const pulseClasses = computed(() => {
  const statusPulseClasses = {
    active: 'bg-green-400',
    online: 'bg-blue-400',
    pending: 'bg-yellow-400'
  }
  
  return statusPulseClasses[props.status] || 'bg-gray-400'
})
</script>

<style scoped>
/* Custom pulse animation for better visual feedback */
@keyframes status-pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-status-pulse {
  animation: status-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Enhanced glass morphism effect */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
  .animate-ping,
  .animate-pulse,
  .animate-status-pulse {
    animation: none !important;
  }
  
  .hover\:scale-105:hover {
    transform: none !important;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .relative {
    border-width: 2px;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .backdrop-blur-sm {
    background-color: rgba(0, 0, 0, 0.1);
  }
}
</style>
