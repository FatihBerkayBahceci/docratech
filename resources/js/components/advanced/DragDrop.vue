<!-- 
  Project: DocraTech Enterprise Drag & Drop Component
  Enterprise Brandkit Revamp - Mor/Lila Palet
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div class="drag-drop bg-bg-primary dark:bg-bg-dark border border-border dark:border-border-dark rounded-3xl shadow-2xl overflow-hidden relative">
    <!-- Header -->
    <div class="drag-drop-header flex items-center justify-between gap-4 px-8 py-5 border-b border-border dark:border-border-dark bg-bg-header dark:bg-bg-header-dark">
      <h3 v-if="title" class="drag-drop-title text-2xl font-semibold text-brand tracking-tight select-none">
        {{ title }}
      </h3>
      <div class="drag-drop-actions flex items-center gap-3">
        <slot name="actions" />
      </div>
    </div>

    <!-- Zones -->
    <div class="drag-drop-container grid grid-cols-1 md:grid-cols-2 gap-8 px-8 py-6 min-h-[420px] transition-all duration-300">
      <!-- Source Zone -->
      <div
        class="drag-drop-zone drag-drop-source group"
        :class="{
          'drag-drop-zone-active ring-4 ring-brand-gradient bg-brand/5 scale-[1.01] shadow-brand-md': isDragging && dragOrigin === 'source',
          'opacity-95 scale-[1.02] shadow-brand-xl': dragOverSource,
        }"
        @dragover="onDragOver('source', $event)"
        @dragleave="onDragLeave('source', $event)"
        @drop="onDrop('source', $event)"
        tabindex="0"
        role="region"
        :aria-label="`${sourceTitle} drop zone`"
      >
        <div class="drag-drop-zone-header flex items-center justify-between mb-4 pb-2 border-b border-border dark:border-border-dark">
          <h4 class="drag-drop-zone-title text-lg font-semibold text-brand-dark dark:text-accent">{{
            sourceTitle
          }}</h4>
          <span
            class="drag-drop-zone-count text-xs px-3 py-1 rounded-xl bg-gradient-to-r from-brand to-accent text-white font-medium shadow-brand-sm"
          >
            {{ sourceItems.length }} öğe
          </span>
        </div>
        <!-- Loading State -->
        <div v-if="loadingSource" class="flex flex-col gap-2 py-7 animate-pulse">
          <div v-for="i in 3" :key="i" class="h-6 bg-brand/10 rounded-xl w-5/6 mx-auto"></div>
        </div>
        <div v-else class="drag-drop-items flex flex-col gap-4 min-h-[180px] animate-fade-in">
          <transition-group name="list-fade" tag="div">
            <div
              v-for="item in sourceItems"
              :key="item.id"
              class="drag-drop-item group"
              :class="{
                'drag-drop-item-dragging opacity-40 scale-95 animate-pulse': draggingItem?.id === item.id,
              }"
              draggable="true"
              @dragstart="onDragStart(item, 'source', $event)"
              @dragend="onDragEnd"
              tabindex="0"
              role="option"
              :aria-grabbed="draggingItem?.id === item.id"
              @keydown.space.prevent="onKeyboardDrag(item, 'source')"
              @keydown.enter.prevent="onKeyboardDrag(item, 'source')"
            >
              <slot name="item" :item="item">
                <div class="drag-drop-item-content flex items-center gap-3">
                  <Icon
                    v-if="item.icon"
                    :name="item.icon"
                    size="sm"
                    class="text-brand-dark group-hover:scale-115 group-hover:rotate-3 transition-transform duration-200"
                  />
                  <span class="drag-drop-item-text text-base font-medium text-txt dark:text-txt-dark">
                    {{ item.label }}
                  </span>
                </div>
              </slot>
            </div>
          </transition-group>
          <!-- Empty State -->
          <div v-if="!sourceItems.length" class="text-center text-accent/70 text-base py-10 animate-fade-in-slow">
            Kaynak listesi boş.
          </div>
        </div>
      </div>
      <!-- Target Zone -->
      <div
        class="drag-drop-zone drag-drop-target group"
        :class="{
          'drag-drop-zone-active ring-4 ring-accent-gradient bg-accent/10 scale-[1.01] shadow-brand-md': isDragging && dragOrigin === 'target',
          'drag-drop-zone-dragover scale-[1.02] bg-accent/10 shadow-brand-xl': dragOverTarget,
        }"
        @dragover="onDragOver('target', $event)"
        @dragleave="onDragLeave('target', $event)"
        @drop="onDrop('target', $event)"
        tabindex="0"
        role="region"
        :aria-label="`${targetTitle} drop zone`"
      >
        <div class="drag-drop-zone-header flex items-center justify-between mb-4 pb-2 border-b border-border dark:border-border-dark">
          <h4 class="drag-drop-zone-title text-lg font-semibold text-accent">{{
            targetTitle
          }}</h4>
          <span
            class="drag-drop-zone-count text-xs px-3 py-1 rounded-xl bg-gradient-to-r from-accent to-brand text-white font-medium shadow-brand-sm"
          >
            {{ targetItems.length }} öğe
          </span>
        </div>
        <!-- Loading State -->
        <div v-if="loadingTarget" class="flex flex-col gap-2 py-7 animate-pulse">
          <div v-for="i in 3" :key="i" class="h-6 bg-accent/10 rounded-xl w-5/6 mx-auto"></div>
        </div>
        <div v-else class="drag-drop-items flex flex-col gap-4 min-h-[180px] animate-fade-in">
          <transition-group name="list-fade" tag="div">
            <div
              v-for="item in targetItems"
              :key="item.id"
              class="drag-drop-item group"
              :class="{
                'drag-drop-item-dragging opacity-40 scale-95 animate-pulse': draggingItem?.id === item.id,
              }"
              draggable="true"
              @dragstart="onDragStart(item, 'target', $event)"
              @dragend="onDragEnd"
              tabindex="0"
              role="option"
              :aria-grabbed="draggingItem?.id === item.id"
              @keydown.space.prevent="onKeyboardDrag(item, 'target')"
              @keydown.enter.prevent="onKeyboardDrag(item, 'target')"
            >
              <slot name="item" :item="item">
                <div class="drag-drop-item-content flex items-center gap-3">
                  <Icon
                    v-if="item.icon"
                    :name="item.icon"
                    size="sm"
                    class="text-accent group-hover:scale-115 group-hover:-rotate-3 transition-transform duration-200"
                  />
                  <span class="drag-drop-item-text text-base font-medium text-txt dark:text-txt-dark">
                    {{ item.label }}
                  </span>
                </div>
              </slot>
            </div>
          </transition-group>
          <!-- Empty State -->
          <div v-if="!targetItems.length" class="text-center text-accent/70 text-base py-10 animate-fade-in-slow">
            Hedef listesi boş.
          </div>
        </div>
      </div>
    </div>

    <!-- Drop Preview -->
    <transition name="fade">
      <div
        v-if="isDragging"
        class="drag-drop-preview fixed z-50 pointer-events-none px-5 py-2 rounded-2xl bg-gradient-to-r from-brand to-accent text-white shadow-xl flex items-center gap-2 font-semibold"
        :style="previewStyle"
        aria-live="assertive"
        aria-atomic="true"
      >
        <Icon v-if="draggingItem?.icon" :name="draggingItem.icon" size="sm" class="animate-bounce" />
        <span>{{ draggingItem?.label }}</span>
      </div>
    </transition>

    <!-- Error/Status Slot -->
    <slot name="status" />
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { ref } from 'vue'
import Icon from '../visual/Icon.vue'

