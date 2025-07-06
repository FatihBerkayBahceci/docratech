<!--
  Project: Corporate Healthcare Platform UI Library
  Component: SearchInput
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="search-group"
    :class="{
      'search-group--focused': focused,
      'search-group--loading': loading,
      'search-group--has-value': !!modelValue
    }"
  >
    <div class="search-wrapper">
      <i class="bi bi-search search-icon" aria-hidden="true"></i>
      <input
        type="text"
        class="search-input"
        :placeholder="placeholder"
        :value="modelValue"
        @focus="focused = true"
        @blur="focused = false"
        @input="$emit('update:modelValue', $event.target.value)"
        autocomplete="off"
        spellcheck="false"
        :aria-label="placeholder"
      />
      <div v-if="loading" class="search-loading" aria-label="Loading" role="status">
        <div class="spinner"></div>
      </div>
      <button
        v-if="clearable && modelValue"
        class="search-clear"
        @click="clear"
        type="button"
        aria-label="Clear search"
        tabindex="0"
      >
        <i class="bi bi-x"></i>
      </button>
      <slot name="append"></slot>
    </div>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref } from 'vue'

const props = defineProps({
  modelValue: String,
  placeholder: { type: String, default: 'Search...' },
  loading: Boolean,
  clearable: { type: Boolean, default: true }
})
const emit = defineEmits(['update:modelValue', 'clear'])

const focused = ref(false)

function clear() {
  emit('update:modelValue', '')
  emit('clear')
}
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-input: #181124;
  --color-label: #FFF;
  --color-desc: #B1A9C7;
  --color-placeholder: #7C7192;
  --color-border: #6D488D;
}
.search-group {
  position: relative;
  margin-bottom: 1.25rem;
  font-family: Inter, system-ui, sans-serif;
}
.search-wrapper {
  display: flex;
  align-items: center;
  background: var(--color-bg-input);
  border-radius: 0.85rem;
  border: 2px solid var(--color-border);
  transition: border 0.3s cubic-bezier(.22,1,.36,1), box-shadow 0.3s;
  box-shadow: 0 2px 12px rgba(90,17,136,0.07);
  position: relative;
  overflow: hidden;
}
.search-group--focused .search-wrapper,
.search-group--loading .search-wrapper {
  border-color: var(--color-accent);
  box-shadow: 0 0 0 4px rgba(157,56,207,0.10);
}
.search-icon {
  margin-left: 1rem;
  color: var(--color-desc);
  font-size: 1.18em;
  transition: color 0.2s;
}
.search-group--focused .search-icon {
  color: var(--color-accent);
}
.search-input {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 1.05rem;
  padding: 1rem 1.1rem;
  color: var(--color-label);
  border-radius: 0.85rem;
  transition: color 0.2s;
}
.search-input::placeholder {
  color: var(--color-placeholder);
  opacity: 0.88;
  font-weight: 400;
  letter-spacing: 0.01em;
}
.search-group--focused .search-input::placeholder {
  color: var(--color-accent);
  opacity: 1;
}
.search-loading {
  margin-right: 1rem;
  display: flex;
  align-items: center;
}
.spinner {
  width: 18px;
  height: 18px;
  border: 2.5px solid #2A183D;
  border-top: 2.5px solid var(--color-accent);
  border-radius: 50%;
  animation: spin 0.65s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg);}
  100% { transform: rotate(360deg);}
}
.search-clear {
  background: none;
  border: none;
  color: var(--color-desc);
  cursor: pointer;
  padding: 9px;
  margin-right: 10px;
  border-radius: 7px;
  transition: all 0.18s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.search-clear:hover,
.search-clear:focus-visible {
  background: #291952;
  color: var(--color-accent);
  transform: scale(1.11);
  outline: none;
}
.search-clear i {
  font-size: 1.11em;
}
</style>
