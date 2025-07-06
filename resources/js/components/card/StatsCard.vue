<!--
  Project: Enterprise Health StatsCard
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <AppCard class="stats-card" :class="cardClasses">
    <div class="stats-content">
      <div class="stats-icon" :class="iconClasses" aria-hidden="true">
        <Icon v-if="icon" :name="icon" :size="iconSize" />
      </div>

      <div class="stats-info">
        <div class="stats-label" tabindex="0">{{ label }}</div>
        <div class="stats-value" :class="valueClasses" aria-live="polite" aria-atomic="true">
          <span v-if="loading" class="stats-loading" aria-label="Loading data">
            <div class="loading-dots" role="status" aria-live="polite" aria-atomic="true">
              <span></span>
              <span></span>
              <span></span>
            </div>
          </span>
          <span v-else>{{ formattedValue }}</span>
        </div>

        <div v-if="change !== null && !loading" class="stats-change" :class="changeClasses" aria-label="Change percentage">
          <Icon 
            :name="changeIcon" 
            size="xs" 
            class="change-icon"
            aria-hidden="true"
          />
          <span>{{ formattedChange }}</span>
        </div>
      </div>
    </div>

    <div v-if="trend && !loading" class="stats-trend" aria-hidden="true">
      <div class="trend-chart">
        <svg viewBox="0 0 100 30" class="trend-svg" preserveAspectRatio="none" role="img" aria-label="Trend chart">
          <path 
            :d="trendPath" 
            :stroke="trendColor" 
            fill="none" 
            stroke-width="2"
            class="trend-line"
          />
        </svg>
      </div>
    </div>
  </AppCard>
</template>

<script>
import { computed } from 'vue'
import AppCard from './AppCard.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'StatsCard',
  components: {
    AppCard,
    Icon
  },
  props: {
    label: {
      type: String,
      required: true
    },
    value: {
      type: [String, Number],
      required: true
    },
    change: {
      type: Number,
      default: null
    },
    icon: {
      type: String,
      default: null
    },
    color: {
      type: String,
      default: 'primary',
      validator: (value) => ['primary', 'secondary', 'success', 'warning', 'danger', 'info'].includes(value)
    },
    loading: {
      type: Boolean,
      default: false
    },
    trend: {
      type: Array,
      default: null
    },
    format: {
      type: String,
      default: 'number',
      validator: (value) => ['number', 'currency', 'percentage', 'decimal'].includes(value)
    },
    currency: {
      type: String,
      default: 'USD'
    },
    decimals: {
      type: Number,
      default: 0
    }
  },
  setup(props) {
    const cardClasses = computed(() => [
      'stats-card',
      `stats-card-${props.color}`,
      { 'stats-card-loading': props.loading }
    ])

    const iconClasses = computed(() => [
      'stats-icon',
      `stats-icon-${props.color}`
    ])

    const iconSize = computed(() => 'lg')

    const valueClasses = computed(() => [
      'stats-value',
      { 'stats-value-loading': props.loading }
    ])

    const changeClasses = computed(() => [
      'stats-change',
      {
        'stats-change-positive': props.change > 0,
        'stats-change-negative': props.change < 0,
        'stats-change-neutral': props.change === 0
      }
    ])

    const changeIcon = computed(() => {
      if (props.change > 0) return 'trending-up'
      if (props.change < 0) return 'trending-down'
      return 'minus'
    })

    const formattedValue = computed(() => {
      if (props.loading) return ''
      
      const value = props.value
      
      switch (props.format) {
        case 'currency':
          return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: props.currency
          }).format(value)
        case 'percentage':
          return `${value}%`
        case 'decimal':
          return Number(value).toFixed(props.decimals)
        default:
          return new Intl.NumberFormat('en-US').format(value)
      }
    })

    const formattedChange = computed(() => {
      if (props.change === null) return ''
      
      const absChange = Math.abs(props.change)
      const sign = props.change > 0 ? '+' : props.change < 0 ? '-' : ''
      
      return `${sign}${absChange}%`
    })

    const trendPath = computed(() => {
      if (!props.trend || props.trend.length < 2) return ''
      
      const maxVal = Math.max(...props.trend)
      const points = props.trend.map((value, index) => {
        const x = (index / (props.trend.length - 1)) * 100
        const y = 30 - (value / maxVal) * 30
        return `${x},${y}`
      })
      
      return `M ${points.join(' L ')}`
    })

    const trendColor = computed(() => {
      const colors = {
        primary: '#6D28D9',
        secondary: '#3B82F6',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        info: '#06B6D4'
      }
      return colors[props.color] || colors.primary
    })

    return {
      cardClasses,
      iconClasses,
      iconSize,
      valueClasses,
      changeClasses,
      changeIcon,
      formattedValue,
      formattedChange,
      trendPath,
      trendColor
    }
  }
}
</script>

<style scoped>
.stats-card {
  position: relative;
  overflow: hidden;
  transition: var(--transition);
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.stats-content {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stats-icon {
  flex-shrink: 0;
  width: 3rem;
  height: 3rem;
  border-radius: var(--border-radius);
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-primary-alpha);
  color: var(--color-primary);
}

.stats-icon-primary {
  background: var(--color-primary-alpha);
  color: var(--color-primary);
}

.stats-icon-secondary {
  background: var(--color-secondary-alpha);
  color: var(--color-secondary);
}

.stats-icon-success {
  background: var(--color-success-alpha);
  color: var(--color-success);
}

.stats-icon-warning {
  background: var(--color-warning-alpha);
  color: var(--color-warning);
}

.stats-icon-danger {
  background: var(--color-danger-alpha);
  color: var(--color-danger);
}

.stats-icon-info {
  background: var(--color-info-alpha);
  color: var(--color-info);
}

.stats-info {
  flex: 1;
  min-width: 0;
}

.stats-label {
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
  margin-bottom: 0.25rem;
  font-weight: var(--font-weight-medium);
}

.stats-value {
  font-size: var(--font-size-2xl);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
  line-height: 1;
  margin-bottom: 0.25rem;
}

.stats-value-loading {
  color: var(--color-text-muted);
}

.stats-change {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
}

.stats-change-positive {
  color: var(--color-success);
}

.stats-change-negative {
  color: var(--color-danger);
}

.stats-change-neutral {
  color: var(--color-text-secondary);
}

.change-icon {
  flex-shrink: 0;
}

.stats-trend {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2rem;
  opacity: 0.3;
}

.trend-chart {
  width: 100%;
  height: 100%;
}

.trend-svg {
  width: 100%;
  height: 100%;
}

.trend-line {
  stroke-linecap: round;
  stroke-linejoin: round;
}

/* Loading animation */
.loading-dots {
  display: flex;
  gap: 0.25rem;
}

.loading-dots span {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  background: var(--color-text-muted);
  animation: loading-dots 1.4s ease-in-out infinite both;
}

.loading-dots span:nth-child(1) {
  animation-delay: -0.32s;
}

.loading-dots span:nth-child(2) {
  animation-delay: -0.16s;
}

@keyframes loading-dots {
  0%, 80%, 100% {
    transform: scale(0);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Responsive */
@media (max-width: 640px) {
  .stats-content {
    gap: 0.75rem;
  }

  .stats-icon {
    width: 2.5rem;
    height: 2.5rem;
  }

  .stats-value {
    font-size: var(--font-size-xl);
  }
}
</style>
