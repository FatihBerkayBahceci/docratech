<!--
  Project: Corporate Healthcare Platform UI Library
  Component: FileInput
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="flex flex-col gap-2 mb-5 relative"
    :class="{
      'opacity-60 pointer-events-none': disabled,
      'file-group--focused': focused,
      'file-group--error': !!error,
      'file-group--dragging': dragging
    }"
  >
    <label v-if="label" class="font-semibold text-[var(--color-label)] mb-1">{{ label }}</label>
    <div
      class="flex flex-col items-center justify-center px-6 py-10 bg-[var(--color-bg-dropzone)] border-2 border-dashed border-[var(--color-border)] rounded-2xl cursor-pointer transition-all duration-300
      hover:border-[var(--color-accent)] hover:shadow-[0_4px_24px_rgba(157,56,207,0.09)]
      focus-within:border-[var(--color-accent)] focus-within:ring-2 focus-within:ring-[var(--color-accent)]
      outline-none
      "
      tabindex="0"
      role="button"
      :aria-disabled="disabled"
      @click="triggerFileInput"
      @focus="focused = true"
      @blur="focused = false"
      @dragover.prevent="dragging = true"
      @dragleave.prevent="dragging = false"
      @drop.prevent="handleDrop"
    >
      <input
        ref="fileInput"
        type="file"
        class="hidden"
        :accept="accept"
        :multiple="multiple"
        @change="handleFileSelect"
        :disabled="disabled"
        tabindex="-1"
      />
      <div class="flex flex-col items-center gap-3 pointer-events-none select-none">
        <i class="bi bi-cloud-upload text-4xl text-[var(--color-accent)] transition-colors"></i>
        <div class="text-center">
          <span class="block font-semibold text-lg text-[var(--color-text)] mb-1">Click or drag files to upload</span>
          <span class="block text-sm text-[var(--color-desc)]">
            {{ accept ? accept : 'All file types' }} • Max {{ formatFileSize(maxSize) }}
          </span>
        </div>
      </div>
    </div>

    <!-- File List -->
    <div v-if="selectedFiles.length > 0" class="mt-3 flex flex-col gap-2">
      <div
        v-for="(file, index) in selectedFiles"
        :key="index"
        class="flex items-center justify-between bg-[var(--color-bg-file)] rounded-xl px-4 py-3 animate-file-in"
      >
        <div class="flex items-center gap-3 min-w-0">
          <i class="bi bi-file-earmark text-xl text-[var(--color-accent)]"></i>
          <div class="flex flex-col min-w-0">
            <span class="font-medium text-[var(--color-text)] text-sm truncate">{{ file.name }}</span>
            <span class="text-xs text-[var(--color-desc)]">{{ formatFileSize(file.size) }}</span>
          </div>
        </div>
        <button
          type="button"
          class="bg-transparent border-none text-[var(--color-error)] hover:bg-[var(--color-error-bg)] rounded-md p-2 transition-transform duration-150 active:scale-95 ml-2"
          :aria-label="'Remove ' + file.name"
          @click="removeFile(index)"
        >
          <i class="bi bi-x text-base"></i>
        </button>
      </div>
    </div>

    <!-- Error State -->
    <transition name="fade-slide">
      <div
        v-if="error"
        class="text-[var(--color-error)] text-sm mt-1 min-h-[18px] select-text font-medium"
        role="alert"
      >{{ error }}</div>
    </transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { ref, watch, computed } from 'vue'

const props = defineProps({
  modelValue: [File, Array],
  label: String,
  accept: String,
  multiple: Boolean,
  maxSize: { type: Number, default: 10 * 1024 * 1024 }, // 10MB
  error: String,
  disabled: Boolean
})
const emit = defineEmits(['update:modelValue', 'error'])

const focused = ref(false)
const dragging = ref(false)
const selectedFiles = ref([])

function triggerFileInput() {
  if (!props.disabled) {
    fileInput.value?.click()
  }
}
const fileInput = ref(null)

function handleFileSelect(event) {
  const files = Array.from(event.target.files)
  processFiles(files)
}

function handleDrop(event) {
  dragging.value = false
  const files = Array.from(event.dataTransfer.files)
  processFiles(files)
}

function processFiles(files) {
  const validFiles = files.filter(file => {
    if (file.size > props.maxSize) {
      emit('error', `${file.name} is too large.`)
      return false
    }
    return true
  })

  if (props.multiple) {
    selectedFiles.value = [...selectedFiles.value, ...validFiles]
    emit('update:modelValue', selectedFiles.value)
  } else {
    selectedFiles.value = validFiles.slice(0, 1)
    emit('update:modelValue', selectedFiles.value[0] || null)
  }
}

function removeFile(index) {
  selectedFiles.value.splice(index, 1)
  if (props.multiple) {
    emit('update:modelValue', selectedFiles.value)
  } else {
    emit('update:modelValue', selectedFiles.value[0] || null)
  }
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Re-sync with modelValue prop externally
watch(() => props.modelValue, val => {
  if (props.multiple) {
    selectedFiles.value = Array.isArray(val) ? val : []
  } else {
    selectedFiles.value = val ? [val] : []
  }
}, { immediate: true })
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-dropzone: #181124;
  --color-bg-file: #2A183D;
  --color-label: #FFF;
  --color-text: #FFF;
  --color-desc: #B1A9C7;
  --color-error: #ef4444;
  --color-error-bg: #2f1515;
  --color-border: #6D488D;
}
.file-group--focused .file-dropzone {
  border-color: var(--color-accent);
  box-shadow: 0 0 0 4px rgba(157,56,207,0.14);
}
.file-group--error .file-dropzone {
  border-color: var(--color-error);
  box-shadow: 0 0 0 4px rgba(239,68,68,0.12);
}
.file-group--dragging .file-dropzone {
  border-color: var(--color-accent);
  background: #2A183D;
  transform: scale(1.025);
  transition: all 0.16s cubic-bezier(.22,1,.36,1);
}
/* File list animation */
@keyframes file-in {
  0% { opacity: 0; transform: translateY(14px);}
  100% { opacity: 1; transform: translateY(0);}
}
.animate-file-in { animation: file-in 0.3s cubic-bezier(.22,1,.36,1);}
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(.22,1,.36,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
/* Responsive for mobile */
@media (max-width: 640px) {
  .px-6 { padding-left: 1rem !important; padding-right: 1rem !important; }
  .py-10 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }
  .px-4 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
  .py-3 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
}
</style>
