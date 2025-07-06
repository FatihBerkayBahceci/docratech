<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Enterprise QuickActions with Tooltip, Skeleton, Badge, Keyboard, etc.
-->
<template>
  <div
    :class="[
      'flex flex-wrap items-center',
      variant === 'stacked' ? 'flex-col items-stretch space-y-2 space-x-0' : 'flex-row space-x-2',
      variant === 'compact' ? 'space-x-1' : '',
      sizeClass
    ]"
    role="group"
    aria-label="Hızlı Aksiyonlar"
  >
    <!-- Skeleton Loading State -->
    <template v-if="loading">
      <div v-for="i in 3" :key="i"
        class="animate-pulse rounded-2xl h-9 w-28 bg-gradient-to-r from-[#f3f4f6] to-[#ede9fe] dark:from-[#2A183D] dark:to-[#181124] my-1">
      </div>
    </template>
    <template v-else>
      <div
        v-for="action in actions"
        :key="action.key"
        class="relative group"
        @keydown.enter.space.prevent="handleAction(action)"
        tabindex="0"
        :aria-disabled="action.disabled ? 'true' : 'false'"
        :aria-label="action.label"
      >
        <AppButton
          :variant="action.variant || 'secondary'"
          :size="action.size ? (action.size === 'sm' ? 'small' : action.size === 'md' ? 'medium' : action.size === 'lg' ? 'large' : action.size) : size"
          :icon="action.icon"
          :loading="action.loading"
          :disabled="action.disabled"
          @click="handleAction(action)"
          class="transition-all duration-150 quick-action-btn"
          :class="[
            'rounded-2xl font-semibold shadow-sm select-none',
            'focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2',
            'hover:scale-[1.05] active:scale-95',
            action.danger ? 'border-red-200 text-red-700 hover:bg-red-50 dark:border-red-500 dark:text-red-300' : '',
            action.success ? 'border-green-200 text-green-700 hover:bg-green-50 dark:border-green-600 dark:text-green-300' : '',
            action.disabled ? 'opacity-50 cursor-not-allowed' : ''
          ]"
          v-bind="action.attrs"
        >
          {{ action.label }}
          <!-- Badge desteği -->
          <span
            v-if="action.badge"
            class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gradient-to-r from-[#5A1188] to-[#9D38CF] text-white font-bold animate-bounce"
          >
            {{ action.badge }}
          </span>
        </AppButton>

        <!-- Tooltip desteği -->
        <span
          v-if="action.tooltip"
          class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2
          whitespace-nowrap bg-[#181124] text-white text-xs font-medium px-3 py-1 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-all duration-200 z-50"
        >
          {{ action.tooltip }}
        </span>
        <!-- Disable reason badge -->
        <span v-if="action.disabled && action.disableReason"
          class="absolute -bottom-7 left-1/2 -translate-x-1/2 text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-100 px-2 py-0.5 rounded shadow-sm">
          <i class="bi bi-info-circle mr-1"></i>{{ action.disableReason }}
        </span>
        <!-- Divider örneği -->
        <div v-if="divider && action.key !== actions[actions.length-1].key"
          class="inline-block mx-1 w-1 h-7 rounded-full bg-gradient-to-b from-[#9D38CF]/70 to-[#5A1188]/60"></div>
      </div>
    </template>
  </div>
</template>

<script setup>
defineOptions({ name: 'QuickActions' })
import { computed } from 'vue'
import AppButton from '@/components/button/AppButton.vue'

const props = defineProps({
  actions: {
    type: Array,
    required: true,
    default: () => []
  },
  variant: {
    type: String,
    default: 'default',
    validator: v => ['default', 'compact', 'stacked'].includes(v)
  },
  size: {
    type: String,
    default: 'sm',
    validator: v => ['xs', 'sm', 'md', 'lg'].includes(v)
  },
  loading: {
    type: Boolean,
    default: false
  },
  divider: {
    type: Boolean,
    default: false
  }
})
const emit = defineEmits(['action'])

const sizeClass = computed(() => ({
  xs: 'space-x-1 [&_.quick-action-btn]:px-2 [&_.quick-action-btn]:py-1 text-xs',
  sm: '[&_.quick-action-btn]:px-3 [&_.quick-action-btn]:py-1.5 text-sm',
  md: '[&_.quick-action-btn]:px-4 [&_.quick-action-btn]:py-2 text-base',
  lg: '[&_.quick-action-btn]:px-6 [&_.quick-action-btn]:py-3 text-lg',
}[props.size || 'sm']))

function handleAction(action) {
  if (action.disabled) return
  if (action.handler) action.handler()
  emit('action', action)
}
</script>

<style scoped>
.quick-action-btn {
  transition:
    box-shadow 0.18s cubic-bezier(.4,0,.2,1),
    transform 0.18s cubic-bezier(.4,0,.2,1),
    background 0.2s, color 0.2s;
  will-change: transform, box-shadow;
}
.quick-action-btn:active {
  box-shadow: 0 2px 8px 0 rgba(109,40,217,0.09);
}
</style>