const props = defineProps({
  title: { type: String, default: null },
  sourceItems: { type: Array, default: () => [] },
  targetItems: { type: Array, default: () => [] },
  sourceTitle: { type: String, default: 'Kaynak' },
  targetTitle: { type: String, default: 'Hedef' },
  allowBack: { type: Boolean, default: true },
  loadingSource: { type: Boolean, default: false },
  loadingTarget: { type: Boolean, default: false }
})
const emit = defineEmits(['item-move', 'item-drop'])

const isDragging = ref(false)
const draggingItem = ref(null)
const dragOrigin = ref(null)
const dragOverSource = ref(false)
const dragOverTarget = ref(false)
const previewStyle = ref({ left: '-9999px', top: '-9999px' })

function updatePreviewPos(e) {
  previewStyle.value = {
    left: e.clientX + 20 + 'px',
    top: e.clientY - 28 + 'px'
  }
}
function onDragStart(item, origin, event) {
  isDragging.value = true
  draggingItem.value = item
  dragOrigin.value = origin
  document.body.style.userSelect = 'none'
  window.addEventListener('mousemove', updatePreviewPos)
  if (event.dataTransfer) {
    event.dataTransfer.effectAllowed = 'move'
    event.dataTransfer.setData('text/plain', JSON.stringify(item))
  }
}
function onDragEnd() {
  isDragging.value = false
  draggingItem.value = null
  dragOrigin.value = null
  dragOverSource.value = false
  dragOverTarget.value = false
  document.body.style.userSelect = ''
  window.removeEventListener('mousemove', updatePreviewPos)
  previewStyle.value = { left: '-9999px', top: '-9999px' }
}
function onDragOver(zone, event) {
  event.preventDefault()
  if (zone === 'source') dragOverSource.value = true
  if (zone === 'target') dragOverTarget.value = true
}
function onDragLeave(zone, event) {
  if (!event.currentTarget.contains(event.relatedTarget)) {
    if (zone === 'source') dragOverSource.value = false
    if (zone === 'target') dragOverTarget.value = false
  }
}
function onDrop(zone, event) {
  event.preventDefault()
  if (!draggingItem.value) return
  const from = dragOrigin.value
  const to = zone
  if (from !== to && (to !== 'source' || props.allowBack)) {
    emit('item-move', {
      item: draggingItem.value,
      from,
      to
    })
  }
  emit('item-drop', {
    item: draggingItem.value,
    target: to
  })
  onDragEnd()
}
function onKeyboardDrag(item, origin) {
  if (!isDragging.value) {
    onDragStart(item, origin, { dataTransfer: { setData(){}, effectAllowed: 'move' } })
  } else {
    const to = origin === 'source' ? 'target' : 'source'
    onDrop(to, { preventDefault(){}})
  }
}
</script>

