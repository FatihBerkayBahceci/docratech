<!--
  Project: Corporate Healthcare Platform UI Library
  Component: MultiSelect
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div class="multi-select" ref="multiSelectRef">
    <div
      class="multi-select-trigger"
      :class="{ 'multi-select-trigger--disabled': disabled }"
      @click="toggleDropdown"
      tabindex="0"
      role="combobox"
      :aria-expanded="isOpen"
      :aria-disabled="disabled"
    >
      <div class="multi-select-selected">
        <div v-if="selectedItems.length === 0" class="multi-select-placeholder">
          {{ placeholder }}
        </div>
        <div v-else class="multi-select-tags">
          <span
            v-for="item in selectedItems"
            :key="item.value"
            class="multi-select-tag"
          >
            {{ item.label }}
            <button
              type="button"
              class="multi-select-tag-remove"
              @click.stop="removeItem(item)"
              :aria-label="`Remove ${item.label}`"
            >
              <Icon name="x" size="xs" />
            </button>
          </span>
        </div>
      </div>
      <Icon name="chevron-down" size="sm" class="multi-select-chevron" :class="{ 'multi-select-chevron-open': isOpen }" />
    </div>

    <Transition name="dropdown">
      <div v-if="isOpen" class="multi-select-dropdown" role="listbox">
        <div v-if="searchable" class="multi-select-search">
          <SearchInput
            v-model="searchQuery"
            placeholder="Search..."
            size="sm"
            @input="filterOptions"
          />
        </div>

        <div class="multi-select-options">
          <div
            v-for="option in filteredOptions"
            :key="option.value"
            class="multi-select-option"
            :class="{ 'multi-select-option-selected': isSelected(option), 'multi-select-option-disabled': option.disabled }"
            @click="toggleOption(option)"
            :aria-selected="isSelected(option)"
            :aria-disabled="option.disabled"
            tabindex="0"
            role="option"
          >
            <Checkbox
              :model-value="isSelected(option)"
              :disabled="option.disabled"
              @update:model-value="toggleOption(option)"
            />
            <span class="multi-select-option-label">{{ option.label }}</span>
          </div>
        </div>

        <div v-if="filteredOptions.length === 0" class="multi-select-empty">
          No results found
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Icon from '../visual/Icon.vue'
import SearchInput from './SearchInput.vue'
import Checkbox from './Checkbox.vue'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Select...' },
  searchable: { type: Boolean, default: true },
  disabled: { type: Boolean, default: false }
})
const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const searchQuery = ref('')
const multiSelectRef = ref(null)

