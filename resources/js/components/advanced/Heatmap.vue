<!--
  Project: Enterprise Health Heatmap (Brandkit Revamp)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section class="w-full rounded-3xl border border-brand-dark/30 bg-bg-primary dark:bg-bg-dark shadow-brand-md overflow-hidden heatmap-root">
    <!-- Header & Legend -->
    <div class="flex flex-col md:flex-row md:items-center justify-between px-8 py-5 border-b border-brand-dark/20 bg-gradient-to-r from-brand-dark/80 to-accent/10 dark:bg-gradient-to-r dark:from-brand-dark dark:to-brand/10 gap-3">
      <h3 v-if="title" class="font-semibold text-2xl text-brand-dark tracking-tight">{{ title }}</h3>
      <div class="flex items-center gap-2 min-w-[160px]">
        <span class="text-xs text-brand/90 font-semibold">Düşük</span>
        <div class="flex flex-1 min-w-[60px] h-3 rounded-xl overflow-hidden border border-accent/30 bg-gradient-to-r from-brand via-accent to-brand-dark shadow-brand-sm">
          <div
            v-for="(color, idx) in legendColors"
            :key="idx"
            class="flex-1"
            :style="{ background: color }"
          ></div>
        </div>
        <span class="text-xs text-accent font-semibold">Yüksek</span>
      </div>
    </div>

    <!-- Heatmap Grid -->
    <div class="relative p-5 overflow-auto" ref="heatmapContainer" tabindex="0" aria-label="Isı haritası">
      <!-- Y labels -->
      <div class="absolute left-4 top-12 flex flex-col z-10 select-none pointer-events-none">
        <div
          v-for="(label, i) in yLabels"
          :key="i"
          class="h-[var(--cell-size)] flex items-center px-3 py-0.5 text-sm font-semibold text-brand/90 dark:text-accent bg-bg-card dark:bg-bg-dark border-r border-brand-dark/15 min-w-[70px] justify-end rounded-xl mb-[2px]"
          :style="{ height: cellSize+'px' }"
        >
          {{ label }}
        </div>
      </div>
      <!-- X labels -->
      <div class="absolute left-[90px] top-0 flex z-10 select-none pointer-events-none">
        <div
          v-for="(label, i) in xLabels"
          :key="i"
          class="min-w-[var(--cell-size)] w-[var(--cell-size)] flex justify-center items-center py-0.5 text-xs font-semibold text-brand/80 dark:text-accent bg-bg-card dark:bg-bg-dark border-b border-brand-dark/15 rounded-xl"
          :style="{ width: cellSize+'px' }"
        >
          {{ label }}
        </div>
      </div>
      <!-- Heatmap Cells -->
      <div class="ml-[90px] mt-[44px] flex flex-col gap-[4px]">
        <div
          v-for="(row, y) in data"
          :key="y"
          class="flex gap-[4px]"
        >
          <button
            v-for="(value, x) in row"
            :key="x"
            class="rounded-xl outline-none focus:ring-2 focus:ring-accent/50 transition-transform duration-150 heatmap-cell"
            :class="{
              'opacity-50 pointer-events-none': value===null||value===undefined
            }"
            :style="getCellStyle(value)"
            @click="handleCellClick(y, x, value)"
            @mouseenter="handleCellHover(y, x, value, $event)"
            @mouseleave="handleCellLeave"
            :aria-label="`${yLabels[y]}-${xLabels[x]}: ${formatValue(value)}`"
          >
            <transition name="fade">
              <span v-if="showValues && value!==null && value!==undefined"
                class="text-[13px] font-bold text-white drop-shadow heatmap-value">
                {{ formatValue(value) }}
              </span>
            </transition>
          </button>
        </div>
      </div>
      <!-- Tooltip -->
      <transition name="fade-slide">
        <div
          v-if="tooltip"
          class="fixed md:absolute px-4 py-3 rounded-2xl bg-gradient-to-tr from-brand-dark to-accent text-white text-xs font-semibold shadow-brand-xl z-50 pointer-events-none transition-all duration-200 heatmap-tooltip"
          :style="tooltipStyle"
          role="tooltip"
        >
          <div>{{ tooltip.label }}</div>
          <div class="font-bold text-accent text-lg mt-1">{{ formatValue(tooltip.value) }}</div>
        </div>
      </transition>
    </div>
  </section>
</template>

<script setup>
// Project: Enterprise Health Heatmap (Brandkit Revamp)
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  title: { type: String, default: null },
  data: { type: Array, required: true },
  xLabels: { type: Array, default: () => [] },
  yLabels: { type: Array, default: () => [] },
  cellSize: { type: Number, default: 54 },
  showValues: { type: Boolean, default: false },
  colorScheme: {
    type: String,
    default: 'brand',
    validator: v => ['brand', 'green', 'red', 'blue', 'orange'].includes(v)
  },
  minValue: { type: Number, default: null },
  maxValue: { type: Number, default: null }
})

const emit = defineEmits(['cell-click', 'cell-hover'])

const heatmapContainer = ref(null)
const tooltip = ref(null)
const tooltipStyle = ref({})

