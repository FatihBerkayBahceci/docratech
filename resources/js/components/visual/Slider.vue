<!--
Developer: DocraTech Team | Fatih Berkay Bahceci
Language: American English (US)
License: MIT
Project: DocraTech Medical Website Platform
-->
<template>
  <div class="slider" :class="sliderClasses">
    <div v-if="label" class="slider-label">
      {{ label }}
      <span v-if="showValue" class="slider-value">{{ displayValue }}</span>
    </div>

    <div class="slider-container">
      <input
        ref="sliderRef"
        :value="modelValue"
        :min="min"
        :max="max"
        :step="step"
        :disabled="disabled"
        type="range"
        class="slider-input"
        @input="handleInput"
        @change="handleChange"
        @focus="handleFocus"
        @blur="handleBlur"
        :aria-valuemin="min"
        :aria-valuemax="max"
        :aria-valuenow="modelValue"
        role="slider"
        :aria-label="label || 'Slider control'"
      />

      <div class="slider-track">
        <div class="slider-fill" :style="{ width: fillPercentage + '%' }" />
        <div class="slider-thumb" :style="{ left: fillPercentage + '%' }" />
      </div>

      <div v-if="marks.length > 0" class="slider-marks">
        <div
          v-for="mark in marks"
          :key="mark.value"
          class="slider-mark"
          :style="{ left: getMarkPosition(mark.value) + '%' }"
          :class="{ 'slider-mark-active': modelValue >= mark.value }"
        >
          <span class="slider-mark-label">{{ mark.label }}</span>
        </div>
      </div>
    </div>

    <div v-if="showRange" class="slider-range">
      <span class="slider-range-min">{{ formatValue(min) }}</span>
      <span class="slider-range-max">{{ formatValue(max) }}</span>
    </div>
  </div>
</template>

<script>
import { computed, ref } from 'vue'

export default {
  name: 'Slider',
  props: {
    modelValue: { type: Number, required: true },
    min: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    step: { type: Number, default: 1 },
    label: { type: String, default: null },
    showValue: { type: Boolean, default: false },
    showRange: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    marks: { type: Array, default: () => [] },
    format: { type: Function, default: null },
    animated: { type: Boolean, default: true },
    variant: {
      type: String,
      default: 'default',
      validator: value => ['default', 'primary', 'success', 'warning', 'danger'].includes(value)
    }
  },
  emits: ['update:modelValue', 'change', 'focus', 'blur'],
  setup(props, { emit }) {
    const sliderRef = ref(null)

    const sliderClasses = computed(() => [
      'slider',
      {
        'slider-disabled': props.disabled,
        'slider-animated': props.animated,
        [`slider-${props.variant}`]: props.variant !== 'default'
      }
    ])

    const fillPercentage = computed(() => ((props.modelValue - props.min) / (props.max - props.min)) * 100)

    const displayValue = computed(() => props.format ? props.format(props.modelValue) : props.modelValue)

    const handleInput = event => emit('update:modelValue', parseFloat(event.target.value))
    const handleChange = event => emit('change', parseFloat(event.target.value))
    const handleFocus = () => emit('focus')
    const handleBlur = () => emit('blur')

    const getMarkPosition = value => ((value - props.min) / (props.max - props.min)) * 100
    const formatValue = value => props.format ? props.format(value) : value

    return {
      sliderRef,
      sliderClasses,
      fillPercentage,
      displayValue,
      handleInput,
      handleChange,
      handleFocus,
      handleBlur,
      getMarkPosition,
      formatValue
    }
  }
}
</script>

<style scoped>
.slider {
  --slider-transition: cubic-bezier(0.4, 0, 0.2, 1);
  --slider-primary: #9D38CF;
  --slider-success: #10b981;
  --slider-warning: #f59e0b;
  --slider-danger: #ef4444;
}

.slider-value {
  font-weight: 600;
  color: var(--slider-primary);
}

.slider-track {
  background: #E5E7EB;
}

.dark .slider-track {
  background: #32284e;
}

.slider-thumb {
  border-color: var(--slider-primary);
  background: white;
}

.dark .slider-thumb {
  background: #181124;
  border-color: var(--slider-primary);
}

.slider-fill {
  background: var(--slider-primary);
}

.slider:not(.slider-disabled):hover .slider-thumb {
  transform: translate(-50%, -50%) scale(1.1);
  box-shadow: 0 4px 10px rgba(157, 56, 207, 0.25);
}
</style>