const selectedItems = computed(() =>
  props.options.filter(option => props.modelValue.includes(option.value))
)

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options
  return props.options.filter(option =>
    option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

function toggleDropdown() {
  if (props.disabled) return
  isOpen.value = !isOpen.value
}

function toggleOption(option) {
  if (option.disabled) return

  const newValue = [...props.modelValue]
  const index = newValue.indexOf(option.value)

  if (index > -1) {
    newValue.splice(index, 1)
  } else {
    newValue.push(option.value)
  }

  emit('update:modelValue', newValue)
  emit('change', newValue)
}

function removeItem(item) {
  const newValue = props.modelValue.filter(value => value !== item.value)
  emit('update:modelValue', newValue)
  emit('change', newValue)
}

function isSelected(option) {
  return props.modelValue.includes(option.value)
}

function filterOptions() {
  // No-op; handled by computed
}

function handleClickOutside(event) {
  if (multiSelectRef.value && !multiSelectRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-card: #181124;
  --color-bg-tag: #2A183D;
  --color-text: #FFF;
  --color-desc: #B1A9C7;
  --color-placeholder: #7C7192;
  --color-border: #6D488D;
  --color-border-light: #31214d;
}

/* Main wrapper */
.multi-select {
  position: relative;
  width: 100%;
  font-family: Inter, system-ui, sans-serif;
}
.multi-select-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 2.75rem;
  padding: 0.75rem 1rem;
  background: var(--color-bg-card);
  border: 2px solid var(--color-border);
  border-radius: 1rem;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(.22,1,.36,1);
  font-weight: 500;
  outline: none;
  box-shadow: 0 2px 12px rgba(90,17,136,0.04);
}
.multi-select-trigger--disabled {
  opacity: 0.6;
  pointer-events: none;
}
.multi-select-trigger:hover,
.multi-select-trigger:focus-within {
  border-color: var(--color-accent);
  box-shadow: 0 0 0 4px rgba(157,56,207,0.12);
}
.multi-select-selected {
  flex: 1;
  min-width: 0;
}
.multi-select-placeholder {
  color: var(--color-placeholder);
  font-size: 1rem;
}
.multi-select-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
}
.multi-select-tag {
  display: inline-flex;
  align-items: center;
  gap: 0.2rem;
  padding: 0.2rem 0.7rem 0.2rem 0.7rem;
  background: var(--color-bg-tag);
  color: var(--color-accent);
  border-radius: 999px;
  font-size: 0.93rem;
  font-weight: 500;
  transition: background 0.2s, color 0.2s;
  animation: tag-in 0.25s cubic-bezier(.22,1,.36,1);
}
@keyframes tag-in {
  0% { opacity: 0; transform: scale(0.8);}
  100% { opacity: 1; transform: scale(1);}
}
.multi-select-tag-remove {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.1em;
  height: 1.1em;
  background: none;
  border: none;
  color: var(--color-accent);
  cursor: pointer;
  border-radius: 50%;
  transition: background 0.15s, transform 0.15s;
  margin-left: 0.2em;
  font-size: 1em;
}
.multi-select-tag-remove:hover {
  background: #321c4d;
  color: #fff;
  transform: scale(1.17);
}
.multi-select-chevron {
  color: var(--color-desc);
  transition: transform 0.25s cubic-bezier(.22,1,.36,1);
  flex-shrink: 0;
}
.multi-select-chevron-open {
  transform: rotate(180deg);
}
.multi-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: var(--color-bg-card);
  border: 2px solid var(--color-border);
  border-radius: 1rem;
  box-shadow: 0 10px 32px -4px rgba(90,17,136,0.16);
  z-index: 40;
  margin-top: 0.4rem;
  max-height: 320px;
  overflow: hidden;
  animation: dropdown-in 0.23s cubic-bezier(.22,1,.36,1);
}
@keyframes dropdown-in {
  0% { opacity: 0; transform: translateY(-16px) scale(0.98);}
  100% { opacity: 1; transform: translateY(0) scale(1);}
}
.multi-select-search {
  padding: 0.8rem;
  border-bottom: 1px solid var(--color-border-light);
  background: var(--color-bg-card);
}
.multi-select-options {
  max-height: 200px;
  overflow-y: auto;
}
.multi-select-option {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  padding: 0.85rem 1.1rem;
  cursor: pointer;
  transition: background 0.2s cubic-bezier(.22,1,.36,1);
  font-size: 1rem;
  color: var(--color-text);
  border-radius: 0.7rem;
}
.multi-select-option:hover {
  background: #26183b;
}
.multi-select-option-selected {
  background: #351d55;
}
.multi-select-option-disabled {
  color: #998fc1 !important;
  opacity: 0.6;
  pointer-events: none;
}
.multi-select-option-label {
  flex: 1;
  font-size: 1rem;
  color: var(--color-text);
  font-weight: 500;
}
.multi-select-empty {
  padding: 1.1rem;
  text-align: center;
  color: var(--color-desc);
  font-size: 1rem;
  opacity: 0.8;
}

/* Dropdown micro animation */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.23s cubic-bezier(.22,1,.36,1);
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-12px) scale(0.97);
}

/* Mobile responsive */
@media (max-width: 640px) {
  .multi-select-dropdown { left: -1rem; right: -1rem;}
  .multi-select-trigger { padding: 0.65rem 0.7rem;}
  .multi-select-option { padding: 0.65rem 0.7rem;}
}
</style>
