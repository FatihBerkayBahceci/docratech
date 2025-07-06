<template>
  <div
    class="searchable-select-group"
    :class="{
      'searchable-select-group--open': open,
      'searchable-select-group--error': !!error,
      'searchable-select-group--disabled': disabled
    }"
    @click="focusInput"
    tabindex="0"
    role="combobox"
    :aria-expanded="open"
    :aria-disabled="disabled"
    :aria-errormessage="error ? 'searchable-select-error-message' : undefined"
    @keydown.down.prevent="navigateOptions(1)"
    @keydown.up.prevent="navigateOptions(-1)"
    @keydown.enter.prevent="selectActiveOption"
    @keydown.esc="closeDropdown"
  >
    <div class="searchable-select-control" :aria-invalid="!!error">
      <input
        ref="searchInput"
        v-model="searchTerm"
        type="text"
        :placeholder="placeholder"
        :disabled="disabled"
        class="searchable-select-input"
        @input="handleInput"
        @focus="openDropdown"
        @blur="handleBlur"
        autocomplete="off"
      />
      <div class="searchable-select-icons">
        <Icon 
          v-if="selectedOption && searchTerm === selectedOption.label"
          name="check" 
          class="searchable-select-check-icon" 
        />
        <Icon 
          name="chevron-down" 
          class="searchable-select-chevron-icon"
          :class="{ 'searchable-select-chevron-icon--open': open }"
        />
      </div>
    </div>

    <transition name="dropdown">
      <div
        v-if="open && filteredOptions.length > 0"
        class="searchable-select-dropdown"
        role="listbox"
        :aria-activedescendant="'searchable-select-option-' + activeIndex"
      >
        <div
          v-for="(option, idx) in filteredOptions"
          :key="option.value"
          class="searchable-select-option"
          :class="{ 
            'searchable-select-option--selected': modelValue === option.value, 
            'searchable-select-option--active': idx === activeIndex 
          }"
          @click.stop="selectOption(option)"
          :id="'searchable-select-option-' + idx"
          role="option"
          :aria-selected="modelValue === option.value"
          @mouseenter="activeIndex = idx"
        >
          <div class="searchable-select-option-content">
            <div class="searchable-select-option-label">{{ option.label }}</div>
            <div v-if="option.description" class="searchable-select-option-description">
              {{ option.description }}
            </div>
          </div>
          <Icon 
            v-if="modelValue === option.value" 
            name="check" 
            class="searchable-select-option-check" 
          />
        </div>
      </div>
    </transition>

    <div
      v-if="open && filteredOptions.length === 0 && searchTerm"
      class="searchable-select-no-results"
    >
      <Icon name="search" class="searchable-select-no-results-icon" />
      <span>No options match "{{ searchTerm }}"</span>
    </div>

    <transition name="fade-slide">
      <div v-if="error" class="searchable-select-error" id="searchable-select-error-message" role="alert">
        <Icon name="alert-circle" class="searchable-select-error-icon" />
        {{ error }}
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import Icon from '@/components/visual/Icon.vue'

const props = defineProps({
  modelValue: [String, Number],
  options: { type: Array, required: true },
  placeholder: { type: String, default: 'Search and select...' },
  error: String,
  success: Boolean,
  disabled: Boolean,
  required: Boolean
})

const emit = defineEmits(['update:modelValue', 'blur'])

const open = ref(false)
const searchTerm = ref('')
const activeIndex = ref(-1)
const searchInput = ref(null)

const selectedOption = computed(() => {
  return props.options.find(option => option.value === props.modelValue)
})

const filteredOptions = computed(() => {
  if (!searchTerm.value) {
    return props.options
  }
  
  const term = searchTerm.value.toLowerCase()
  return props.options.filter(option => 
    option.label.toLowerCase().includes(term) ||
    (option.description && option.description.toLowerCase().includes(term))
  )
})

const focusInput = () => {
  if (!props.disabled && searchInput.value) {
    searchInput.value.focus()
  }
}

const openDropdown = () => {
  if (!props.disabled) {
    open.value = true
    activeIndex.value = filteredOptions.value.findIndex(option => option.value === props.modelValue)
    if (activeIndex.value === -1) {
      activeIndex.value = 0
    }
  }
}

const closeDropdown = () => {
  open.value = false
  activeIndex.value = -1
  
  // Reset search term to selected option label or empty
  if (selectedOption.value) {
    searchTerm.value = selectedOption.value.label
  } else {
    searchTerm.value = ''
  }
}

const handleInput = () => {
  if (!open.value) {
    openDropdown()
  }
  activeIndex.value = 0
}

const handleBlur = () => {
  // Delay closing to allow option clicks
  setTimeout(() => {
    closeDropdown()
    emit('blur')
  }, 150)
}

const navigateOptions = (direction) => {
  if (!open.value) {
    openDropdown()
    return
  }
  
  const newIndex = activeIndex.value + direction
  if (newIndex >= 0 && newIndex < filteredOptions.value.length) {
    activeIndex.value = newIndex
  }
}

const selectActiveOption = () => {
  if (open.value && activeIndex.value >= 0 && filteredOptions.value[activeIndex.value]) {
    selectOption(filteredOptions.value[activeIndex.value])
  } else if (!open.value) {
    openDropdown()
  }
}

