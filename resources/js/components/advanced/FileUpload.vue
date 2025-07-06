<!-- 
  Project: Enterprise Health File Upload (Brandkit Revamp)
  Brandkit: DocraTech | Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section class="w-full">
    <!-- File Drop Zone (with animation) -->
    <Transition name="fade-scale">
      <div
        v-if="!uploading"
        class="group relative flex flex-col items-center justify-center border-2 border-dashed rounded-3xl p-10 sm:p-7 cursor-pointer bg-gradient-to-tr from-brand via-accent/20 to-brand-dark transition-all duration-300 file-upload-zone"
        :class="{
          'ring-4 ring-brand/30 bg-accent/10 scale-[1.025] shadow-brand-xl': isDragOver,
          'opacity-50 pointer-events-none': disabled
        }"
        tabindex="0"
        role="button"
        :aria-disabled="disabled"
        @click="triggerFileInput"
        @keydown.enter.space="triggerFileInput"
        @dragover="handleDragOver"
        @dragleave="handleDragLeave"
        @drop="handleDrop"
        aria-label="Dosya yükleme alanı"
      >
        <!-- Hidden Input -->
        <input
          ref="fileInput"
          type="file"
          :multiple="multiple"
          :accept="accept"
          :disabled="disabled"
          class="sr-only"
          @change="handleFileSelect"
          tabindex="-1"
        />
        <!-- Content -->
        <div class="flex flex-col items-center gap-2">
          <Icon name="upload-cloud" size="xl"
                class="text-accent group-hover:scale-110 transition-transform duration-200" />
          <h3 class="font-semibold text-xl text-brand-dark dark:text-accent">{{ title }}</h3>
          <p class="text-sm text-accent/80 dark:text-accent/60">{{ description }}</p>
        </div>
        <!-- Micro Interaction: Drag state -->
        <Transition name="fade-slide">
          <div
            v-if="isDragOver"
            class="absolute inset-0 flex items-center justify-center pointer-events-none"
          >
            <span class="animate-bounce text-brand font-bold text-base">Bırakabilirsin!</span>
          </div>
        </Transition>
        <!-- CTA Row -->
        <div v-if="!disabled" class="flex items-center gap-2 mt-5 flex-wrap justify-center">
          <AppButton variant="gradient" size="sm"
                     class="rounded-xl px-5 font-semibold hover:scale-105 active:scale-100 shadow-brand-sm transition-transform"
                     @click.stop.prevent="triggerFileInput"
                     aria-label="Dosya seç">
            Dosya Seç
          </AppButton>
          <span class="text-xs text-brand font-medium">veya</span>
          <span class="text-xs font-semibold text-accent/90">dosyaları buraya sürükleyin</span>
        </div>
      </div>
    </Transition>

    <!-- File List + Loading Skeleton -->
    <TransitionGroup name="fade-list" tag="div" class="flex flex-col gap-3 mt-5">
      <!-- Skeleton Loading State -->
      <div v-if="skeletonLoading" v-for="n in 2" :key="'skel-'+n"
           class="animate-pulse flex items-center gap-4 p-4 rounded-2xl bg-brand-dark/30">
        <div class="w-9 h-9 bg-accent/30 rounded-full"></div>
        <div class="flex-1 space-y-2">
          <div class="h-3 w-2/3 rounded bg-brand/20"></div>
          <div class="h-2 w-1/3 rounded bg-accent/20"></div>
        </div>
      </div>
      <!-- File Items -->
      <div
        v-for="(file, idx) in files"
        :key="file.id"
        class="flex flex-col md:flex-row md:items-center justify-between gap-2 p-5 bg-bg-card dark:bg-bg-dark border border-accent/20 rounded-2xl shadow-md hover:shadow-brand-xl transition-shadow relative group"
        :class="{ 'border-red-400 bg-red-50/80 dark:bg-red-950/50 animate-shake': file.error }"
      >
        <!-- File Info -->
        <div class="flex items-center gap-3 flex-1 min-w-0">
          <Icon :name="getFileIcon(file)" size="md" class="text-brand" />
          <div class="flex flex-col min-w-0">
            <span class="text-base font-medium text-brand-dark dark:text-accent truncate" :title="file.name">{{ file.name }}</span>
            <span class="text-xs text-accent/80">{{ formatFileSize(file.size) }}</span>
          </div>
        </div>
        <!-- Status + Actions -->
        <div class="flex items-center gap-3">
          <!-- Progress -->
          <div v-if="file.uploading" class="flex items-center gap-2 min-w-[100px]">
            <ProgressBar :value="file.progress" size="sm" aria-label="Yükleniyor" />
            <span class="text-xs text-accent font-mono w-9">{{ file.progress }}%</span>
          </div>
          <!-- Error -->
          <Transition name="fade">
            <span v-if="file.error" class="text-xs text-red-600 dark:text-red-400 font-semibold truncate max-w-[150px]" aria-live="polite">
              {{ file.error }}
            </span>
          </Transition>
          <!-- Success -->
          <Transition name="fade">
            <div v-if="file.uploaded" class="flex items-center gap-1 text-xs text-brand font-semibold" aria-live="polite">
              <Icon name="check-circle" size="sm" /> Yüklendi
            </div>
          </Transition>
          <!-- Remove Btn -->
          <AppButton v-if="!file.uploading"
            variant="ghost"
            size="icon-sm"
            @click="removeFile(idx)"
            aria-label="Dosyayı kaldır"
            class="opacity-70 hover:opacity-100 focus:outline-brand"
          >
            <Icon name="x" size="sm" />
          </AppButton>
        </div>
      </div>
    </TransitionGroup>

    <!-- Overall Progress Macro Animation -->
    <Transition name="slide-down-fade">
      <div v-if="uploading && showOverallProgress" class="w-full mt-6 bg-gradient-to-r from-brand-dark/30 to-accent/10 rounded-2xl p-5 shadow-brand-md">
        <div class="flex items-center justify-between mb-2 text-brand-dark text-base font-semibold">
          <span>{{ uploadingCount }} dosya yükleniyor...</span>
          <span>{{ overallProgress }}%</span>
        </div>
        <ProgressBar :value="overallProgress" />
      </div>
    </Transition>

    <!-- Error Summary -->
    <Transition name="fade">
      <div v-if="errorFiles.length > 0" class="w-full mt-4 bg-red-50/90 dark:bg-red-950/70 border border-red-200 dark:border-red-700 rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-2 text-red-700 dark:text-red-300 font-semibold">
          <Icon name="alert-triangle" size="sm" />
          <span>{{ errorFiles.length }} dosya yüklenemedi</span>
        </div>
        <div class="flex flex-col gap-1">
          <div
            v-for="file in errorFiles"
            :key="file.id"
            class="flex items-center justify-between text-xs"
          >
            <span class="font-semibold text-red-700 dark:text-red-200">{{ file.name }}</span>
            <span class="truncate max-w-[160px] text-red-400 dark:text-red-400">{{ file.error }}</span>
          </div>
        </div>
      </div>
    </Transition>
  </section>
