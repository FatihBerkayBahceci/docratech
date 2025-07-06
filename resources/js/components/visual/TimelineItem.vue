<!-- Developer: DocraTech Team - Fatih Berkay Bahceci -->
<template>
  <div :class="itemClasses" :style="itemStyles">
    <!-- Dot -->
    <div :class="dotClasses">
      <Icon
        v-if="icon"
        :name="icon"
        size="sm"
        :color="iconColor"
        :animated="timelineAnimated"
      />
      <div v-else class="w-2 h-2 bg-current rounded-full" />
    </div>

    <!-- Content -->
    <div :class="contentClasses">
      <div class="flex justify-between items-start mb-2">
        <div class="flex items-center gap-2">
          <h3 class="text-sm font-semibold text-white">{{ title }}</h3>
          <Tag
            v-if="status"
            :variant="statusVariant"
            size="sm"
            :animated="timelineAnimated"
          >
            {{ status }}
          </Tag>
        </div>
        <time
          v-if="date"
          :datetime="date"
          class="text-xs text-white/50 font-mono"
        >
          {{ formattedDate }}
        </time>
      </div>

      <p v-if="description" class="text-sm text-white/80 mb-2">
        {{ description }}
      </p>

      <div v-if="$slots.default" class="mt-2">
        <slot />
      </div>

      <div v-if="actions" class="flex items-center gap-2 mt-3">
        <slot name="actions" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, inject } from 'vue'
import Icon from './Icon.vue'
import Tag from './Tag.vue'

const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: null },
  date: { type: String, default: null },
  icon: { type: String, default: null },
  status: { type: String, default: null },
  variant: {
    type: String,
    default: 'default',
    validator: val => ['default', 'primary', 'success', 'warning', 'danger', 'info'].includes(val)
  },
  actions: { type: Boolean, default: false }
})

const timelineVariant = inject('timelineVariant', 'vertical')
const timelineAnimated = inject('timelineAnimated', true)
const timelineStaggered = inject('timelineStaggered', true)

const itemClasses = computed(() => [
  'relative',
  timelineVariant === 'horizontal' ? 'w-72 flex-shrink-0' : 'mb-8',
  timelineAnimated ? 'timeline-item-animated' : ''
])

const dotClasses = computed(() => [
  'absolute z-10 w-6 h-6 rounded-full flex items-center justify-center border-2',
  'border-docratech-backgroundDark bg-docratech-secondary text-white shadow-md transition-all duration-300',
  timelineVariant === 'horizontal'
    ? 'top-1/2 -translate-y-1/2 left-0 -translate-x-1/2'
    : 'left-0 -translate-x-1/2',
  timelineAnimated ? 'hover:scale-110' : ''
])

const contentClasses = computed(() => [
  'relative bg-docratech-backgroundDark border border-docratech-border rounded-xl p-4 shadow-lg transition-all',
  timelineVariant === 'horizontal' ? 'mt-8 ml-2' : 'ml-8',
  timelineAnimated ? 'hover:scale-[1.02] hover:shadow-xl' : ''
])

const iconColor = computed(() => {
  const map = {
    default: theme('colors.docratech.primaryLight'),
    primary: theme('colors.docratech.primaryLight'),
    success: theme('colors.green.500'),
    warning: theme('colors.yellow.500'),
    danger: theme('colors.red.500'),
    info: theme('colors.blue.500')
  }
  return map[props.variant] || theme('colors.docratech.textMuted')
})

const statusVariant = computed(() => {
  const map = {
    completed: 'success',
    pending: 'warning',
    failed: 'danger',
    in_progress: 'info',
    active: 'primary'
  }
  return map[props.status] || 'default'
})

const formattedDate = computed(() => {
  if (!props.date) return ''
  return new Date(props.date).toLocaleDateString()
})

const itemStyles = computed(() => ({
  '--item-delay': timelineStaggered ? '0.1s' : '0s'
}))
</script>

<style scoped>
.timeline-item-animated {
  animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
  animation-delay: var(--item-delay);
}

@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
</style>
