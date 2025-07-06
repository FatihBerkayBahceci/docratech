<!--
  Project: Enterprise Rating Component
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="inline-flex items-center gap-1 select-none outline-none focus-visible:ring-2 focus-visible:ring-accent/60 rounded-xl px-1 py-1"
    @mouseleave="resetHover"
    @keydown="handleKeydown"
    tabindex="0"
    role="slider"
    :aria-valuenow="modelValue"
    :aria-valuemin="min"
    :aria-valuemax="max"
    :aria-label="ariaLabel"
  >
    <button
      v-for="n in max"
      :key="n"
      type="button"
      class="relative group rounded-full p-1 transition-transform duration-200 ease-in-out focus:outline-none"
      :class="{
        'scale-110 rotate-[6deg] drop-shadow-md': n <= (hoverValue || modelValue),
      }"
      @mouseenter="setHover(n)"
      @mousemove="allowHalf ? setHalfHover($event, n) : null"
      @focus="setHover(n)"
      @blur="resetHover"
      @click="select(n)"
      :aria-label="getAriaLabel(n)"
      :tabindex="-1"
    >
      <Icon
        :name="iconName(n)"
        :size="iconSize"
        :color="n <= (hoverValue || modelValue) ? activeColorResolved : inactiveColorResolved"
        :animated="animated"
        class="transition-all duration-300 ease-out"
      />
      <Icon
        v-if="allowHalf && isHalf(n)"
        :name="iconHalf"
        :size="iconSize"
        :color="activeColorResolved"
        :animated="animated"
        class="absolute top-0 left-0 overflow-hidden"
        style="clip-path: inset(0 50% 0 0);"
      />
    </button>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Icon from './Icon.vue'

defineProps({
  modelValue: { type: Number, default: 0 },
  max: { type: Number, default: 5 },
  min: { type: Number, default: 0 },
  allowHalf: { type: Boolean, default: false },
  icon: { type: String, default: 'star' },
  iconHalf: { type: String, default: 'star-half' },
  iconSize: { type: String, default: 'lg' },
  activeColor: { type: String, default: '#9D38CF' }, // BrandKit Açık Mor
  inactiveColor: { type: String, default: '#4B5563' }, // Tailwind zinc/gray
  animated: { type: Boolean, default: true },
  ariaLabel: { type: String, default: 'Rating' }
})

const emit = defineEmits(['update:modelValue', 'change'])

const hoverValue = ref(null)
const isHalfHovered = ref(false)

const setHover = (n) => {
  hoverValue.value = n
  isHalfHovered.value = false
}

const setHalfHover = (e, n) => {
  const { left, width } = e.target.getBoundingClientRect()
  const x = e.clientX - left
  if (x < width / 2) {
    hoverValue.value = n - 0.5
    isHalfHovered.value = true
  } else {
    hoverValue.value = n
    isHalfHovered.value = false
  }
}

const resetHover = () => {
  hoverValue.value = null
  isHalfHovered.value = false
}

const select = (n) => {
  let value = n
  if (allowHalf && isHalfHovered.value) value = n - 0.5
  emit('update:modelValue', value)
  emit('change', value)
  resetHover()
}

const isHalf = (n) =>
  allowHalf &&
  (hoverValue.value === n - 0.5 || (!hoverValue.value && modelValue === n - 0.5))

const iconName = (n) => {
  if (allowHalf && isHalf(n)) return iconHalf
  return icon
}

const getAriaLabel = (n) => `${n} ${icon}`

const handleKeydown = (e) => {
  let value = modelValue
  if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
    value = Math.min(max, value + (allowHalf ? 0.5 : 1))
    emit('update:modelValue', value)
    emit('change', value)
  } else if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
    value = Math.max(min, value - (allowHalf ? 0.5 : 1))
    emit('update:modelValue', value)
    emit('change', value)
  }
}

// Resolved colors with fallback
const activeColorResolved = computed(() => activeColor || '#9D38CF')
const inactiveColorResolved = computed(() => inactiveColor || '#4B5563')

watch(() => modelValue, resetHover)
</script>

<style scoped>
/* Ekstra keyframe sadece aktif animasyon için */
@keyframes rating-bounce {
  0% { transform: scale(0.8) rotate(-10deg); }
  50% { transform: scale(1.2) rotate(6deg); }
  100% { transform: scale(1) rotate(0); }
}
</style>
