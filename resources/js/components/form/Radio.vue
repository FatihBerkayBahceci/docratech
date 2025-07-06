<!--
  Project: Corporate Healthcare Platform UI Library
  Component: Radio
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <label
    class="radio-group"
    :class="{
      'radio-group--checked': modelValue === value,
      'radio-group--error': !!error,
      'radio-group--disabled': disabled
    }"
    :aria-checked="modelValue === value"
    :aria-disabled="disabled"
    tabindex="0"
    role="radio"
    @keydown.space.prevent="emitChecked"
    @keydown.enter.prevent="emitChecked"
  >
    <input
      type="radio"
      class="radio-input"
      :checked="modelValue === value"
      :disabled="disabled"
      @change="emitChecked"
      tabindex="-1"
      aria-hidden="true"
    />
    <span class="radio-box">
      <span v-if="modelValue === value" class="radio-dot"></span>
    </span>
    <span class="radio-label">{{ label }}</span>
    <span v-if="description" class="radio-desc">{{ description }}</span>
    <transition name="fade-slide">
      <span v-if="error" class="radio-error" role="alert">{{ error }}</span>
    </transition>
  </label>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

const props = defineProps({
  modelValue: [String, Number, Boolean],
  value: [String, Number, Boolean],
  label: String,
  description: String,
  error: String,
  disabled: Boolean
})

const emit = defineEmits(['update:modelValue'])

function emitChecked() {
  if (!props.disabled && props.modelValue !== props.value) {
    emit('update:modelValue', props.value)
  }
}
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-radio: #181124;
  --color-checked: #6D28D9;
  --color-border: #6D488D;
  --color-error: #ef4444;
  --color-label: #FFF;
  --color-desc: #B1A9C7;
}

.radio-group {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  user-select: none;
  position: relative;
  margin-bottom: 14px;
  font-size: 1rem;
  transition: color 0.22s cubic-bezier(.22,1,.36,1);
}
.radio-group--disabled {
  opacity: 0.6;
  pointer-events: none;
}
.radio-input {
  display: none;
}
.radio-box {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 2px solid var(--color-border);
  background: var(--color-bg-radio);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border 0.3s cubic-bezier(.22,1,.36,1), box-shadow 0.3s;
  box-shadow: 0 2px 8px rgba(90,17,136,0.04);
  position: relative;
}
.radio-group--checked .radio-box {
  border-color: var(--color-primary);
  background: #2A183D;
  box-shadow: 0 0 0 4px rgba(157,56,207,0.10);
}
.radio-group--error .radio-box {
  border-color: var(--color-error);
  box-shadow: 0 0 0 4px rgba(239,68,68,0.10);
}
.radio-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--color-primary);
  animation: dot-in 0.28s cubic-bezier(.22,1,.36,1);
}
@keyframes dot-in {
  0% { transform: scale(0);}
  100% { transform: scale(1);}
}
.radio-label {
  font-weight: 500;
  color: var(--color-label);
  letter-spacing: 0.01em;
}
.radio-desc {
  color: var(--color-desc);
  font-size: 0.96em;
  margin-left: 4px;
}
.radio-error {
  color: var(--color-error);
  font-size: 0.95em;
  margin-left: 10px;
}
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(.22,1,.36,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
</style>
