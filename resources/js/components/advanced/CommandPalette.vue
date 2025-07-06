<!--
  CommandPalette.vue
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Marka kiti, enterprise micro UX, erişilebilirlik ve animasyon odaklı command palette
-->

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-[9999] bg-gradient-to-br from-[#2A183Dd9] to-[#3f217acc] flex items-start justify-center px-3 pt-16 md:pt-28 backdrop-blur-sm"
        @click="close"
        aria-modal="true"
        role="dialog"
      >
        <div
          class="w-full max-w-xl bg-[#211232] border-2 border-[#34215a] rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-modal-pop"
          @click.stop
          tabindex="0"
        >
          <!-- Header -->
          <div class="flex items-center px-7 py-5 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] border-b border-[#34215a]">
            <div class="flex items-center flex-1 bg-[#23132b] border border-[#34215a] rounded-lg px-3 py-2 gap-3">
              <Icon name="search" size="sm" class="text-[#9D38CF]" />
              <input
                ref="searchInput"
                v-model="searchQuery"
                type="text"
                class="flex-1 bg-transparent outline-none text-white placeholder-[#a1a1b5] font-inter text-[1.08rem]"
                placeholder="Komut veya arama yapın..."
                @keydown="handleKeydown"
                @input="handleSearch"
                aria-label="Komut veya arama"
              />
            </div>
            <button
              type="button"
              class="ml-3 p-2 rounded-lg text-[#9D38CF] hover:bg-[#9d38cf25] hover:text-white transition"
              @click="close"
              aria-label="Kapat"
            >
              <Icon name="x" size="sm" />
            </button>
          </div>
          <!-- Content -->
          <div class="flex-1 overflow-y-auto max-h-[390px] px-5 py-4 bg-[#211232]">
            <!-- No result -->
            <div v-if="searchQuery && filteredCommands.length === 0" class="flex flex-col items-center py-14 text-[#9D38CF] animate-fade-in-up">
              <Icon name="search" size="lg" />
              <p class="mt-2 font-semibold">Sonuç bulunamadı</p>
              <p class="text-xs text-[#a1a1b5]">Farklı anahtar kelimeler deneyin</p>
            </div>
            <!-- Recent -->
            <div v-else-if="!searchQuery && recentCommands.length > 0" class="mb-3">
              <div class="text-xs text-[#9D38CF] font-semibold mb-1">Son Kullanılan</div>
              <div class="flex flex-col gap-[1px]">
                <div
                  v-for="command in recentCommands"
                  :key="command.id"
                  class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition hover:bg-[#9d38cf14] text-white"
                  @click="executeCommand(command)"
                >
                  <Icon :name="command.icon" size="sm" />
                  <span class="flex-1 text-sm font-medium">{{ command.title }}</span>
                  <span v-if="command.shortcut" class="text-xs bg-[#34215a] text-[#9D38CF] rounded px-2 py-0.5 font-mono ml-2">{{ command.shortcut }}</span>
                </div>
              </div>
            </div>
            <!-- Commands (filtered) -->
            <div v-if="filteredCommands.length > 0" class="mb-2">
              <div class="text-xs text-[#9D38CF] font-semibold mb-1">Komutlar</div>
              <div>
                <div
                  v-for="(command, index) in filteredCommands"
                  :key="command.id"
                  class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition font-medium"
                  :class="selectedIndex === index
                    ? 'bg-[#9d38cf18] text-[#9D38CF]'
                    : 'text-white hover:bg-[#9d38cf10] hover:text-[#9D38CF]'
                  "
                  @click="executeCommand(command)"
                  @mouseenter="selectedIndex = index"
                >
                  <Icon :name="command.icon" size="sm" />
                  <span class="flex-1 text-sm">{{ command.title }}</span>
                  <span v-if="command.shortcut" class="text-xs bg-[#34215a] text-[#9D38CF] rounded px-2 py-0.5 font-mono ml-2">{{ command.shortcut }}</span>
                </div>
              </div>
            </div>
            <!-- Categories -->
            <div v-if="!searchQuery && categories.length > 0" class="pt-2">
              <div
                v-for="category in categories"
                :key="category.id"
                class="mb-4"
              >
                <div class="text-xs text-[#9D38CF] font-semibold mb-1 uppercase">{{ category.title }}</div>
                <div class="flex flex-col gap-[1px]">
                  <div
                    v-for="command in category.commands"
                    :key="command.id"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition text-white hover:bg-[#9d38cf10] hover:text-[#9D38CF]"
                    @click="executeCommand(command)"
                  >
                    <Icon :name="command.icon" size="sm" />
                    <span class="flex-1 text-sm">{{ command.title }}</span>
                    <span v-if="command.shortcut" class="text-xs bg-[#34215a] text-[#9D38CF] rounded px-2 py-0.5 font-mono ml-2">{{ command.shortcut }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Footer -->
          <div class="py-3 px-7 border-t border-[#34215a] bg-[#211232]">
            <div class="flex justify-center items-center gap-6 flex-wrap text-[#a1a1b5] text-xs font-inter">
              <span class="flex items-center gap-1"><kbd class="kbd">↑↓</kbd> Navigasyon</span>
              <span class="flex items-center gap-1"><kbd class="kbd">Enter</kbd> Seç</span>
              <span class="flex items-center gap-1"><kbd class="kbd">Esc</kbd> Kapat</span>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<!-- Script aynı kalabilir, sadece import path’lerini güncelle -->
<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import Icon from '../visual/Icon.vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  commands: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] }
})
const emit = defineEmits(['update:modelValue', 'command-execute'])

const isOpen = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})
const searchQuery = ref('')
const selectedIndex = ref(0)
const searchInput = ref(null)
const recentCommands = ref([])

