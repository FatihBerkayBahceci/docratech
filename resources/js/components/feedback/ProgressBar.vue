<!--
  Project: Enterprise Health ProgressBar
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="flex flex-col gap-2 w-full"
    :class="[`progress-${size}`]"
  >
    <div
      v-if="showLabel"
      class="flex items-center justify-between"
    >
      <span class="font-medium text-base text-white">
        <slot name="label">{{ label }}</slot>
      </span>
      <span
        class="font-semibold text-sm"
        :class="percentageClass"
        >{{ percentage }}%</span>
    </div>

    <div
      class="relative w-full bg-[#241A35] rounded-full shadow-inner"
      :class="trackHeightClass"
      role="progressbar"
      :aria-valuenow="percentage"
      :aria-valuemin="0"
      :aria-valuemax="100"
      :tabindex="0"
    >
      <!-- Bar -->
      <div
        class="progress-bar absolute left-0 top-0 h-full rounded-full transition-all duration-700"
        :class="[barBgClass, striped ? 'progress-striped' : '', animated ? 'progress-animated' : '']"
        :style="{ width: `${percentage}%` }"
      >
        <div
          v-if="striped && animated"
          class="absolute inset-0 pointer-events-none progress-stripe"
        ></div>
      </div>
    </div>

    <div v-if="showValue" class="flex justify-end">
      <span class="text-xs text-[#C9C6D5] font-medium">{{ value }} / {{ max }}</span>
    </div>
  </div>
</template>

<script setup>
// Project: Enterprise Health ProgressBar
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { computed } from 'vue'

const props = defineProps({
  value: {
    type: Number,
    required: true,
    validator: value => value >= 0
  },
  max: {
    type: Number,
    default: 100,
    validator: value => value > 0
  },
  size: {
    type: String,
    default: 'md',
    validator: v => ['sm', 'md', 'lg'].includes(v)
  },
  color: {
    type: String,
    default: 'primary',
    validator: v => ['primary', 'secondary', 'success', 'warning', 'danger', 'info'].includes(v)
  },
  animated: {
    type: Boolean,
    default: true
  },
  showLabel: {
    type: Boolean,
    default: true
  },
  showValue: {
    type: Boolean,
    default: false
  },
  striped: {
    type: Boolean,
    default: false
  },
  label: String
})

// Compute progress percentage
const percentage = computed(() =>
  Math.min(Math.max((props.value / props.max) * 100, 0), 100).toFixed(0)
)

// Variant style mapping
const barBgMap = {
  primary:  'bg-gradient-to-r from-[#5A1188] to-[#9D38CF]',
  secondary:'bg-gradient-to-r from-[#3b82f6] to-[#60a5fa]',
  success:  'bg-gradient-to-r from-[#10b981] to-[#34d399]',
  warning:  'bg-gradient-to-r from-[#f59e0b] to-[#fbbf24]',
  danger:   'bg-gradient-to-r from-[#ef4444] to-[#f87171]',
  info:     'bg-gradient-to-r from-[#06b6d4] to-[#22d3ee]'
}
const percentTextMap = {
  primary:  'text-[#9D38CF]',
  secondary:'text-[#3b82f6]',
  success:  'text-[#10b981]',
  warning:  'text-[#f59e0b]',
  danger:   'text-[#ef4444]',
  info:     'text-[#06b6d4]'
}
const barBgClass = computed(() => barBgMap[props.color] || barBgMap.primary)
const percentageClass = computed(() => percentTextMap[props.color] || percentTextMap.primary)
const trackHeightClass = computed(() => ({
  'h-2': props.size === 'sm',
  'h-3': props.size === 'md',
  'h-4': props.size === 'lg'
}))
</script>

<style scoped>
.progress-bar {
  z-index: 1;
  box-shadow: 0 2px 12px #5A118833, 0 1px 1px #2A183D22;
  transition: width 0.7s cubic-bezier(.22,1,.36,1), background 0.3s;
}
.progress-striped {
  background-image: repeating-linear-gradient(
    45deg,
    rgba(255,255,255,0.14) 0 10%,
    transparent 10% 20%
  );
  background-size: 32px 32px;
}
.progress-animated.progress-striped {
  animation: progress-stripe-move 1.5s linear infinite;
}
@keyframes progress-stripe-move {
  0% { background-position: 32px 0; }
  100% { background-position: 0 0; }
}
.progress-stripe {
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255,255,255,0.18) 50%,
    transparent 100%
  );
  animation: progress-shine 1.8s ease-in-out infinite;
  border-radius: inherit;
}
@keyframes progress-shine {
  0% { transform: translateX(-80%);}
  100% { transform: translateX(110%);}
}

/* Focus visible for progressbar accessibility */
[role="progressbar"]:focus-visible {
  outline: 3px solid #9D38CF;
  outline-offset: 3px;
}

/* Responsive tweaks (if needed) */
</style>