const selectOption = (option) => {
  if (!props.disabled) {
    emit('update:modelValue', option.value)
    searchTerm.value = option.label
    closeDropdown()
  }
}

// Watch for external value changes
watch(() => props.modelValue, (newValue) => {
  const option = props.options.find(opt => opt.value === newValue)
  searchTerm.value = option ? option.label : ''
}, { immediate: true })

// Handle click outside
const handleClickOutside = (event) => {
  const element = event.target.closest('.searchable-select-group')
  if (!element) {
    closeDropdown()
  }
}

watch(open, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      document.addEventListener('click', handleClickOutside, true)
    })
  } else {
    document.removeEventListener('click', handleClickOutside, true)
  }
})
</script>

<style scoped>
.searchable-select-group {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
  position: relative;
  min-width: 200px;
  font-family: Inter, system-ui, sans-serif;
  outline: none;
}

.searchable-select-control {
  display: flex;
  align-items: center;
  background: white;
  border-radius: 0.75rem;
  border: 2px solid #E2E8F0;
  transition: border 0.3s cubic-bezier(.22,1,.36,1), box-shadow 0.3s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  position: relative;
  min-height: 48px;
  cursor: text;
}

.searchable-select-group--open .searchable-select-control {
  border-color: #9D38CF;
  box-shadow: 0 0 0 3px rgba(157, 56, 207, 0.1);
}

.searchable-select-group--error .searchable-select-control {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.searchable-select-group--disabled .searchable-select-control {
  background: #F8FAFC;
  border-color: #E2E8F0;
  opacity: 0.6;
  cursor: not-allowed;
}

.searchable-select-input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  color: #1E293B;
  font-weight: 500;
  line-height: 1.5;
}

.searchable-select-input::placeholder {
  color: #94A3B8;
  font-weight: 400;
}

.searchable-select-input:disabled {
  cursor: not-allowed;
  color: #94A3B8;
}

.searchable-select-icons {
  display: flex;
  align-items: center;
  padding-right: 1rem;
  gap: 0.5rem;
}

.searchable-select-check-icon {
  width: 1rem;
  height: 1rem;
  color: #22C55E;
}

.searchable-select-chevron-icon {
  width: 1.25rem;
  height: 1.25rem;
  color: #64748B;
  transition: transform 0.2s, color 0.2s;
}

.searchable-select-chevron-icon--open {
  transform: rotate(180deg);
  color: #9D38CF;
}

.searchable-select-dropdown {
  position: absolute;
  left: 0;
  right: 0;
  top: calc(100% + 4px);
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  z-index: 50;
  padding: 0.5rem 0;
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #E2E8F0;
}

.searchable-select-option {
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 0 0.5rem;
  border-radius: 0.5rem;
}

.searchable-select-option:hover,
.searchable-select-option--active {
  background: #F8F9FA;
  color: #5A1188;
}

.searchable-select-option--selected {
  background: linear-gradient(90deg, #5A1188 0%, #9D38CF 100%);
  color: white;
}

.searchable-select-option--selected:hover {
  background: linear-gradient(90deg, #4A0F75 0%, #8B31B8 100%);
}

.searchable-select-option-content {
  flex: 1;
}

.searchable-select-option-label {
  font-size: 0.95rem;
  font-weight: 500;
  line-height: 1.4;
}

.searchable-select-option-description {
  font-size: 0.85rem;
  opacity: 0.8;
  margin-top: 0.125rem;
  line-height: 1.3;
}

.searchable-select-option-check {
  width: 1rem;
  height: 1rem;
  margin-left: 0.5rem;
  flex-shrink: 0;
}

.searchable-select-no-results {
  padding: 1rem;
  text-align: center;
  color: #64748B;
  font-size: 0.9rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.searchable-select-no-results-icon {
  width: 1.5rem;
  height: 1.5rem;
  color: #94A3B8;
}

.searchable-select-error {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  color: #ef4444;
  line-height: 1.4;
  margin-top: 0.25rem;
}

.searchable-select-error-icon {
  width: 1rem;
  height: 1rem;
  flex-shrink: 0;
}

/* Dropdown animation */
.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.25s cubic-bezier(.22,1,.36,1);
}

.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.98);
}

/* Error animation */
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(.22,1,.36,1);
}

.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

/* Scrollbar styling */
.searchable-select-dropdown::-webkit-scrollbar {
  width: 6px;
}

.searchable-select-dropdown::-webkit-scrollbar-track {
  background: #F1F5F9;
  border-radius: 3px;
}

.searchable-select-dropdown::-webkit-scrollbar-thumb {
  background: #CBD5E1;
  border-radius: 3px;
}

.searchable-select-dropdown::-webkit-scrollbar-thumb:hover {
  background: #94A3B8;
}

/* Focus styles for accessibility */
.searchable-select-group:focus-within .searchable-select-control {
  border-color: #9D38CF;
  box-shadow: 0 0 0 3px rgba(157, 56, 207, 0.1);
}

/* Responsive design */
@media (max-width: 640px) {
  .searchable-select-dropdown {
    max-height: 200px;
  }
  
  .searchable-select-option {
    padding: 0.625rem 0.75rem;
  }
  
  .searchable-select-input {
    padding: 0.625rem 0.75rem;
  }
}
</style> 