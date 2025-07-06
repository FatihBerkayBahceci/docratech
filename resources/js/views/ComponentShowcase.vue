<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Component Showcase</h1>
            <p class="mt-2 text-gray-600">Tüm Vue.js bileşenlerinin otomatik ve canlı önizlemesi</p>
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="toggleTheme"
              class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
              </svg>
            </button>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-500">Toplam:</span>
              <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                {{ filteredComponents.length }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search and Filter -->
      <div class="mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Bileşen ara..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Components Grid -->
      <div v-if="filteredComponents.length > 0">
        <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="comp in filteredComponents"
            :key="comp.name"
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
          >
            <!-- Component Header -->
            <div class="p-6 border-b border-gray-100">
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">{{ comp.name }}</h3>
                  <p class="mt-1 text-xs text-gray-500 break-all">{{ comp.path }}</p>
                </div>
              </div>
            </div>

            <!-- Component Preview -->
            <div class="p-6 bg-gray-50 min-h-[120px] flex items-center justify-center">
              <component :is="comp.component" />
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Bileşen bulunamadı</h3>
        <p class="mt-1 text-sm text-gray-500">Arama kriterlerinize uygun bileşen bulunamadı.</p>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Tüm bileşenleri otomatik olarak import et
const modules = import.meta.glob('@/components/**/*.vue', { eager: true })

const components = Object.entries(modules).map(([path, mod]) => {
  // Dosya adını bileşen adı olarak kullan
  const name = path.split('/').pop().replace('.vue', '')
  return {
    name,
    path,
    component: mod.default
  }
})

function isDangerousComponent(name) {
  const keywords = ['Modal', 'Dialog', 'Layout', 'Provider']
  return keywords.some(k => name.toLowerCase().includes(k.toLowerCase()))
}

const searchQuery = ref('')
const filteredComponents = computed(() => {
  const q = searchQuery.value.toLowerCase()
  return components.filter(c =>
    !isDangerousComponent(c.name) &&
    (q === '' || c.name.toLowerCase().includes(q) || c.path.toLowerCase().includes(q))
  )
})

const toggleTheme = () => {
  // Theme toggle logic
  document.documentElement.classList.toggle('dark')
}
</script>

<style scoped>
/* Custom styles for the showcase */
.component-preview {
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Smooth transitions */
.transition-all {
  transition: all 0.3s ease;
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: theme('colors.docratech.surface');
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: theme('colors.docratech.borderLight');
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: theme('colors.docratech.secondary');
}
</style> 