</template>

<script setup>
// Project: Enterprise Health File Upload
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, watch } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'
import ProgressBar from '../feedback/ProgressBar.vue'

const props = defineProps({
  title: { type: String, default: 'Dosya Yükle' },
  description: { type: String, default: 'Dosyalarınızı yüklemek için tıklayın veya sürükleyin' },
  multiple: { type: Boolean, default: false },
  accept: { type: String, default: '*' },
  maxSize: { type: Number, default: 10 * 1024 * 1024 },
  maxFiles: { type: Number, default: null },
  disabled: { type: Boolean, default: false },
  autoUpload: { type: Boolean, default: true },
  showOverallProgress: { type: Boolean, default: true }
})

const emit = defineEmits(['file-select', 'file-upload', 'file-remove', 'upload-complete'])

const fileInput = ref(null)
const isDragOver = ref(false)
const files = ref([])
const uploading = ref(false)
const skeletonLoading = ref(false)

const uploadingCount = computed(() => files.value.filter(f => f.uploading).length)
const overallProgress = computed(() => {
  const upFiles = files.value.filter(f => f.uploading)
  if (upFiles.length === 0) return 0
  return Math.round(upFiles.reduce((sum, f) => sum + f.progress, 0) / upFiles.length)
})
const errorFiles = computed(() => files.value.filter(f => f.error))