// Brandkit gradyan paleti ve diğer şemalar
const colorSchemes = {
  brand: [
    'linear-gradient(90deg, #2A183D, #5A1188)', // en düşük
    'linear-gradient(90deg, #5A1188, #6D488D)',
    'linear-gradient(90deg, #6D488D, #9D38CF)',
    'linear-gradient(90deg, #9D38CF, #D2A6F8)',
    'linear-gradient(90deg, #C099E4, #E4C8FB)',
    'linear-gradient(90deg, #E4C8FB, #F5EBFF)', // accent mor tonları
    'linear-gradient(90deg, #F5EBFF, #F7F6FD)', // en yüksek (beyaza yakın lila)
  ],
  green:  ['#f0fdf4','#dcfce7','#bbf7d0','#86efac','#4ade80','#22c55e','#16a34a','#15803d'],
  red:    ['#fef2f2','#fee2e2','#fecaca','#fca5a5','#f87171','#ef4444','#dc2626','#b91c1c'],
  blue:   ['#eff6ff','#dbeafe','#bfdbfe','#93c5fd','#60a5fa','#3b82f6','#2563eb','#1d4ed8'],
  orange: ['#fff7ed','#ffedd5','#fed7aa','#fdba74','#fb923c','#f97316','#ea580c','#c2410c']
}
const legendColors = computed(() => colorSchemes[props.colorScheme])

const dataRange = computed(() => {
  const values = props.data.flat().filter(v=>v!==null && v!==undefined)
  const min = props.minValue !== null ? props.minValue : Math.min(...values)
  const max = props.maxValue !== null ? props.maxValue : Math.max(...values)
  return { min, max }
})

const getCellStyle = (value) => {
  const style = {
    width: props.cellSize + 'px',
    height: props.cellSize + 'px',
    border: '2px solid transparent'
  }
  if (value === null || value === undefined) {
    style.background = '#302047'
    style.cursor = 'default'
    style.opacity = '.45'
    return style
  }
  const { min, max } = dataRange.value
  const norm = (max === min) ? 0 : (value - min) / (max - min)
  const colorIdx = Math.round(norm * (legendColors.value.length - 1))
  style.background = legendColors.value[colorIdx] || legendColors.value[0]
  style.boxShadow = '0 4px 22px -6px #5A118810'
  style.transition = 'background 0.25s, box-shadow 0.18s'
  return style
}

const formatValue = (value) => {
  if (value === null || value === undefined) return '-'
  return typeof value === 'number' ? value.toFixed(1) : value
}

const handleCellClick = (y, x, value) => {
  emit('cell-click', {
    row: y,
    col: x,
    value,
    xLabel: props.xLabels[x],
    yLabel: props.yLabels[y]
  })
}

const handleCellHover = (y, x, value, evt) => {
  if (value === null || value === undefined) return
  tooltip.value = {
    label: `${props.yLabels[y] ?? ''} - ${props.xLabels[x] ?? ''}`,
    value
  }
  positionTooltip(evt)
  emit('cell-hover', {
    row: y, col: x, value,
    xLabel: props.xLabels[x], yLabel: props.yLabels[y]
  })
}

const handleCellLeave = () => { tooltip.value = null }

function positionTooltip(evt) {
  const container = heatmapContainer.value
  if (!container) return
  const containerRect = container.getBoundingClientRect()
  const mouseX = evt?.clientX || 0
  const mouseY = evt?.clientY || 0
  tooltipStyle.value = {
    left: (mouseX - containerRect.left + 14) + 'px',
    top: (mouseY - containerRect.top - 42) + 'px'
  }
}

function handleMouseMove(event) {
  if (!tooltip.value) return
  positionTooltip(event)
}
onMounted(() => {
  heatmapContainer.value?.addEventListener('mousemove', handleMouseMove)
})
onUnmounted(() => {
  heatmapContainer.value?.removeEventListener('mousemove', handleMouseMove)
})
</script>

<style scoped>
:root {
  --cell-size: 54px;
  --brand: #5A1188;
  --accent: #9D38CF;
  --brand-dark: #2A183D;
  --bg-primary: #181124;
  --bg-dark: #211733;
  --bg-card: #2A183D;
}

.bg-brand { background: var(--brand);}
.bg-accent { background: var(--accent);}
.bg-brand-dark { background: var(--brand-dark);}
.bg-bg-primary { background: var(--bg-primary);}
.bg-bg-dark { background: var(--bg-dark);}
.bg-bg-card { background: var(--bg-card);}
.text-brand { color: var(--brand);}
.text-accent { color: var(--accent);}
.text-brand-dark { color: var(--brand-dark);}
.shadow-brand-md { box-shadow: 0 6px 24px 0 #9D38CF25;}
.shadow-brand-xl { box-shadow: 0 18px 56px 0 #9D38CF15;}
.shadow-brand-sm { box-shadow: 0 2px 8px 0 #5A118814;}

.heatmap-root { --cell-size: 54px; }
@media (max-width: 600px) {
  .heatmap-root { --cell-size: 32px; }
}

.heatmap-tooltip {
  min-width: 80px;
  min-height: 34px;
  pointer-events: none;
  font-family: 'Inter', sans-serif;
}

.heatmap-cell:focus, .heatmap-cell:hover {
  z-index: 11;
  box-shadow: 0 8px 28px 0 #9D38CF25;
  transform: scale(1.08);
  border: 2px solid #9D38CF90 !important;
}

.heatmap-value {
  text-shadow: 0 1px 10px #2A183D80, 0 0px 2px #fff6;
}

@media (max-width: 768px) {
  .heatmap-tooltip { font-size: 13px; padding: 8px 10px;}
  .heatmap-root { --cell-size: 32px; }
}
</style>
