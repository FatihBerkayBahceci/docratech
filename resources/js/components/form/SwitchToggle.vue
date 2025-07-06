<!--
  Project: Corporate Healthcare Platform UI Library
  Component: SwitchToggle
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <label
    class="switch-group"
    :class="{
      'switch-group--checked': modelValue,
      'switch-group--error': !!error,
      'switch-group--disabled': disabled
    }"
    tabindex="0"
    role="switch"
    :aria-checked="modelValue"
    :aria-disabled="disabled"
    @keydown.space.prevent="toggleSwitch"
    @keydown.enter.prevent="toggleSwitch"
  >
    <input
      type="checkbox"
      class="switch-input"
      :checked="modelValue"
      :disabled="disabled"
      @change="toggleSwitch"
      tabindex="-1"
      aria-hidden="true"
    />
    <span class="switch-track">
      <span class="switch-thumb"></span>
    </span>
    <span class="switch-label">{{ label }}</span>
    <span v-if="description" class="switch-desc">{{ description }}</span>
    <transition name="fade-slide">
      <span v-if="error" class="switch-error" role="alert">{{ error }}</span>
    </transition>
  </label>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

const props = defineProps({
  modelValue: Boolean,
  label: String,
  description: String,
  error: String,
  disabled: Boolean
})
const emit = defineEmits(['update:modelValue'])

function toggleSwitch() {
  if (!props.disabled) emit('update:modelValue', !props.modelValue)
}
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-dark-bg: #181124;
  --color-border: #6D488D;
  --color-error: #ef4444;
  --color-label: #FFF;
  --color-desc: #B1A9C7;
}
.switch-group {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  user-select: none;
  position: relative;
  margin-bottom: 14px;
  font-size: 1rem;
  transition: color 0.2s;
  outline: none;
}
.switch-group--disabled {
  opacity: 0.6;
  pointer-events: none;
}
.switch-input {
  display: none;
}
.switch-track {
  width: 42px;
  height: 24px;
  border-radius: 14px;
  background: #342555;
  position: relative;
  transition: background 0.25s cubic-bezier(.22,1,.36,1), box-shadow 0.22s;
  display: flex;
  align-items: center;
  box-shadow: 0 1.5px 10px 0 rgba(90,17,136,0.07);
}
.switch-group--checked .switch-track {
  background: linear-gradient(90deg, #5A1188 0%, #9D38CF 100%);
  box-shadow: 0 0 0 4px rgba(157,56,207,0.08);
}
.switch-group--error .switch-track {
  background: #fee2e2;
  box-shadow: 0 0 0 4px rgba(239,68,68,0.10);
}
.switch-thumb {
  position: absolute;
  left: 3px;
  top: 3px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 2px 8px rgba(90,17,136,0.12);
  transition: left 0.23s cubic-bezier(.22,1,.36,1), background 0.22s;
  will-change: left, background;
}
.switch-group--checked .switch-thumb {
  left: 21px;
  background: #9D38CF;
  animation: thumb-bounce 0.26s cubic-bezier(.22,1,.36,1);
}
@keyframes thumb-bounce {
  0% { left: 3px; }
  62% { left: 25px; }
  85% { left: 18px; }
  100% { left: 21px; }
}
.switch-label {
  font-weight: 500;
  color: var(--color-label);
  letter-spacing: 0.01em;
  user-select: none;
}
.switch-desc {
  color: var(--color-desc);
  font-size: 0.95em;
  margin-left: 4px;
}
.switch-error {
  color: var(--color-error);
  font-size: 0.95em;
  margin-left: 10px;
}
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.28s cubic-bezier(.22,1,.36,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
</style>
