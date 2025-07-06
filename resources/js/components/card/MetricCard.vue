<!--
  Project: Enterprise Health MetricCard
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div :class="cardClasses" role="region" :aria-label="label">
    <header class="metric-card-header">
      <div :class="iconClasses" aria-hidden="true">
        <Icon :name="icon" size="lg" />
      </div>

      <div v-if="trend" :class="trendClasses" aria-live="polite" aria-atomic="true">
        <Icon :name="trendIcon" size="sm" />
        <span class="metric-card-trend-value">{{ formattedTrend }}</span>
      </div>
    </header>

    <section class="metric-card-content">
      <div class="metric-card-main">
        <div class="metric-card-value" tabindex="0" aria-label="Value">{{ formattedValue }}</div>
        <div class="metric-card-label">{{ label }}</div>
      </div>

      <p v-if="description" class="metric-card-description">{{ description }}</p>
    </section>

    <nav v-if="actions && actions.length" class="metric-card-actions" aria-label="Actions">
      <AppButton
        v-for="action in actions"
        :key="action.key"
        :variant="action.variant || 'outline'"
        :size="action.size || 'sm'"
        @click="handleAction(action)"
        :aria-label="action.label"
        type="button"
      >
        <Icon v-if="action.icon" :name="action.icon" size="sm" />
        {{ action.label }}
      </AppButton>
    </nav>

    <section v-if="chart" class="metric-card-chart" aria-label="Chart visualization">
      <Chart
        :data="chart.data"
        :type="chart.type"
        :options="chart.options"
        :height="chart.height || 60"
      />
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'
import Chart from '../advanced-data/Chart.vue'

defineOptions({ name: 'MetricCard' })

const props = defineProps({
  title: { type: String, default: null },
  value: { type: [String, Number], required: true },
  label: { type: String, required: true },
  description: { type: String, default: null },
  icon: { type: String, default: 'trending-up' },
  variant: { 
    type: String, 
    default: 'default', 
    validator: (v) => ['default', 'primary', 'success', 'warning', 'danger', 'info'].includes(v) 
  },
  trend: { type: Object, default: null },
  actions: { type: Array, default: () => [] },
  chart: { type: Object, default: null },
  format: { type: String, default: 'number' },
  currency: { type: String, default: 'TRY' },
  locale: { type: String, default: 'tr-TR' }
})

const emit = defineEmits(['action'])

const cardClasses = computed(() => {
  const classes = ['metric-card']
  if (props.variant !== 'default') classes.push(`metric-card-${props.variant}`)
  return classes
})

const iconClasses = computed(() => {
  const classes = ['metric-card-icon']
  if (props.variant !== 'default') classes.push(`metric-card-icon-${props.variant}`)
  return classes
})

const trendClasses = computed(() => {
  if (!props.trend) return ''
  const classes = ['metric-card-trend']
  if (props.trend.direction === 'up') classes.push('metric-card-trend-up')
  else if (props.trend.direction === 'down') classes.push('metric-card-trend-down')
  return classes
})

const trendIcon = computed(() => {
  if (!props.trend) return ''
  if (props.trend.direction === 'up') return 'trending-up'
  if (props.trend.direction === 'down') return 'trending-down'
  return 'minus'
})

const formattedValue = computed(() => {
  if (props.format === 'currency') {
    return new Intl.NumberFormat(props.locale, { style: 'currency', currency: props.currency }).format(props.value)
  }
  if (props.format === 'number') {
    return new Intl.NumberFormat(props.locale).format(props.value)
  }
  if (props.format === 'percentage') {
    return `${props.value}%`
  }
  return props.value
})

const formattedTrend = computed(() => {
  if (!props.trend) return ''
  const prefix = props.trend.direction === 'up' ? '+' : ''
  if (props.trend.format === 'percentage') return `${prefix}${props.trend.value}%`
  return `${prefix}${props.trend.value}`
})

function handleAction(action) {
  emit('action', action)
}
</script>

<style scoped>
.metric-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s;
  position: relative;
  overflow: hidden;
  user-select: none;
}

.dark .metric-card {
  background: #1f2937;
  border-color: #374151;
}

.metric-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.metric-card-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.metric-card-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  border-radius: 0.5rem;
  background: #f3f4f6;
  color: #6b7280;
}

.dark .metric-card-icon {
  background: #374151;
  color: #9ca3af;
}

.metric-card-icon-primary {
  background: #eff6ff;
  color: #3b82f6;
}

.metric-card-icon-success {
  background: #f0fdf4;
  color: #10b981;
}

.metric-card-icon-warning {
  background: #fffbeb;
  color: #f59e0b;
}

.metric-card-icon-danger {
  background: #fef2f2;
  color: #ef4444;
}

.metric-card-icon-info {
  background: #f0f9ff;
  color: #06b6d4;
}

.metric-card-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
}

.metric-card-trend-up {
  background: #f0fdf4;
  color: #16a34a;
}

.metric-card-trend-down {
  background: #fef2f2;
  color: #dc2626;
}

.metric-card-content {
  margin-bottom: 1rem;
}

.metric-card-main {
  margin-bottom: 0.5rem;
}

.metric-card-value {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.dark .metric-card-value {
  color: #f9fafb;
}

.metric-card-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.dark .metric-card-label {
  color: #9ca3af;
}

.metric-card-description {
  font-size: 0.75rem;
  color: #9ca3af;
  line-height: 1.4;
}

.dark .metric-card-description {
  color: #6b7280;
}

.metric-card-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.metric-card-chart {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.dark .metric-card-chart {
  border-top-color: #374151;
}

/* Variant styles */
.metric-card-primary {
  border-color: #3b82f6;
  background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
}

.dark .metric-card-primary {
  background: linear-gradient(135deg, #1e3a8a 0%, #111827 100%);
}

.metric-card-success {
  border-color: #10b981;
  background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);
}

.dark .metric-card-success {
  background: linear-gradient(135deg, #064e3b 0%, #111827 100%);
}

.metric-card-warning {
  border-color: #f59e0b;
  background: linear-gradient(135deg, #fffbeb 0%, #f8fafc 100%);
}

.dark .metric-card-warning {
  background: linear-gradient(135deg, #451a03 0%, #111827 100%);
}

.metric-card-danger {
  border-color: #ef4444;
  background: linear-gradient(135deg, #fef2f2 0%, #f8fafc 100%);
}

.dark .metric-card-danger {
  background: linear-gradient(135deg, #450a0a 0%, #111827 100%);
}

.metric-card-info {
  border-color: #06b6d4;
  background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
}

.dark .metric-card-info {
  background: linear-gradient(135deg, #0c4a6e 0%, #111827 100%);
}

/* Responsive design */
@media (max-width: 640px) {
  .metric-card {
    padding: 1rem;
  }

  .metric-card-value {
    font-size: 1.5rem;
  }

  .metric-card-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .metric-card-actions .app-button {
    width: 100%;
  }
}
</style>
