<!--
  Project: Corporate Healthcare Platform UI Library
  Component: Select
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="select-group"
    :class="{
      'select-group--open': open,
      'select-group--error': !!error,
      'select-group--disabled': disabled
    }"
    @click="toggle"
    tabindex="0"
    role="combobox"
    :aria-expanded="open"
    :aria-disabled="disabled"
    :aria-errormessage="error ? 'select-error-message' : undefined"
    @keydown.down.prevent="openList"
    @keydown.up.prevent="openList"
    @keydown.enter.prevent="open ? selectActive() : openList()"
    @keydown.space.prevent="open ? selectActive() : openList()"
    @keydown.esc="open = false"
    @blur="open = false"
  >
    <label v-if="label" class="select-label">{{ label }}</label>
    <div class="select-control" :aria-invalid="!!error">
      <span class="select-value">
        {{ selectedLabel }}
      </span>
      <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'" aria-hidden="true"></i>
    </div>
    <transition name="dropdown">
      <ul
        v-if="open"
        class="select-list"
        role="listbox"
        :aria-activedescendant="'select-option-' + activeIndex"
      >
        <li
          v-for="(option, idx) in options"
          :key="option.value"
          class="select-option"
          :class="{ selected: modelValue === option.value, 'option--active': idx === activeIndex }"
          @click.stop="select(option)"
          :id="'select-option-' + idx"
          role="option"
          :aria-selected="modelValue === option.value"
          @mouseenter="activeIndex = idx"
        >
          <slot name="option" :option="option">
            {{ option.label }}
          </slot>
        </li>
      </ul>
    </transition>
    <transition name="fade-slide">
      <div v-if="error" class="select-error" id="select-error-message" role="alert">{{ error }}</div>
    </transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: [String, Number],
  options: { type: Array, required: true },
  label: String,
  placeholder: { type: String, default: 'Select...' },
  error: String,
  disabled: Boolean
})
const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const activeIndex = ref(-1)

const selectedLabel = computed(() => {
  const found = props.options.find(o => o.value === props.modelValue)
  return found ? found.label : props.placeholder
})

function toggle() {
  if (!props.disabled) {
    open.value = !open.value
    if (open.value) {
      // Set activeIndex to currently selected or first
      activeIndex.value =
        props.options.findIndex(o => o.value === props.modelValue) !== -1
          ? props.options.findIndex(o => o.value === props.modelValue)
          : 0
    }
  }
}
function openList() {
  if (!open.value && !props.disabled) {
    open.value = true
    activeIndex.value =
      props.options.findIndex(o => o.value === props.modelValue) !== -1
        ? props.options.findIndex(o => o.value === props.modelValue)
        : 0
  }
}
function select(option) {
  if (!props.disabled) {
    emit('update:modelValue', option.value)
    open.value = false
  }
}
function selectActive() {
  if (open.value && activeIndex.value >= 0) {
    select(props.options[activeIndex.value])
  }
}
// Close on click outside
function handleClickOutside(event) {
  const el = event.target.closest('.select-group')
  if (!el || el !== event.currentTarget) {
    open.value = false
  }
}
watch(open, val => {
  if (val) setTimeout(() => document.addEventListener('click', handleClickOutside, true), 10)
  else document.removeEventListener('click', handleClickOutside, true)
})
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-card: #181124;
  --color-label: #FFF;
  --color-border: #6D488D;
  --color-error: #ef4444;
  --color-placeholder: #7C7192;
}
.select-group {
  display: flex;
  flex-direction: column;
  gap: 0.33rem;
  margin-bottom: 1.15rem;
  position: relative;
  min-width: 180px;
  font-family: Inter, system-ui, sans-serif;
  outline: none;
}
.select-label {
  font-weight: 600;
  color: var(--color-label);
  margin-bottom: 1px;
  transition: color 0.2s;
  font-size: 1.01rem;
}
.select-control {
  display: flex;
  align-items: center;
  background: var(--color-bg-card);
  border-radius: 1.1rem;
  border: 2px solid var(--color-border);
  transition: border 0.3s cubic-bezier(.22,1,.36,1), box-shadow 0.3s;
  box-shadow: 0 2px 8px rgba(90,17,136,0.06);
  position: relative;
  padding: 1.08rem 1.2rem;
  cursor: pointer;
  font-size: 1rem;
  min-height: 48px;
}
.select-group--open .select-control {
  border-color: var(--color-accent);
  box-shadow: 0 0 0 4px rgba(157,56,207,0.10);
}
.select-group--error .select-control {
  border-color: var(--color-error);
  box-shadow: 0 0 0 4px rgba(239,68,68,0.10);
}
.select-group--disabled .select-control {
  background: #211934;
  border-color: #38235f;
  opacity: 0.58;
  pointer-events: none;
}
.select-value {
  flex: 1;
  color: var(--color-label);
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.01em;
}
.bi {
  font-size: 1.22em;
  margin-left: 0.9rem;
  color: var(--color-placeholder);
  transition: transform 0.18s, color 0.18s;
  will-change: transform, color;
}
.select-group--open .bi {
  transform: rotate(180deg);
  color: var(--color-accent);
}
.select-list {
  position: absolute;
  left: 0;
  right: 0;
  top: calc(100% + 5px);
  background: var(--color-bg-card);
  border-radius: 1.1rem;
  box-shadow: 0 14px 38px 0 rgba(157,56,207,0.13);
  z-index: 24;
  padding: 5px 0;
  animation: dropdown-in 0.25s cubic-bezier(.22,1,.36,1);
  border: 1.5px solid var(--color-border);
}
@keyframes dropdown-in {
  0% { opacity: 0; transform: translateY(-10px) scale(0.97);}
  100% { opacity: 1; transform: translateY(0) scale(1);}
}
.select-option {
  padding: 12px 26px;
  cursor: pointer;
  color: var(--color-label);
  font-size: 1.01rem;
  transition: background 0.2s, color 0.2s, transform 0.18s;
  border-radius: 0.8rem;
  margin: 2px 10px;
  font-weight: 500;
}
.select-option.selected,
.select-option.option--active {
  background: linear-gradient(90deg, #5A1188 0%, #9D38CF 100%);
  color: #FFF;
  font-weight: 600;
  transform: scale(1.06);
  box-shadow: 0 1px 7px rgba(157,56,207,0.11);
}
.select-option:hover {
  background: #291952;
  color: var(--color-accent);
  transform: scale(1.07);
}
.select-error {
  color: var(--color-error);
  font-size: 0.97em;
  margin-top: 3px;
  min-height: 18px;
  font-weight: 500;
  letter-spacing: 0.01em;
}
.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.25s cubic-bezier(.22,1,.36,1);
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.97);
}
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.28s cubic-bezier(.22,1,.36,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
@media (max-width: 640px) {
  .select-control, .select-option { padding-left: 0.9rem; padding-right: 0.9rem;}
  .select-list { min-width: 160px;}
}
</style>
