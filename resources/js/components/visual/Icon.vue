<!--
Proje: Enterprise Sağlık Platformu
Bileşen: Advanced Icon
Developer: DocraTech Team - Fatih Berkay Bahceci
Açıklama: Marka uyumlu, gelişmiş animasyonlu Icon bileşeni.
-->
<template>
  <i
    :class="iconClasses"
    @click="handleClick"
    v-bind="$attrs"
  >
    <slot />
  </i>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  name: { type: String, required: true },
  size: { type: String, default: 'md' },
  color: { type: String, default: 'current' },
  animated: { type: Boolean, default: true },
  spin: Boolean,
  pulse: Boolean,
  clickable: Boolean
})

const emit = defineEmits(['click'])

const sizes = {
  xs: 'text-xs',
  sm: 'text-sm',
  md: 'text-base',
  lg: 'text-lg',
  xl: 'text-xl',
  '2xl': 'text-2xl'
}

const colors = {
  current: 'text-current',
  accent: 'text-[#9D38CF]',
  white: 'text-white',
  gray: 'text-gray-400'
}

const iconClasses = computed(() => [
  'inline-block transition-transform duration-300',
  sizes[props.size],
  colors[props.color],
  {
    'cursor-pointer hover:scale-125 hover:text-[#5A1188]': props.clickable,
    'animate-spin': props.spin,
    'animate-pulse': props.pulse,
    'hover:animate-wiggle': props.animated && !props.spin && !props.pulse
  }
])

const handleClick = (event) => {
  if (props.clickable) emit('click', event)
}
</script>

<style scoped>
@keyframes wiggle {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(10deg); }
  75% { transform: rotate(-10deg); }
}

.hover\:animate-wiggle:hover {
  animation: wiggle 0.5s ease-in-out;
}
</style>
