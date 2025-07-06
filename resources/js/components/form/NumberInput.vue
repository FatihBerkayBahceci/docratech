<!--
  Project: Corporate Healthcare Platform UI Library
  Component: NumberInput
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="flex flex-col gap-2 mb-5 relative"
    :class="{
      'number-group--focused': focused,
      'number-group--error': !!error,
      'number-group--disabled': disabled
    }"
  >
    <label
      v-if="label"
      class="font-semibold text-[var(--color-label)] mb-1 transition-colors"
      >{{ label }}</label>
    <div
      class="flex items-center bg-[var(--color-bg-input)] rounded-xl border-2 border-[var(--color-border)] transition-all duration-300 overflow-hidden number-wrapper"
    >
      <button
        type="button"
        class="number-btn number-decrease"
        @click="decrease"
        :disabled="disabled || (min !== undefined && Number(modelValue) <= min)"
        aria-label="Decrease value"
        tabindex="0"
      >
        <i class="bi bi-dash"></i>
      </button>
      <input
        type="number"
        class="flex-1 border-0 bg-transparent outline-none text-base px-4 py-3 text-[var(--color-text)] rounded-xl transition-colors text-center number-input"
        :placeholder="placeholder"
        :value="modelValue"
        :min="min"
        :max="max"
        :step="step"
        :disabled="disabled"
        @focus="focused = true"
        @blur="focused = false"
        @input="handleInput"
        autocomplete="off"
        :aria-invalid="!!error"
        inputmode="decimal"
        spellcheck="false"
      />
      <button
        type="button"
        class="number-btn number-increase"
        @click="increase"
        :disabled="disabled || (max !== undefined && Number(modelValue) >= max)"
        aria-label="Increase value"
        tabindex="0"
      >
        <i class="bi bi-plus"></i>
      </button>
    </div>
    <transition name="fade-slide">
      <div
        v-if="error"
        class="text-[var(--color-error)] text-sm mt-1 min-h-[18px] font-medium number-error"
        role="alert"
      >{{ error }}</div>
    </transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref } from 'vue'

const props = defineProps({
  modelValue: [Number, String],
  label: String,
  placeholder: String,
  min: Number,
  max: Number,
  step: { type: Number, default: 1 },
  error: String,
  disabled: Boolean
})

const emit = defineEmits(['update:modelValue'])

const focused = ref(false)

function handleInput(event) {
  const value = event.target.value === '' ? null : Number(event.target.value)
  emit('update:modelValue', value)
}
function increase() {
  if (props.disabled) return
  const value = (Number(props.modelValue) || 0) + props.step
  if (props.max === undefined || value <= props.max) {
    emit('update:modelValue', value)
  }
}
function decrease() {
  if (props.disabled) return
  const value = (Number(props.modelValue) || 0) - props.step
  if (props.min === undefined || value >= props.min) {
    emit('update:modelValue', value)
  }
}
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-input: #181124;
  --color-label: #FFF;
  --color-text: #FFF;
  --color-desc: #B1A9C7;
  --color-error: #ef4444;
  --color-border: #6D488D;
}
.number-group--focused .number-wrapper {
  border-color: var(--color-accent);
  box-shadow: 0 0 0 4px rgba(157,56,207,0.13);
}
.number-group--error .number-wrapper {
  border-color: var(--color-error);
  box-shadow: 0 0 0 4px rgba(239,68,68,0.13);
}
.number-group--disabled .number-wrapper {
  background: #221736;
  border-color: var(--color-border);
  opacity: 0.6;
  pointer-events: none;
}
.number-btn {
  background: none;
  border: none;
  color: var(--color-desc);
  cursor: pointer;
  padding: 12px 16px;
  transition: background 0.18s, color 0.18s, transform 0.18s;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 44px;
  font-size: 1.08em;
}
.number-btn:hover:not(:disabled) {
  background: #321c4d;
  color: var(--color-accent);
  transform: scale(1.08);
}
.number-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.number-btn i {
  font-size: 1.15em;
  pointer-events: none;
}
.number-input:disabled {
  background: none;
  color: #9ca3af;
}
.number-input::-webkit-outer-spin-button,
.number-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.number-input[type=number] {
  -moz-appearance: textfield;
}
/* Error micro-animation */
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(.22,1,.36,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
/* Responsive fix */
@media (max-width: 640px) {
  .px-4 { padding-left: 1rem !important; padding-right: 1rem !important;}
  .py-3 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important;}
}
</style>
