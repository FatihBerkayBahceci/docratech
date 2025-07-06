<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Component: Avatar (Enterprise/Brand)
Project: DocraTech Medical Website Platform
License: MIT
-->

<template>
  <div
    class="relative inline-flex items-center justify-center select-none avatar-enter"
    :class="[sizeClass, variantClass, { 'ring-2 ring-[#9D38CF55]': focus, 'avatar-grouped': group }]"
    :style="avatarStyle"
    tabindex="0"
    @focus="focus = true"
    @blur="focus = false"
    :aria-label="alt || name || 'User Avatar'"
    role="img"
  >
    <!-- Skeleton Loading -->
    <transition name="fade" mode="out-in">
      <div
        v-if="loading"
        key="skeleton"
        class="absolute inset-0 w-full h-full bg-gradient-to-br from-[#ede9fe] to-[#6D28D9]/10 animate-pulse rounded-inherit"
      ></div>
      <!-- Image -->
      <img
        v-else-if="src && !error"
        key="img"
        :src="src"
        :alt="alt || name"
        class="object-cover w-full h-full block"
        @error="handleImageError"
        @load="loading = false"
        loading="lazy"
        draggable="false"
        :style="{ filter: loading ? 'blur(6px)' : 'none', transition: 'filter 0.3s' }"
      />
      <!-- Fallback: Brand Gradient Initials or Icon -->
      <div
        v-else
        key="fallback"
        class="flex items-center justify-center w-full h-full font-bold uppercase"
        :style="initialsStyle"
      >
        <Icon v-if="icon" :name="icon" :size="iconSize" />
        <span v-else>{{ initials }}</span>
      </div>
    </transition>

    <!-- Status Dot (with pulse anim for online) -->
    <span
      v-if="status"
      class="absolute border-2 border-white dark:border-[#181124] rounded-full"
      :class="[statusClass, statusSizeClass, { 'animate-pulse-dot': status === 'online' }]"
      :style="statusPosition"
      aria-label="status"
    ></span>

    <!-- Tooltip (isteğe bağlı, slot ile) -->
    <slot name="tooltip" />
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Icon from '@/components/visual/Icon.vue'

const props = defineProps({
  src: String,
  alt: String,
  size: { type: String, default: 'md', validator: v => ['xs','sm','md','lg','xl','2xl'].includes(v) },
  status: { type: String, default: null, validator: v => ['online','offline','away','busy'].includes(v) },
  icon: String,
  name: String,
  variant: { type: String, default: 'circle', validator: v => ['circle','rounded','square'].includes(v) },
  group: { type: Boolean, default: false }
})

const error = ref(false)
const focus = ref(false)
const loading = ref(!!props.src)

// Responsive size classes
const sizeClass = computed(() => ({
  xs: 'w-7 h-7 text-xs',
  sm: 'w-9 h-9 text-sm',
  md: 'w-12 h-12 text-base',
  lg: 'w-16 h-16 text-lg',
  xl: 'w-20 h-20 text-xl',
  '2xl': 'w-28 h-28 text-2xl'
}[props.size] || 'w-12 h-12'))

const variantClass = computed(() =>
  props.variant === 'square'
    ? 'rounded-none'
    : props.variant === 'rounded'
      ? 'rounded-xl'
      : 'rounded-full'
)

// Status dot renkleri & konumları
const statusColors = {
  online: 'bg-gradient-to-tr from-[#6EE7B7] to-[#16A34A]',
  offline: 'bg-gray-400 dark:bg-gray-600',
  away: 'bg-gradient-to-tr from-[#FACC15] to-[#F59E42]',
  busy: 'bg-gradient-to-tr from-[#F43F5E] to-[#A21CAF]'
}
const statusSizes = {
  xs: 'w-2 h-2',
  sm: 'w-2.5 h-2.5',
  md: 'w-3 h-3',
  lg: 'w-4 h-4',
  xl: 'w-5 h-5',
  '2xl': 'w-6 h-6'
}
const statusOffsets = {
  xs: { bottom: '-3px', right: '-3px' },
  sm: { bottom: '-4px', right: '-4px' },
  md: { bottom: '-6px', right: '-6px' },
  lg: { bottom: '-7px', right: '-7px' },
  xl: { bottom: '-9px', right: '-9px' },
  '2xl': { bottom: '-12px', right: '-12px' }
}

const statusClass = computed(() => statusColors[props.status] || 'bg-gray-400')
const statusSizeClass = computed(() => statusSizes[props.size])
const statusPosition = computed(() => ({
  bottom: statusOffsets[props.size]?.bottom,
  right: statusOffsets[props.size]?.right
}))

// Brand gradient for initials
const initialsStyle = computed(() => ({
  background: 'linear-gradient(90deg,#5A1188 0%,#9D38CF 100%)',
  WebkitBackgroundClip: 'text',
  WebkitTextFillColor: 'transparent',
  color: '#5A1188',
  fontWeight: 700,
  fontSize: '1.2em',
  letterSpacing: '0.05em'
}))

const iconSize = computed(() => props.size)

// Dynamic initials (ilk iki harf)
const initials = computed(() => {
  if (!props.name) return '?'
  return props.name
    .split(' ')
    .map(w => w.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0,2)
})

// Avatar style (bg ve fade in animasyon)
const avatarStyle = computed(() => ({
  '--avatar-size': {
    xs: '1.75rem', sm: '2.25rem', md: '3rem', lg: '4rem', xl: '5rem', '2xl': '7rem'
  }[props.size],
  background: (!props.src || error.value)
    ? 'linear-gradient(135deg,#ede9fe 0%,#9D38CF11 100%)'
    : 'transparent',
  transition: 'background 0.4s'
}))

const handleImageError = () => {
  error.value = true
  loading.value = false
}

// Skeleton loader varsa load event ile kapanır
watch(() => props.src, () => {
  loading.value = !!props.src
})
</script>

<style scoped>
.avatar-enter {
  box-shadow: 0 2px 8px 0 #6D28D910;
  transition: box-shadow 0.24s cubic-bezier(.4,0,.2,1), transform 0.22s;
  outline: none;
}
.avatar-enter:focus-visible {
  outline: 2px solid #9D38CF;
  outline-offset: 2px;
  box-shadow: 0 0 0 2px #5A118855;
}
.avatar-enter:hover {
  box-shadow: 0 6px 24px 0 #9D38CF22;
  transform: scale(1.05);
}

.avatar-grouped {
  margin-left: -1.2rem;
  border: 2px solid #fff;
  z-index: 1;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity .2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.animate-pulse-dot {
  animation: pulse-dot 1.2s infinite cubic-bezier(.4,0,.2,1);
}
@keyframes pulse-dot {
  0%,100% { transform: scale(1); opacity: 1;}
  50% { transform: scale(1.2); opacity: 0.7;}
}
</style>