const triggerFileInput = () => {
  if (!props.disabled) fileInput.value?.click()
}
const handleDragOver = (e) => {
  if (props.disabled) return
  e.preventDefault()
  isDragOver.value = true
}
const handleDragLeave = (e) => {
  if (e.target === e.currentTarget) isDragOver.value = false
}
const handleDrop = (e) => {
  if (props.disabled) return
  e.preventDefault()
  isDragOver.value = false
  const droppedFiles = Array.from(e.dataTransfer.files)
  addFiles(droppedFiles)
}
const handleFileSelect = (e) => {
  addFiles(Array.from(e.target.files))
  e.target.value = ''
}
const addFiles = (newFiles) => {
  if (!newFiles?.length) return

  skeletonLoading.value = true
  setTimeout(() => skeletonLoading.value = false, 600)

  const validFiles = []
  newFiles.forEach(file => {
    let error = null
    if (file.size > props.maxSize) error = `Dosya boyutu çok büyük (${formatFileSize(file.size)})`
    if (props.maxFiles && files.value.length + 1 > props.maxFiles) error = `En fazla ${props.maxFiles} dosya yükleyebilirsiniz`
    validFiles.push({
      id: generateId(),
      file,
      name: file.name,
      size: file.size,
      type: file.type,
      uploading: false,
      uploaded: false,
      progress: 0,
      error
    })
  })

  files.value.push(...validFiles)
  emit('file-select', validFiles)

  if (props.autoUpload) uploadFiles(validFiles.filter(f => !f.error))
}
const uploadFiles = async (fileObjs) => {
  if (!fileObjs?.length) return
  uploading.value = true

  for (const fileObj of fileObjs) {
    fileObj.uploading = true
    fileObj.progress = 0
    try {
      for (let i = 0; i <= 100; i += 10) {
        fileObj.progress = i
        await new Promise(res => setTimeout(res, 80 + Math.random() * 40))
      }
      fileObj.uploaded = true
      fileObj.uploading = false
      emit('file-upload', fileObj)
    } catch (err) {
      fileObj.error = err?.message || 'Yüklenemedi'
      fileObj.uploading = false
    }
  }
  uploading.value = false
  emit('upload-complete', files.value)
}
const removeFile = (idx) => {
  const [removed] = files.value.splice(idx, 1)
  emit('file-remove', removed)
}
const getFileIcon = (file) => {
  if (file.type.startsWith('image/')) return 'image'
  if (file.type.startsWith('video/')) return 'video'
  if (file.type.startsWith('audio/')) return 'music'
  if (file.type.includes('pdf')) return 'file-text'
  if (file.type.includes('word')) return 'file-text'
  if (file.type.includes('excel')) return 'file-text'
  if (file.type.includes('powerpoint')) return 'file-text'
  return 'file'
}
const formatFileSize = (bytes) => {
  if (!bytes) return '0 Bytes'
  const k = 1024, sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
const generateId = () => Math.random().toString(36).substr(2, 9)

watch(isDragOver, (v) => {
  if (v) {
    const escListener = (e) => {
      if (e.key === 'Escape') isDragOver.value = false
    }
    window.addEventListener('keydown', escListener, { once: true })
  }
})
</script>

<style scoped>
:root {
  --brand: #5A1188;
  --accent: #9D38CF;
  --brand-dark: #2A183D;
  --bg-dark: #181124;
  --bg-card: #211733;
  --shadow-brand: 0 8px 28px 0 #5A118825;
}

.bg-brand { background: var(--brand);}
.bg-accent { background: var(--accent);}
.bg-bg-dark { background: var(--bg-dark);}
.bg-bg-card { background: var(--bg-card);}
.text-brand { color: var(--brand);}
.text-accent { color: var(--accent);}
.text-brand-dark { color: var(--brand-dark);}
.shadow-brand-md { box-shadow: 0 6px 18px 0 #9D38CF26;}
.shadow-brand-xl { box-shadow: 0 18px 40px 0 #9D38CF13;}
.shadow-brand-sm { box-shadow: 0 2px 8px 0 #5A118812;}

.file-upload-zone {
  border-color: var(--brand);
  background: linear-gradient(120deg, #5A118820 0%, #9D38CF15 90%);
  transition: box-shadow .22s cubic-bezier(.44,1.3,.51,1), background .22s;
}

.file-upload-zone:focus-visible {
  outline: 3px solid var(--accent);
}

@keyframes shake {
  0% { transform: translateX(0);}
  18% { transform: translateX(-6px);}
  36% { transform: translateX(6px);}
  54% { transform: translateX(-4px);}
  72% { transform: translateX(4px);}
  90% { transform: translateX(-2px);}
  100% { transform: translateX(0);}
}
.animate-shake {
  animation: shake 0.36s cubic-bezier(.36,.07,.19,.97) both;
}

/* Animations (tüm orijinal animasyonlar korundu) */
.fade-scale-enter-active, .fade-scale-leave-active {
  transition: opacity 0.3s cubic-bezier(.55,0,.1,1), transform 0.3s cubic-bezier(.55,0,.1,1);
}
.fade-scale-enter-from, .fade-scale-leave-to { opacity: 0; transform: scale(0.98);}
.fade-scale-leave-from, .fade-scale-enter-to { opacity: 1; transform: scale(1);}
.fade-list-enter-active, .fade-list-leave-active {
  transition: all 0.28s cubic-bezier(.55,1.5,.34,1.13);
}
.fade-list-enter-from, .fade-list-leave-to {
  opacity: 0; transform: translateY(18px);
}
.fade-list-leave-from, .fade-list-enter-to {
  opacity: 1; transform: none;
}
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: opacity 0.28s, transform 0.24s;
}
.fade-slide-enter-from, .fade-slide-leave-to { opacity: 0; transform: translateY(-10px);}
.fade-slide-enter-to, .fade-slide-leave-from { opacity: 1; transform: none;}
.slide-down-fade-enter-active, .slide-down-fade-leave-active {
  transition: opacity 0.22s, transform 0.22s;
}
.slide-down-fade-enter-from, .slide-down-fade-leave-to {
  opacity: 0; transform: translateY(-18px);
}
.slide-down-fade-enter-to, .slide-down-fade-leave-from { opacity: 1; transform: none;}
.fade-enter-active, .fade-leave-active { transition: opacity 0.24s;}
.fade-enter-from, .fade-leave-to { opacity: 0;}
.fade-enter-to, .fade-leave-from { opacity: 1;}
</style>
