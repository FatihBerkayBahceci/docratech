/*
  Project: Kurumsal Sağlık Platformu Frontend
  Component: ColorPicker
  Developer: DocraTech Team - Fatih Berkay Bahceci
*/

<template>
  <div class="color-picker">
    <div class="color-preview" @click="togglePicker">
      <div 
        class="color-swatch"
        :style="{ backgroundColor: modelValue }"
      ></div>
      <span class="color-text">{{ modelValue }}</span>
      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </div>
    
    <div v-if="showPicker" class="color-dropdown">
      <!-- Preset Colors -->
      <div class="preset-colors">
        <h4 class="preset-title">Preset Colors</h4>
        <div class="preset-grid">
          <button
            v-for="color in presetColors"
            :key="color"
            class="preset-color"
            :class="{ 'selected': modelValue === color }"
            :style="{ backgroundColor: color }"
            @click="selectColor(color)"
            :title="color"
          ></button>
        </div>
      </div>
      
      <!-- Color Input -->
      <div class="color-input-section">
        <label class="input-label">Custom Color</label>
        <div class="input-group">
          <input
            type="color"
            :value="modelValue"
            @input="selectColor($event.target.value)"
            class="color-input"
          />
          <input
            type="text"
            :value="modelValue"
            @input="handleTextInput"
            @blur="validateColorInput"
            placeholder="#000000"
            class="text-input"
          />
        </div>
      </div>
      
      <!-- Recent Colors -->
      <div v-if="recentColors.length" class="recent-colors">
        <h4 class="recent-title">Recent Colors</h4>
        <div class="recent-grid">
          <button
            v-for="color in recentColors"
            :key="color"
            class="recent-color"
            :class="{ 'selected': modelValue === color }"
            :style="{ backgroundColor: color }"
            @click="selectColor(color)"
            :title="color"
          ></button>
        </div>
      </div>
      
      <!-- Actions -->
      <div class="picker-actions">
        <button
          type="button"
          @click="resetToDefault"
          class="btn btn-secondary"
        >
          Reset
        </button>
        <button
          type="button"
          @click="closePicker"
          class="btn btn-primary"
        >
          Done
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '#000000'
  },
  presetColors: {
    type: Array,
    default: () => [
      '#000000', '#ffffff', '#f3f4f6', '#9ca3af', '#6b7280',
      '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16',
      '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9',
      '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef',
      '#ec4899', '#f43f5e'
    ]
  },
  defaultColor: {
    type: String,
    default: '#3b82f6'
  }
})

const emit = defineEmits(['update:modelValue'])

const showPicker = ref(false)
const recentColors = ref([])
const textInputValue = ref(props.modelValue)

// Load recent colors from localStorage
onMounted(() => {
  const saved = localStorage.getItem('color-picker-recent')
  if (saved) {
    try {
      recentColors.value = JSON.parse(saved)
    } catch (e) {
      recentColors.value = []
    }
  }
  
  // Close picker when clicking outside
  document.addEventListener('click', handleOutsideClick)
})

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick)
})

const togglePicker = () => {
  showPicker.value = !showPicker.value
}

const closePicker = () => {
  showPicker.value = false
}

const selectColor = (color) => {
  if (isValidColor(color)) {
    emit('update:modelValue', color)
    addToRecent(color)
    textInputValue.value = color
  }
}

const handleTextInput = (event) => {
  textInputValue.value = event.target.value
}

const validateColorInput = () => {
  if (isValidColor(textInputValue.value)) {
    selectColor(textInputValue.value)
  } else {
    textInputValue.value = props.modelValue
  }
}

const resetToDefault = () => {
  selectColor(props.defaultColor)
}

const addToRecent = (color) => {
  if (!recentColors.value.includes(color)) {
    recentColors.value.unshift(color)
    if (recentColors.value.length > 10) {
      recentColors.value = recentColors.value.slice(0, 10)
    }
    localStorage.setItem('color-picker-recent', JSON.stringify(recentColors.value))
  }
}

const isValidColor = (color) => {
  const hexRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/
  const rgbRegex = /^rgb\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*\)$/
  const rgbaRegex = /^rgba\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*[\d.]+\s*\)$/
  const namedColors = ['red', 'blue', 'green', 'yellow', 'orange', 'purple', 'pink', 'brown', 'black', 'white', 'gray', 'cyan', 'magenta']
  
  return hexRegex.test(color) || 
         rgbRegex.test(color) || 
         rgbaRegex.test(color) || 
         namedColors.includes(color.toLowerCase())
}

const handleOutsideClick = (event) => {
  if (!event.target.closest('.color-picker')) {
    showPicker.value = false
  }
}
</script>

<style scoped>
.color-picker {
  position: relative;
  display: inline-block;
}

.color-preview {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 140px;
}

.color-preview:hover {
  border-color: #9ca3af;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.color-swatch {
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 0.25rem;
  border: 1px solid #e5e7eb;
  flex-shrink: 0;
}

.color-text {
  font-size: 0.875rem;
  color: #374151;
  font-family: monospace;
  flex: 1;
}

.color-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 50;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  padding: 1rem;
  min-width: 280px;
  margin-top: 0.25rem;
}

.preset-colors,
.recent-colors {
  margin-bottom: 1rem;
}

.preset-title,
.recent-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.preset-grid,
.recent-grid {
  display: grid;
  grid-template-columns: repeat(11, 1fr);
  gap: 0.25rem;
}

.recent-grid {
  grid-template-columns: repeat(auto-fit, minmax(1.5rem, 1fr));
}

.preset-color,
.recent-color {
  width: 1.5rem;
  height: 1.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.25rem;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
}

.preset-color:hover,
.recent-color:hover {
  transform: scale(1.1);
  z-index: 1;
}

.preset-color.selected,
.recent-color.selected {
  box-shadow: 0 0 0 2px white, 0 0 0 4px #3b82f6;
}

.color-input-section {
  margin-bottom: 1rem;
}

.input-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.input-group {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.color-input {
  width: 2.5rem;
  height: 2.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  cursor: pointer;
  padding: 0;
}

.text-input {
  flex: 1;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-family: monospace;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.text-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.picker-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  padding-top: 0.75rem;
  border-top: 1px solid #e5e7eb;
}

.btn {
  padding: 0.375rem 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: 1px solid transparent;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: white;
  color: #374151;
  border-color: #d1d5db;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}
</style>