const filteredCommands = computed(() => {
  if (!searchQuery.value) return []
  const allCommands = [
    ...props.commands,
    ...props.categories.flatMap(cat => cat.commands)
  ]
  return allCommands.filter(command =>
    command.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    command.keywords?.some(keyword =>
      keyword.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  )
})

function close() {
  isOpen.value = false
  searchQuery.value = ''
  selectedIndex.value = 0
}
function executeCommand(command) {
  // Add to recent commands
  const existingIndex = recentCommands.value.findIndex(cmd => cmd.id === command.id)
  if (existingIndex > -1) recentCommands.value.splice(existingIndex, 1)
  recentCommands.value.unshift(command)
  if (recentCommands.value.length > 5)
    recentCommands.value = recentCommands.value.slice(0, 5)
  emit('command-execute', command)
  close()
}
function handleSearch() {
  selectedIndex.value = 0
}
function handleKeydown(event) {
  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      selectedIndex.value = Math.min(selectedIndex.value + 1, filteredCommands.value.length - 1)
      break
    case 'ArrowUp':
      event.preventDefault()
      selectedIndex.value = Math.max(selectedIndex.value - 1, 0)
      break
    case 'Enter':
      event.preventDefault()
      if (filteredCommands.value[selectedIndex.value])
        executeCommand(filteredCommands.value[selectedIndex.value])
      break
    case 'Escape':
      event.preventDefault()
      close()
      break
  }
}
function handleGlobalKeydown(event) {
  if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
    event.preventDefault()
    isOpen.value = true
  }
  if (event.key === 'Escape' && isOpen.value) {
    close()
  }
}
onMounted(() => {
  document.addEventListener('keydown', handleGlobalKeydown)
})
onUnmounted(() => {
  document.removeEventListener('keydown', handleGlobalKeydown)
})
watch(isOpen, async (newValue) => {
  if (newValue) {
    await nextTick()
    searchInput.value?.focus()
  }
})
</script>

<style scoped>
@keyframes modal-pop {
  from { opacity: 0; transform: scale(0.93) translateY(26px);}
  to { opacity: 1; transform: scale(1) translateY(0);}
}
.animate-modal-pop {
  animation: modal-pop 0.29s cubic-bezier(.32,1.2,.47,1);
}
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(10px);}
  to { opacity: 1; transform: translateY(0);}
}
.animate-fade-in-up {
  animation: fade-in-up 0.29s cubic-bezier(.4,0,.2,1);
}
.kbd {
  display: inline-block;
  padding: 0.13rem 0.46rem;
  font-size: 0.87em;
  font-weight: 600;
  color: #9D38CF;
  background: #181124;
  border: 1px solid #34215a;
  border-radius: 0.28rem;
  font-family: monospace;
}
</style>
