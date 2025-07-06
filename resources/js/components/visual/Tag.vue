<!-- Developer: DocraTech Team - Fatih Berkay Bahceci -->
<template>
  <span
    :class="tagClasses"
    @click="handleClick"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
    v-bind="$attrs"
  >
    <Icon
      v-if="icon"
      :name="icon"
      size="xs"
      class="mr-1 shrink-0 text-white/80"
      :animated="animated"
    />
    <slot />
    <Icon
      v-if="removable"
      name="x"
      size="xs"
      class="ml-1 cursor-pointer transition-transform hover:scale-110 text-white/60"
      @click.stop="handleRemove"
      :animated="true"
    />
  </span>
</template>

<script setup>
import { ref, computed } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (val) =>
      ['default', 'primary', 'success', 'warning', 'danger', 'info', 'secondary'].includes(val)
  },
  size: {
    type: String,
    default: 'md',
    validator: (val) => ['sm', 'md', 'lg'].includes(val)
  },
  rounded: { type: Boolean, default: false },
  outlined: { type: Boolean, default: false },
  icon: { type: String, default: null },
  removable: { type: Boolean, default: false },
  animated: { type: Boolean, default: false },
  clickable: { type: Boolean, default: false }
})

const emit = defineEmits(['click', 'remove'])

const isHovered = ref(false)

const sizeClasses = {
  sm: 'px-2 py-1 text-xs',
  md: 'px-3 py-1.5 text-sm',
  lg: 'px-4 py-2 text-base'
}

const variantMap = {
  default: 'bg-[#2A183D] text-white/80',
  primary: 'bg-gradient-to-r from-[#5A1188] to-[#9D38CF] text-white',
  success: 'bg-green-600 text-white',
  warning: 'bg-yellow-500 text-black',
  danger: 'bg-red-600 text-white',
  info: 'bg-cyan-600 text-white',
  secondary: 'bg-[#6D488D] text-white'
}

const outlinedMap = {
  default: 'border border-white/20 text-white/80 bg-transparent',
  primary: 'border border-[#9D38CF] text-[#9D38CF] bg-transparent',
  success: 'border border-green-500 text-green-500 bg-transparent',
  warning: 'border border-yellow-400 text-yellow-400 bg-transparent',
  danger: 'border border-red-500 text-red-500 bg-transparent',
  info: 'border border-cyan-400 text-cyan-400 bg-transparent',
  secondary: 'border border-[#6D488D] text-[#6D488D] bg-transparent'
}

const tagClasses = computed(() => [
  'inline-flex items-center font-medium',
  'transition-all duration-300',
  'select-none',
  'backdrop-blur-sm',
  sizeClasses[props.size],
  props.outlined ? outlinedMap[props.variant] : variantMap[props.variant],
  props.rounded ? 'rounded-full' : 'rounded-xl',
  {
    'cursor-pointer hover:scale-105 hover:shadow-lg': props.clickable,
    'transform-gpu': props.animated || props.clickable,
    'hover:rotate-[1deg]': props.animated && !props.clickable,
    'ring-2 ring-[#9D38CF]/50': isHovered.value && props.clickable
  }
])

function handleClick(event) {
  if (props.clickable) emit('click', event)
}

function handleRemove(event) {
  event.stopPropagation()
  emit('remove', event)
}

function handleMouseEnter() {
  if (props.animated || props.clickable) isHovered.value = true
}

function handleMouseLeave() {
  if (props.animated || props.clickable) isHovered.value = false
}
</script>

<style scoped>
@keyframes tag-fade-in {
  0% {
    opacity: 0;
    transform: scale(0.9) translateY(-4px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes tag-pulse {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(157, 56, 207, 0.7);
  }
  50% {
    box-shadow: 0 0 0 8px rgba(157, 56, 207, 0);
  }
}

.tag-animated {
  animation: tag-fade-in 0.4s ease-out;
}

.tag-pulse {
  animation: tag-pulse 1.8s infinite;
}
</style>