<style scoped>
/* Brandkit: Ana Mor #5A1188, Accent #9D38CF, Orta #6D488D, Koyu #2A183D */
:root {
  --brand: #5A1188;
  --accent: #9D38CF;
  --brand-dark: #6D488D;
  --accent-dark: #2A183D;
  --brand-gradient: linear-gradient(90deg, #5A1188 0%, #9D38CF 100%);
  --accent-gradient: linear-gradient(90deg, #9D38CF 0%, #5A1188 100%);
  --bg-primary: #181124;
  --bg-dark: #2A183D;
  --bg-header: #211733;
  --bg-header-dark: #1B1530;
  --border: #432A5C;
  --border-dark: #311B47;
  --txt: #FFF;
  --txt-dark: #ECE6F6;
}

body { font-family: 'Inter', sans-serif; }

.drag-drop {
  font-family: 'Inter', sans-serif;
  background: var(--bg-primary);
  color: var(--txt);
  border-radius: 2rem;
  box-shadow: 0 6px 24px 0 #5a11882a;
  transition: box-shadow 0.18s cubic-bezier(.39,1.66,.51,.97);
}

.drag-drop-header {
  background: var(--bg-header);
  border-bottom: 1.5px solid var(--border);
}

.drag-drop-zone {
  transition: all 0.22s cubic-bezier(.42,0,.58,1);
  border: 2px dashed var(--border);
  border-radius: 1.2rem;
  min-height: 270px;
  padding: 1.2rem 1rem;
  background: var(--bg-header);
  box-shadow: 0 1.5px 6px 0 #5a11882a;
}

.drag-drop-zone-active {
  border-image: var(--brand-gradient) 1;
  background: linear-gradient(120deg, #5A11880c 40%, #9D38CF10 100%);
  box-shadow: 0 8px 24px 0 #9d38cf30;
}

.ring-brand-gradient {
  box-shadow: 0 0 0 4px #5A118870, 0 0 12px 2px #9D38CF40;
}

.ring-accent-gradient {
  box-shadow: 0 0 0 4px #9D38CF80, 0 0 16px 2px #5A118830;
}

.shadow-brand-md {
  box-shadow: 0 4px 16px 0 #9D38CF26;
}
.shadow-brand-xl {
  box-shadow: 0 10px 40px 0 #5A118826;
}
.shadow-brand-sm {
  box-shadow: 0 2px 6px 0 #9D38CF13;
}

.drag-drop-item {
  background: #211733e0;
  border: 1.5px solid var(--border);
  border-radius: 1rem;
  padding: 1rem 1.1rem;
  cursor: grab;
  transition: all 0.18s cubic-bezier(.47,1.64,.41,.99);
  user-select: none;
  box-shadow: 0 2px 12px -2px #9d38cf16;
  color: var(--txt);
}
.drag-drop-item:hover,
.drag-drop-item:focus-visible {
  background: linear-gradient(90deg, #5A118813 10%, #9D38CF15 90%);
  transform: scale(1.035) translateY(-2px) rotate(-1deg);
  box-shadow: 0 4px 24px 0 #9D38CF24;
  outline: none;
}
.drag-drop-item-dragging { opacity: 0.45; transform: rotate(6deg) scale(0.97); }
.drag-drop-item[aria-grabbed="true"] { opacity: 0.5; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(12px);}
  to { opacity: 1; transform: none;}
}
.animate-fade-in { animation: fadeIn .7s cubic-bezier(.42,0,.58,1) both;}
.animate-fade-in-slow { animation: fadeIn 1.1s cubic-bezier(.42,0,.58,1) both;}

.list-fade-move,
.list-fade-enter-active,
.list-fade-leave-active {
  transition: all 0.28s cubic-bezier(.44,1.72,.49,1);
}
.list-fade-enter-from, .list-fade-leave-to { opacity: 0; transform: scale(0.95) translateY(12px);}
.list-fade-enter-to, .list-fade-leave-from { opacity: 1; transform: none;}

.drag-drop-preview {
  position: fixed;
  pointer-events: none;
  z-index: 9999;
  background: var(--brand-gradient);
  color: white;
  padding: 0.8rem 1.2rem;
  border-radius: 1.1rem;
  box-shadow: 0 12px 36px -3px #9D38CF50;
  font-size: 1.07rem;
  font-weight: 600;
  min-width: 130px;
  filter: blur(0) brightness(1.08);
  opacity: .97;
  animation: fadeIn .3s cubic-bezier(.45,1.48,.49,1) both;
}
.drag-drop-preview-content { display: flex; align-items: center; gap: 0.7rem;}
.fade-enter-active, .fade-leave-active { transition: opacity .25s;}
.fade-enter-from, .fade-leave-to { opacity: 0;}
.fade-enter-to, .fade-leave-from { opacity: 1;}

/* Responsive & Utilities */
@media (max-width: 1024px) {
  .drag-drop-container { grid-template-columns: 1fr; gap: 1.2rem; padding: 1.2rem; }
  .drag-drop-header { flex-direction: column; gap: 1.5rem; align-items: flex-start;}
  .drag-drop-actions { width: 100%; justify-content: flex-end;}
}
.text-brand { color: var(--brand); }
.text-accent { color: var(--accent); }
.text-brand-dark { color: var(--brand-dark);}
.text-txt { color: var(--txt);}
.text-txt-dark { color: var(--txt-dark);}
.bg-brand { background: var(--brand);}
.bg-accent { background: var(--accent);}
.bg-bg-primary { background: var(--bg-primary);}
.bg-bg-dark { background: var(--bg-dark);}
.bg-bg-header { background: var(--bg-header);}
.bg-bg-header-dark { background: var(--bg-header-dark);}
.border-border { border-color: var(--border);}
.border-border-dark { border-color: var(--border-dark);}
.bg-brand-gradient { background: var(--brand-gradient);}
.bg-accent-gradient { background: var(--accent-gradient);}
</style>
