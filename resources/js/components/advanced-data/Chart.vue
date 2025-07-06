<!--
  Project: Enterprise Health ChartComponent
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div class="chart-container" ref="chartContainer">
    <div v-if="loading" class="flex items-center justify-center h-64">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
    </div>
    <div v-else-if="!data || !data.length" class="flex items-center justify-center h-64 text-gray-500">
      No data available
    </div>
    <div v-else class="chart-content">
      <!-- Simple bar chart implementation -->
      <div v-if="type === 'bar'" class="bar-chart">
        <div class="flex items-end justify-between h-64 p-4 bg-gray-50 rounded-lg">
          <div 
            v-for="(item, index) in normalizedData" 
            :key="index"
            class="flex flex-col items-center space-y-2"
          >
            <div 
              class="bg-blue-500 rounded-t transition-all duration-300 hover:bg-blue-600"
              :style="{ height: `${item.height}px`, width: '40px' }"
            ></div>
            <span class="text-xs text-gray-600">{{ item.label }}</span>
          </div>
        </div>
      </div>
      
      <!-- Simple line chart implementation -->
      <div v-else-if="type === 'line'" class="line-chart">
        <div class="h-64 p-4 bg-gray-50 rounded-lg">
          <svg width="100%" height="100%" viewBox="0 0 400 200">
            <polyline 
              :points="linePoints"
              fill="none"
              stroke="rgb(59, 130, 246)"
              stroke-width="2"
            />
            <circle 
              v-for="(point, index) in linePointsArray"
              :key="index"
              :cx="point.x"
              :cy="point.y"
              r="4"
              fill="rgb(59, 130, 246)"
            />
          </svg>
        </div>
      </div>
      
      <!-- Simple pie chart implementation -->
      <div v-else-if="type === 'pie'" class="pie-chart">
        <div class="flex items-center justify-center h-64">
          <svg width="200" height="200" viewBox="0 0 200 200">
            <circle 
              v-for="(slice, index) in pieSlices"
              :key="index"
              :cx="100"
              :cy="100"
              :r="80"
              fill="none"
              :stroke="slice.color"
              :stroke-width="20"
              :stroke-dasharray="`${slice.length} ${circumference - slice.length}`"
              :stroke-dashoffset="slice.offset"
              transform="rotate(-90 100 100)"
            />
          </svg>
        </div>
      </div>
      
      <!-- Default: simple stats display -->
      <div v-else class="stats-display">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div 
            v-for="(item, index) in data" 
            :key="index"
            class="bg-white p-4 rounded-lg shadow text-center"
          >
            <div class="text-2xl font-bold text-gray-900">{{ item.value }}</div>
            <div class="text-sm text-gray-600">{{ item.label }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Project: Enterprise Health ChartComponent
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, nextTick, watch, onMounted } from 'vue'

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  },
  type: {
    type: String,
    default: 'bar',
    validator: (value) => ['bar', 'line', 'pie', 'stats'].includes(value)
  },
  loading: {
    type: Boolean,
    default: false
  },
  height: {
    type: [String, Number],
    default: 256
  },
  colors: {
    type: Array,
    default: () => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
  }
})

const chartContainer = ref(null)

// Normalize data for bar chart
const normalizedData = computed(() => {
  if (!props.data || !props.data.length) return []
  
  const maxValue = Math.max(...props.data.map(d => d.value))
  return props.data.map(item => ({
    ...item,
    height: (item.value / maxValue) * 200 // Max height 200px
  }))
})

// Generate line chart points
const linePoints = computed(() => {
  if (!props.data || !props.data.length) return ''
  
  const width = 400
  const height = 200
  const maxValue = Math.max(...props.data.map(d => d.value))
  const minValue = Math.min(...props.data.map(d => d.value))
  const range = maxValue - minValue || 1
  
  return props.data.map((item, index) => {
    const x = (index / (props.data.length - 1)) * (width - 40) + 20
    const y = height - 20 - ((item.value - minValue) / range) * (height - 40)
    return `${x},${y}`
  }).join(' ')
})

const linePointsArray = computed(() => {
  if (!linePoints.value) return []
  return linePoints.value.split(' ').map(point => {
    const [x, y] = point.split(',')
    return { x: parseFloat(x), y: parseFloat(y) }
  })
})

// Generate pie chart slices
const circumference = 2 * Math.PI * 80
const pieSlices = computed(() => {
  if (!props.data || !props.data.length) return []
  
  const total = props.data.reduce((sum, item) => sum + item.value, 0)
  let currentOffset = 0
  
  return props.data.map((item, index) => {
    const percentage = item.value / total
    const length = percentage * circumference
    const slice = {
      length,
      offset: currentOffset,
      color: props.colors[index % props.colors.length]
    }
    currentOffset += length
    return slice
  })
})

// Watch for data changes
watch(() => props.data, () => {
  nextTick(() => {
    // Trigger any necessary re-renders
  })
}, { deep: true })

onMounted(() => {
  // Initialize chart if needed
})
</script>

<style scoped>
.chart-container {
  width: 100%;
}

.bar-chart .bg-blue-500:hover {
  transform: scale(1.05);
}

.chart-content {
  transition: all 0.3s ease;
}

svg {
  overflow: visible;
}

circle {
  transition: all 0.3s ease;
}

circle:hover {
  r: 6;
}
</style>
